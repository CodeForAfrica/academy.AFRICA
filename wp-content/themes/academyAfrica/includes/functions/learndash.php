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
    $count = \AcademyAfrica\Theme\Cache\Cache::get($cache_key);

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

    \AcademyAfrica\Theme\Cache\Cache::set($cache_key, $count, HOUR_IN_SECONDS);

    return $count;
}

/**
 * Branded replacement for LearnDash's `modules/alert.php`, used by the course
 * prerequisites, points-access, drip-feed "not available" and quiz
 * retry-limit messages (see the learndash/ld30/modules/messages/*.php
 * overrides and quiz.php). The stock component has no unscoped base CSS
 * outside the registration wrapper, so it renders as plain, unstyled text
 * wherever LearnDash shows it inside a single course/lesson/quiz view.
 *
 * @param array $args {
 *     @type string     $type    'warning' or 'info'. Controls the accent color.
 *     @type string     $icon    'alert' or 'calendar'.
 *     @type string     $message HTML message, already escaped/kses'd by the caller.
 *     @type array|false $button  Optional ['url' => ..., 'label' => ...].
 * }
 */
function academyafrica_render_ld_notice($args)
{
    $args = wp_parse_args($args, [
        'type'    => 'info',
        'icon'    => 'alert',
        'message' => '',
        'button'  => false,
    ]);

    if (empty($args['message'])) {
        return;
    }
    ?>
    <div class="ld-notice ld-notice--<?php echo esc_attr($args['type']); ?>" role="status">
        <span class="ld-notice__icon" aria-hidden="true">
            <?php academyafrica_render_ld_notice_icon($args['icon']); ?>
        </span>
        <div class="ld-notice__body">
            <div class="ld-notice__message"><?php echo wp_kses_post($args['message']); ?></div>
            <?php if (!empty($args['button']['url'])) : ?>
                <a class="button primary small ld-notice__button" href="<?php echo esc_url($args['button']['url']); ?>">
                    <?php echo esc_html($args['button']['label'] ?? ''); ?>
                </a>
            <?php endif; ?>
        </div>
    </div>
    <?php
}

function academyafrica_render_ld_notice_icon($icon)
{
    if ('calendar' === $icon) {
        ?>
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <rect x="3" y="5" width="18" height="16" rx="2" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
            <path d="M3 9H21" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
            <path d="M8 3V6M16 3V6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
        </svg>
        <?php
        return;
    }
    ?>
    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
        <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="2" />
        <path d="M12 8V13" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
        <circle cx="12" cy="16.5" r="1" fill="currentColor" />
    </svg>
    <?php
}
