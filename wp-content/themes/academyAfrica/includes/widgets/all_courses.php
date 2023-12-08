<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

require_once __DIR__ . '/../utils/courses.php';


use AcademyAfrica\Theme\Courses\CoursesFunctions;

class Academy_Africa_All_Courses  extends \Elementor\Widget_Base
{

    public function get_name()
    {
        return 'All Courses';
    }

    public function get_style_depends()
    {
        return ['academy-africa-all-courses', 'academy-africa-pathways', 'academy-africa'];
    }

    public function get_script_depends()
    {
        return ['academy-africa-filters', "academy-africa_learndash_course_grid", "academy-africa_all_courses"];
    }

    public function get_title()
    {
        return esc_html__('All Courses');
    }

    public function get_icon()
    {
        return 'eicon-code';
    }

    public function get_categories()
    {
        return ['academy-africa'];
    }

    public function get_query_param($param)
    {
        if (isset($_GET[$param])) {
            if ($_GET[$param]) {
                return explode(",", $_GET[$param]);
            }
        }
    }


    protected function register_controls()
    {
        $this->start_controls_section(
            'courses_settings',
            [
                'label' => __('Courses Settings', 'academy-africa'),
            ]
        );

        $this->add_control(
            'courses_title',
            [
                'label' => __('Courses  Title', 'academy-africa'),
                'type' => \Elementor\Controls_Manager::TEXTAREA,
                'default' => __('All Courses', 'academy-africa'),
                'label_block' => true,
            ]
        );
        $this->add_control(
            'filter_by_text',
            [
                'label' => __('Filter By Text', 'academy-africa'),
                'type' => \Elementor\Controls_Manager::TEXTAREA,
                'default' => __('Filter by', 'academy-africa'),
                'label_block' => true,
            ]
        );
        $this->add_control(
            'sort_by_text',
            [
                'label' => __('Sort BY Text', 'academy-africa'),
                'type' => \Elementor\Controls_Manager::TEXTAREA,
                'default' => __('Sort by', 'academy-africa'),
                'label_block' => true,
            ]
        );
        $this->end_controls_section();
    }



    protected function render()
    {
        $settings = $this->get_settings_for_display();
        $filter_by = $settings['filter_by_text'];
        $filter_options = CoursesFunctions::get_filter_by();
        $courses_title = $settings['courses_title'];
        $current_page = get_query_var('paged') ? get_query_var('paged') : 1;
        $orgs = $this->get_query_param('organization');
        $instructors = $this->get_query_param('instructor');

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

        $sort_by = $settings['sort_by_text'];
        $sort = $this->get_query_param('sort');
        if ($sort) {
            $sort = $sort[0];
            $order_by = $sort_options[$sort]["orderby"];
            $order = $sort_options[$sort]["order"];
        } else {
            $order_by = "date";
            $order = "DESC";
        }
        $atts = [
            'per_page' => '9',
            'paged' => $current_page,
            'organization' => $orgs,
            'instructor' => $instructors,
            'orderby' => $order_by,
            'order' => $order
        ];
        $default_atts = CoursesFunctions::get_default_atts();
        $atts = shortcode_atts($default_atts, $atts, 'academy-africa_course_grid');

        $query = CoursesFunctions::build_query($atts);
        $query = new WP_Query($query);
        $no_of_pages = $query->max_num_pages;

        if ($no_of_pages > 1 && $current_page <= $no_of_pages) {
            $has_pagination = true;
        } else {
            $has_pagination = false;
        }

        $posts = $query->get_posts();
        $courses = $posts;


?>
        <main class="all-courses" id="all-courses">
            <aside class="filter-sidebar">
                <div class="sidebar" id="sidebar">
                    <div class="sort">
                        <p class="sort-by">
                            <? echo $sort_by ?>
                        </p>
                        <select name="sort" id="courses-sort" class="select" onchange="sortCourses()">
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
                    <div class="filter" id="side-filter-bar">
                        <p class="filter-by">
                            <? echo $filter_by ?>
                        </p>
                        <div class="filter-body">
                            <?
                            if (!empty($filter_options)) {
                                foreach ($filter_options as $item) {
                                    $title = $item["title"];
                                    $options = $item["options"];
                            ?>
                                    <div class="filter-item">
                                        <p class="filter-by-title">
                                            <? echo $title ?>
                                        </p>
                                        <?
                                        if (!empty($options)) {
                                        ?>
                                            <ul class="filter-list">
                                                <?
                                                foreach ($options as $options_index => $option) {
                                                ?>
                                                    <? if ($options_index >= 3) {
                                                    ?>
                                                        <li class="hidden">
                                                            <label class="mui-checkbox">
                                                                <input type="checkbox" onclick="filterCourses(this, '<? echo $item["name"] ?>', '<? echo $option->name ?>')" value="<? echo $option->id ?>" name="<? echo $item["name"] . '-' . $option->name ?>">
                                                                <span class="checkmark"></span>
                                                                <? echo $option->name ?>
                                                            </label>
                                                        </li>
                                                    <?
                                                    } else {
                                                    ?>
                                                        <li>
                                                            <label class="mui-checkbox">
                                                                <input type="checkbox" onclick="filterCourses(this, '<? echo $item["name"] ?>', '<? echo $option->name ?>')" value="<? echo $option->id ?>" name="<? echo $item["name"] . '-' . $option->name ?>">
                                                                <span class="checkmark"></span>
                                                                <? echo $option->name ?>
                                                            </label>
                                                        </li>
                                                    <?
                                                    }
                                                    ?>
                                                <?
                                                }
                                                ?>
                                            </ul>
                                            <? if (count($options) > 3) {
                                            ?>
                                                <div class="show-more">
                                                    <button class="show-more-btn">
                                                        Show More
                                                    </button>
                                                </div>
                                            <?
                                            } ?>
                                        <?
                                        }
                                        ?>
                                    </div>
                            <?
                                }
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </aside>
            <div class="courses-main">
                <section class="course-grid">
                    <h4 class="cfa-title">
                        <? echo $courses_title ?>
                    </h4>
                    <div class="filter-section">
                        <div class="sort">
                            <div class="label">
                                <? echo $sort_by ?>
                            </div>
                            <select name="sort" id="sort" class="select">
                                <option value="newest">Newest</option>
                                <option value="oldest">Oldest</option>
                            </select>
                        </div>
                        <div class="filter">
                            <button id="courses-mobile-filter" class="button primary large filter-btn">
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
                    <div class="course-list">
                        <?
                        if (!empty($courses)) {
                            foreach ($courses as $course) {
                                $course_title = get_the_title($course);
                                $course_author = get_the_author_meta('display_name', $course->post_author);
                                $course_thumbnail = get_the_post_thumbnail_url($course);
                                $course_link = get_permalink($course);
                                $course_meta = get_post_meta($course, 'sfwd-courses', true);
                                $course_price = $course_meta['sfwd-courses_course_price'];
                                $course_price = $course_price == 0 ? "Free" : $course_price;

                                $course_attrs = CoursesFunctions::get_post_attr($course, $atts);
                                extract($course_attrs);
                        ?>
                                <a href="<? echo $course_link ?>" class="course-card">
                                    <div class="card">
                                        <div class="course-card-pattern">
                                            <img src="<? echo $course_thumbnail ?>" alt="course-thumbnail">
                                        </div>
                                        <div class="course-card-content">
                                            <p class="course-title">
                                                <? echo $course_title ?>
                                            </p>
                                            <div class="course-meta">
                                                <p class="course-author">
                                                    By <? echo $course_author ?>
                                                </p>
                                                <div class="course-details">
                                                    <div class="course-students">
                                                        <div class="icon">
                                                            <img src="<? echo get_stylesheet_directory_uri() ?>/assets/images/user.svg" alt="students">
                                                        </div>
                                                        <p class="value"><? echo $students ?></p>

                                                    </div>
                                                    <p class="course-price">
                                                        <? echo $course_price ?>
                                                    </p>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </a>
                        <?
                            }
                        }
                        ?>
                    </div>
                    <?
                    if ($has_pagination) {
                    ?>
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
                    <?
                    }
                    ?>
                </section>
            </div>
        </main>
        <!-- Mobile filter modal -->
        <div class="filter-modal d-none" id="filter-modal">
            <div class="filter-options">
                <div id="mobile-filters" class="mobile-filter">
                    <h4 class="filter-title">
                        Filter By:
                    </h4>
                    <div class="filters">
                        <div class="accordion-parent">
                            <?
                            if (!empty($filter_options)) {
                                foreach ($filter_options as $item) {
                                    $title = $item["title"];
                                    $options = $item["options"];
                            ?>
                                    <button class="accordion"><? echo $title ?></button>
                                    <?
                                    if (!empty($options)) {
                                    ?>
                                        <div class="panel">
                                            <ul>
                                                <?
                                                foreach ($options as $option) {
                                                ?>
                                                    <li>
                                                        <label class="mui-checkbox">
                                                            <input type="checkbox" value="<? echo $option->id ?>" name="<? echo $item["name"] . '-' . $option->name ?>">
                                                            <span class="checkmark"></span>
                                                            <? echo $option->name ?>
                                                        </label>
                                                    </li>
                                                <?
                                                }
                                                ?>
                                            </ul>
                                        </div>
                            <?
                                    }
                                }
                            }
                            ?>
                        </div>
                        <hr class="divider">
                        <div class="actions">
                            <button href="" class="button primary medium" onclick="applyFilters()">Apply</button>
                            <!-- clear filter button with X icon -->
                            <button class="clear-filters" onclick="clearFilters()" id="clear-filters-btn">
                                <div class="icon">
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
                                </div>
                                Clear all filters
                        </div>
                    </div>
                    <div class="close">
                        <button onclick="closeFilters()" class="buttons" id="close-filter-modal">
                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <g clip-path="url(#clip0_11905_80128)">
                                    <path d="M8.0026 14.6654C11.6845 14.6654 14.6693 11.6806 14.6693 7.9987C14.6693 4.3168 11.6845 1.33203 8.0026 1.33203C4.32071 1.33203 1.33594 4.3168 1.33594 7.9987C1.33594 11.6806 4.32071 14.6654 8.0026 14.6654Z" stroke="black" stroke-linecap="round" stroke-linejoin="round" />
                                    <path d="M10 6L6 10" stroke="black" stroke-linecap="round" stroke-linejoin="round" />
                                    <path d="M6 6L10 10" stroke="black" stroke-linecap="round" stroke-linejoin="round" />
                                </g>
                                <defs>
                                    <clipPath id="clip0_11905_80128">
                                        <rect width="16" height="16" fill="white" />
                                    </clipPath>
                                </defs>
                            </svg>

                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
<?
    }
}
