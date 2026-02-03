-- MySQL Query Performance Analysis Script
-- Run this against your production database to identify slow queries

-- 1. Find tables without proper indexes
SELECT 
    t.TABLE_NAME,
    t.TABLE_ROWS,
    ROUND((DATA_LENGTH + INDEX_LENGTH) / 1024 / 1024, 2) AS size_mb,
    IF(s.INDEX_NAME IS NULL, 'NO INDEX', 'INDEXED') as status
FROM information_schema.TABLES t
LEFT JOIN information_schema.STATISTICS s ON t.TABLE_SCHEMA = s.TABLE_SCHEMA AND t.TABLE_NAME = s.TABLE_NAME
WHERE t.TABLE_SCHEMA = DATABASE()
GROUP BY t.TABLE_NAME, t.TABLE_ROWS, t.DATA_LENGTH, t.INDEX_LENGTH
ORDER BY t.TABLE_ROWS DESC;

-- 2. Identify large WordPress tables that might be slow
SELECT 
    TABLE_NAME,
    TABLE_ROWS,
    ROUND((DATA_LENGTH + INDEX_LENGTH) / 1024 / 1024, 2) AS size_mb
FROM information_schema.TABLES 
WHERE TABLE_SCHEMA = DATABASE()
ORDER BY TABLE_ROWS DESC
LIMIT 10;

-- 3. Check postmeta and termmeta tables (often sources of slow queries)
SELECT 
    t.TABLE_NAME,
    COUNT(*) as row_count,
    ROUND((DATA_LENGTH + INDEX_LENGTH) / 1024 / 1024, 2) AS size_mb
FROM information_schema.TABLES t
WHERE TABLE_SCHEMA = DATABASE() 
AND TABLE_NAME IN ('wp_postmeta', 'wp_termmeta', 'wp_usermeta')
GROUP BY t.TABLE_NAME;

-- 4. Find missing indexes on commonly queried columns
-- This is for postmeta - often a bottleneck
SHOW INDEX FROM wp_postmeta;

-- 5. Enable slow query log (requires MySQL administrative access)
-- SET GLOBAL slow_query_log = 'ON';
-- SET GLOBAL long_query_time = 0.5;
-- SET GLOBAL log_queries_not_using_indexes = 'ON';

-- 6. Query the slow query log (if enabled)
-- SELECT * FROM mysql.slow_log ORDER BY query_time DESC LIMIT 20;

-- 7. Check current database statistics
SELECT 
    ROUND(SUM(DATA_LENGTH + INDEX_LENGTH) / 1024 / 1024, 2) as total_size_mb,
    COUNT(*) as table_count,
    ROUND(AVG((DATA_LENGTH + INDEX_LENGTH) / 1024 / 1024), 2) as avg_table_size_mb
FROM information_schema.TABLES 
WHERE TABLE_SCHEMA = DATABASE();

-- 8. Look for tables with fragmentation
SELECT 
    TABLE_NAME,
    ROUND((DATA_FREE / (DATA_LENGTH + INDEX_LENGTH)) * 100, 2) as fragmentation_percent
FROM information_schema.TABLES
WHERE TABLE_SCHEMA = DATABASE() 
AND DATA_FREE > 0
ORDER BY fragmentation_percent DESC;

-- 9. Get WordPress options that might affect performance
SELECT option_id, option_name, option_value
FROM wp_options
WHERE option_name IN (
    'home',
    'siteurl',
    'admin_email',
    'active_plugins',
    'alloptions',
    'autoload'
)
ORDER BY option_name;

-- 10. Check for transients (temporary data) that might be expired
SELECT COUNT(*) as expired_transients
FROM wp_options
WHERE option_name LIKE '%_transient_%'
AND option_name NOT LIKE '%_transient_timeout_%';
