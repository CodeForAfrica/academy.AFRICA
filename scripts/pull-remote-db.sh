#!/usr/bin/env bash
#
# pull-remote-db.sh
# ------------------------------------------------------------------------------
# Copy the LIVE WP Engine database into the local Docker MySQL, rewriting all
# production URLs to the local dev URL in a serialized-data-safe way.
#
#   Remote (source) : WP Engine  -> https://academy.africa
#   Local  (target) : Docker DB  -> value of SITE_URL in ./.env (http://localhost:8080)
#
# The remote export is produced with `wp search-replace --export`, so the URL
# rewrite happens on the fully-functional live WordPress (correctly handling
# PHP-serialized option/widget data). The live site is NEVER modified: --export
# writes the transformed dump to STDOUT and leaves the live DB untouched.
#
# The local Homebrew `mysql` client hangs on this machine's DB handshake, so all
# local DB work is done through `docker exec` into the DB container instead.
#
# It also (by default) rsyncs the live media library (wp-content/uploads, ~6.4 GB)
# into the local ./wp-content/uploads volume. rsync is incremental, so repeat runs
# only transfer changed/new files.
#
# Usage:
#   ./scripts/pull-remote-db.sh            # DB + uploads (with confirmation)
#   FORCE=1 ./scripts/pull-remote-db.sh    # skip the confirmation prompt
#   DB_ONLY=1 ./scripts/pull-remote-db.sh  # database only, skip uploads rsync
#   UPLOADS_ONLY=1 ./scripts/pull-remote-db.sh  # only rsync uploads, skip the DB
#
# Requirements: docker, ssh, gzip, rsync (all present locally); wp-cli + mysqldump
# on the WP Engine host (already present).
# ------------------------------------------------------------------------------
set -euo pipefail

# ---------------------------------------------------------------------------
# 0. Resolve paths / load .env
# ---------------------------------------------------------------------------
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_ROOT="$(cd "$SCRIPT_DIR/.." && pwd)"
ENV_FILE="$PROJECT_ROOT/.env"
BACKUP_DIR="$PROJECT_ROOT/db-backups"
TS="$(date +%Y%m%d-%H%M%S)"

log()  { printf '\033[1;34m==>\033[0m %s\n' "$*"; }
warn() { printf '\033[1;33m[warn]\033[0m %s\n' "$*"; }
die()  { printf '\033[1;31m[error]\033[0m %s\n' "$*" >&2; exit 1; }

[ -f "$ENV_FILE" ] || die ".env not found at $ENV_FILE"

# Pull only the keys we need from .env (avoids sourcing the salts, which contain
# spaces and shell metacharacters).
get_env() { grep -E "^$1=" "$ENV_FILE" | head -n1 | cut -d= -f2- | sed -e 's/^["'\'']//' -e 's/["'\'']$//'; }

LOCAL_DB_NAME="$(get_env WORDPRESS_DB_NAME)"
LOCAL_DB_USER="$(get_env WORDPRESS_DB_USER)"
LOCAL_DB_PASS="$(get_env WORDPRESS_DB_PASSWORD)"
LOCAL_URL="$(get_env SITE_URL)"

: "${LOCAL_DB_NAME:?WORDPRESS_DB_NAME missing in .env}"
: "${LOCAL_DB_USER:?WORDPRESS_DB_USER missing in .env}"
: "${LOCAL_DB_PASS:?WORDPRESS_DB_PASSWORD missing in .env}"
: "${LOCAL_URL:?SITE_URL missing in .env}"

# ---------------------------------------------------------------------------
# 1. Remote (WP Engine) connection
# ---------------------------------------------------------------------------
SSH_KEY="${SSH_KEY:-$HOME/.ssh/wpengine_ed25519}"
SSH_TARGET="${SSH_TARGET:-academyafrica1@academyafrica1.ssh.wpengine.net}"
SSH=(ssh -i "$SSH_KEY" -o IdentitiesOnly=yes -o ConnectTimeout=20 "$SSH_TARGET")

# Media library (wp-content/uploads)
REMOTE_UPLOADS="${REMOTE_UPLOADS:-/home/wpe-user/sites/academyafrica1/wp-content/uploads}"
LOCAL_UPLOADS="${LOCAL_UPLOADS:-$PROJECT_ROOT/wp-content/uploads}"

# wp-cli image used locally for the serialized-safe URL rewrite
WPCLI_IMAGE="${WPCLI_IMAGE:-wordpress:cli-php8.2}"

# ---------------------------------------------------------------------------
# 2. Resolve what to run (DB, uploads, or both) + locate the DB container
# ---------------------------------------------------------------------------
DO_DB=1; DO_UPLOADS=1
[ "${DB_ONLY:-0}" = "1" ]      && DO_UPLOADS=0
[ "${UPLOADS_ONLY:-0}" = "1" ] && DO_DB=0
[ "$DO_DB" = 1 ] || [ "$DO_UPLOADS" = 1 ] || die "Nothing to do (DB_ONLY and UPLOADS_ONLY both set)."

# Helper: run the mysql / mysqldump client *inside* the DB container.
dbx()   { docker exec -i "$DB_CONTAINER" mysql     -u"$LOCAL_DB_USER" -p"$LOCAL_DB_PASS" "$@"; }
dbdump(){ docker exec    "$DB_CONTAINER" mysqldump -u"$LOCAL_DB_USER" -p"$LOCAL_DB_PASS" "$@"; }

if [ "$DO_DB" = 1 ]; then
  DB_CONTAINER="${DB_CONTAINER:-}"
  if [ -z "$DB_CONTAINER" ]; then
    DB_CONTAINER="$(docker ps --format '{{.Names}} {{.Ports}}' | awk '/:3307->/{print $1; exit}')"
  fi
  [ -n "$DB_CONTAINER" ] || die "Could not find the local DB container (expected one publishing :3307). Set DB_CONTAINER=<name>."
  docker inspect "$DB_CONTAINER" >/dev/null 2>&1 || die "DB container '$DB_CONTAINER' not running."
  log "Local  target : $LOCAL_URL  (db=$LOCAL_DB_NAME, container=$DB_CONTAINER)"
fi
[ "$DO_UPLOADS" = 1 ] && log "Uploads sync  : $SSH_TARGET:$REMOTE_UPLOADS -> $LOCAL_UPLOADS"

if [ "$DO_DB" = 1 ]; then
# ---------------------------------------------------------------------------
# 3. Read the live site URL (source of the rewrite)
# ---------------------------------------------------------------------------
log "Reading live site URL from WP Engine ..."
REMOTE_URL="$("${SSH[@]}" 'wp option get siteurl --skip-plugins --skip-themes' 2>/dev/null | tr -d '\r' | grep -E '^https?://' | head -n1)"
[ -n "$REMOTE_URL" ] || die "Could not read remote siteurl via wp-cli."
log "Remote source : $REMOTE_URL"

if [ "$REMOTE_URL" = "$LOCAL_URL" ]; then
  warn "Remote and local URLs are identical; no URL rewrite will be applied."
fi

# ---------------------------------------------------------------------------
# 4. Confirm (destructive: local DB is dropped & replaced)
# ---------------------------------------------------------------------------
if [ "${FORCE:-0}" != "1" ]; then
  printf '\n\033[1;33mThis will DROP the local database "%s" and replace it with the live copy.\033[0m\n' "$LOCAL_DB_NAME"
  read -r -p 'Continue? [y/N] ' ans
  case "$ans" in y|Y|yes|YES) ;; *) die "Aborted." ;; esac
fi

mkdir -p "$BACKUP_DIR"
REMOTE_DUMP="$BACKUP_DIR/remote-${TS}.sql.gz"
LOCAL_BACKUP="$BACKUP_DIR/local-before-${TS}.sql.gz"

# ---------------------------------------------------------------------------
# 5. Back up the current local DB first (safety net)
# ---------------------------------------------------------------------------
log "Backing up current local DB -> $LOCAL_BACKUP"
if dbdump --no-tablespaces --single-transaction --quick "$LOCAL_DB_NAME" 2>/dev/null | gzip -c > "$LOCAL_BACKUP"; then
  log "Local backup saved ($(du -h "$LOCAL_BACKUP" | cut -f1))."
else
  warn "Local backup failed (DB may be empty/new). Continuing."
  rm -f "$LOCAL_BACKUP"
fi

# ---------------------------------------------------------------------------
# 6. Export the live DB (raw), streamed + gzipped to local.
#    NOTE: this WP Engine host's wp-cli cannot enumerate tables (wp db tables /
#    wp search-replace --export return empty), so we take a raw `wp db export`
#    (which shells out to mysqldump and works reliably) and do the URL rewrite
#    locally in step 8. `wp db export` reads only; the live DB is not modified.
# ---------------------------------------------------------------------------
log "Exporting live DB from WP Engine (raw). This can take a few minutes ..."
"${SSH[@]}" 'wp db export - --single-transaction --default-character-set=utf8mb4 2>/dev/null | gzip -c' > "$REMOTE_DUMP"
# Validate real content (gzip-of-empty is a 20-byte file, so -s is not enough).
# `|| true` masks gunzip's SIGPIPE (141) when grep -m1 closes the pipe early,
# which would otherwise trip `set -o pipefail` on a perfectly good dump.
if ! { gunzip -c "$REMOTE_DUMP" 2>/dev/null || true; } | grep -qm1 'CREATE TABLE'; then
  die "Remote export produced no tables (dump is empty). Check wp-cli/mysqldump on the host."
fi
log "Remote dump saved -> $REMOTE_DUMP ($(du -h "$REMOTE_DUMP" | cut -f1))."

# ---------------------------------------------------------------------------
# 7. Recreate the local schema and import
# ---------------------------------------------------------------------------
log "Recreating local database '$LOCAL_DB_NAME' ..."
dbx -e "DROP DATABASE IF EXISTS \`$LOCAL_DB_NAME\`; CREATE DATABASE \`$LOCAL_DB_NAME\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

log "Importing dump into local DB (this can take a few minutes) ..."
gunzip -c "$REMOTE_DUMP" | dbx "$LOCAL_DB_NAME"

# ---------------------------------------------------------------------------
# 8. Serialized-data-safe URL rewrite, run locally via wordpress:cli.
#    A plain sed would corrupt PHP-serialized data because the URLs differ in
#    length; wp search-replace fixes the serialized string lengths correctly.
# ---------------------------------------------------------------------------
if [ "$REMOTE_URL" != "$LOCAL_URL" ]; then
  DB_NET="$(docker inspect "$DB_CONTAINER" --format '{{range $k,$v := .NetworkSettings.Networks}}{{$k}} {{end}}' | awk '{print $1}')"
  wpcli() {
    docker run --rm --network "$DB_NET" --user root \
      -e WORDPRESS_DB_HOST="$DB_CONTAINER:3306" \
      -e WORDPRESS_DB_USER="$LOCAL_DB_USER" \
      -e WORDPRESS_DB_PASSWORD="$LOCAL_DB_PASS" \
      -e WORDPRESS_DB_NAME="$LOCAL_DB_NAME" \
      -v "$PROJECT_ROOT/wordpress:/var/www/html" \
      --entrypoint wp "$WPCLI_IMAGE" --allow-root --skip-plugins --skip-themes "$@"
  }
  # Strip scheme to build both https:// and http:// (and protocol-relative) pairs.
  REMOTE_HOST="${REMOTE_URL#http://}"; REMOTE_HOST="${REMOTE_HOST#https://}"
  LOCAL_HOST="${LOCAL_URL#http://}";   LOCAL_HOST="${LOCAL_HOST#https://}"
  log "Rewriting URLs (serialized-safe) $REMOTE_URL -> $LOCAL_URL via ${WPCLI_IMAGE} ..."
  if wpcli search-replace "https://$REMOTE_HOST" "$LOCAL_URL"       --all-tables --precise --skip-columns=guid \
     && wpcli search-replace "http://$REMOTE_HOST"  "$LOCAL_URL"    --all-tables --precise --skip-columns=guid \
     && wpcli search-replace "//$REMOTE_HOST"       "//$LOCAL_HOST" --all-tables --precise --skip-columns=guid; then
    log "URL rewrite complete."
  else
    warn "wp search-replace failed; falling back to a siteurl/home-only fix (deep links may still point to $REMOTE_URL)."
  fi
else
  warn "Remote and local URLs are identical; skipping URL rewrite."
fi

# ---------------------------------------------------------------------------
# 8b. Belt-and-suspenders: force siteurl/home to the local URL
# ---------------------------------------------------------------------------
OPTIONS_TABLE="$(dbx -N -e "SHOW TABLES FROM \`$LOCAL_DB_NAME\` LIKE '%options';" | head -n1 | tr -d '\r')"
if [ -n "$OPTIONS_TABLE" ]; then
  log "Forcing siteurl/home to $LOCAL_URL in $OPTIONS_TABLE ..."
  dbx "$LOCAL_DB_NAME" -e "UPDATE \`$OPTIONS_TABLE\` SET option_value='$LOCAL_URL' WHERE option_name IN ('siteurl','home');"
else
  warn "Could not locate the *options table to enforce siteurl/home."
fi

# ---------------------------------------------------------------------------
# 9. Verify
# ---------------------------------------------------------------------------
log "Verification:"
dbx -N -e "SELECT option_name, option_value FROM \`$LOCAL_DB_NAME\`.\`${OPTIONS_TABLE:-wp_options}\` WHERE option_name IN ('siteurl','home');" 2>/dev/null || true
TABLE_COUNT="$(dbx -N -e "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema='$LOCAL_DB_NAME';" 2>/dev/null | tr -d '\r')"
log "Imported ${TABLE_COUNT:-?} tables."
fi  # end DO_DB

# ---------------------------------------------------------------------------
# 10. Sync the media library (wp-content/uploads) via rsync — incremental.
# ---------------------------------------------------------------------------
if [ "$DO_UPLOADS" = 1 ]; then
  mkdir -p "$LOCAL_UPLOADS"
  log "Rsyncing media library from WP Engine (~6.4 GB; incremental after first run) ..."
  # Trailing slash on source copies the *contents* of uploads/ into LOCAL_UPLOADS.
  # --delete keeps local in exact sync with live (remove to keep local-only extras).
  # Portable flags: --progress works on both macOS openrsync and GNU rsync.
  rsync -az --progress --delete \
    -e "ssh -i $SSH_KEY -o IdentitiesOnly=yes -o ConnectTimeout=20" \
    "$SSH_TARGET:$REMOTE_UPLOADS/" "$LOCAL_UPLOADS/"
  log "Uploads synced -> $LOCAL_UPLOADS ($(du -sh "$LOCAL_UPLOADS" 2>/dev/null | cut -f1))."
fi

cat <<EOF

Done.
  Live dump          : ${REMOTE_DUMP:-<skipped>}
  Local pre-backup   : ${LOCAL_BACKUP:-<none>}
  Uploads target     : $([ "$DO_UPLOADS" = 1 ] && echo "$LOCAL_UPLOADS" || echo "<skipped>")

Notes:
  * If pages look stale, restart the WordPress container to clear PHP opcache:
        docker restart $(docker ps --format '{{.Names}}' | grep -m1 wordpress || echo sacademy-wordpress-1)
  * To roll back the local DB:
        gunzip -c ${LOCAL_BACKUP:-<backup>} | docker exec -i ${DB_CONTAINER:-innodb-db-1} mysql -u$LOCAL_DB_USER -p'****' $LOCAL_DB_NAME
EOF
