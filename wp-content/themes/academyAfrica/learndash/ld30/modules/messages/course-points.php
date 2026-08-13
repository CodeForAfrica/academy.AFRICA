<?php

/**
 * Theme override of sfwd-lms/themes/ld30/templates/modules/messages/course-points.php.
 * Same message as the stock template, but rendered via
 * academyafrica_render_ld_notice() instead of the unstyled modules/alert.php.
 *
 * Available variables (see class-ld-cpt-instance.php):
 * $current_post, $content_type, $course_access_points, $user_course_points, $course_settings
 */

if (!defined('ABSPATH')) {
    exit;
}

$message = sprintf(
    // translators: placeholders: %1$s: Course, %2$s: course access points, %3$s: user course points.
    esc_html_x(
        'To take this %1$s you need at least %2$.01f total points. You currently have %3$.01f points.',
        'placeholders: (1) will be Course. (2) course_access_points. (3) user_course_points ',
        'learndash'
    ),
    $content_type,
    $course_access_points,
    $user_course_points
);

academyafrica_render_ld_notice([
    'type'    => 'warning',
    'icon'    => 'alert',
    'message' => $message,
]);
