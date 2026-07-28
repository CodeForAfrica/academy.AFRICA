#!/usr/bin/env bash
#
# Regression gate: syntax-check custom source JavaScript with `node --check`
# (parse only, no execution) so a broken script can't ship. Built/minified
# bundles under dist/ and *.min.js are skipped.
#
set -euo pipefail

root="$(cd "$(dirname "$0")/../.." && pwd)"
theme="$root/wp-content/themes/academyAfrica"

status=0
count=0
while IFS= read -r -d '' file; do
    count=$((count + 1))
    if ! node --check "$file" 2>/tmp/aa_jscheck_err; then
        echo "❌ JS syntax error: ${file#$root/}"
        sed 's/^/      /' /tmp/aa_jscheck_err
        status=1
    fi
done < <(find "$theme/assets/js" -type f -name '*.js' ! -name '*.min.js' -not -path '*/dist/*' -print0 2>/dev/null)

if [ "$status" -eq 0 ]; then
    echo "✅ ${count} custom JS file(s) pass node --check."
fi
exit "$status"
