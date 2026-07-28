<?php

/**
 * Self-contained tests for the academyAfrica cache-invalidation helper.
 *
 * There is no WordPress/PHPUnit harness in this repo, so this file stubs the
 * handful of WordPress functions the helper touches and drives Cache directly.
 *
 * Run:  php wp-content/themes/academyAfrica/tests/cache-invalidation-test.php
 * Exit code 0 = all passed, 1 = a failure (suitable for CI).
 */

// ---------------------------------------------------------------------------
// Minimal WordPress environment stubs
// ---------------------------------------------------------------------------

define('ABSPATH', __DIR__);
define('MINUTE_IN_SECONDS', 60);
define('HOUR_IN_SECONDS', 3600);
define('WEEK_IN_SECONDS', 604800);

// In-memory object cache: keys are "group\0key". Persists across a flush() so
// it behaves like a persistent backend (Redis/Memcached) — exactly the setup
// where the old version-bump fallback silently failed.
$GLOBALS['__store'] = [];
// Durable options store (survives object-cache flush/eviction).
$GLOBALS['__options'] = [];
// Toggle: does the simulated backend support wp_cache_flush_group()?
$GLOBALS['__supports_flush_group'] = false;
// Captured hook callbacks so we can fire enrollment/completion handlers.
$GLOBALS['__hooks'] = [];

function __ck($key, $group)
{
    return $group . "\0" . $key;
}

function wp_cache_get($key, $group = '')
{
    $ck = __ck($key, $group);
    return array_key_exists($ck, $GLOBALS['__store']) ? $GLOBALS['__store'][$ck] : false;
}

function wp_cache_set($key, $data, $group = '', $ttl = 0)
{
    $GLOBALS['__store'][__ck($key, $group)] = $data;
    return true;
}

function wp_cache_delete($key, $group = '')
{
    unset($GLOBALS['__store'][__ck($key, $group)]);
    return true;
}

function wp_cache_supports($feature)
{
    if ('flush_group' === $feature) {
        return (bool) $GLOBALS['__supports_flush_group'];
    }
    return false;
}

function wp_cache_flush_group($group)
{
    foreach (array_keys($GLOBALS['__store']) as $ck) {
        if (strpos($ck, $group . "\0") === 0) {
            unset($GLOBALS['__store'][$ck]);
        }
    }
    return true;
}

function add_action($hook, $cb, $priority = 10, $args = 1)
{
    $GLOBALS['__hooks'][$hook][] = $cb;
}

function do_action() {}

function wp_is_post_autosave($id)
{
    return false;
}
function wp_is_post_revision($id)
{
    return false;
}
function get_post_type($id)
{
    return $GLOBALS['__post_types'][$id] ?? 'post';
}

// Durable options store — the namespace version now lives here, not in the
// object cache, so it survives cache eviction/flush (the reviewed fix).
function get_option($name, $default = false)
{
    return array_key_exists($name, $GLOBALS['__options']) ? $GLOBALS['__options'][$name] : $default;
}

function update_option($name, $value, $autoload = null)
{
    $GLOBALS['__options'][$name] = $value;
    return true;
}

// ---------------------------------------------------------------------------
// Load the code under test
// ---------------------------------------------------------------------------

require_once __DIR__ . '/../includes/utils/cache.php';

use AcademyAfrica\Theme\Cache\Cache;

// ---------------------------------------------------------------------------
// Tiny assertion framework
// ---------------------------------------------------------------------------

$GLOBALS['__pass'] = 0;
$GLOBALS['__fail'] = 0;

function check($label, $cond)
{
    if ($cond) {
        $GLOBALS['__pass']++;
        echo "  \033[32mPASS\033[0m  $label\n";
    } else {
        $GLOBALS['__fail']++;
        echo "  \033[31mFAIL\033[0m  $label\n";
    }
}

function reset_cache($supports_flush_group)
{
    $GLOBALS['__store'] = [];
    $GLOBALS['__options'] = [];
    $GLOBALS['__supports_flush_group'] = $supports_flush_group;
}

// ---------------------------------------------------------------------------
// Tests
// ---------------------------------------------------------------------------

echo "\nCache invalidation tests\n========================\n";

// --- 1. Versioned keys embed the group version -----------------------------
echo "\n[versioned keys]\n";
reset_cache(false);
$k1 = Cache::key('academy_learning_paths_abc');
check('key contains a version suffix', (bool) preg_match('/:v\d+$/', $k1));
check('same key is stable within a version', $k1 === Cache::key('academy_learning_paths_abc'));

// --- 2. FALLBACK PATH: flush works WITHOUT group-flush support --------------
// This is the regression from findings 27/28: previously the version bump was
// never part of the key, so on a backend without flush_group the value stayed.
echo "\n[fallback: no wp_cache flush_group support]\n";
reset_cache(false);
check('backend reports no group-flush support', Cache::supports_group_flush() === false);
Cache::set('academy_learning_paths_abc', ['stale'], WEEK_IN_SECONDS);
check('value readable before flush', Cache::get('academy_learning_paths_abc') === ['stale']);
$before = Cache::version();
Cache::flush();
check('version bumped by flush', Cache::version() === $before + 1);
check('stale value is now a MISS after flush', Cache::get('academy_learning_paths_abc') === false);
Cache::set('academy_learning_paths_abc', ['fresh'], WEEK_IN_SECONDS);
check('fresh value readable after re-set', Cache::get('academy_learning_paths_abc') === ['fresh']);

// --- 3. flush WITH group-flush support (Redis Object Cache Pro) -------------
echo "\n[with wp_cache flush_group support]\n";
reset_cache(true);
check('backend reports group-flush support', Cache::supports_group_flush() === true);
Cache::set('academy_organizations', ['org'], HOUR_IN_SECONDS);
check('value readable before flush', Cache::get('academy_organizations') === ['org']);
Cache::flush();
check('value is a MISS after flush', Cache::get('academy_organizations') === false);
// Group was physically flushed and the version lives in an option, not the
// group — so the object-cache store is completely empty after a flush.
check('group physically emptied (no stranded keys)', count($GLOBALS['__store']) === 0);

// --- 3b. Version counter survives object-cache eviction (reviewed fix) ------
// Simulate a persistent backend that evicts the whole object cache while
// older :vN data entries would otherwise linger. Because the version is a
// durable option, it must NOT reset to 1 and stale reads must stay misses.
echo "\n[version survives cache eviction]\n";
reset_cache(false);
Cache::set('academy_organizations', ['stale'], HOUR_IN_SECONDS);
Cache::flush();                       // version -> 2
$v_after_flush = Cache::version();
$GLOBALS['__store'] = [];              // object cache evicted/cleared entirely
check('version persists across eviction (no reset to 1)', Cache::version() === $v_after_flush);
check('version is still >= 2 after eviction', Cache::version() >= 2);
// A value written before the eviction at :v1 must never be resurrected.
$GLOBALS['__store']["academy_africa\0academy_organizations:v1"] = ['zombie'];
check('stranded :v1 entry is not read after bump+eviction', Cache::get('academy_organizations') === false);

// --- 4. Targeted per-user delete (completion / enrollment) ------------------
echo "\n[targeted per-user delete]\n";
reset_cache(false);
Cache::set('course_status_u7_c42', 'In Progress', 300);
Cache::set('course_progress_u7_c42', '<div>50%</div>', 300);
Cache::set('course_content_u7_c42', '<ul>…</ul>', 300);
Cache::set('course_status_u9_c42', 'Not Started', 300); // another user, must survive
Cache::delete('course_status_u7_c42');
Cache::delete('course_progress_u7_c42');
Cache::delete('course_content_u7_c42');
check('user 7 status deleted', Cache::get('course_status_u7_c42') === false);
check('user 7 progress deleted', Cache::get('course_progress_u7_c42') === false);
check('user 7 content deleted', Cache::get('course_content_u7_c42') === false);
check('user 9 status untouched', Cache::get('course_status_u9_c42') === 'Not Started');

// --- 5. Enrollment hook clears the right keys -------------------------------
echo "\n[learndash_update_course_access hook]\n";
reset_cache(false);
Cache::set('course_status_u7_c42', 'In Progress', 300);
Cache::set('course_progress_u7_c42', 'x', 300);
Cache::set('course_content_u7_c42', 'y', 300);
Cache::set('42_students_count', 123, HOUR_IN_SECONDS);
$enroll_cbs = $GLOBALS['__hooks']['learndash_update_course_access'] ?? [];
check('enrollment hook is registered', count($enroll_cbs) === 1);
foreach ($enroll_cbs as $cb) {
    $cb(7, 42);
}
check('enrollment clears user status', Cache::get('course_status_u7_c42') === false);
check('enrollment clears user progress', Cache::get('course_progress_u7_c42') === false);
check('enrollment clears user content', Cache::get('course_content_u7_c42') === false);
check('enrollment clears students count', Cache::get('42_students_count') === false);

// --- 6. Completion hook clears the right keys -------------------------------
echo "\n[learndash_*_completed hooks]\n";
reset_cache(false);
Cache::set('course_status_u7_c42', 'In Progress', 300);
Cache::set('course_progress_u7_c42', 'x', 300);
Cache::set('course_content_u7_c42', 'y', 300);
$lesson_cbs = $GLOBALS['__hooks']['learndash_lesson_completed'] ?? [];
check('lesson-completed hook is registered', count($lesson_cbs) === 1);
foreach ($lesson_cbs as $cb) {
    $cb(['user_id' => 7, 'course_id' => 42]);
}
check('completion clears user status', Cache::get('course_status_u7_c42') === false);
check('completion clears user progress', Cache::get('course_progress_u7_c42') === false);
check('completion clears user content', Cache::get('course_content_u7_c42') === false);

// --- 7. Verified-user counter meta hook -------------------------------------
echo "\n[is_verified meta hook]\n";
reset_cache(false);
Cache::set('verified_users_count', 500, HOUR_IN_SECONDS);
$meta_cbs = $GLOBALS['__hooks']['updated_user_meta'] ?? [];
check('user-meta hook is registered', count($meta_cbs) === 1);
foreach ($meta_cbs as $cb) {
    $cb(1, 55, 'unrelated_meta'); // should NOT clear
}
check('unrelated meta leaves counter intact', Cache::get('verified_users_count') === 500);
foreach ($meta_cbs as $cb) {
    $cb(1, 55, 'is_verified'); // should clear
}
check('is_verified change clears counter', Cache::get('verified_users_count') === false);

// ---------------------------------------------------------------------------
// Summary
// ---------------------------------------------------------------------------

echo "\n========================\n";
echo "Passed: {$GLOBALS['__pass']}   Failed: {$GLOBALS['__fail']}\n\n";
exit($GLOBALS['__fail'] === 0 ? 0 : 1);
