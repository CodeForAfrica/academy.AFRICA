<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

require_once __DIR__ . '/../../includes/utils/courses.php';

use AcademyAfrica\Theme\Courses\CoursesFunctions;

$course_id = get_the_ID();

do_action('qm/start', 'course:init');

$course_price      = learndash_get_course_price($course_id);
$price             = $course_price['price'] ? $course_price['price'] : 'Free';
$user_id           = get_current_user_id();
$is_enrolled       = sfwd_lms_has_access($course_id, $user_id);
$organizations     = get_field('organization', $course_id) ?: [];
$related_courses   = get_field('related_courses', $course_id) ?: [];
$short_description = get_field('short_description', $course_id) ?: '';
$cs_cache_key  = 'course_status_u' . $user_id . '_c' . $course_id;
$course_status = wp_cache_get($cs_cache_key, 'academy_africa');
if (false === $course_status) {
    $course_status = learndash_course_status($course_id);
    wp_cache_set($cs_cache_key, $course_status, 'academy_africa', 5 * MINUTE_IN_SECONDS);
}
$post_data         = get_post($course_id);
if (!$post_data) {
    do_action('qm/error', 'course.php: get_post() returned null for course_id={id}', ['id' => $course_id]);
}
$course_intro    = $post_data ? $post_data->post_content : '';
$course_language = function_exists('pll_get_post_language') ? pll_get_post_language($course_id) : 'en';

// ------------------------------------------------------------------
// Lessons — cached per course (list is user-agnostic; we only need
// count and first-lesson URL, not per-user completion state)
// ------------------------------------------------------------------
do_action('qm/start', 'course:fetch_lessons');

$lessons = wp_cache_get('course_lessons_' . $course_id, 'academy_africa');
if (false === $lessons) {
    $lessons = learndash_get_course_lessons_list($course_id, 0);
    if (empty($lessons)) {
        // Fallback: LearnDash steps index out of sync — query by meta directly
        $lessons = get_posts([
            'post_type'              => 'sfwd-lessons',
            'numberposts'            => -1,
            'post_status'            => 'publish',
            'orderby'                => 'menu_order',
            'order'                  => 'ASC',
            'meta_query'             => [['key' => 'course_id', 'value' => $course_id]],
            'lang'                   => '',
            'update_post_meta_cache' => false,
            'update_post_thumbnail_cache' => false,
        ]);
    }
    $lessons = $lessons ?: [];
    wp_cache_set('course_lessons_' . $course_id, $lessons, 'academy_africa', HOUR_IN_SECONDS);
    if (empty($lessons)) {
        do_action('qm/warning', 'course.php: no lessons found for course_id={id}', ['id' => $course_id]);
    }
}

do_action('qm/stop', 'course:fetch_lessons');
do_action('qm/debug', 'course:fetch_lessons: {count} lessons for course {id}', [
    'count' => count($lessons),
    'id'    => $course_id,
]);
$lesson_topics = $lessons;

// ------------------------------------------------------------------
// Learning paths — already cached in CoursesFunctions
// ------------------------------------------------------------------
do_action('qm/start', 'course:getLearningPaths');
$pathways = CoursesFunctions::getLearningPaths(['per_page' => -1]);
do_action('qm/stop', 'course:getLearningPaths');
do_action('qm/debug', 'course:getLearningPaths: {count} paths', ['count' => count($pathways['learning_paths'])]);

do_action('qm/stop', 'course:init');

$course_pathways = array_filter($pathways['learning_paths'], function ($pathway) use ($course_id) {
    foreach ($pathway['courses'] as $course) {
        if ($course['id'] == $course_id) {
            return true;
        }
    }
    return false;
});

// ------------------------------------------------------------------
// Instructor data — cached per course; multiple get_field() +
// get_user_meta() calls per author add up fast with co-authors
// ------------------------------------------------------------------
$authors_data = wp_cache_get('course_author_data_' . $course_id, 'academy_africa');
if (false === $authors_data) {
    $raw_authors = get_coauthors();
    if (empty($raw_authors)) {
        do_action('qm/warning', 'course.php: get_coauthors() empty for course_id={id}', ['id' => $course_id]);
    }
    $authors_data = [];
    foreach ($raw_authors as $author) {
        $first_name = get_the_author_meta('first_name', $author->ID);
        $last_name  = get_the_author_meta('last_name', $author->ID);
        $name       = (!empty($first_name) && !empty($last_name)) ? $first_name . ' ' . $last_name : $author->display_name;

        // get_user_meta with no key primes the full meta cache — subsequent
        // get_the_author_meta() calls below are then served from cache
        $user_meta   = get_user_meta($author->ID);
        $description = !empty($user_meta['description'][0]) ? $user_meta['description'][0] : ($author->description ?? '');

        $acf_twitter  = get_field('twitter',    $author->ID);
        $acf_facebook = get_field('facebook',   $author->ID);
        $acf_linkedin = get_field('linked_in',  $author->ID);
        $acf_instagram = get_field('instagram', $author->ID);

        $authors_data[] = [
            'name'        => $name,
            'avatar_url'  => get_avatar_url($author->ID),
            'description' => $description,
            'twitter'     => get_the_author_meta('twitter',   $author->ID) ?: (is_array($acf_twitter) && isset($acf_twitter['url']) ? $acf_twitter['url'] : $acf_twitter),
            'facebook'    => get_the_author_meta('facebook',  $author->ID) ?: $acf_facebook,
            'linkedin'    => get_the_author_meta('linked_in', $author->ID) ?: $acf_linkedin,
            'instagram'   => get_the_author_meta('instagram', $author->ID) ?: $acf_instagram,
            'website'     => get_the_author_meta('website',   $author->ID) ?: ($author->website ?? ''),
        ];
    }
    wp_cache_set('course_author_data_' . $course_id, $authors_data, 'academy_africa', HOUR_IN_SECONDS);
}

// ------------------------------------------------------------------
// Organization data — cached per course; 6× get_field() per org
// ------------------------------------------------------------------
$orgs_data = wp_cache_get('course_orgs_data_' . $course_id, 'academy_africa');
if (false === $orgs_data) {
    $orgs_data = [];
    foreach ($organizations as $organization) {
        $orgs_data[] = [
            'id'        => $organization->ID,
            'title'     => $organization->post_title,
            'excerpt'   => $organization->post_excerpt,
            'thumbnail' => get_the_post_thumbnail_url($organization->ID),
            'twitter'   => get_field('twitter',   $organization->ID),
            'facebook'  => get_field('facebook',  $organization->ID),
            'linkedin'  => get_field('linked_in', $organization->ID),
            'instagram' => get_field('instagram', $organization->ID),
            'website'   => get_field('website',   $organization->ID),
        ];
    }
    wp_cache_set('course_orgs_data_' . $course_id, $orgs_data, 'academy_africa', HOUR_IN_SECONDS);
}

?>

<style>
    .entry-title {
        display: none;
    }
</style>
<?php
$is_cert = isset($_GET["certificate"]);
if ($course_status == "Completed" && $is_cert) {
    get_template_part('template-parts/course_completed', null, array('course_id' => $course_id));
} else {
?>
    <div class="single-courses wysiwyg">
        <div class="wrapper">
            <div class="title-section">
                <div class="title">
                    <p class="cfa-title">
                        <?php the_title(); ?>
                    </p>
                </div>
                <div class="avatar">
                    <?php
                    $course_thumbnail = get_the_post_thumbnail_url($course_id);
                    $mooc_logo = get_stylesheet_directory_uri() . '/assets/images/mooc-logo-blue.svg';
                    $logo_url = $course_thumbnail ? $course_thumbnail : $mooc_logo;
                    echo '<img src="' . $logo_url . '" alt="">';
                    ?>
                </div>
            </div>
            <?php if (!empty($short_description)) : ?>
                <div class="description">
                    <?php echo $short_description; ?>
                </div>
            <?php endif; ?>

            <?php if (!$is_enrolled) : ?>
                <div class="price">
                    <p class="cfa-price">
                        <?php echo $price ?>
                    </p>
                </div>
                <div class="certificate-text">
                    <!-- The certificate for this course can be downloaded for a small fee when the course is completed -->
                </div>
            <?php endif; ?>

            <div class="share">
                <?php get_template_part('template-parts/social_share', 'template'); ?>
            </div>

            <?php if ($is_enrolled && count($lesson_topics) > 0) : ?>
                <div class='progress'>
                    <?php
                    $cp_cache_key = 'course_progress_u' . $user_id . '_c' . $course_id;
                    $cp_output    = wp_cache_get($cp_cache_key, 'academy_africa');
                    if (false === $cp_output) {
                        $cp_output = do_shortcode('[learndash_course_progress]');
                        wp_cache_set($cp_cache_key, $cp_output, 'academy_africa', 5 * MINUTE_IN_SECONDS);
                    }
                    echo $cp_output;
                    ?>
                    <?php
                    if ($course_status == "Completed" && academyafrica_course_has_certificate($course_id)) {
                        $cert_label = function_exists('pll__') ? pll__('Download Certificate') : 'Download Certificate';
                        echo "<a href='" . esc_url(get_permalink($course_id) . '?certificate=true') . "' class='pathways-link'>" . esc_html($cert_label) . "</a>";
                    }
                    ?>
                </div>
                <div class="continue">
                    <?php
                    $continue_label = function_exists('pll__') ? pll__('Continue the Course') : 'Continue the Course';
                    $resume_output = do_shortcode('[ld_course_resume label="' . $continue_label . ' <span></span>"]');
                    if (!empty(trim($resume_output))) {
                        echo $resume_output;
                    } elseif (!empty($lesson_topics)) {
                        $first_lesson = is_array($lesson_topics[0]) ? $lesson_topics[0]['post'] : $lesson_topics[0];
                        $first_lesson_url = get_permalink($first_lesson->ID);
                        echo '<a href="' . esc_url($first_lesson_url) . '" class="ld-button">' . esc_html($continue_label) . ' <span></span></a>';
                    }
                    ?>
                </div>
            <?php else : ?>
                <div class="enroll enroll-btn" id="enroll-button">
                    <?php
                    $enroll_label = function_exists('pll__') ? pll__('Enroll Now') : 'Enroll Now';
                    echo do_shortcode('[learndash_payment_buttons label="' . $enroll_label . '"]');
                    ?>
                </div>
            <?php endif; ?>

            <hr class="divider">
            <div class="introduction">
                <p class="cfa-introduction-title">
                    <?php echo function_exists('pll__') ? esc_html(pll__('Introduction')) : 'Introduction'; ?>
                </p>
                <div class="cfa-introduction">
                    <?php echo do_shortcode($course_intro); ?>
                </div>
            </div>
            <hr class="divider">

            <div class="pathways">
                <?php if (!empty($course_pathways)) : ?>
                    <p class="pathways-title"><?php echo function_exists('pll__') ? esc_html(pll__('Completing this course can bring you closer to completing the following pathways')) : 'Completing this course can bring you closer to completing the following pathways'; ?></p>
                    <ul class="pathways-list">
                        <?php foreach ($course_pathways as $pathway) : ?>
                            <li>
                                <a class="pathways-link" href="<?php echo esc_url(get_permalink($pathway['id'])); ?>">
                                    <?php echo esc_html($pathway['title']); ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>

            <?php if (count($lesson_topics) > 0) : ?>
                <div class="carriculum">
                    <div class="title">
                        <p class="cfa-introduction-title"><?php echo function_exists('pll__') ? esc_html(pll__('Course Curriculum')) : 'Course Curriculum'; ?></p>
                    </div>
                    <?php
                    // Cache course_content output for non-enrolled users — the curriculum
                    // tree is identical for all anonymous/unenrolled visitors and is the
                    // single most expensive shortcode on the page.
                    do_action('qm/start', 'course:course_content_shortcode');
                    if (!$is_enrolled) {
                        $cc_cache_key = 'course_content_html_' . $course_id;
                        $cc_output = wp_cache_get($cc_cache_key, 'academy_africa');
                        if (false === $cc_output) {
                            $cc_output = do_shortcode('[course_content course_id="' . $course_id . '"]');
                            wp_cache_set($cc_cache_key, $cc_output, 'academy_africa', HOUR_IN_SECONDS);
                        }
                        echo $cc_output;
                    } else {
                        echo do_shortcode('[course_content course_id="' . $course_id . '"]');
                    }
                    do_action('qm/stop', 'course:course_content_shortcode');
                    ?>
                </div>
            <?php endif; ?>

            <div class="instructor">
                <div class="title">
                    <p class="cfa-introduction-title"><?php echo function_exists('pll__') ? esc_html(pll__('The Instructor')) : 'The Instructor'; ?></p>
                </div>
                <div class="authors">
                    <?php foreach ($authors_data as $author) : ?>
                        <div class="author">
                            <div class="avatar-name">
                                <div class="avatar">
                                    <img height="100px" src="<?php echo esc_url($author['avatar_url']); ?>" alt="">
                                </div>
                                <div class="name">
                                    <p><?php echo esc_html($author['name']); ?></p>
                                </div>
                                <div class="share-icons">
                                    <?php if (!empty($author['linkedin'])) : ?>
                                        <a style="color: #000; margin-right: 8px;" href="<?php echo esc_url($author['linkedin']); ?>" target="_blank">
                                            <img class='icon-image' src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/icons/Type=linkedin, Size=24, Color=Black.svg" alt="LinkedIn">
                                        </a>
                                    <?php endif; ?>
                                    <?php if (!empty($author['twitter'])) : ?>
                                        <a style="color: #000; margin-right: 8px;" href="<?php echo esc_url($author['twitter']); ?>" target="_blank">
                                            <img style="margin-bottom: -2px" class='icon-image' src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/icons/Type=twitter, Size=24, Color=Black.svg" alt="Twitter">
                                        </a>
                                    <?php endif; ?>
                                    <?php if (!empty($author['facebook'])) : ?>
                                        <a style="color: #000; margin-right: 8px;" href="<?php echo esc_url($author['facebook']); ?>" target="_blank">
                                            <img class='icon-image' src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/icons/Type=facebook, Size=24, Color=Black.svg" alt="Facebook">
                                        </a>
                                    <?php endif; ?>
                                    <?php if (!empty($author['website'])) : ?>
                                        <a style="color: #000; margin-right: 8px;" href="<?php echo esc_url($author['website']); ?>" target="_blank">
                                            <img class='icon-image' src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/icons/Type=world, Size=24, Color=Black.svg" alt="Website">
                                        </a>
                                    <?php endif; ?>
                                    <?php if (!empty($author['instagram'])) : ?>
                                        <a style="color: #000; margin-right: 8px;" href="<?php echo esc_url($author['instagram']); ?>" target="_blank">
                                            <img class='icon-image' src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/icons/Type=instagram, Size=24, Color=Black.svg" alt="Instagram">
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="description wysiwyg">
                                <?php echo $author['description']; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <?php if (!empty($orgs_data)) : ?>
                <div class="instructor organization">
                    <div class="title">
                        <p class="cfa-introduction-title"><?php echo function_exists('pll__') ? esc_html(pll__('The Organization')) : 'The Organization'; ?></p>
                    </div>
                    <div class="authors">
                        <?php foreach ($orgs_data as $org) : ?>
                            <div class="author">
                                <div class="avatar-name">
                                    <div class="avatar">
                                        <img height="100px" style="border-radius: 0;" src="<?php echo esc_url($org['thumbnail']); ?>" alt="">
                                    </div>
                                    <div class="name">
                                        <p><?php echo esc_html($org['title']); ?></p>
                                    </div>
                                    <div class="share-icons">
                                        <?php if (!empty($org['linkedin'])) : ?>
                                            <a style="color: #000; margin-right: 8px;" href="<?php echo esc_url($org['linkedin']); ?>" target="_blank">
                                                <img class='icon-image' src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/icons/Type=linkedin, Size=24, Color=Black.svg" alt="LinkedIn">
                                            </a>
                                        <?php endif; ?>
                                        <?php if (!empty($org['twitter'])) : ?>
                                            <a style="color: #000; margin-right: 8px;" href="<?php echo esc_url($org['twitter']); ?>" target="_blank">
                                                <img style="margin-bottom: -2px" class='icon-image' src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/icons/Type=twitter, Size=24, Color=Black.svg" alt="Twitter">
                                            </a>
                                        <?php endif; ?>
                                        <?php if (!empty($org['facebook'])) : ?>
                                            <a style="color: #000; margin-right: 8px;" href="<?php echo esc_url($org['facebook']); ?>" target="_blank">
                                                <img class='icon-image' src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/icons/Type=facebook, Size=24, Color=Black.svg" alt="Facebook">
                                            </a>
                                        <?php endif; ?>
                                        <?php if (!empty($org['website'])) : ?>
                                            <a style="color: #000; margin-right: 8px;" href="<?php echo esc_url($org['website']); ?>" target="_blank">
                                                <img class='icon-image' src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/icons/Type=world, Size=24, Color=Black.svg" alt="Website">
                                            </a>
                                        <?php endif; ?>
                                        <?php if (!empty($org['instagram'])) : ?>
                                            <a style="color: #000; margin-right: 8px;" href="<?php echo esc_url($org['instagram']); ?>" target="_blank">
                                                <img class='icon-image' src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/icons/Type=instagram, Size=24, Color=Black.svg" alt="Instagram">
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="description wysiwyg">
                                    <?php echo $org['excerpt']; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (!$is_enrolled) : ?>
                <div class="enroll enrolllled" id="enroll-button">
                    <?php
                    $enroll_label = function_exists('pll__') ? pll__('Enroll Now') : 'Enroll Now';
                    echo do_shortcode('[learndash_payment_buttons label="' . $enroll_label . '"]');
                    ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($related_courses)) : ?>
                <div class="related">
                    <div class="accordion-parent">
                        <div class="accordion">
                            <?php echo function_exists('pll__') ? esc_html(pll__('Related')) : 'Related'; ?>
                        </div>
                        <div class="panel">
                            <div class="related-courses">
                                <div class="title"><?php echo function_exists('pll__') ? esc_html(pll__('Related Courses')) : 'Related Courses'; ?></div>
                                <div class="list">
                                    <?php
                                    foreach (array_slice($related_courses, 0, 3) as $course) {
                                        $course_meta  = get_post_meta($course->ID, 'sfwd-courses', true);
                                        $course_price = is_array($course_meta) ? $course_meta['sfwd-courses_course_price'] : 0;
                                        $course_price = empty($course_price) ? "Free" : $course_price;
                                        get_template_part('template-parts/course_card', 'template', [
                                            'course_title'     => $course->post_title,
                                            'course_author'    => get_the_author_meta('display_name', $course->post_author),
                                            'course_thumbnail' => get_the_post_thumbnail_url($course, 'full'),
                                            'course_link'      => get_permalink($course->ID),
                                            'course_price'     => $course_price,
                                            'students'         => academyafrica_count_students($course->ID),
                                        ]);
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

        </div>
    </div>
<?php
}
?>
