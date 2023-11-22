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
        return ['academy-africa-filters'];
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

    public function get_events($args)
    {
        $query = new WP_Query($args);
        $result = array();

        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                $user_data = get_userdata(get_post_meta(get_the_ID(), 'speaker', true));
                $post_data = array(
                    'title' => get_the_title(),
                    'speaker' => $user_data->display_name,
                    'organisation' => get_post_meta(get_the_ID(), 'organisation', true),
                    'is_virtual' => get_post_meta(get_the_ID(), 'is_virtual', true) ? 1 : 0,
                    'date' => get_post_meta(get_the_ID(), 'date_time', true),
                    'time' => get_post_meta(get_the_ID(), 'date_time', true),
                    'image' => get_the_post_thumbnail_url(get_the_ID(), 'thumbnail'),
                    'country_code' => get_post_meta(get_the_ID(), 'country', true)[0],
                    'language' => get_post_meta(get_the_ID(), 'language', true),
                );

                $result[] = $post_data;
            }

            wp_reset_postdata(); // Restore global post data
        }

        return $result;
    }

    public function get_filter_by()
    {
        return [
            [
                "title" => "Country",
                "options" => [
                    "Nigeria",
                    "Kenya",
                    "South Africa",
                    "Tanzania",

                ]
            ],
            [
                "title" => "Date",
                "options" => [
                    "All",
                    "This Week",
                    "This Month",
                    "Closed",

                ]
            ]
        ];
    }
    public function get_upcoming_events()
    {
        $args = array(
            'post_type' => 'event',
            'posts_per_page' => 10,
        );
        return $this->get_events($args);
    }
    protected function render()
    {
        $filter_by = "Filter by:";
        $page_title = "Events";
        $upcoming_events = $this->get_upcoming_events();
        $previous_events = $this->get_upcoming_events();
        $upcoming_events_title = "Upcoming";
        $previous_events_title = "Previous Events";
        $filter_options = $this->get_filter_by();
?>
        <main class="events">
            <script>
                console.log(<?php echo json_encode($previous_events) ?>)
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
            <section class="events-content">
                <div class="cfa-title" <?php echo $this->get_render_attribute_string('page_title'); ?>>
                    <? echo $page_title ?>
                </div>
                <div class="selected-filters"></div>
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
                <div class="section-title" <?php echo $this->get_render_attribute_string('previous_events_title'); ?>>
                    <? echo $previous_events_title ?>
                </div>
                <div class="content">
                    <?
                    if (!empty($previous_events)) {
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
        </main>
<?
    }
}
