<?php

/**
 *Template Name: Single Course
 *Template Post Type: sfwd-courses
 */


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
$course_status = learndash_course_status($course_id);

$social_media_links = [
    [
        'link' => [
            'url' => 'https://www.facebook.com/CodeForAfrica',
        ],
        'type' => 'facebook',
    ],
    [
        'link' => [
            'url' => 'https://twitter.com/Code4Africa',
        ],
        'type' => 'twitter',
    ],
    [
        'link' => [
            'url' => 'https://www.instagram.com/code4africa/',
        ],
        'type' => 'instagram',
    ],
    [
        'link' => [
            'url' => 'https://www.linkedin.com/company/code-for-africa/',
        ],
        'type' => 'linkedin',
    ],
];
?>

<?
get_header();
?>
<script>
    let p = <? echo json_encode($course_status) ?>;
</script>
<?
if ($course_status == "Completed") {
    get_template_part('template-parts/course_completed', null, array('course_id' => $course_id));
} else {
?>
    <div class="single-courses">
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
                    echo '<img src="' . $course_thumbnail . '" alt="">';
                    ?>
                </div>
            </div>
            <div class="description">
                <?php the_excerpt(); ?>
            </div>
            <?
            if (!$is_enrolled) {
            ?>
                <div class="price">
                    <p class="cfa-price">
                        <?php echo $price ?>
                    </p>
                </div>
                <div class="certificate-text">
                    The certificate for this course can be downloaded for a small fee when the course is completed
                </div>
            <?
            }
            ?>
            <div class="share">
                <div class="social-icons">
                    <?
                    if (!empty($social_media_links)) {
                        foreach ($social_media_links as $item) {
                            $link = esc_url($item['link']['url']);
                            $type = esc_html($item['type']);
                            $icon = get_stylesheet_directory_uri() . ('/assets/images/icons/Type=' . $type . ', Size=24, Color=Black.svg');
                            $image = "<img class='icon-image' src='" . $icon . "' alt='" . $type . "' />";
                            echo '<a style="color: #000" href="' . $link . '" class="icon">' . $image . '</a>';
                        }
                    }
                    ?>
                </div>
            </div>
            <?
            if ($is_enrolled) {
            ?>
                <div class='progress'>
                    <?php echo do_shortcode('[learndash_course_progress]'); ?>
                </div>
                <div class="continue">
                    <?php echo do_shortcode('[ld_course_resume label="Continue the Course"]'); ?>
                </div>
            <?
            } else {
            ?>
                <div class="enroll">
                    <button class="button secondary large enroll-button">Enroll Now</button>
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
                    <?php echo the_content(); ?>
                </div>
            </div>
            <div class="carriculum">
                <div class="title">
                    <p class="cfa-introduction-title">Course Carriculum</p>
                </div>
                <?php echo do_shortcode('[course_content]'); ?>
            </div>
            <div class="instructor">
                <div class="title">
                    <p class="cfa-introduction-title">The Instructor</p>
                </div>
                <div class="name">
                    <p>
                        <?php
                        $author = get_post_field('post_author', $course_id);
                        $name = get_the_author_meta('display_name', $author);
                        $avatar = get_avatar_url($author);
                        echo $name;
                        ?>
                    </p>
                </div>
                <div class="avatar">
                    <?php
                    echo '<img src="' . $avatar . '" alt="">';
                    ?>
                </div>
                <div class="description">
                    <?php echo get_the_author_meta('description', $author); ?>
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
                <div class="enroll">
                    <button class="button secondary large enroll-button">Enroll Now</button>
                </div>
            <?
            }
            ?>
            <div class="related">
                <div class="accordion-parent">
                    <div class="accordion">
                        Related
                    </div>
                    <div class="panel">
                        <?
                        if ($related_courses) {
                        ?>
                            <div class="related-courses">
                                <div class="title"> Related Courses </div>
                                <div class="list">
                                    <?php
                                    $related_courses = array_slice($related_courses, 0, 3);
                                    foreach ($related_courses as $course) {
                                        $course_thumbnail = get_the_post_thumbnail_url($course);
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
get_footer();
?>