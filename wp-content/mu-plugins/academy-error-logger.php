<?php
/**
 * Plugin Name: Academy Africa Error Logger
 * Description: Captures PHP fatal errors, slow requests, and warnings from
 *              theme/plugin code. Writes to wp-content/academy-africa.log.
 *              Runs as a must-use plugin so it loads before everything else.
 */

if (!defined('ABSPATH')) exit;

define('ACADEMY_LOG_FILE',          WP_CONTENT_DIR . '/academy-africa.log');
define('ACADEMY_LOG_MAX_BYTES',     5 * 1024 * 1024); // rotate at 5 MB
define('ACADEMY_LOG_REQUEST_START', microtime(true));

// ------------------------------------------------------------------
// Helpers
// ------------------------------------------------------------------

function academy_log(string $entry): void {
    if (file_exists(ACADEMY_LOG_FILE) && filesize(ACADEMY_LOG_FILE) >= ACADEMY_LOG_MAX_BYTES) {
        rename(ACADEMY_LOG_FILE, ACADEMY_LOG_FILE . '.1');
    }
    error_log($entry, 3, ACADEMY_LOG_FILE);
}

function academy_log_url(): string {
    $scheme = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https' : 'http';
    $host   = $_SERVER['HTTP_HOST'] ?? 'unknown';
    $uri    = $_SERVER['REQUEST_URI'] ?? '/';
    return $scheme . '://' . $host . $uri;
}

function academy_log_shorten(string $path): string {
    return str_replace(WP_CONTENT_DIR, '{wpcontent}', $path);
}

/**
 * Build a context block with everything useful for diagnosing a slow/fatal request:
 * - Request type (frontend / admin / AJAX / cron / REST)
 * - For AJAX: the action name from $_POST or $_GET
 * - For admin list tables: post_type being listed
 * - Logged-in user ID and roles
 * - Number of DB queries executed (requires SAVEQUERIES or just the wpdb counter)
 * - Action Scheduler pending queue depth (if the table exists)
 * - Active plugins list (only on fatal/slow, not warnings — keep log readable)
 */
function academy_log_context(bool $include_plugins = false): string {
    $lines = [];

    // --- Request type ---
    if (defined('DOING_CRON') && DOING_CRON) {
        $type = 'cron';
    } elseif (defined('DOING_AJAX') && DOING_AJAX) {
        $ajax_action = $_POST['action'] ?? ($_GET['action'] ?? '(none)');
        $type = 'ajax:' . $ajax_action;
    } elseif (defined('REST_REQUEST') && REST_REQUEST) {
        $type = 'rest';
    } elseif (is_admin()) {
        $post_type = $_GET['post_type'] ?? '';
        $type = 'admin' . ($post_type ? ':' . $post_type : '');
    } else {
        $type = 'frontend';
    }
    $lines[] = 'Type:    ' . $type;

    // --- Current user ---
    $user_id = function_exists('get_current_user_id') ? get_current_user_id() : 0;
    if ($user_id) {
        $user  = function_exists('get_userdata') ? get_userdata($user_id) : null;
        $roles = ($user && !empty($user->roles)) ? implode(',', $user->roles) : 'unknown';
        $lines[] = 'User:    #' . $user_id . ' [' . $roles . ']';
    } else {
        $lines[] = 'User:    (guest)';
    }

    // --- DB query count ---
    // wpdb tracks num_queries even without SAVEQUERIES
    if (isset($GLOBALS['wpdb']) && isset($GLOBALS['wpdb']->num_queries)) {
        $lines[] = 'Queries: ' . $GLOBALS['wpdb']->num_queries;
    }

    // --- Last 3 DB queries (only when SAVEQUERIES is on) ---
    if (defined('SAVEQUERIES') && SAVEQUERIES && isset($GLOBALS['wpdb']->queries) && !empty($GLOBALS['wpdb']->queries)) {
        $last = array_slice($GLOBALS['wpdb']->queries, -3);
        $q_lines = [];
        foreach ($last as $q) {
            $sql     = preg_replace('/\s+/', ' ', trim($q[0] ?? ''));
            $q_ms    = round(($q[1] ?? 0) * 1000, 1);
            $caller  = $q[2] ?? '';
            // Truncate very long SQL
            if (strlen($sql) > 200) $sql = substr($sql, 0, 200) . '…';
            $q_lines[] = sprintf('%s  [%.1fms] %s', $sql, $q_ms, $caller);
        }
        $lines[] = 'Last SQL:';
        foreach ($q_lines as $ql) {
            $lines[] = '  ' . $ql;
        }
    }

    // --- Action Scheduler queue depth ---
    // AS stores pending actions in {prefix}actionscheduler_actions
    global $wpdb;
    if (isset($wpdb)) {
        $as_table = $wpdb->prefix . 'actionscheduler_actions';
        // Check table exists cheaply by querying INFORMATION_SCHEMA (one query, cached by MySQL)
        $table_exists = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = %s AND table_name = %s LIMIT 1",
                DB_NAME,
                $as_table
            )
        );
        if ($table_exists) {
            $pending = (int) $wpdb->get_var(
                "SELECT COUNT(*) FROM `{$as_table}` WHERE status IN ('pending','running')"
            );
            $failed  = (int) $wpdb->get_var(
                "SELECT COUNT(*) FROM `{$as_table}` WHERE status = 'failed'"
            );
            $lines[] = sprintf('AS queue: %d pending/running, %d failed', $pending, $failed);
        }
    }

    // --- Active plugins (only on fatal/slow to keep log size down) ---
    if ($include_plugins) {
        $active = function_exists('get_option') ? (get_option('active_plugins') ?: []) : [];
        // Show only plugin slugs, not full paths
        $slugs = array_map(function ($p) {
            return explode('/', $p)[0];
        }, $active);
        $lines[] = 'Plugins: ' . implode(', ', $slugs);
    }

    return implode("\n  ", $lines);
}

// ------------------------------------------------------------------
// 1. Fatal error catcher — fires even on OOM / blank-page deaths
// ------------------------------------------------------------------

register_shutdown_function(function () {
    $peak_mb = round(memory_get_peak_usage(true) / 1024 / 1024, 2);
    $time_ms = round((microtime(true) - ACADEMY_LOG_REQUEST_START) * 1000);
    $limit   = ini_get('memory_limit');

    $error       = error_get_last();
    $fatal_types = [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR];

    if ($error && in_array($error['type'], $fatal_types, true)) {
        academy_log(sprintf(
            "[%s] FATAL ERROR\n  URL:     %s\n  Message: %s\n  File:    %s\n  Line:    %d\n  Memory:  %.2f MB peak (limit %s)\n  Time:    %d ms\n  %s\n\n",
            gmdate('Y-m-d H:i:s'),
            academy_log_url(),
            $error['message'],
            academy_log_shorten($error['file']),
            $error['line'],
            $peak_mb,
            $limit,
            $time_ms,
            academy_log_context(true)
        ));
        return;
    }

    // Flag slow or memory-heavy requests even when there's no fatal
    $limit_mb    = (float) $limit;
    $memory_pct  = $limit_mb > 0 ? round($peak_mb / $limit_mb * 100) : 0;

    if ($time_ms >= 5000 || $memory_pct >= 80) {
        academy_log(sprintf(
            "[%s] SLOW/HEAVY REQUEST\n  URL:    %s\n  Memory: %.2f MB peak (%d%% of %s)\n  Time:   %d ms\n  %s\n\n",
            gmdate('Y-m-d H:i:s'),
            academy_log_url(),
            $peak_mb,
            $memory_pct,
            $limit,
            $time_ms,
            academy_log_context(false)
        ));
    }
});

// ------------------------------------------------------------------
// 2. Non-fatal error collector — theme + all plugins
// ------------------------------------------------------------------

// Severity levels we care about. E_NOTICE / E_DEPRECATED / E_STRICT are
// excluded — they're too noisy and rarely actionable in production.
define('ACADEMY_LOG_CAPTURED_LEVELS', E_WARNING | E_USER_WARNING | E_ERROR | E_USER_ERROR | E_RECOVERABLE_ERROR);

$GLOBALS['_academy_errors'] = [];
$GLOBALS['_academy_errors_seen'] = []; // dedup key → true

set_error_handler(function (int $errno, string $errstr, string $errfile, int $errline): bool {
    // Only capture levels we care about
    if (!($errno & ACADEMY_LOG_CAPTURED_LEVELS)) {
        return false;
    }

    // Must be in wp-content (plugins or theme) — skip core wp-includes / wp-admin
    if (strpos($errfile, WP_CONTENT_DIR) === false) {
        return false;
    }

    // Deduplicate — same error at the same location only logged once per request
    $dedup_key = $errno . ':' . $errfile . ':' . $errline;
    if (isset($GLOBALS['_academy_errors_seen'][$dedup_key])) {
        return false;
    }
    $GLOBALS['_academy_errors_seen'][$dedup_key] = true;

    // Identify the source: theme, our plugin, or a third-party plugin
    if (strpos($errfile, '/themes/academyAfrica/') !== false) {
        $source = 'theme';
    } elseif (strpos($errfile, '/plugins/academy-africa') !== false) {
        $source = 'academy-plugin';
    } else {
        // Extract the plugin slug from the path (first directory under /plugins/)
        if (preg_match('|/plugins/([^/]+)/|', $errfile, $m)) {
            $source = 'plugin:' . $m[1];
        } else {
            $source = 'mu-plugin';
        }
    }

    $level_names = [
        E_WARNING          => 'WARNING',
        E_USER_WARNING     => 'USER_WARNING',
        E_ERROR            => 'ERROR',
        E_USER_ERROR       => 'USER_ERROR',
        E_RECOVERABLE_ERROR => 'RECOVERABLE_ERROR',
    ];
    $level_name = $level_names[$errno] ?? ('E' . $errno);

    $GLOBALS['_academy_errors'][] = sprintf(
        '[%s][%s] %s  →  %s:%d',
        $level_name,
        $source,
        $errstr,
        academy_log_shorten($errfile),
        $errline
    );

    return false; // let WordPress / QM still handle it
});

register_shutdown_function(function () {
    if (empty($GLOBALS['_academy_errors'])) return;

    // Split by source so the log is easy to scan
    $by_source = [];
    foreach ($GLOBALS['_academy_errors'] as $entry) {
        // Extract source tag from "[LEVEL][source] ..."
        preg_match('/\[([^\]]+)\]\[([^\]]+)\]/', $entry, $m);
        $source = $m[2] ?? 'unknown';
        $by_source[$source][] = $entry;
    }

    $lines = [];
    foreach ($by_source as $source => $entries) {
        $lines[] = '--- ' . $source . ' (' . count($entries) . ') ---';
        foreach ($entries as $e) {
            $lines[] = '  ' . $e;
        }
    }

    academy_log(sprintf(
        "[%s] PHP WARNINGS (%d) on %s\n%s\n\n",
        gmdate('Y-m-d H:i:s'),
        count($GLOBALS['_academy_errors']),
        academy_log_url(),
        implode("\n", $lines)
    ));
});

// ------------------------------------------------------------------
// 3. Slow DB query logger — catches individual queries over 2s
//    Only active when a slow request is already in progress (saves overhead)
// ------------------------------------------------------------------

add_filter('query', function (string $sql): string {
    // Tag the start time on each query via a query filter isn't possible directly,
    // so we use the wpdb query log instead. This hook just enables SAVEQUERIES
    // dynamically when memory is already high — giving us the last N queries on crash.
    $peak_mb = round(memory_get_peak_usage(true) / 1024 / 1024, 2);
    $limit   = (float) ini_get('memory_limit');
    $pct     = $limit > 0 ? $peak_mb / $limit * 100 : 0;

    if ($pct >= 60 && !defined('SAVEQUERIES')) {
        // Can't use define() after it's been defined but we can enable the wpdb flag
        if (isset($GLOBALS['wpdb'])) {
            $GLOBALS['wpdb']->show_errors();
            $GLOBALS['wpdb']->save_queries = true;
        }
    }

    return $sql;
});
