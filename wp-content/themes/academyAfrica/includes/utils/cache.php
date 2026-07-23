<?php

namespace AcademyAfrica\Theme\Cache;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Central object-cache helper for the academyAfrica theme.
 *
 * All custom caches live in the single group `academy_africa`. Reliable
 * invalidation is hard because many object-cache backends do not implement
 * group flushing (`wp_cache_flush_group()` / `WP_Object_Cache::flush_group()`).
 * When that is unavailable we cannot delete "everything in a group" directly.
 *
 * The fix is a *versioned key namespace*: every cache key is suffixed with a
 * monotonically increasing version counter (`Cache::key()`), and invalidation
 * bumps that counter (`Cache::flush()`). After a bump, every previously written
 * key is unreachable — reads miss and regenerate — regardless of whether the
 * backend supports group flushing. Where the backend *does* support it, we also
 * flush the group to reclaim the orphaned entries immediately.
 *
 * See CACHE.md (in the theme root) for the full key/owner/TTL/invalidation map.
 */
class Cache
{
    /** Cache group shared by every custom cache in the theme. */
    public const GROUP = 'academy_africa';

    /** Key holding the group's namespace version counter. */
    public const VERSION_KEY = 'academy_africa_cache_version';

    /**
     * Current namespace version for the group. Seeded to 1 on first read so
     * keys never carry `:v0`, which reads as ambiguous.
     */
    public static function version(): int
    {
        $version = wp_cache_get(self::VERSION_KEY, self::GROUP);
        if (false === $version) {
            $version = 1;
            wp_cache_set(self::VERSION_KEY, $version, self::GROUP, WEEK_IN_SECONDS);
        }
        return (int) $version;
    }

    /**
     * Build a version-namespaced cache key. A `Cache::flush()` bumps the
     * version, so the returned string changes and every prior key is stranded.
     */
    public static function key(string $key): string
    {
        return $key . ':v' . self::version();
    }

    /** Read a value written through {@see self::set()}. */
    public static function get(string $key)
    {
        return wp_cache_get(self::key($key), self::GROUP);
    }

    /** Write a value under the versioned key. TTL defaults to non-expiring. */
    public static function set(string $key, $data, int $ttl = 0): bool
    {
        return wp_cache_set(self::key($key), $data, self::GROUP, $ttl);
    }

    /** Delete a single versioned key (used for targeted per-user invalidation). */
    public static function delete(string $key): bool
    {
        return wp_cache_delete(self::key($key), self::GROUP);
    }

    /**
     * Whether the active object cache backend can flush an entire group.
     * Prefers the canonical `wp_cache_supports()` (WP 6.1+) and falls back to
     * a plain function-existence check on older cores.
     */
    public static function supports_group_flush(): bool
    {
        if (function_exists('wp_cache_supports')) {
            return wp_cache_supports('flush_group');
        }
        return function_exists('wp_cache_flush_group');
    }

    /**
     * Invalidate every user-agnostic cache in the group.
     *
     * Always bumps the namespace version (the universally reliable path). When
     * the backend supports group flushing we flush first to reclaim memory, then
     * bump — after a real flush the version key is gone, so the bump restarts the
     * namespace cleanly with no risk of colliding with stranded entries.
     */
    public static function flush(): void
    {
        if (self::supports_group_flush()) {
            wp_cache_flush_group(self::GROUP);
        }
        $next = ((int) wp_cache_get(self::VERSION_KEY, self::GROUP)) + 1;
        wp_cache_set(self::VERSION_KEY, $next, self::GROUP, WEEK_IN_SECONDS);

        do_action('qm/debug', 'Cache: flushed academy_africa group (version {version})', [
            'version' => $next,
        ]);
    }
}

/*
 * -----------------------------------------------------------------------------
 * Invalidation hooks
 * -----------------------------------------------------------------------------
 * Registered once, on include. Two kinds of invalidation:
 *   1. Content changes  -> Cache::flush() (bumps the group version).
 *   2. Per-user changes -> targeted Cache::delete() of the affected keys.
 */

if (!defined('ACADEMY_AFRICA_CACHE_HOOKS_REGISTERED')) {
    define('ACADEMY_AFRICA_CACHE_HOOKS_REGISTERED', true);

    /**
     * Post types whose creation/update/deletion can change any user-agnostic
     * cache (course listings, learning paths, curricula, org/author data,
     * published counts).
     */
    $academy_africa_cache_post_types = [
        'ac-learning-path',
        'sfwd-courses',
        'sfwd-lessons',
        'sfwd-topic',
        'sfwd-quiz',
        'ac-organization',
        'event',
    ];

    // Flush on save of any relevant post type. save_post_{type} keeps this
    // cheap — the callback only fires for the types we care about.
    foreach ($academy_africa_cache_post_types as $academy_africa_cache_type) {
        add_action('save_post_' . $academy_africa_cache_type, static function ($post_id): void {
            // Skip autosaves/revisions — they never change published content.
            if (wp_is_post_autosave($post_id) || wp_is_post_revision($post_id)) {
                return;
            }
            Cache::flush();
        });
    }

    // Flush on deletion of any relevant post type (delete_post is generic).
    add_action('delete_post', static function ($post_id) use ($academy_africa_cache_post_types): void {
        if (in_array(get_post_type($post_id), $academy_africa_cache_post_types, true)) {
            Cache::flush();
        }
    });

    // Author/instructor data (academy_instructors, course_author_data_*)
    // is built from user profile + ACF user meta.
    add_action('profile_update', static function (): void {
        Cache::flush();
    });

    // Verified-user hero counter: clear when the is_verified meta changes.
    $academy_africa_verified_meta = static function ($meta_id, $object_id, $meta_key): void {
        if ('is_verified' === $meta_key) {
            Cache::delete('verified_users_count');
        }
    };
    add_action('added_user_meta', $academy_africa_verified_meta, 10, 3);
    add_action('updated_user_meta', $academy_africa_verified_meta, 10, 3);
    add_action('deleted_user_meta', $academy_africa_verified_meta, 10, 3);

    /**
     * Enrollment / access changes. Fires when a user gains or loses access to a
     * course (manual enrollment, group enrollment, purchase, expiry). Clears the
     * affected user's per-course status/progress/curriculum caches plus the
     * course's student-count cache.
     *
     * @param int  $user_id
     * @param int  $course_id
     */
    add_action('learndash_update_course_access', static function ($user_id, $course_id): void {
        $user_id   = (int) $user_id;
        $course_id = (int) $course_id;
        if (!$user_id || !$course_id) {
            return;
        }
        Cache::delete('course_status_u' . $user_id . '_c' . $course_id);
        Cache::delete('course_progress_u' . $user_id . '_c' . $course_id);
        Cache::delete('course_content_u' . $user_id . '_c' . $course_id);
        Cache::delete($course_id . '_students_count');
    }, 10, 2);

    /**
     * Lesson / topic / quiz completion. Clears the completing user's per-course
     * status, progress bar, and rendered curriculum so their progress marks
     * update on the next request.
     */
    foreach (['learndash_lesson_completed', 'learndash_topic_completed', 'learndash_quiz_completed'] as $academy_africa_completion_hook) {
        add_action($academy_africa_completion_hook, static function ($data): void {
            $course_id = 0;
            $user_id   = 0;
            if (is_array($data)) {
                $course_id = isset($data['course']->ID) ? (int) $data['course']->ID : (int) ($data['course_id'] ?? 0);
                $user_id   = isset($data['user']->ID) ? (int) $data['user']->ID : (int) ($data['user_id'] ?? 0);
            }
            if ($course_id && $user_id) {
                Cache::delete('course_content_u' . $user_id . '_c' . $course_id);
                Cache::delete('course_status_u' . $user_id . '_c' . $course_id);
                Cache::delete('course_progress_u' . $user_id . '_c' . $course_id);
            }
        });
    }
}
