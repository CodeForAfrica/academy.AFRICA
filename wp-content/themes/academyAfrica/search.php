<?php

/**
 * Template Name: Search Page
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

require_once __DIR__ . '/includes/utils/courses.php';

use AcademyAfrica\Theme\Courses\CoursesFunctions;

function get_query_param($param)
{
    if (isset($_GET[$param])) {
        if ($_GET[$param]) {
            return explode(",", $_GET[$param]);
        }
    }
}

$orgs = get_query_param('organization');
$instructors = get_query_param('instructor');
$sort = get_query_param('sort');
if ($sort) {
    $sort = $sort[0];
    $order_by = $sort_options[$sort]["orderby"];
    $order = $sort_options[$sort]["order"];
} else {
    $order_by = "date";
    $order = "DESC";
}
$s = get_search_query();
$current_page = get_query_var('paged') ? get_query_var('paged') : 1;


$filter_options = CoursesFunctions::get_filter_by();

$sort_options = [
    "date-desc" => [
        "orderby" => "date",
        "order" => "DESC",
        "name" => "Newest"
    ],
    "date-asc" => [
        "orderby" => "date",
        "order" => "ASC",
        "name" => "Oldest"
    ],
    "name-asc" => [
        "orderby" => "title",
        "order" => "ASC",
        "name" => "Name (A-Z)"
    ],
    "name-desc" => [
        "orderby" => "title",
        "order" => "DESC",
        "name" => "Name (Z-A)"
    ]
];

$atts = [
    'per_page' => '9',
    'paged' => $current_page,
    'organization' => $orgs,
    'instructor' => $instructors,
    'search' => $s,
    'orderby' => $order_by,
    'order' => $order
];

$default_atts = CoursesFunctions::get_default_atts();
$atts = shortcode_atts($default_atts, $atts, 'academy-africa_course_grid');
$query = CoursesFunctions::build_query($atts);

$the_query = new WP_Query($query);
$no_of_pages = $the_query->max_num_pages;
if ($no_of_pages > 1 && $current_page <= $no_of_pages) {
    $has_pagination = true;
} else {
    $has_pagination = false;
}

?>

<?php get_header(); ?>
<div class="search-page">
    <?php get_template_part('template-parts/filter_bar', 'template', [
        'filter_by' => $filter_by,
        'filter_options' => $filter_options,
        'sort_by' => $sort_by,
        'sort_options' => $sort_options,
        'sort' => $sort
    ]); ?>
    <div class="search-page-main">
        <div class="search-page-header">
            <? if (!empty($s)) {
            ?>
                <p class="search-page-subtitle">
                    <? echo $the_query->found_posts ?> results for:
                </p>
                <h1 class="search-page-title">
                    "<? echo $s ?>"
                </h1>
            <?
            }
            ?>

        </div>
        <div class="search-page-results">
            <?php
            if ($the_query->have_posts()) {
            ?>
                <div class="list">
                    <?

                    foreach ($the_query->posts as $post) {
                        $course_title = get_the_title($course);
                        $course_author = get_the_author_meta('display_name', $post->post_author);
                        $course_thumbnail = get_the_post_thumbnail_url($post);
                        $course_link = get_permalink($post);
                        $course_meta = get_post_meta($post, 'sfwd-courses', true);
                        $course_price = $course_meta['sfwd-courses_course_price'];
                        $course_price = $course_price == 0 ? "Free" : $course_price;

                        $course_attrs = CoursesFunctions::get_post_attr($post, $atts);
                        extract($course_attrs);
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
                                'students' => $students
                            ]
                        ); ?>
                    <?
                    }
                    ?>
                </div>
            <?
            } else {
                echo '<div class="no-results">';
                echo '<button class="clear-filters" onclick="clearFilters()">Clear Filters</button>';
                echo '<div class="no-results-text">';
                echo '<p class="no-results-title">Sorry, there are no search results for your query. Please try these suggestions:</p>';
                echo '<ul>';
                echo '<li>Check your spelling.</li>';
                echo '<li>Enter other keywords.</li>';
                echo '<li>Clear any filters to widen the results.</li>';
                echo '</ul>';
                echo '</div>';
                echo '</div>';
            }
            ?>
        </div>

        <div class="footer">
            <hr class="divider">
            <div class="pagination-container">
                <a href="/" class="see-all">
                    View All
                </a>
                <ul class="pagination">
                    <?
                    if ($current_page > 1) {
                    ?>
                        <li class="page-item"><a class="page-link" href="<? echo get_pagenum_link($current_page - 1) ?>">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none">
                                    <path d="M10 12L6 8L10 4" stroke="#616582" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </a></li>
                        <?
                    }
                    for ($i = 1; $i <= $no_of_pages; $i++) {
                        if ($i == $current_page) {
                        ?>
                            <li class="page-item active">
                                <a class="page-link" href="<? echo get_pagenum_link($i) ?>"><? echo $i ?></a>
                            </li>
                        <?
                        } else {
                        ?>
                            <li class="page-item"><a class="page-link" href="<? echo get_pagenum_link($i) ?>"><? echo $i ?></a></li>
                        <?
                        }
                    }
                    if ($current_page < $no_of_pages) {
                        ?>
                        <li class="page-item"><a class="page-link" href="<? echo get_pagenum_link($current_page + 1) ?>">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none">
                                    <path d="M6 12L10 8L6 4" stroke="#616582" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </a></li>
                    <?
                    }
                    ?>
                </ul>
            </div>
        </div>
    </div>
</div>



<?php get_footer(); ?>