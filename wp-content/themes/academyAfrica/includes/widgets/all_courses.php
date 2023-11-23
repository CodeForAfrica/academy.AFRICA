<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

class Academy_Africa_All_Courses  extends \Elementor\Widget_Base
{

    public function get_name()
    {
        return 'All Courses';
    }

    public function get_style_depends()
    {
        return ['academy-africa-all-courses', 'academy-africa', 'academy-africa-pathways'];
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
        return [
            [
                "title" => "Organization",
                "options" => [
                    "Swathmore University",
                    "DW Akademie",
                    "Institut Supérieur Des Sciences De L'information Et De La Communication (ISSIC)",
                    "Code for Africa",

                ]
            ],
            [
                "title" => "Instructors",
                "options" => [
                    "Swathmore University",
                    "DW Akademie",
                    "Institut Supérieur Des Sciences De L'information Et De La Communication (ISSIC)",
                    "Code for Africa",

                ]
            ], [
                "title" => "Price",
                "options" => [
                    "Free",
                ]
            ]
        ];
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
    public function get_default_atts()
    {
        return apply_filters('academy-africa_course_grid_default_shortcode_attributes', [
            // Query
            'post_type' => defined('LEARNDASH_VERSION') ? 'sfwd-courses' : 'post',
            'per_page'  => 9,
            'orderby'   => 'ID',
            'order'     => 'DESC',
            'taxonomies' => '',
            'enrollment_status' => '',
            'progress_status' => '',
            // Elements
            'thumbnail' => true,
            'thumbnail_size' => 'thumbnail',
            'ribbon' => true,
            'video' => false,
            /**
             * Content includes title, description and button
             */
            'content' => true,
            'title' => true,
            'title_clickable' => true,
            'description' => true,
            'description_char_max' => 120,
            'button' => true,
            'filter' => true,
            /**
             * Accepts:
             * 
             * 'button'   : Load more button
             * 'infinite' : Infinite scrolling 
             * 'pages'    : Normal AJAX pagination with number 1, 2, 3, and so on
             * 'false'    : Doesn't have pagination
             */
            'pagination' => 'button',
            'grid_height_equal' => false,
            'progress_bar' => false,
            'post_meta' => true,
            // Template
            /**
             * Accepts: 
             * 
             * All values available in templates/skins 
             */
            'skin' => 'grid',
            'card' => 'grid-1',
            /**
             * Only used in certain skin such as 'grid' and 'masonry'
             */
            'columns' => 3,
            'min_column_width' => 250,
            /**
             * Only used in certain skin such as 'carousel'
             */
            'items_per_row' => 3,
            // Styles
            'font_family_title' => '',
            'font_family_description' => '',
            'font_size_title' => '',
            'font_size_description' => '',
            'font_color_title' => '',
            'font_color_description' => '',
            'background_color_title' => '',
            'background_color_description' => '',
            'background_color_ribbon' => '',
            'font_color_ribbon' => '',
            'background_color_icon' => '',
            'font_color_icon' => '',
            'background_color_button' => '',
            'font_color_button' => '',
            // Misc
            'class_name' => '',
            /**
             * Random unique ID for CSS styling purpose
             */
            'id' => '',
            // Filter
            'filter_search' => true,
            'filter_taxonomies' => '',
            'filter_price' => true,
            'filter_price_min' => 0,
            'filter_price_max' => 1000,
        ]);
    }


    public function build_query($atts = [])
    {
        if (empty($atts['per_page'])) {
            $atts['per_page'] = -1;
        }

        $tax_query = [];

        $taxonomies = !empty($atts['taxonomies']) ? array_filter(explode(';', sanitize_text_field(str_replace('"', '', wp_unslash($atts['taxonomies']))))) : [];

        foreach ($taxonomies as $taxonomy_entry) {
            $taxonomy_parts = explode(':', $taxonomy_entry);

            if (empty($taxonomy_parts[0]) || empty($taxonomy_parts[1])) {
                continue;
            }

            $taxonomy = trim($taxonomy_parts[0]);
            $terms = array_map('trim', explode(',', $taxonomy_parts[1]));

            if (!empty($taxonomy) && !empty($terms)) {
                $tax_query[] = [
                    'taxonomy' => $taxonomy,
                    'field' => 'slug',
                    'terms' => $terms,
                ];
            }
        }

        $tax_query['relation'] = 'OR';
        $post__in = null;
        $query_args = apply_filters('academy-africa_course_grid_query_args', [
            'post_type' => sanitize_text_field($atts['post_type']),
            'posts_per_page' => intval($atts['per_page']),
            'post_status' => 'publish',
            'orderby' => sanitize_text_field($atts['orderby']),
            'order' => sanitize_text_field($atts['order']),
            'tax_query' => $tax_query,
            'post__in' => $post__in,
        ], $atts, $filter = null);

        return $query_args;
    }

    public static function format_price($price, $format = 'plain')
    {
        if ($format == 'output') {
            preg_match('/(((\d+)[,\.]?)*(\d+)([\.,]?\d+)?)/', $price, $matches);

            $price = $matches[1];

            if (!empty($price)) {
                $match_comma_decimal = preg_match('/(?:\d+\.?)*\d+(,\d{1,2})$/', $price, $comma_matches);

                $match_dot_decimal = preg_match('/(?:\d+,?)*\d+(\.\d{1,2})$/', $price, $dot_matches);

                if ($match_comma_decimal) {
                    $has_decimal = !empty($comma_matches[1]) ? true : false;
                    $thousands_separator = '.';
                    $decimal_separator = ',';
                    $price = str_replace('.', '', $price);
                    $price = str_replace(',', '.', $price);
                } else {
                    $has_decimal = !empty($dot_matches[1]) ? true : false;
                    $thousands_separator = ',';
                    $decimal_separator = '.';
                    $price = str_replace(',', '', $price);
                }

                $price = floatval($price);

                if ($has_decimal) {
                    $price = number_format($price, 2, $decimal_separator, $thousands_separator);
                } else {
                    $price = number_format($price, 0, $decimal_separator, $thousands_separator);
                }
            }

            return $price;
        }

        return $price;
    }

    public function get_post_attr($post, $atts = [], $args = [])
    {
        if (is_numeric($post)) {
            $post = get_post($post);
        }
        $user_id = get_current_user_id();

        // $course_options = null;
        $price = '';
        $price_type = '';
        $price_text = '';
        if ($post->post_type == 'sfwd-courses') {
            // $course_options = get_post_meta($post->ID, '_sfwd-courses', true);
            $students_count = learndash_course_grid_count_students($post->ID);
            $price_args = learndash_get_course_price($post->ID);
        }
        $currency = learndash_get_currency_symbol();


        if (!empty($price_args)) {
            $price = $price_args['price'];
            $price_type = $price_args['type'];
            $price_format = apply_filters('academy-africa_course_grid_price_format', '{currency}{price}');

            if (is_numeric($price) && !empty($price)) {
                $price = self::format_price($price, 'output');
                $price_text = str_replace(['{currency}', '{price}'], [$currency, $price], $price_format);
            } elseif (is_string($price) && !empty($price)) {
                if (preg_match('/(((\d+),?)*(\d+)(\.?\d+)?)/', $price)) {
                    $price = self::format_price($price, 'output');
                    $price_text = str_replace(['{currency}', '{price}'], [$currency, $price], $price_format);
                } else {
                    $price_text = $price;
                }
            } elseif (empty($price)) {
                if ('closed' === $price_type || 'open' === $price_type) {
                    $price_text = '';
                } else {
                    $price_text = __('Free', 'learndash-course-grid');
                }
            }
        }

        if (empty($price)) {
            $price = __('Free', 'academy-africa-course-grid');
        }

        $user_object = get_user_by('ID', $post->post_author);
        $author = apply_filters('academy-africa_course_grid_author', [
            'name' => $user_object->display_name,
            'avatar' => get_avatar_url($post->post_author),
        ], $post->ID, $post->post_author);
        $course_link = get_permalink($post->ID);


        $post_attr = [
            'user_id' => $user_id,
            'post_type' => $post->post_type,
            'title' => $post->post_title,
            'price' => $price,
            'students' => $students_count,
            'author' => $author,
            'link' => $course_link,
        ];

        return apply_filters('academy-africa_course_grid_post_attr', $post_attr, $post->ID, $atts, $args);
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


        $atts = [
            'per_page' => '4',
            // "taxonomies" => "ld_course_category:featured"
        ];
        $atts = shortcode_atts($this->get_default_atts(), $atts, 'academy-africa_course_grid');

        $query = $this->build_query($atts);
        $query = new WP_Query($query);
        $max_num_pages = $query->max_num_pages;

        if ($max_num_pages > 1) {
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
                                                <? echo $option ?>
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
                                                            <? echo $option ?>
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
                                $course_excerpt = get_the_excerpt($course);
                                $course_author = get_the_author_meta('display_name', $course->post_author);

                                $course_thumbnail = get_the_post_thumbnail_url($course);
                                $course_link = get_permalink($course);
                                $course_meta = get_post_meta($course, 'sfwd-courses', true);
                                $course_price = $course_meta['sfwd-courses_course_price'];
                                $course_price = $course_price == 0 ? "Free" : $course_price;


                                $course_id = $course->ID;
                                $organizations = get_field('organization', $course_id);


                                $course_attrs = $this->get_post_attr($course, $atts);
                                extract($course_attrs);
                        ?>
                                <div class="card">
                                    <div class="course-card-pattern">
                                        <img src="<? echo $course_thumbnail ?>" alt="course-thumbnail">
                                    </div>
                                    <div>
                                        <?
                                        if ($organizations) {
                                            $organization = $organizations[0];
                                            // echo '<pre>';
                                            // print_r($organizations);
                                            // echo '</pre>';
                                            echo 'Org:::' . $organization->post_title;
                                        } else {
                                            echo 'No organization';
                                        }
                                        ?>
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
                                <br>
                        <?
                            }
                        }
                        ?>
                    </div>
                </section>
            </div>
        </main>
<?
    }
}
