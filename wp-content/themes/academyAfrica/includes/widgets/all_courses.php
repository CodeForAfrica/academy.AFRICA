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

        $leaning_attr = [
            'per_page' => '-1',
            'paged' => '1',
            'orderby' => 'title',
            'order' => 'ASC'
        ];
        $pathways = CoursesFunctions::getLearningPaths($leaning_attr);
        $learning_paths = $pathways["learning_paths"];
        $learning_paths = array_reduce($learning_paths, function ($carry, $pathway) {
            $carry[$pathway["id"]] = esc_html__($pathway["title"], 'textdomain');
            return $carry;
        }, []);

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
        $this->start_controls_section(
            'learning_settings',
            [
                'label' => __('Career Development Settings', 'academy-africa'),
            ]
        );
        $this->add_control(
            'learning_title',
            [
                'label' => __('Career Development  Title', 'academy-africa'),
                'type' => \Elementor\Controls_Manager::TEXTAREA,
                'default' => __('Career Development', 'academy-africa'),
                'label_block' => true,
            ]
        );

        $this->add_control(
            'learning_description',
            [
                'label' => __('Career Development Description', 'academy-africa'),
                'type' => \Elementor\Controls_Manager::TEXTAREA,
                'default' => __('Learn how you can boost your career in your chosen field by taking a collection of academy.africa courses and being part of our communities. Click on the button to see all the different career paths that ca be boosted with academy.africa courses.', 'academy-africa'),
                'label_block' => true,
            ]
        );

        $this->add_control(
            'learning_button_text',
            [
                'label' => __('Career Development Button Text', 'academy-africa'),
                'type' => \Elementor\Controls_Manager::TEXTAREA,
                'default' => __('Explore Pathways', 'academy-africa'),
                'label_block' => true,
            ]
        );

        $this->add_control(
            'learning_button_link',
            [
                'label' => __('Career Development Button Link', 'academy-africa'),
                'type' => \Elementor\Controls_Manager::TEXTAREA,
                'default' => __('https://academy.africa/pathways', 'academy-africa'),
                'label_block' => true,
            ]
        );

        $this->add_control(
            'learning_sample_course',
            [
                'label' => __('Sample Learning Pathways', 'academy-africa'),
                'type' => \Elementor\Controls_Manager::SELECT2,
                'multiple' => false,
                'options' => $learning_paths,
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
        $view = $this->get_query_param('view');
        $pathway_title = $settings['learning_title'];
        $pathway_description = $settings['learning_description'];
        $pathway_button_text = $settings['learning_button_text'];
        $pathway_button_link = $settings['learning_button_link'];
        $pathway_sample_course = $settings['learning_sample_course'];
        $pathway = get_post($pathway_sample_course);
        $pathway_courses = get_field('courses', $pathway);

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
            'per_page' => $view[0] == 'all' ? -1 : 9,
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
        <main class="all-courses">
            <?php get_template_part('template-parts/filter_bar', 'template', [
                'filter_by' => $filter_by,
                'filter_options' => $filter_options,
                'sort_by' => $sort_by,
                'sort_options' => $sort_options,
                'sort' => $sort
            ]); ?>
            <div class="courses-main" id="all-courses">
                <section class="course-grid">
                    <h4 class="cfa-title">
                        <? echo $courses_title ?>
                    </h4>
                    <div class="filter-section">
                        <div class="sort">
                            <div class="label">
                                <? echo $sort_by ?>
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
                        }
                        ?>
                    </div>
                    <?
                    if ($has_pagination) {
                    ?>
                        <hr class="divider">
                        <div class="pagination-container">
                            <button class="see-all" onclick="viewAll()">
                                View All
                            </button>
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
                <div class="career-dev">
                    <div class="title">
                        <? echo $pathway_title ?>
                    </div>
                    <div class="body">
                        <div class="content">
                            <div class="text">
                                <? echo $pathway_description ?>
                            </div>
                            <div class="cta">
                                <a href="<? echo $pathway_button_link ?>" class="button primary medium">
                                    <? echo $pathway_button_text ?>
                                    <i class="fas fa-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                        <div class="sample-course">
                            <a href="/" class="pathway-link">
                                <div class="card">
                                    <div class="course-card-pattern">
                                        <div class="icon">
                                            <img src="<?php echo get_the_post_thumbnail_url($pathway); ?>" alt="<? echo $pathway->post_title ?>" />
                                        </div>
                                    </div>
                                    <div class="pathway-card-content">
                                        <p class="pathway-name">
                                            <? echo $pathway->post_title ?>
                                        </p>
                                        <p class="course-count">
                                            <? echo count($pathway_courses) . ' Courses' ?>
                                        </p>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
        </main>
<?
    }
}
