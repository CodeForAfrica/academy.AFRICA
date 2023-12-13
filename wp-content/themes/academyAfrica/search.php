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
    return array();
}

$orgs = get_query_param('organization');
$instructors = get_query_param('instructor');
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
// merge $orgs and $instructors into one array
$allFilters  = array_merge($orgs, $instructors);
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
    <div class="search-page-main" id="all-courses">
        <? if (!empty($s)) {
        ?>
            <div class="search-page-header">

                <p class="search-page-subtitle">
                    <? echo $the_query->found_posts ?> results for:
                </p>
                <h1 class="search-page-title">
                    "<? echo $s ?>"
                </h1>

            </div>
        <?
        }
        ?>
        <div class="filters">
            <? if (empty($allFilters)) { ?>
                <div class="filter-section">
                    <div class="sort">
                        <div class="label">
                            Sort By:
                        </div>
                        <select name="sort" id="courses-sort" class="select" onchange="sortCourses(this)">
                            <?
                            foreach ($sort_options as $key => $option) {
                                $selected = $sort == $key ? "selected" : "";
                            ?>
                                <option <? echo $selected ?> value="<? echo $key ?>"><? echo $option["name"] ?></option>
                            <?
                            }
                            ?>
                        </select>
                    </div>
                    <div class="filter">
                        <button id="courses-mobile-filter" class="button primary medium filter-btn">
                            <svg width="17" height="16" viewBox="0 0 17 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <g clip-path="url(#clip0_11905_79908)">
                                    <path d="M15.1693 2H1.83594L7.16927 8.30667V12.6667L9.83594 14V8.30667L15.1693 2Z" stroke="#EFF0FD" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </g>
                                <defs>
                                    <clipPath id="clip0_11905_79908">
                                        <rect width="16" height="16" fill="white" transform="translate(0.5)" />
                                    </clipPath>
                                </defs>
                            </svg>
                            Filter
                        </button>
                    </div>
                </div>
            <? } ?>
            <? if (!empty($allFilters)) { ?>
                <div class="selected-filters">
                    <div class="filters-list">
                        <?
                        foreach ($orgs as $org) {
                        ?>
                            <div class="filter">
                                <div class="filter-name">
                                    <? echo $org ?>
                                </div>
                                <button class="filter-remove" onclick="removeFilter('organization', '<? echo $org ?>')">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="17" viewBox="0 0 16 17" fill="none">
                                        <path d="M8.0026 15.1693C11.6845 15.1693 14.6693 12.1845 14.6693 8.5026C14.6693 4.82071 11.6845 1.83594 8.0026 1.83594C4.32071 1.83594 1.33594 4.82071 1.33594 8.5026C1.33594 12.1845 4.32071 15.1693 8.0026 15.1693Z" stroke="#0C1A81" stroke-linecap="round" stroke-linejoin="round" />
                                        <path d="M10 6.5L6 10.5" stroke="#0C1A81" stroke-linecap="round" stroke-linejoin="round" />
                                        <path d="M6 6.5L10 10.5" stroke="#0C1A81" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                </button>
                            </div>
                        <?
                        }
                        ?>
                    </div>
                    <div class="clear-section">
                        <button class="clear" onclick="clearFilters()">
                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <g clip-path="url(#clip0_11905_80119)">
                                    <path d="M8.0026 14.6654C11.6845 14.6654 14.6693 11.6806 14.6693 7.9987C14.6693 4.3168 11.6845 1.33203 8.0026 1.33203C4.32071 1.33203 1.33594 4.3168 1.33594 7.9987C1.33594 11.6806 4.32071 14.6654 8.0026 14.6654Z" stroke="#B6131E" stroke-linecap="round" stroke-linejoin="round" />
                                    <path d="M10 6L6 10" stroke="#B6131E" stroke-linecap="round" stroke-linejoin="round" />
                                    <path d="M6 6L10 10" stroke="#B6131E" stroke-linecap="round" stroke-linejoin="round" />
                                </g>
                                <defs>
                                    <clipPath id="clip0_11905_80119">
                                        <rect width="16" height="16" fill="white" />
                                    </clipPath>
                                </defs>
                            </svg>
                            Clear all filters
                        </button>
                    </div>
                </div>
            <? } ?>
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