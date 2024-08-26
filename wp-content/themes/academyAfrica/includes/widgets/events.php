<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}
include dirname(__FILE__) . '/' . '../utils/african_countries.php';
include_once dirname(__FILE__) . '/' . '../utils/countries.php';
class Academy_Africa_Events  extends \Elementor\Widget_Base
{

    public function get_name()
    {
        return 'Events';
    }

    public function get_style_depends()
    {
        return ['academy-africa-events'];
    }

    public function get_script_depends()
    {
        return ['academy-africa_events'];
    }

    public function get_title()
    {
        return esc_html__('Events');
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

    public function get_events($args)
    {
        $meta_query = $args["meta_query"] ?? array();
        $country_filters = $this->get_query_param('country')[0];
        if (!empty($country_filters)) {
            $meta_query[] = array(
                'relation' => 'OR',
                'key'     => 'countries',
                'value'   => $country_filters,
                'compare' => 'LIKE',
            );
        }
        $date_param = $this->get_query_param('date');
        $date_filter = !empty($date_param) ? $date_param[0] : null;
        if (!empty($date_filter) && $date_filter !== "all") {
            $current_date = date('Y-m-d');

            $first_day_of_week = date('Y-m-d', strtotime('last monday', strtotime($current_date)));
            $last_day_of_week = date('Y-m-d', strtotime('next sunday', strtotime($current_date)));
            $first_day_of_month = date('Y-m-01', strtotime($current_date));
            $last_day_of_month = date('Y-m-t', strtotime($current_date));

            if ($date_filter == "week") {
                $meta_query[] = array(
                    'key'     => 'date',
                    'value'   => array($first_day_of_week, $last_day_of_week),
                    'type'    => 'DATE',
                    'compare' => 'BETWEEN',
                );
            }
            if ($date_filter == "month") {
                $meta_query[] = array(
                    'key'     => 'date',
                    'value'   => array($first_day_of_month, $last_day_of_month),
                    'type'    => 'DATE',
                    'compare' => 'BETWEEN',
                );
            }
            if ($date_filter == "closed") {
                $meta_query[] = array(
                    'key'     => 'date',
                    'value'   => $current_date,
                    'type'    => 'DATE',
                    'compare' => '<',
                );
            }
        }
        if (!empty($meta_query)) {
            $args['meta_query'] = $meta_query;
        }
        $query = new WP_Query($args);
        $result = array();

        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                $user_data = get_userdata(get_post_meta(get_the_ID(), 'speaker', true));
                $raw_date = get_post_meta(get_the_ID(), 'date', true);
                $date = date_format(date_create($raw_date), 'd/m/Y');
                $countries = get_post_meta(get_the_ID(), 'countries', true);
                $post_data = array(
                    'title' => get_the_title(),
                    'speaker' => $user_data->display_name,
                    'organisation' => get_post_meta(get_the_ID(), 'organisation', true),
                    'is_virtual' => get_post_meta(get_the_ID(), 'is_virtual', true) ? "&#x1F5A5;" : "",
                    'date' => $date,
                    'time' => get_post_meta(get_the_ID(), 'time', true) . ' ' . "GMT +00:00",
                    'image' => get_the_post_thumbnail_url(get_the_ID(), 'full'),
                    'country_code' => isset($countries) ? country_flag_emoji($countries[0]) : country_flag_emoji("ZA"),
                    'language' => get_post_meta(get_the_ID(), 'language', true),
                    'post_url' => get_permalink(get_the_ID()),
                    'countries' => get_post_meta(get_the_ID(), 'countries', true)
                );

                $result[] = $post_data;
            }

            $pagination = array(
                'current_page' => max(1, get_query_var('paged')),
                'total_pages'  => $query->max_num_pages,
            );

            wp_reset_postdata();
        }

        return array($result, $pagination);
    }

    public function filter_labels()
    {
        return [
            'country'  => esc_html__('Country', 'academy-africa'),
            'date' => esc_html__('Date', 'academy-africa'),
            'speaker' => esc_html__('Speaker', 'academy-africa'),
            'status' => esc_html__('Status', 'academy-africa'),
            'organisation' => esc_html__('Organisation', 'academy-africa'),
        ];
    }

    protected function register_controls()
    {
        $this->start_controls_section(
            'labels',
            [
                'label' => __('Labels', 'academy-africa'),
            ]
        );
        $this->add_control(
            'filter_by',
            [
                'label' => __('Filter By', 'academy-africa'),
                'type' => \Elementor\Controls_Manager::TEXTAREA,
                'default' => __('Filter by:', 'academy-africa'),
                'label_block' => true,
            ]
        );
        $this->add_control(
            'page_title',
            [
                'label' => __('Pages Title', 'academy-africa'),
                'type' => \Elementor\Controls_Manager::TEXTAREA,
                'default' => __('Events', 'academy-africa'),
                'label_block' => true,
            ]
        );
        $this->add_control(
            'upcoming_title',
            [
                'label' => __('Upcoming Events Title', 'academy-africa'),
                'type' => \Elementor\Controls_Manager::TEXTAREA,
                'default' => __('Upcoming', 'academy-africa'),
                'label_block' => true,
            ]
        );
        $this->add_control(
            'previous_events_title',
            [
                'label' => __('Previous Events Title', 'academy-africa'),
                'type' => \Elementor\Controls_Manager::TEXTAREA,
                'default' => __('Previous Events', 'academy-africa'),
                'label_block' => true,
            ]
        );
        $this->end_controls_section();
        $this->start_controls_section(
            'Filter',
            [
                'label' => __('Labels', 'academy-africa'),
            ]
        );
        $this->add_control(
            'sort_by',
            [
                'label' => __('Sort by label', 'academy-africa'),
                'type' => \Elementor\Controls_Manager::TEXTAREA,
                'default' => __('Sort by:', 'academy-africa'),
                'label_block' => true,
            ]
        );
        $this->add_control(
            'filter_options',
            [
                'label' => esc_html__('Filter options', 'academy-africa'),
                'type' => \Elementor\Controls_Manager::SELECT2,
                'label_block' => true,
                'multiple' => true,
                'options' => $this->filter_labels(),
                'default' => ['country', 'date'],
            ]
        );
        $this->end_controls_section();
    }
    public function get_filter_by()
    {
        $settings = $this->get_settings_for_display();
        $filter_options = $settings['filter_options'];
        $output = array();
        if (!empty($filter_options)) {
            foreach ($filter_options as $option) {
                if ($option == "date") {
                    $output[] = array(
                        "title" => "Date",
                        "name" => "date",
                        "options" => array(
                            "All" => "all",
                            "This Week" => "week",
                            "This Month" => "month",
                            "Closed" => "closed"
                        )
                    );
                } else {
                    $args = array(
                        'post_type' => 'event',
                        'posts_per_page' => -1,
                    );
                    $query = new WP_Query($args);
                    $options = array();
                    if ($option == "country") {
                        if ($query->have_posts()) {
                            while ($query->have_posts()) {
                                $query->the_post();
                                $post_id = get_the_ID();
                                $values = get_post_meta($post_id, "countries", true);
                                if (isset($values)) {
                                    foreach ($values as $field_name) {
                                        $c_name = get_country_code($field_name);
                                        $opt = isset($c_name) ? $c_name["name"] : "";
                                        $options[$opt] = $field_name;
                                    }
                                }
                            }
                            wp_reset_postdata();
                        }
                        $output[] = array(
                            "title" => $this->filter_labels()[$option] ?? $option,
                            "name" => $option,
                            "options" => $options
                        );
                    } else {
                        if ($query->have_posts()) {
                            while ($query->have_posts()) {
                                $query->the_post();
                                $post_id = get_the_ID();
                                $field_name = get_post_meta($post_id, $option, true);
                                if ($field_name) {
                                    $options[$field_name] = $field_name;
                                }
                            }
                            wp_reset_postdata();
                        }
                        $output[] = array(
                            "title" => $this->filter_labels()[$option] ?? $option,
                            "name" => $option,
                            "options" => $options
                        );
                    }
                }
            }
        }
        return $output;
    }
    public function get_upcoming_events()
    {
        $current_date = date('Y-m-d');
        $args = array(
            'post_type' => 'event',
            'posts_per_page' => 10,
            'paged' => $this->get_query_param('upcoming_page') ? $this->get_query_param('upcoming_page')[0] : 1,
            'meta_query' => array(
                array(
                    'key'     => 'date',
                    'value'   => $current_date,
                    'type'    => 'DATE',
                    'compare' => '>=',
                ),
            )
        );
        return $this->get_events($args);
    }

    public function get_previous_events()
    {
        $current_date = date('Y-m-d');
        $args = array(
            'post_type' => 'event',
            'posts_per_page' => 10,
            'paged' => $this->get_query_param('previous_events_page') ? $this->get_query_param('previous_events_page')[0] : 1,
            'meta_query' => array(
                array(
                    'key'     => 'date',
                    'value'   => $current_date,
                    'type'    => 'DATE',
                    'compare' => '<',
                ),
            )
        );
        return $this->get_events($args);
    }
    protected function render()
    {
        $settings = $this->get_settings_for_display();
        $filter_by = $settings["filter_by"];
        $page_title = $settings["page_title"];
        $upcoming = $this->get_upcoming_events();
        $upcoming_events = $upcoming[0];
        $upcoming_pagination = $upcoming[1];
        $previous = $this->get_previous_events();
        $previous_events = $previous[0];
        $previous_pagination = $previous[1];
        $upcoming_events_title = $settings["upcoming_title"];
        $previous_events_title = $settings["previous_events_title"];
        $filter_options = $this->get_filter_by();
?>
        <main class="events">
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
                            $option_name = $item["name"];
                    ?>
                            <p style="margin-top: 40px" class="filter-by-title">
                                <? echo $title ?>
                            </p>
                            <?
                            if (!empty($options)) {
                            ?>
                                <ul>

                                    <?
                                    foreach ($options as $option => $opt_value) {
                                    ?>

                                        <li>
                                            <label class="mui-checkbox">
                                                <input type="checkbox" name="<? echo $option_name . '-' . $opt_value ?>" onclick="onChangeCheckBox(this, '<? echo $option_name ?>','<? echo $opt_value ?>', true)">
                                                <span class="checkmark"></span>
                                                <? echo $option ?>
                                            </label>
                                        </li>

                                    <?
                                    }
                                    ?>
                                </ul>
                    <?
                            }
                        }
                    }
                    ?>
                    <button class="button clear-filters" onclick="clearFilters()">
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

            </aside>

            <section class="events-content">
                <div id="filters" class="events-mobile-filters">
                    <div style="padding: 60px;">
                        <div class="filters">
                            <div style="display:flex; justify-content: space-between; margin: 0 0 40px; align-items: center;" class="close-filters">
                                <h1 class="filter-by">
                                    <? echo $filter_by ?>
                                </h1>
                                <button onclick="closeFilters()" style="margin: 0" class="button clear-filters">
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
                            <?
                            if (!empty($filter_options)) {
                                foreach ($filter_options as $item) {
                                    $title = $item["title"];
                            ?>
                                    <div class="accordion-parent">
                                        <button class="accordion"><? echo $title ?></button>
                                        <?
                                        $options = $item["options"];
                                        $option_name = $item["name"];
                                        if (!empty($options)) {
                                        ?><div class="panel">
                                                <ul><?
                                                    foreach ($options as $option => $opt_value) {
                                                    ?>

                                                        <li>
                                                            <label class="mui-checkbox">
                                                                <input type="checkbox" name="<? echo $option_name . '-' . $opt_value ?>" onclick="onChangeCheckBox(this, '<? echo $option_name ?>','<? echo $opt_value ?>')">
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
                                        ?>
                                    </div><?
                                        }
                                    }
                                            ?>

                        </div>
                        <div class="actions">
                            <button onclick="applyFilters()" class="button primary">
                                Apply
                            </button>
                            <button class="button clear-filters" style="margin-top: 0; padding: 0" onclick="clearFilters()">
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
                </div>
                <div class="cfa-title" <?php echo $this->get_render_attribute_string('page_title'); ?>>
                    <? echo $page_title ?>
                </div>
                <button onclick="openFilters()" class="button primary open-filter"><svg width="17" height="17" viewBox="0 0 17 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <g id="Icon" clip-path="url(#clip0_11892_105059)">
                            <path id="Vector" d="M15.1693 2.5H1.83594L7.16927 8.80667V13.1667L9.83594 14.5V8.80667L15.1693 2.5Z" stroke="#EFF0FD" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </g>
                        <defs>
                            <clipPath id="clip0_11892_105059">
                                <rect width="16" height="16" fill="currentColor" transform="translate(0.5 0.5)" />
                            </clipPath>
                        </defs>
                    </svg>Filter</button>
                <div class="selected-filters" id="selected-filters"></div>
                <? if (!empty($upcoming_events)) { ?>
                    <div class="section-title" <?php echo $this->get_render_attribute_string('upcoming_events_title'); ?>>
                        <? echo $upcoming_events_title ?>
                    </div>
                    <div class="content">
                        <?

                        foreach ($upcoming_events as $event) {
                            $image = $event["image"];
                            $title = $event["title"];
                            $speaker = $event["speaker"];
                            $date = $event["date"];
                            $language = $event["language"];
                            $country = $event["country_code"];
                            $time = $event["time"];
                            $is_virtual = $event["is_virtual"];
                        ?>
                            <a href="<? echo $event["post_url"] ?>">
                                <div class="card">
                                    <img width="100%" src="<? echo $image ?>" alt="<? echo $title ?>">

                                    <p class="event-title">
                                        <? echo $title ?>
                                    </p>
                                    <p class="speaker-name">
                                        <? echo $speaker ?>
                                    </p>
                                    <div class="flex-between date-and-language">
                                        <p class="date">
                                            <? echo $date ?>
                                        </p>
                                        <p class="language">
                                            <? echo $language ?>
                                        </p>

                                    </div>
                                    <div class="flex-between time-and-country">
                                        <p class="time">
                                            <? echo $time ?>
                                        </p>
                                        <p class="country">
                                            <? echo $is_virtual ?>
                                        </p>
                                    </div>
                                </div>
                            </a>

                        <?
                        }
                        ?>
                    </div>
                    <hr class="divider">
                    <div class="pagination-container">
                        <ul class="pagination">

                            <!-- Previous page link -->
                            <li class="page-item">
                                <a class="page-link" href="#">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none">
                                        <path d="M10 12L6 8L10 4" stroke="#616582" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                </a>
                            </li>

                            <?
                            $current = $this->get_query_param('upcoming_page') ? $this->get_query_param('upcoming_page')[0] : "1"
                            ?>
                            <!-- Page links -->
                            <?php for ($i = 1; $i <= $upcoming_pagination['total_pages']; $i++) : ?>
                                <?
                                $current_url_params = $_GET;
                                $current_url_params["upcoming_page"] = $i;
                                $new_url = add_query_arg($current_url_params, home_url($_SERVER['REQUEST_URI']));
                                $cls = intval($current) === $i ? "active" : "";
                                ?>
                                <li class="page-item <? echo $cls ?>"><a class="page-link" href="<? echo $new_url ?>"><?php echo $i; ?></a></li>
                            <?php endfor; ?>

                            <!-- Next page link -->
                            <li class="page-item">
                                <a class="page-link" href="#">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none">
                                        <path d="M6 12L10 8L6 4" stroke="#616582" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                </a>
                            </li>

                        </ul>
                    </div>
                <?
                }
                if (!empty($previous_events)) {

                ?>
                    <div class="section-title" <?php echo $this->get_render_attribute_string('previous_events_title'); ?>>
                        <? echo $previous_events_title ?>
                    </div>
                    <div class="content">
                        <?
                        foreach ($previous_events as $event) {
                            $image = $event["image"];
                            $title = $event["title"];
                            $speaker = $event["speaker"];
                            $date = $event["date"];
                            $language = $event["language"];
                            $country = $event["country_code"];
                            $time = $event["time"];
                            $is_virtual = $event["is_virtual"];
                        ?>
                            <a href="<? echo $event["post_url"] ?>">
                                <div class="card">
                                    <img width="100%" src="<? echo $image ?>" alt="<? echo $title ?>">

                                    <p class="event-title">
                                        <? echo $title ?>
                                    </p>
                                    <p class="speaker-name">
                                        <? echo $speaker ?>
                                    </p>
                                    <div class="flex-between date-and-language">
                                        <p class="date">
                                            <? echo $date ?>
                                        </p>
                                        <p class="language">
                                            <? echo $language ?>
                                        </p>

                                    </div>
                                    <div class="flex-between time-and-country">
                                        <p class="time">
                                            <? echo $time ?>
                                        </p>
                                        <p class="country">
                                            <? echo $is_virtual ?>
                                        </p>
                                    </div>
                                </div>
                            </a>
                        <?
                        }

                        ?>
                    </div>
                    <hr class="divider">
                    <div class="pagination-container">
                        <ul class="pagination">

                            <!-- Previous page link -->
                            <li class="page-item">
                                <a class="page-link" href="#">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none">
                                        <path d="M10 12L6 8L10 4" stroke="#616582" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                </a>
                            </li>

                            <!-- Page links -->
                            <?
                            $current = $this->get_query_param('previous_events_page') ? $this->get_query_param('previous_events_page')[0] : "1"
                            ?>
                            <?php for ($i = 1; $i <= $previous_pagination['total_pages']; $i++) : ?>
                                <?
                                $current_url_params = $_GET;
                                $current_url_params["previous_events_page"] = $i;
                                $new_url = add_query_arg($current_url_params, home_url($_SERVER['REQUEST_URI']));
                                $cls = intval($current) === $i ? "active" : "";
                                ?>
                                <li class="page-item <? echo $cls ?>"><a class="page-link" href="<? echo $new_url ?>"><?php echo $i; ?></a></li>
                            <?php endfor; ?>

                            <!-- Next page link -->
                            <li class="page-item">
                                <a class="page-link" href="#">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none">
                                        <path d="M6 12L10 8L6 4" stroke="#616582" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                </a>
                            </li>

                        </ul>
                    </div>
                <?
                }
                ?>
            </section>
        </main>
<?
    }
}
