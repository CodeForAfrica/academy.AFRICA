<?php
/**
 * Advanced Query Analysis Tool
 * 
 * Run from WordPress root:
 * php analyze-db-queries.php
 * 
 * Provides detailed analysis of slow queries and database performance
 */

// Load WordPress
if ( ! file_exists( 'wp-load.php' ) ) {
	die( "Error: wp-load.php not found. Run this script from WordPress root directory.\n" );
}

require_once( 'wp-load.php' );

// Colors for CLI output
class Colors {
	const RED = "\033[91m";
	const GREEN = "\033[92m";
	const YELLOW = "\033[93m";
	const BLUE = "\033[94m";
	const RESET = "\033[0m";
}

echo "\n" . Colors::BLUE . "=== WordPress Database Query Analysis ===" . Colors::RESET . "\n\n";

// Check if queries are being logged
if ( ! defined( 'SAVEQUERIES' ) || ! SAVEQUERIES ) {
	echo Colors::YELLOW . "Warning: SAVEQUERIES is not enabled. Add to wp-config.php:\n";
	echo "define( 'SAVEQUERIES', true );" . Colors::RESET . "\n\n";
}

// Get debug.log path
$log_file = WP_CONTENT_DIR . '/debug.log';

// 1. Current Queries in Memory
echo Colors::BLUE . "1. Current Page Load Queries" . Colors::RESET . "\n";
echo "─────────────────────────────────────\n";

if ( isset( $GLOBALS['wpdb']->queries ) ) {
	$queries = $GLOBALS['wpdb']->queries;
	$total_time = 0;
	$slow_queries = [];

	foreach ( $queries as $query_data ) {
		$time = (float) $query_data[1];
		$total_time += $time;

		if ( $time > 0.5 ) {
			$slow_queries[] = $query_data;
		}
	}

	echo "Total queries: " . count( $queries ) . "\n";
	printf( "Total time: %.3f seconds\n", $total_time );
	echo "Slow queries (>0.5s): " . count( $slow_queries ) . "\n\n";

	if ( ! empty( $slow_queries ) ) {
		echo Colors::RED . "Slow Queries:" . Colors::RESET . "\n";
		usort( $slow_queries, function( $a, $b ) {
			return (float) $b[1] <=> (float) $a[1];
		});

		foreach ( array_slice( $slow_queries, 0, 5 ) as $index => $query_data ) {
			echo "\n" . Colors::YELLOW . "#" . ( $index + 1 ) . " - " . 
			sprintf( "%.3f seconds", (float) $query_data[1] ) . Colors::RESET . "\n";
			echo "Query: " . substr( $query_data[0], 0, 100 ) . "...\n";
			echo "Called from: {$query_data[2]}\n";
		}
	}
} else {
	echo "No queries in memory.\n";
}

echo "\n";

// 2. Analyze debug.log if it exists
if ( file_exists( $log_file ) ) {
	echo Colors::BLUE . "2. Debug Log Analysis" . Colors::RESET . "\n";
	echo "─────────────────────────────────────\n";
	printf( "Log file: %s\n", $log_file );
	printf( "File size: %.2f MB\n", filesize( $log_file ) / 1024 / 1024 );
	printf( "Last modified: %s\n\n", date( 'Y-m-d H:i:s', filemtime( $log_file ) ) );

	$log_content = file_get_contents( $log_file );
	$error_count = substr_count( $log_content, 'ERROR' );
	$slow_query_count = substr_count( $log_content, 'SLOW QUERIES' );

	echo "Errors found: " . $error_count . "\n";
	echo "Slow query reports: " . $slow_query_count . "\n";
} else {
	echo Colors::YELLOW . "2. Debug Log Analysis" . Colors::RESET . "\n";
	echo "No debug.log found at: " . $log_file . "\n";
	echo "Logs will be created after enabling WP_DEBUG_LOG in wp-config.php\n";
}

echo "\n";

// 3. Database diagnostics
echo Colors::BLUE . "3. Database Diagnostics" . Colors::RESET . "\n";
echo "─────────────────────────────────────\n";

global $wpdb;

// Check database size
$db_size = $wpdb->get_var( "SELECT ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) FROM information_schema.TABLES WHERE table_schema = DATABASE()" );
printf( "Database size: %.2f MB\n", $db_size );

// Check table count
$table_count = $wpdb->get_var( "SELECT COUNT(*) FROM information_schema.TABLES WHERE table_schema = DATABASE()" );
echo "Total tables: " . $table_count . "\n";

// Check for tables without indexes
echo "\nTables without indexes:\n";
$unindexed = $wpdb->get_results( "
	SELECT t.TABLE_NAME, t.TABLE_ROWS 
	FROM information_schema.TABLES t
	LEFT JOIN information_schema.STATISTICS s ON t.TABLE_SCHEMA = s.TABLE_SCHEMA AND t.TABLE_NAME = s.TABLE_NAME
	WHERE t.TABLE_SCHEMA = DATABASE() AND s.COLUMN_NAME IS NULL
	ORDER BY t.TABLE_ROWS DESC
" );

if ( ! empty( $unindexed ) ) {
	foreach ( $unindexed as $table ) {
		echo Colors::RED . "  - {$table->TABLE_NAME} ({$table->TABLE_ROWS} rows)" . Colors::RESET . "\n";
	}
} else {
	echo "  All tables are indexed ✓\n";
}

// Check for large tables
echo "\nLargest tables:\n";
$large_tables = $wpdb->get_results( "
	SELECT TABLE_NAME, TABLE_ROWS, ROUND((DATA_LENGTH + INDEX_LENGTH) / 1024 / 1024, 2) AS size_mb
	FROM information_schema.TABLES 
	WHERE table_schema = DATABASE()
	ORDER BY DATA_LENGTH DESC
	LIMIT 5
" );

foreach ( $large_tables as $table ) {
	printf( "  - %s: %d rows (%.2f MB)\n", $table->TABLE_NAME, $table->TABLE_ROWS, $table->size_mb );
}

echo "\n";

// 4. Connection info
echo Colors::BLUE . "4. Database Connection Info" . Colors::RESET . "\n";
echo "─────────────────────────────────────\n";
echo "Host: " . DB_HOST . "\n";
echo "Database: " . DB_NAME . "\n";

$version = $wpdb->get_var( "SELECT VERSION()" );
echo "MySQL Version: " . $version . "\n";

// Check variables
$max_connections = $wpdb->get_var( "SELECT @@max_connections" );
$max_allowed_packet = $wpdb->get_var( "SELECT @@max_allowed_packet" );

echo "Max connections: " . $max_connections . "\n";
echo "Max allowed packet: " . $max_allowed_packet . "\n";

echo "\n" . Colors::GREEN . "=== Analysis Complete ===" . Colors::RESET . "\n";
echo "Next steps:\n";
echo "  1. Enable WP_DEBUG_LOG in wp-config.php for persistent logging\n";
echo "  2. Install Query Monitor plugin for real-time profiling\n";
echo "  3. Check debug.log for slow query details\n";
echo "  4. Add database indexes for frequently queried columns\n";
echo "  5. Consider enabling persistent object cache (Redis/Memcached)\n";
echo "\n";
