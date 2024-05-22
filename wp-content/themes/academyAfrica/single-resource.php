<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}
get_header();
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
?>

<style>
    .entry-title {
        display: none;
    }
</style>

<script>
    console.log(<? echo json_encode($course_id)?>)
</script>
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
            <hr class="divider">
            <div class="introduction">
                <p class="cfa-introduction-title">
                    Introduction
                </p>
                <div class="cfa-introduction">
                    <?php echo do_shortcode($course_intro); ?>
                </div>
            </div>
        </div>
    </div>
<?
get_footer();
?>
