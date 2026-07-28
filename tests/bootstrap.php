<?php
/**
 * PHPUnit bootstrap for the custom-code unit tests (issue #48).
 *
 * These are isolated unit tests: rather than boot WordPress, we stub the few
 * WP functions the loaded includes call at include-time, then require the
 * specific custom file under test. This keeps the date/metadata-normalization
 * regression coverage fast and dependency-free.
 */

if (!defined('ABSPATH')) {
    // The custom includes guard on ABSPATH; define a dummy so they don't exit().
    define('ABSPATH', dirname(__DIR__) . '/');
}

// No-op stubs for the hook API invoked when the include is loaded.
if (!function_exists('add_action')) {
    function add_action(...$args)
    {
        return true;
    }
}
if (!function_exists('add_filter')) {
    function add_filter(...$args)
    {
        return true;
    }
}

require_once __DIR__ . '/../vendor/autoload.php';

// Load the file under test (pure date helpers live here).
require_once dirname(__DIR__) . '/wp-content/themes/academyAfrica/posts/events.php';
