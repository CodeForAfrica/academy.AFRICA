<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

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
<?
if ($course_status == "Completed") {
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
            <?
            if (count($lesson_topics) > 0) {
            ?>
                <div class="carriculum">
                    <div class="title">
                        <p class="cfa-introduction-title">Course Curriculum</p>
                    </div>
                    <?php echo do_shortcode('[course_content]'); ?>
                </div>
            <?
            }
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
                        $description = wpautop($author->description);
                    ?>
                        <div class="author">
                            <div class="name">
                                <p><?php echo $name; ?></p>
                            </div>
                            <div class="avatar">
                                <img src="<?php echo $avatar_url; ?>" alt="">
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
                <div class="organization">
                    <div class="title">
                        <p class="cfa-introduction-title">The Organization</p>
                    </div>
                    <div class="list">
                        <?php
                        foreach ($organizations as $organization) {
                            echo "
                <div class='item'>
                    <div class='name'>
                        <p>" . $organization->post_title . "</p>
                    </div>
                    <div class='avatar'>
                        <img src='" . get_the_post_thumbnail_url($organization->ID) . "' alt=''>
                    </div>
                    <div class='description'>
                        <div>" . $organization->post_excerpt . "</div>
                    </div>
                </div>
                ";
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
