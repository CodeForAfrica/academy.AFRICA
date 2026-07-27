<?php

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Return a course's assigned certificate post only when it exists and is
 * published. Guards missing, deleted, or unpublished certificates so callers
 * get a controlled null instead of a fatal on ->post_content (#46).
 *
 * @param int $course_id
 * @return WP_Post|null
 */
function academyafrica_get_course_certificate_post($course_id)
{
    if (!$course_id || !function_exists('learndash_get_setting')) {
        return null;
    }

    $certificate_id = learndash_get_setting($course_id, 'certificate');
    if (empty($certificate_id)) {
        return null;
    }

    $cert_post = get_post($certificate_id);
    if (!($cert_post instanceof WP_Post) || 'publish' !== $cert_post->post_status) {
        return null;
    }

    return $cert_post;
}

/**
 * Whether a course has a usable (published) certificate assigned.
 *
 * @param int $course_id
 * @return bool
 */
function academyafrica_course_has_certificate($course_id)
{
    return (bool) academyafrica_get_course_certificate_post($course_id);
}

function academyafrica_count_students($post_id)
{
    if (function_exists('learndash_course_grid_count_students')) {
        return learndash_course_grid_count_students($post_id);
    }

    $cache_key = absint($post_id) . '_students_count';
    $count = wp_cache_get($cache_key, 'academy_africa');

    if (false !== $count) {
        return $count;
    }

    global $wpdb;

    $post_type = get_post_type($post_id);
    if ('sfwd-courses' === $post_type) {
        $meta_key = 'course_%d_access_from';
    } elseif ('groups' === $post_type) {
        $meta_key = 'learndash_group_users_%d';
    } else {
        return false;
    }

    $query = $wpdb->prepare(
        "SELECT COUNT(*) FROM {$wpdb->base_prefix}usermeta WHERE meta_key = %s",
        sprintf($meta_key, absint($post_id))
    );
    $count = (int) $wpdb->get_var($query);

    wp_cache_set($cache_key, $count, 'academy_africa', HOUR_IN_SECONDS);

    return $count;
}
