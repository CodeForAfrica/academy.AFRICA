<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

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
        $country_filters = $this->get_query_param('country');
        if (!empty($country_filters)) {
            $meta_query[] = array(
                'key'     => 'country',
                'value'   => $country_filters,
                'compare' => 'IN',
            );
        }
        $date_filter = $this->get_query_param('date')[0];
        if (!empty($date_filters) && $date_filter !== "all") {
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
        }
        if (!empty($meta_query)) {
            $args['meta_query'] = $meta_query;
        }
        $query = new WP_Query($args);
        $result = array();

        if ($query->have_posts()) {
            include dirname(__FILE__) . '/' . '../utils/african_countries.php';
            while ($query->have_posts()) {
                $query->the_post();
                $user_data = get_userdata(get_post_meta(get_the_ID(), 'speaker', true));
                $raw_date = get_post_meta(get_the_ID(), 'date', true);
                $date = date_format(date_create($raw_date), 'd/m/Y');
                $post_data = array(
                    'title' => get_the_title(),
                    'speaker' => $user_data->display_name,
                    'organisation' => get_post_meta(get_the_ID(), 'organisation', true),
                    'is_virtual' => get_post_meta(get_the_ID(), 'is_virtual', true) ? "&#x1F5A5;" : "",
                    'date' => $date,
                    'time' => str_replace("Africa/", "", get_post_meta(get_the_ID(), 'time', true) . ' ' . get_post_meta(get_the_ID(), 'timezone', true)),
                    'image' => get_the_post_thumbnail_url(get_the_ID(), 'thumbnail'),
                    'country_code' => $african_countries[get_post_meta(get_the_ID(), 'country', true)],
                    'language' => get_post_meta(get_the_ID(), 'language', true),
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

    public function get_filter_by()
    {
        return [
            [
                "title" => "Country",
                "name" => "country",
                "options" => [
                    "Nigeria" => "Nigeria",
                    "Kenya" => "Kenya",
                    "South Africa" => "South Africa",
                    "Tanzania" => "Tanzania",
                ]
            ],
            [
                "title" => "Date",
                "name" => "date",
                "options" => [
                    "All" => "all",
                    "This Week" => "week",
                    "This Month" => "month",
                    "Closed" => "closed",

                ]
            ]
        ];
    }
    public function get_upcoming_events()
    {
        $current_date = date('Y-m-d');
        $args = array(
            'post_type' => 'event',
            'posts_per_page' => 10,
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
        $filter_by = "Filter by:";
        $page_title = "Events";
        $upcoming = $this->get_upcoming_events();
        $upcoming_events = $upcoming[0];
        $upcoming_pagination = $upcoming[1];
        $previous = $this->get_previous_events();
        $previous_events = $previous[0];
        $previous_pagination = $previous[1];
        $upcoming_events_title = "Upcoming";
        $previous_events_title = "Previous Events";
        $filter_options = $this->get_filter_by();
?>
        <main class="events">
            <script>
                console.log(<?php echo json_encode($previous_events) ?>);
                console.log(<?php echo json_encode($upcoming_events) ?>);
            </script>
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
                                                <input type="checkbox" id="<? echo $option_name . '-' . $opt_value ?>" onclick="onChangeCheckBox(this, '<? echo $option_name ?>','<? echo $opt_value ?>')">
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
                <div id="filters" class="mobile-filter">
                    <h4 class="filter-title">
                        Filter By:
                    </h4>
                    <div class="filters">
                        <div class="accordion-parent">
                            <?
                            if (!empty($filter_options)) {
                                foreach ($filter_options as $item) {
                                    $title = $item["title"];
                            ?>
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
                                                            <input type="checkbox" id="<? echo $option_name . '-' . $opt_value ?>" onclick="onChangeCheckBox(this, '<? echo $option_name ?>','<? echo $opt_value ?>')">
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
            <section class="events-content">
                <div class="cfa-title" <?php echo $this->get_render_attribute_string('page_title'); ?>>
                    <? echo $page_title ?>
                </div>
                <div class="selected-filters" id="selected-filters"></div>
                <div class="section-title" <?php echo $this->get_render_attribute_string('upcoming_events_title'); ?>>
                    <? echo $upcoming_events_title ?>
                </div>
                <div class="content">
                    <?
                    if (!empty($upcoming_events)) {
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
                                        <? echo $country ?>
                                        <? echo $is_virtual ?>
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

                        <!-- Previous page link -->
                        <li class="page-item">
                            <a class="page-link" href="#">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none">
                                    <path d="M10 12L6 8L10 4" stroke="#616582" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </a>
                        </li>

                        <!-- Page links -->
                        <?php for ($i = 1; $i <= $upcoming_pagination['total_pages']; $i++) : ?>
                            <?
                            $current_url_params = $_GET;
                            $current_url_params["upcoming_page"] = $i;
                            $new_url = add_query_arg($current_url_params, home_url($_SERVER['REQUEST_URI']));
                            ?>
                            <li class="page-item"><a class="page-link" href="<? echo $new_url ?>"><?php echo $i; ?></a></li>
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
                                        <? echo $country ?>
                                        <? echo $is_virtual ?>
                                    </p>
                                </div>
                            </div>
                        <?
                        }

                        ?>
                    </div>
                    <hr class="divider">
                    <div class="pagination-container">
                        <a href="/" class="see-all">
                            View All
                        </a>
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
                            <?php for ($i = 1; $i <= $previous_pagination['total_pages']; $i++) : ?>
                                <?
                                $current_url_params = $_GET;
                                $current_url_params["previous_events_page"] = $i;
                                $new_url = add_query_arg($current_url_params, home_url($_SERVER['REQUEST_URI']));
                                ?>
                                <li class="page-item"><a class="page-link" href="<? echo $new_url ?>"><?php echo $i; ?></a></li>
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
