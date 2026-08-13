<?php

/**
 * Theme override of sfwd-lms/themes/ld30/templates/modules/messages/lesson-not-available.php
 * (the drip-feed "not yet available" message). Rendered via
 * academyafrica_render_ld_notice() instead of the unstyled modules/alert.php.
 *
 * Available variables (see ld-course-user-functions.php / ld-course-navigation.php):
 * $user_id, $course_id, $lesson_id, $lesson_access_from_int, $lesson_access_from_date, $context
 */

if (!defined('ABSPATH')) {
    exit;
}

$message = sprintf(
    wp_kses_post(
        // translators: Date when content will be available.
        __('<span class="ld-notice__label">Available on:</span> <span class="ld-notice__date">%s</span>', 'learndash')
    ),
    esc_html(learndash_adjust_date_time_display($lesson_access_from_int))
);

$button = false;
if (in_array($context, ['lesson', 'topic', 'quiz'], true)) {
    if (empty($course_id)) {
        $course_id = learndash_get_course_id($lesson_id);
    }
    if (!empty($course_id)) {
        $button = [
            'url'   => get_permalink($course_id),
            'label' => learndash_get_label_course_step_back(learndash_get_post_type_slug('course')),
        ];
    }
}

academyafrica_render_ld_notice([
    'type'    => 'info',
    'icon'    => 'calendar',
    'button'  => $button,
    'message' => apply_filters('learndash_lesson_available_from_text', $message, get_post($lesson_id), $lesson_access_from_int),
]);
