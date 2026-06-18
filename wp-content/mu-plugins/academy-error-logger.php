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
    // Rotate if over size limit
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
            "[%s] FATAL ERROR\n  URL:     %s\n  Message: %s\n  File:    %s\n  Line:    %d\n  Memory:  %.2f MB peak (limit %s)\n  Time:    %d ms\n\n",
            gmdate('Y-m-d H:i:s'),
            academy_log_url(),
            $error['message'],
            academy_log_shorten($error['file']),
            $error['line'],
            $peak_mb,
            $limit,
            $time_ms
        ));
        return;
    }

    // Flag slow or memory-heavy requests even when there's no fatal
    $limit_bytes = (int) $limit * 1024 * 1024;
    $memory_pct  = $limit_bytes > 0
        ? round($peak_mb / ($limit_bytes / 1024 / 1024) * 100)
        : 0;

    if ($time_ms >= 5000 || $memory_pct >= 80) {
        academy_log(sprintf(
            "[%s] SLOW/HEAVY REQUEST\n  URL:    %s\n  Memory: %.2f MB peak (%d%% of %s)\n  Time:   %d ms\n\n",
            gmdate('Y-m-d H:i:s'),
            academy_log_url(),
            $peak_mb,
            $memory_pct,
            $limit,
            $time_ms
        ));
    }
});

// ------------------------------------------------------------------
// 2. Non-fatal error collector — theme / academy plugin code only
// ------------------------------------------------------------------

$GLOBALS['_academy_errors'] = [];

set_error_handler(function (int $errno, string $errstr, string $errfile, int $errline): bool {
    if (strpos($errfile, '/themes/academyAfrica/') === false &&
        strpos($errfile, '/plugins/academy-africa') === false) {
        return false; // let PHP / WordPress handle everything else
    }

    $GLOBALS['_academy_errors'][] = sprintf(
        '[E%d] %s  →  %s:%d',
        $errno,
        $errstr,
        academy_log_shorten($errfile),
        $errline
    );

    return false; // still surface in default handler / QM
});

register_shutdown_function(function () {
    if (empty($GLOBALS['_academy_errors'])) return;

    academy_log(sprintf(
        "[%s] PHP WARNINGS (%d) on %s\n  %s\n\n",
        gmdate('Y-m-d H:i:s'),
        count($GLOBALS['_academy_errors']),
        academy_log_url(),
        implode("\n  ", $GLOBALS['_academy_errors'])
    ));
});
