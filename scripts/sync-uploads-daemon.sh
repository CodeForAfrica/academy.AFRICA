#!/usr/bin/env bash
#
# sync-uploads-daemon.sh
# ------------------------------------------------------------------------------
# Resilient, self-resuming rsync of the WP Engine media library into the local
# wp-content/uploads. rsync is incremental, so each attempt continues where the
# previous one stopped. The loop retries until rsync exits 0 (fully in sync),
# then writes a .done marker. Designed to be launched DETACHED (nohup/setsid)
# so it survives shell/session teardown:
#
#   nohup ./scripts/sync-uploads-daemon.sh >/dev/null 2>&1 &
#
# Progress log : db-backups/uploads-sync.log
# Done marker  : db-backups/uploads-sync.done   (contains final size + timestamp)
# ------------------------------------------------------------------------------
set -uo pipefail

PROJECT_ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "$PROJECT_ROOT"

SSH_KEY="${SSH_KEY:-$HOME/.ssh/wpengine_ed25519}"
SSH_TARGET="${SSH_TARGET:-academyafrica1@academyafrica1.ssh.wpengine.net}"
REMOTE_UPLOADS="${REMOTE_UPLOADS:-/home/wpe-user/sites/academyafrica1/wp-content/uploads}"
LOCAL_UPLOADS="${LOCAL_UPLOADS:-$PROJECT_ROOT/wp-content/uploads}"

LOG="$PROJECT_ROOT/db-backups/uploads-sync.log"
DONE="$PROJECT_ROOT/db-backups/uploads-sync.done"
MAX_ATTEMPTS="${MAX_ATTEMPTS:-200}"

mkdir -p "$LOCAL_UPLOADS" "$(dirname "$LOG")"
rm -f "$DONE"

{
  echo "=== uploads daemon started $(date) (pid $$) ==="
} >> "$LOG"

attempt=0
while [ "$attempt" -lt "$MAX_ATTEMPTS" ]; do
  attempt=$((attempt + 1))
  echo "--- attempt $attempt $(date) ---" >> "$LOG"
  # -az incremental; each run skips files already transferred. --delete keeps
  # local an exact mirror of live. --progress gives per-file lines in the log.
  rsync -az --progress --delete \
    -e "ssh -i $SSH_KEY -o IdentitiesOnly=yes -o ConnectTimeout=20 -o ServerAliveInterval=30 -o ServerAliveCountMax=4" \
    "$SSH_TARGET:$REMOTE_UPLOADS/" "$LOCAL_UPLOADS/" >> "$LOG" 2>&1
  rc=$?
  if [ "$rc" -eq 0 ]; then
    size="$(du -sh "$LOCAL_UPLOADS" 2>/dev/null | cut -f1)"
    count="$(find "$LOCAL_UPLOADS" -type f 2>/dev/null | wc -l | tr -d ' ')"
    printf 'DONE %s  size=%s  files=%s  attempts=%s\n' "$(date)" "$size" "$count" "$attempt" | tee -a "$LOG" > "$DONE"
    echo "=== uploads daemon finished OK ===" >> "$LOG"
    exit 0
  fi
  echo "rsync exited $rc; retrying in 5s ..." >> "$LOG"
  sleep 5
done

echo "GAVE UP after $MAX_ATTEMPTS attempts $(date)" | tee -a "$LOG" > "$DONE"
exit 1
