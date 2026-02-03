#!/bin/bash
# Query Analysis Script
# Analyzes WordPress debug.log to identify slow queries and performance issues

LOG_FILE="${1:-./wp-content/debug.log}"

if [ ! -f "$LOG_FILE" ]; then
    echo "Error: Log file not found at $LOG_FILE"
    echo "Usage: ./analyze-queries.sh [path/to/debug.log]"
    exit 1
fi

echo "=== WordPress Query Analysis Report ==="
echo "Log file: $LOG_FILE"
echo "Generated: $(date)"
echo ""

# Count total entries
echo "=== Log Summary ==="
TOTAL_LINES=$(wc -l < "$LOG_FILE")
echo "Total log lines: $TOTAL_LINES"
echo ""

# Find slow queries
echo "=== SLOW QUERIES (Top 20) ==="
grep -E "^.*SLOW QUERIES|Query executed" "$LOG_FILE" | tail -20
echo ""

# Find duplicate queries
echo "=== DUPLICATE QUERIES ==="
grep -E "Query executed.*times" "$LOG_FILE" | sort | uniq -c | sort -rn | head -10
echo ""

# Find database errors
echo "=== DATABASE ERRORS ==="
grep -i "database error\|sql\|exception" "$LOG_FILE" | tail -10
echo ""

# Find memory/timeout issues
echo "=== MEMORY & PERFORMANCE ISSUES ==="
grep -iE "memory|timeout|fatal|error" "$LOG_FILE" | tail -10
echo ""

# Summary statistics
echo "=== Recent Activity ==="
echo "Last 5 log entries:"
tail -5 "$LOG_FILE"
