<?php

/**
 * Theme override of sfwd-lms/themes/ld30/templates/modules/messages/prerequisites.php.
 * Same message-building logic as the stock template, but renders via
 * academyafrica_render_ld_notice() instead of delegating to modules/alert.php,
 * which has no unscoped base styling here.
 *
 * Available variables (see class-ld-cpt-instance.php):
 * $current_post, $prerequisite_post, $prerequisite_posts_all, $content_type, $course_settings
 */

if (!defined('ABSPATH')) {
    exit;
}

$post_links = '';
$i = 0;
if (!empty($prerequisite_posts_all)) {
    foreach ($prerequisite_posts_all as $pre_post_id => $pre_status) {
        if (false === (bool) $pre_status) {
            $i++;
            if (!empty($post_links)) {
                $post_links .= ', ';
            }
            $post_links .= '<a href="' . esc_url(get_the_permalink($pre_post_id)) . '">' . wp_kses_post(get_the_title($pre_post_id)) . '</a>';
        }
    }
}

$message = '<p>';
$course_prereq_compare = learndash_get_setting($current_post, 'course_prerequisite_compare');

if ('ANY' === $course_prereq_compare && $i > 1) {
    $message .= sprintf(
        // translators: placeholders: course, courses.
        esc_html_x('To take this %1$s, you need to complete any of the following %2$s first:', 'placeholders: course, courses', 'learndash'),
        $content_type,
        esc_html(learndash_get_custom_label_lower('courses'))
    );
} else {
    $message .= sprintf(
        // translators: placeholders: (1) course singular, (2) course or courses.
        esc_html_x('To take this %1$s, you need to complete the following %2$s first:', 'placeholders: (1) course singular, (2) course or courses', 'learndash'),
        $content_type,
        esc_html(_n(learndash_get_custom_label_lower('course'), learndash_get_custom_label_lower('courses'), $i, 'learndash')) // phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralSingle, WordPress.WP.I18n.NonSingularStringLiteralPlural
    );
}

if (!empty($post_links)) {
    $message .= ' <span class="ld-notice__links">' . $post_links . '</span>';
}
$message .= '</p>';

academyafrica_render_ld_notice([
    'type'    => 'warning',
    'icon'    => 'alert',
    'message' => $message,
]);
