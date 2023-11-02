<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}


class Academy_Africa_My_Courses  extends \Elementor\Widget_Base
{

    public function get_name()
    {
        return 'My Courses';
    }

    public function get_style_depends()
    {
        return ['academy-africa-my-courses', 'academy-africa'];
    }

    public function get_title()
    {
        return esc_html__('My Courses');
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
                    "Free", "$2 to $4.99", "$5 to $7.99", "$7 to $9.99"
                ]
            ]
        ];
    }

    public function concatenate_with_count($array)
    {
        $count = count($array);
        return $count === 0 ? '' : ($count === 1 ? $array[0] : $array[0] . ' + ' . ($count - 1) . ' more');
    }
    public function get_courses()
    {
        return [
            [
                'title' => 'Introduction to Data Visualisation with Flourish',
                'providers' => ['code for africa'],
                'lessons_count' => 75,
                'completed' => '71%',
                'image' => '/wp-content/plugins/academy-africa/includes/assets/images/course-image.png',
            ],
            [
                'title' => 'Fact-checking for Social Media',
                'providers' => ['pesacheck'],
                'lessons_count' => 75,
                'completed' => '71%',
                'image' => '/wp-content/plugins/academy-africa/includes/assets/images/course-image.png',
            ],
            [
                'title' => 'Drone Journalism',
                'providers' => ['Sensors Africa', 'pesacheck', 'code for africa'],
                'lessons_count' => 75,
                'completed' => '71%',
                'image' => '/wp-content/plugins/academy-africa/includes/assets/images/course-image.png',
            ], [
                'title' => 'Introduction to Data Visualisation with Flourish',
                'providers' => ['code for africa'],
                'lessons_count' => 75,
                'completed' => '71%',
                'image' => '/wp-content/plugins/academy-africa/includes/assets/images/course-image.png',
            ],
            [
                'title' => 'Fact-checking for Social Media',
                'providers' => ['pesacheck'],
                'lessons_count' => 75,
                'completed' => '71%',
                'image' => '/wp-content/plugins/academy-africa/includes/assets/images/course-image.png',
            ],
            [
                'title' => 'Drone Journalism',
                'providers' => ['Sensors Africa', 'pesacheck', 'code for africa'],
                'lessons_count' => 75,
                'completed' => '71%',
                'image' => '/wp-content/plugins/academy-africa/includes/assets/images/course-image.png',
            ], [
                'title' => 'Introduction to Data Visualisation with Flourish',
                'providers' => ['code for africa'],
                'lessons_count' => 75,
                'completed' => '71%',
                'image' => '/wp-content/plugins/academy-africa/includes/assets/images/course-image.png',
            ],
            [
                'title' => 'Fact-checking for Social Media',
                'providers' => ['pesacheck'],
                'lessons_count' => 75,
                'completed' => '71%',
                'image' => '/wp-content/plugins/academy-africa/includes/assets/images/course-image.png',
            ],
            [
                'title' => 'Drone Journalism',
                'providers' => ['Sensors Africa', 'pesacheck', 'code for africa'],
                'lessons_count' => 75,
                'completed' => '71%',
                'image' => '/wp-content/plugins/academy-africa/includes/assets/images/course-image.png',
            ],
        ];
    }

    public function get_learning_pathways()
    {
        return [[
            "icon" => "/wp-content/plugins/academy-africa/includes/assets/images/sample-icon.svg",
            "title" => "Investigative Journalism",
            "courses" => "4 Courses"
        ], [
            "icon" => "/wp-content/plugins/academy-africa/includes/assets/images/sample-icon.svg",
            "title" => "Investigative Journalism",
            "courses" => "4 Courses"
        ], [
            "icon" => "/wp-content/plugins/academy-africa/includes/assets/images/sample-icon.svg",
            "title" => "Investigative Journalism",
            "courses" => "4 Courses"
        ]];
    }

    protected function register_controls()
    {
    }

    protected function render()
    {
        $settings = $this->get_settings_for_display();
        $filter_by = "Filter by:";
        $filter_options = $this->get_filter_by();
        $incomplete_courses = $this->get_courses();
        $completed_courses = $this->get_courses();
        $free_tag_key = "Download the certificate for free after completing the course";
        $paid_tag_key = "Download the certificate for free after completing the course";
        $courses_title = "All Courses";
        $courses_description = "we are happy to say All courses are free to complete";
?>
        <main class="body">
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
            <div class="main">
                <section class="incomplete-courses">
                    <h4 class="cfa-title">
                        Welcome <strong>Sakina Salem</strong>
                    </h4>
                    <p class="description">
                        Complete your courses
                    </p>
                    <div class="content">
                        <?
                        if (!empty($incomplete_courses)) {
                            foreach ($incomplete_courses as $course) {
                                $title = $course['title'];
                                $provider = $this->concatenate_with_count($course['providers']);
                                $lessons_count = $course['lessons_count'];
                                $completed = $course['completed'];
                                $image = $course['image'];
                        ?>
                                <div class="card">
                                    <img alt="course-logo" class="logo" src="<? echo $image ?>" />
                                    <div class="card-content">
                                        <div class="card-title">
                                            <p>
                                                <? echo $title ?>
                                            </p>
                                        </div>
                                        <p class="provider">
                                            by <? echo $provider ?>
                                        </p>
                                        <p class="lessons-count">
                                            <? echo $lessons_count ?> lessons
                                        </p>
                                        <div class="progress-bar">
                                            <div style="width: <? echo $completed ?>"></div>
                                        </div>
                                        <div class="card-footer">
                                            <p>Enrolled</p>
                                            <p><? echo $completed ?> Completed</p>
                                        </div>
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
                <section class="your-certificates">
                    <h4 class="your-certificates-title">
                        Your Certificates
                    </h4>
                    <div class="content">
                        <?
                        if (!empty($completed_courses)) {
                            foreach ($completed_courses as $course) {
                                $title = $course['title'];
                                $provider = $this->concatenate_with_count($course['providers']);
                                $lessons_count = $course['lessons_count'];
                                $completed = $course['completed'];
                                $image = $course['image'];
                        ?>
                                <div class="card">
                                    <img alt="course-logo" class="logo" src="/wp-content/plugins/academy-africa/includes/assets/images/course-image.png" />
                                    <div class="card-content">
                                        <div class="card-title">
                                            <p>
                                                <? echo $title ?>
                                            </p>
                                        </div>
                                        <p class="provider">
                                            by <? echo $provider ?>
                                        </p>
                                        <p class="lessons-count">
                                            <? echo $lessons_count ?> lessons
                                        </p>
                                        <div class="completed-progress-bar">
                                        </div>
                                        <div class="card-footer">
                                            <p>Certificate Achieved</p>
                                            <div class="icons">
                                                <img src="/wp-content/plugins/academy-africa/includes/assets/images/download.svg" alt="download" />
                                                <img src="/wp-content/plugins/academy-africa/includes/assets/images/share.svg" alt="share" />
                                            </div>
                                        </div>
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
            </div>
        </main>
<?
    }
}
