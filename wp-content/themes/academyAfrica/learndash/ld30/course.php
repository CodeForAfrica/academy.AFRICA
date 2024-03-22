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
    <div>
        <h1>This is the course page</h1>
    </div>
<?
}
?>