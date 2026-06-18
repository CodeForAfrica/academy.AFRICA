<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

require_once __DIR__ . '/../../includes/utils/courses.php';

use AcademyAfrica\Theme\Courses\CoursesFunctions;

$course_id = get_the_ID();

$course_price = learndash_get_course_price($course_id);
$price = $course_price['price'] ? $course_price['price'] : 'Free';
$user_id = get_current_user_id();
$is_enrolled = sfwd_lms_has_access($course_id, $user_id);
$organizations = get_field('organization', $course_id) ?: [];
$related_courses = get_field('related_courses', $course_id) ?: [];
$short_description = get_field('short_description', $course_id) ?: '';
$course_status = learndash_course_status($course_id);
$post_data = get_post($course_id);
$course_intro    = $post_data->post_content;
// Get course language per Polylang
$course_language = function_exists('pll_get_post_language') ? pll_get_post_language($course_id) : 'en';

// Fetch lessons for this course
$lessons = learndash_get_course_lessons_list($course_id, $user_id);
if (empty($lessons)) {
    // Fallback: LearnDash course steps index may be out of sync; query by meta directly
    $lessons = get_posts([
        'post_type'   => 'sfwd-lessons',
        'numberposts' => -1,
        'post_status' => 'publish',
        'orderby'     => 'menu_order',
        'order'       => 'ASC',
        'meta_query'  => [['key' => 'course_id', 'value' => $course_id]],
        'lang'        => '',
    ]);
}
$lesson_topics = !empty($lessons) ? $lessons : [];

$leaning_attr = [
    'per_page' => -1,
];
$pathways = CoursesFunctions::getLearningPaths($leaning_attr);

$course_pathways = array_filter($pathways['learning_paths'], function ($pathway) use ($course_id) {
    foreach ($pathway['courses'] as $course) {
        if ($course['id']->ID == $course_id) {
            return true;
        }
    }
    return false;
});

?>

<style>
    .entry-title {
        display: none;
    }
</style>
<?
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
            <?php
            if (!empty($short_description)) {
            ?>
                <div class="description">
                    <?php echo $short_description; ?>
                </div>
            <?php
            }
            ?>
            <?
            if (!$is_enrolled) {
            ?>
                <div class="price">
                    <p class="cfa-price">
                        <?php echo $price ?>
                    </p>
                </div>
                <div class="certificate-text">
                    <!-- The certificate for this course can be downloaded for a small fee when the course is completed -->
                </div>
            <?
            }
            ?>
            <div class="share">
                <?php get_template_part('template-parts/social_share', 'template'); ?>
            </div>
            <?
            if ($is_enrolled && count($lesson_topics) > 0) {
            ?>
                <div class='progress'>
                    <?php echo do_shortcode('[learndash_course_progress]'); ?>
                    <?php
                    if ($course_status == "Completed") {
                        $cert_label = function_exists('pll__') ? pll__('Download Certificate') : 'Download Certificate';
                        echo "<a href='" . get_permalink($course_id) . "?certificate=true' class='pathways-link'>" . esc_html($cert_label) . "</a>";
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
                        // User enrolled but hasn't started yet — link to first lesson
                        $first_lesson = is_array($lesson_topics[0]) ? $lesson_topics[0]['post'] : $lesson_topics[0];
                        $first_lesson_url = get_permalink($first_lesson->ID);
                        echo '<a href="' . esc_url($first_lesson_url) . '" class="ld-button">' . esc_html($continue_label) . ' <span></span></a>';
                    }
                    ?>
                </div>
            <?
            } else {
            ?>
                <div class="enroll enroll-btn" id="enroll-button">
                    <?php
                    $enroll_label = function_exists('pll__') ? pll__('Enroll Now') : 'Enroll Now';
                    echo do_shortcode('[learndash_payment_buttons label="' . $enroll_label . '"]');
                    ?>
                </div>
            <?
            }
            ?>
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

            <?
            if (count($lesson_topics) > 0) {
            ?>
                <div class="carriculum">
                    <div class="title">
                        <p class="cfa-introduction-title"><?php echo function_exists('pll__') ? esc_html(pll__('Course Curriculum')) : 'Course Curriculum'; ?></p>
                    </div>
                    <?php echo do_shortcode('[course_content course_id="' . $course_id . '"]'); ?>
                </div>
            <?
            }
            ?>
            <div class="instructor">
                <div class="title">
                    <p class="cfa-introduction-title"><?php echo function_exists('pll__') ? esc_html(pll__('The Instructor')) : 'The Instructor'; ?></p>
                </div>
                <div class="authors">
                    <?php
                    $authors = get_coauthors();
                    foreach ($authors as $author) {
                        $first_name = get_the_author_meta('first_name', $author->ID);
                        $last_name = get_the_author_meta('last_name', $author->ID);
                        $name = (!empty($first_name) && !empty($last_name)) ? $first_name . ' ' . $last_name : $author->display_name;
                        $avatar_url = get_avatar_url($author->ID);
                        $user = get_userdata($author->ID);
                        $user_meta = get_user_meta($author->ID);
                        $description = !empty($user_meta['description'][0]) ? $user_meta['description'][0] : $author->description;
                        $author_twitter = get_field("twitter", $author->ID);
                        $author_facebook = get_field("facebook", $author->ID);
                        $author_linkedin = get_field("linked_in", $author->ID);
                        $author_instagram = get_field("instagram", $author->ID);
                        $twitter = get_the_author_meta('twitter', $author->ID) ?: (is_array($author_twitter) && isset($author_twitter['url']) ? $author_twitter['url'] : null);
                        $facebook = get_the_author_meta('facebook', $author->ID) ?: $author_facebook;
                        $linkedin = get_the_author_meta('linked_in', $author->ID) ?: $author_linkedin;
                        $instagram = get_the_author_meta('instagram', $author->ID) ?: $author_instagram;
                        $website = get_the_author_meta('website', $author->ID) ?: $author->website;
                        $slack = get_the_author_meta('slack', $author->ID);
                    ?>
                        <div class="author">
                            <div class="avatar-name">

                                <div class="avatar">
                                    <img height="100px" src="<?php echo $avatar_url; ?>" alt="">
                                </div>
                                <div class="name">
                                    <p><?php echo $name; ?></p>
                                </div>
                                <div class="share-icons">
                                    <!-- LinkedIn -->
                                    <?php if (!empty($linkedin)) : ?>
                                        <a style="color: #000; margin-right: 8px;" href="<?php echo esc_url($linkedin); ?>" target="_blank">
                                            <img class='icon-image' src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/icons/Type=linkedin, Size=24, Color=Black.svg" alt="LinkedIn">
                                        </a>
                                    <?php endif; ?>

                                    <!-- Twitter -->
                                    <?php if (!empty($twitter)) : ?>
                                        <a style="color: #000; margin-right: 8px;" href="<?php echo esc_url($twitter); ?>" target="_blank">
                                            <img style="margin-bottom: -2px" class='icon-image' src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/icons/Type=twitter, Size=24, Color=Black.svg" alt="Twitter">
                                        </a>
                                    <?php endif; ?>
                                    <!-- Facebook -->
                                    <?php if (!empty($facebook)) : ?>
                                        <a style="color: #000; margin-right: 8px;" href="<?php echo esc_url($facebook); ?>" target="_blank">
                                            <img class='icon-image' src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/icons/Type=facebook, Size=24, Color=Black.svg" alt="Facebook">
                                        </a>
                                    <?php endif; ?>
                                    <!-- Website -->
                                    <?php if (!empty($website)) : ?>
                                        <a style="color: #000; margin-right: 8px;" href="<?php echo esc_url($website); ?>" target="_blank">
                                            <img class='icon-image' src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/icons/Type=world, Size=24, Color=Black.svg" alt="Website">
                                        </a>
                                    <?php endif; ?>
                                    <!-- Instagram -->
                                    <?php if (!empty($instagram)) : ?>
                                        <a style="color: #000; margin-right: 8px;" href="<?php echo esc_url($instagram); ?>" target="_blank">
                                            <img class='icon-image' src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/icons/Type=instagram, Size=24, Color=Black.svg" alt="Instagram">
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="description wysiwyg">
                                <?php echo $description; ?>
                            </div>
                        </div>
                    <?php
                    }
                    ?>
                </div>
            </div>
            <?php
            if ($organizations) {
            ?>
                <div class="instructor organization">
                    <div class="title">
                        <p class="cfa-introduction-title"><?php echo function_exists('pll__') ? esc_html(pll__('The Organization')) : 'The Organization'; ?></p>
                    </div>
                    <div class="authors">
                        <?php
                        foreach ($organizations as $organization) {
                            $org_twitter = get_field("twitter", $organization->ID);
                            $org_facebook = get_field("facebook", $organization->ID);
                            $org_linkedin = get_field("linked_in", $organization->ID);
                            $org_instagram = get_field("instagram", $organization->ID);
                            $org_website = get_field("website", $organization->ID);
                            $org_slack = get_field("slack", $organization->ID);
                        ?>
                            <div class="author">
                                <div class="avatar-name">

                                    <div class="avatar">
                                        <img height="100px" style="border-radius: 0;" src="<?php echo get_the_post_thumbnail_url($organization->ID); ?>" alt="">
                                    </div>
                                    <div class="name">
                                        <p><?php echo $organization->post_title; ?></p>
                                    </div>
                                    <div class="share-icons">
                                        <!-- LinkedIn -->
                                        <?php if (!empty($org_linkedin)) : ?>
                                            <a style="color: #000; margin-right: 8px;" href="<?php echo esc_url($org_linkedin); ?>" target="_blank">
                                                <img class='icon-image' src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/icons/Type=linkedin, Size=24, Color=Black.svg" alt="LinkedIn">
                                            </a>
                                        <?php endif; ?>

                                        <!-- Twitter -->
                                        <?php if (!empty($org_twitter)) : ?>
                                            <a style="color: #000; margin-right: 8px;" href="<?php echo esc_url($org_witter); ?>" target="_blank">
                                                <img style="margin-bottom: -2px" class='icon-image' src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/icons/Type=twitter, Size=24, Color=Black.svg" alt="Twitter">
                                            </a>
                                        <?php endif; ?>
                                        <!-- Facebook -->
                                        <?php if (!empty($org_facebook)) : ?>
                                            <a style="color: #000; margin-right: 8px;" href="<?php echo esc_url($org_facebook); ?>" target="_blank">
                                                <img class='icon-image' src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/icons/Type=facebook, Size=24, Color=Black.svg" alt="Facebook">
                                            </a>
                                        <?php endif; ?>
                                        <!-- Website -->
                                        <?php if (!empty($org_website)) : ?>
                                            <a style="color: #000; margin-right: 8px;" href="<?php echo esc_url($org_website); ?>" target="_blank">
                                                <img class='icon-image' src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/icons/Type=world, Size=24, Color=Black.svg" alt="Website">
                                            </a>
                                        <?php endif; ?>
                                        <!-- Instagram -->
                                        <?php if (!empty($org_instagram)) : ?>
                                            <a style="color: #000; margin-right: 8px;" href="<?php echo esc_url($org_instagram); ?>" target="_blank">
                                                <img class='icon-image' src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/icons/Type=instagram, Size=24, Color=Black.svg" alt="Instagram">
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="description wysiwyg">
                                    <?php echo $organization->post_excerpt; ?>
                                </div>
                            </div>
                        <?
                        }
                        ?>
                    </div>
                </div>
            <?php
            }
            ?>
            <?
            if (!$is_enrolled) {
            ?>
                <div class="enroll enrolllled" id="enroll-button">
                    <?php
                    $enroll_label = function_exists('pll__') ? pll__('Enroll Now') : 'Enroll Now';
                    echo do_shortcode('[learndash_payment_buttons label="' . $enroll_label . '"]');
                    ?>
                </div>
            <?
            }
            ?>
            <?
            if ($related_courses) {
            ?>
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
                                    $related_courses = array_slice($related_courses, 0, 3);
                                    foreach ($related_courses as $course) {
                                        $course_thumbnail = get_the_post_thumbnail_url($course, 'full');
                                        $course_title = $course->post_title;
                                        $course_link = get_permalink($course->ID);
                                        $course_author = get_the_author_meta('display_name', $course->post_author);
                                        $course_meta = get_post_meta($course->ID, 'sfwd-courses', true);
                                        $course_price = is_array($course_meta) ? $course_meta['sfwd-courses_course_price'] : 0;
                                        $course_price = empty($course_price) ? "Free" : $course_price;
                                        $students_count = academyafrica_count_students($course->ID);

                                    ?>
                                        <?php get_template_part(
                                            'template-parts/course_card',
                                            'template',
                                            [
                                                'course_title' => $course_title,
                                                'course_author' => $course_author,
                                                'course_thumbnail' => $course_thumbnail,
                                                'course_link' => $course_link,
                                                'course_price' => $course_price,
                                                'students' => $students_count
                                            ]
                                        ); ?>
                                    <?
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?
            }
            ?>
        </div>
    </div>
<?
}
?>