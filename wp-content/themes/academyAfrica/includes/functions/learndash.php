<?php

if (!defined('ABSPATH')) {
    exit;
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
