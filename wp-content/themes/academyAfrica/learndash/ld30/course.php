<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

require_once __DIR__ . '/../../includes/utils/courses.php';

use AcademyAfrica\Theme\Courses\CoursesFunctions;

use function ElementorDeps\DI\get;

$course_id = get_the_ID();

$course_price = learndash_get_course_price($course_id);
$price = $course_price['price'] ? $course_price['price'] : 'Free';
$user_id = get_current_user_id();
$user_courses = learndash_user_get_enrolled_courses($user_id);
$is_enrolled = in_array($course_id, $user_courses);
$organizations = get_field('organization', $course_id);
$related_courses = get_field('related_courses', $course_id);
$short_description = get_field('short_description', $course_id);
$course_status = learndash_course_status($course_id);
$post_data = get_post($course_id);
$course_intro    = $post_data->post_content;

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
            if ($is_enrolled && count($lesson_topics) > 1) {
            ?>
                <div class='progress'>
                    <?php echo do_shortcode('[learndash_course_progress]'); ?>
                    <?php
                    if ($course_status == "Completed") {
                        echo "<a href='" . get_permalink($course_id) . "?certificate=true' class='pathways-link'>Download Certificate</a>";
                    }
                    ?>
                </div>
                <div class="continue">
                    <?php echo do_shortcode('[ld_course_resume label="Continue the Course <span></span>"]'); ?>
                </div>
            <?
            } else {
            ?>
                <div class="enroll enroll-btn" id="enroll-button">
                    <?php echo do_shortcode('[learndash_payment_buttons label="Enroll Now"]'); ?>
                </div>
            <?
            }
            ?>
            <hr class="divider">
            <div class="introduction">
                <p class="cfa-introduction-title">
                    Introduction
                </p>
                <div class="cfa-introduction">
                    <?php echo do_shortcode($course_intro); ?>
                </div>
            </div>
            <hr class="divider">
            <div class="pathways">
                <?php if (!empty($course_pathways)) : ?>
                    <p class="pathways-title">Completing this course can bring you closer to completing the following pathways</p>
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
 
            ?>
                <div class="carriculum">
                    <div class="title">
                        <p class="cfa-introduction-title">Course Curriculum</p>
                    </div>
                    <?php echo do_shortcode('[course_content]'); ?>
                </div>
            <?
            ?>
            <div class="instructor">
                <div class="title">
                    <p class="cfa-introduction-title">The Instructor</p>
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
                        <p class="cfa-introduction-title">The Organization</p>
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
                    <?php echo do_shortcode('[learndash_payment_buttons label="Enroll Now"]'); ?>
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
                            Related
                        </div>
                        <div class="panel">
                            <div class="related-courses">
                                <div class="title"> Related Courses </div>
                                <div class="list">
                                    <?php
                                    $related_courses = array_slice($related_courses, 0, 3);
                                    foreach ($related_courses as $course) {
                                        $course_thumbnail = get_the_post_thumbnail_url($course, 'full');
                                        $course_title = $course->post_title;
                                        $course_link = get_permalink($course->ID);
                                        $course_author = get_the_author_meta('display_name', $course->post_author);
                                        $course_meta = get_post_meta($course->ID);
                                        $course_price = $course_meta['sfwd-courses_course_price'];
                                        $course_price = $course_price == 0 ? "Free" : $course_price;
                                        $students_count = learndash_course_grid_count_students($course->ID);

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