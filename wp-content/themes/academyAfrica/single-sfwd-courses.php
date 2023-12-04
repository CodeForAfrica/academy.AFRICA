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

$organizations = [
    [
        'name' => 'Strathmore University',
        'description' => 'Strathmore University is a chartered university based in Nairobi, Kenya. Strathmore College was started in 1961, as the first multi-racial, multi-religious Advanced-level Sixth Form College offering science and arts subjects, by a group of professionals who formed a charitable educational trust.',
        'avatar' => 'Strathmore_Uni 1.png',
    ],
]
?>

<?
get_header();
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
        <div class="price">
            <p class="cfa-price">
                <?php echo $price  ?>
            </p>
        </div>
        <div class="certificate-text">
            The certificate for this course can be downloaded for a small fee when the course is completed
        </div>
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
        <div class="enroll left">
            <button class="button secondary large enroll-button">Enroll Now</button>
        </div>
        <hr class="divider">
        <div class="inroduction">
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
                    $avatar = get_avatar($email);
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
                <?php echo get_the_author_meta('description'); ?>
            </div>
        </div>
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
                        <p>" . $organization['name'] . "</p>
                    </div>
                    <div class='avatar'>
                        <img src='" . get_stylesheet_directory_uri() . "/assets/images/" . $organization['avatar'] . "' alt=''>
                    </div>
                    <div class='description'>
                        <div>" . $organization['description'] . "</div>
                    </div>
                </div>
                ";
                }
                ?>
            </div>
        </div>
        <div class="enroll">
            <button class="button secondary large enroll-button">Enroll Now</button>
        </div>
        <!-- TODO: ADD Related -->
    </div>
</div>

<?
get_footer();
?>