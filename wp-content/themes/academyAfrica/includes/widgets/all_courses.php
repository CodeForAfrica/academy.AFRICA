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
        return ['academy-africa-filters', "academy-africa_learndash_course_grid"];
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

    public function get_filter_by()
    {
        $allOrganizations = CoursesFunctions::getOrganizations();
        $allInstructors = CoursesFunctions::getAllInstructors();
        $filter_by = [
            [
                'title' => 'Organizations',
                'options' => []
            ],
            [
                'title' => 'Instructors',
                'options' => []
            ]
        ];

        foreach ($allOrganizations as $organization) {
            $formatedOrganization = (object)[
                'id' => $organization['id'],
                'name' => $organization['title'],
            ];
            array_push($filter_by[0]['options'], $formatedOrganization);
        }

        foreach ($allInstructors as $instructor) {
            $formatedInstructor = (object)[
                'id' => $instructor['id'],
                'name' => $instructor['name'],
            ];
            array_push($filter_by[1]['options'], $formatedInstructor);
        }

        return $filter_by;
    }


    protected function register_controls()
    {
        $this->start_controls_section(
            'learning_pathways',
            [
                'label' => __('Learning Pathways', 'academy-africa'),
            ]
        );

        $this->add_control(
            'pathway_title',
            [
                'label' => __('Learning Pathways Title', 'academy-africa'),
                'type' => \Elementor\Controls_Manager::TEXTAREA,
                'default' => __('Learning Pathways', 'academy-africa'),
                'label_block' => true,
            ]
        );
        $this->add_control(
            'pathway_description',
            [
                'label' => __('Learning Pathways Description', 'academy-africa'),
                'type' => \Elementor\Controls_Manager::TEXTAREA,
                'default' => __('Find out how you can enhance your skills and achieve mastery in specific disciplines within data science and technology.', 'academy-africa'),
                'label_block' => true,
            ]
        );

        $pathways_list = new \Elementor\Repeater();
        $pathways_list->add_control(
            'title',
            [
                'label' => __('Title', 'academy-africa'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => __('Pathway Title', 'academy-africa'),
                'label_block' => true,
            ]
        );
        $pathways_list->add_control(
            'icon',
            [
                'label' => __('Icon', 'academy-africa'),
                'type' => \Elementor\Controls_Manager::MEDIA,
                'default' => [
                    'url' => get_stylesheet_uri() . '/assets/images/sample-icon.svg',
                ],
                'label_block' => true,
            ]
        );
        $pathways_list->add_control(
            'courses',
            [
                'label' => __('Courses', 'academy-africa'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => __('4 Courses', 'academy-africa'),
                'label_block' => true,
            ]
        );
        $this->add_control(
            'pathways',
            [
                'label' => __('Learning Pathways', 'academy-africa'),
                'type' => \Elementor\Controls_Manager::REPEATER,
                'fields' => $pathways_list->get_controls(),
                'title_field' => '{{{ title }}}',
            ]
        );
        $this->end_controls_section();

        // courses section controls
        $this->start_controls_section(
            'courses_grid',
            [
                'label' => __('Courses', 'academy-africa'),
            ]
        );
        $this->add_control(
            'per_page',
            array(
                'label'       => esc_html__('Courses Per Page', 'academy-africa'),
                'type'        => \Elementor\Controls_Manager::NUMBER,
                'default'     => 9,
                'description' => esc_html__('Leave empty for default or 0 to show all items.', 'academy-africa'),
            )
        );

        $this->end_controls_section();
    }



    protected function render()
    {
        $settings = $this->get_settings_for_display();
        $pathway_title = $settings['pathway_title'];
        $pathway_description = $settings['pathway_description'];
        $pathways = $settings['pathways'];
        $filter_by = "Filter by:";
        $filter_options = $this->get_filter_by();
        $courses_title = "All Courses";
        $current_page = get_query_var('paged') ? get_query_var('paged') : 1;

        $atts = [
            'per_page' => '9',
            'paged' => $current_page,
            // "taxonomies" => "ld_course_category:featured"
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
        <main class="all-courses">
            <aside class="filter-sidebar">
                <div class="sidebar" id="sidebar">
                    <p class="filter-by">
                        <? echo $filter_by ?>
                    </p>
                    <?
                    if (!empty($filter_options)) {
                        foreach ($filter_options as $item) {
                            $title = $item["title"];
                            $options = $item["options"];
                    ?>
                            <p style="margin-top: 40px" class="filter-by-title">
                                <? echo $title ?>
                            </p>
                            <?
                            if (!empty($options)) {
                                foreach ($options as $option) {
                            ?>
                                    <ul>
                                        <li>
                                            <label class="mui-checkbox">
                                                <input type="checkbox">
                                                <span class="checkmark"></span>
                                                <? echo $option->name ?>
                                            </label>
                                        </li>
                                    </ul>
                    <?
                                }
                            }
                        }
                    }
                    ?>

                </div>
                <div id="filters" class="mobile-filter">
                    <h4 class="filter-title">
                        Filter By:
                    </h4>
                    <div class="filters">
                        <div class="accordion-parent">
                            <?
                            if (!empty($filter_options)) {
                                foreach ($filter_options as $item) {
                            ?>
                                    <button class="accordion"><? echo $title ?></button>
                                    <?
                                    $title = $item["title"];
                                    $options = $item["options"];
                                    if (!empty($options)) {
                                    ?>
                                        <div class="panel">
                                            <ul>
                                                <?
                                                foreach ($options as $option) {
                                                ?>
                                                    <li>
                                                        <label class="mui-checkbox">
                                                            <input type="checkbox">
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
                            <button href="" class="button primary">Apply</button>
                            <button class="clear-filters">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none">
                                    <g clip-path="url(#clip0_11179_27069)">
                                        <path d="M8.0026 14.6693C11.6845 14.6693 14.6693 11.6845 14.6693 8.0026C14.6693 4.32071 11.6845 1.33594 8.0026 1.33594C4.32071 1.33594 1.33594 4.32071 1.33594 8.0026C1.33594 11.6845 4.32071 14.6693 8.0026 14.6693Z" stroke="#B6131E" stroke-linecap="round" stroke-linejoin="round" />
                                        <path d="M10 6L6 10" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" />
                                        <path d="M6 6L10 10" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" />
                                    </g>
                                    <defs>
                                        <clipPath id="clip0_11179_27069">
                                            <rect width="16" height="16" fill="white" />
                                        </clipPath>
                                    </defs>
                                </svg>
                                Clear all filters
                            </button>
                        </div>
                    </div>
                    <div class="close">
                        <button onclick="closeFilters()" class="buttons">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none">
                                <g clip-path="url(#clip0_11179_27069)">
                                    <path d="M8.0026 14.6693C11.6845 14.6693 14.6693 11.6845 14.6693 8.0026C14.6693 4.32071 11.6845 1.33594 8.0026 1.33594C4.32071 1.33594 1.33594 4.32071 1.33594 8.0026C1.33594 11.6845 4.32071 14.6693 8.0026 14.6693Z" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" />
                                    <path d="M10 6L6 10" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" />
                                    <path d="M6 6L10 10" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" />
                                </g>
                                <defs>
                                    <clipPath id="clip0_11179_27069">
                                        <rect width="16" height="16" fill="white" />
                                    </clipPath>
                                </defs>
                            </svg>
                            Close
                        </button>
                    </div>
            </aside>
            <div class="courses-main">
                <section class="learning-pathways">
                    <div class="title">
                        <h4 class="cfa-title">
                            <? echo $pathway_title ?>
                        </h4>
                    </div>
                    <p class="description">
                        <? echo $pathway_description ?>
                    </p>
                    <div class="content">
                        <?
                        if (!empty($pathways)) {
                            foreach ($pathways as $pathway) {
                                $name = $pathway["title"];
                                $icon = $pathway["icon"]["url"];
                        ?>
                                <div class="card">
                                    <div class="course-card-pattern">
                                        <div class="icon">
                                            <img src="<? echo $icon ?>" alt="sample-icon">
                                        </div>
                                    </div>
                                    <div class="pathway-card-content">
                                        <p class="pathway-name">
                                            <? echo $name ?>
                                        </p>
                                        <p class="course-count">
                                            <? echo $pathway["courses"] ?>
                                        </p>
                                    </div>
                                </div>
                        <?
                            }
                        }
                        ?>
                    </div>
                    <hr class="divider">
                    <div class="pagination-container">
                        <a href="/" class="see-all">
                            View All
                        </a>
                        <ul class="pagination">
                            <li class="page-item"><a class="page-link" href="#">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none">
                                        <path d="M10 12L6 8L10 4" stroke="#616582" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                </a></li>
                            <li class="page-item"><a class="page-link" href="#">1</a></li>
                            <li class="page-item"><a class="page-link" href="#">2</a></li>
                            <li class="page-item"><a class="page-link" href="#">3</a></li>
                            <li class="page-item"><a class="page-link" href="#">...</a></li>
                            <li class="page-item"><a class="page-link" href="#">5</a></li>
                            <li class="page-item"><a class="page-link" href="#">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none">
                                        <path d="M6 12L10 8L6 4" stroke="#616582" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                </a></li>
                        </ul>
                    </div>
                </section>
                <section class="course-grid">
                    <h4 class="cfa-title">
                        <? echo $courses_title ?>
                    </h4>
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
                                                        <div class="icon"></div>
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
                                <!-- list pagination. Only first 3 should be shown and last -->
                                <!-- like 1 2 3 ... 5 -->

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
<?
    }
}
