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
                'students_count' => 75,
                'price' => 'Free',
                'image' => '/wp-content/plugins/academy-africa/includes/assets/images/course-image.png',
            ],
            [
                'title' => 'Fact-checking for Social Media',
                'providers' => ['pesacheck'],
                'students_count' => 75,
                'price' => 'Free',
                'image' => '/wp-content/plugins/academy-africa/includes/assets/images/course-image.png',
            ],
            [
                'title' => 'Drone Journalism',
                'providers' => ['Sensors Africa', 'pesacheck', 'code for africa'],
                'students_count' => 75,
                'price' => 'Free',
                'image' => '/wp-content/plugins/academy-africa/includes/assets/images/course-image.png',
            ], [
                'title' => 'Introduction to Data Visualisation with Flourish',
                'providers' => ['code for africa'],
                'students_count' => 75,
                'price' => 'Free',
                'image' => '/wp-content/plugins/academy-africa/includes/assets/images/course-image.png',
            ],
            [
                'title' => 'Fact-checking for Social Media',
                'providers' => ['pesacheck'],
                'students_count' => 75,
                'price' => 'Free',
                'image' => '/wp-content/plugins/academy-africa/includes/assets/images/course-image.png',
            ],
            [
                'title' => 'Drone Journalism',
                'providers' => ['Sensors Africa', 'pesacheck', 'code for africa'],
                'students_count' => 75,
                'price' => 'Free',
                'image' => '/wp-content/plugins/academy-africa/includes/assets/images/course-image.png',
            ], [
                'title' => 'Introduction to Data Visualisation with Flourish',
                'providers' => ['code for africa'],
                'students_count' => 75,
                'price' => 'Free',
                'image' => '/wp-content/plugins/academy-africa/includes/assets/images/course-image.png',
            ],
            [
                'title' => 'Fact-checking for Social Media',
                'providers' => ['pesacheck'],
                'students_count' => 75,
                'price' => 'Free',
                'image' => '/wp-content/plugins/academy-africa/includes/assets/images/course-image.png',
            ],
            [
                'title' => 'Drone Journalism',
                'providers' => ['Sensors Africa', 'pesacheck', 'code for africa'],
                'students_count' => 75,
                'price' => 'Free',
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
        $learning_pathways_title = "Learning Pathways";
        $learning_pathways_description = "Find out how you can enhance your skills and achieve mastery in specific disciplines within data science and technology.";
        $filter_options = $this->get_filter_by();
        $learning_pathways = $this->get_learning_pathways();
        $courses = $this->get_courses();
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
            <div class="courses-main">
                <section class="learning-pathways">
                    <h4 class="cfa-title">
                        <? echo $learning_pathways_title ?>
                    </h4>
                    <p class="description">
                        <? echo $learning_pathways_description ?>
                    </p>
                    <div class="content">
                        <?
                        if (!empty($learning_pathways)) {
                            foreach ($learning_pathways as $pathway) {
                                $name = $pathway["title"];
                                $icon = $pathway["icon"];
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
                <section class="all-courses">
                    <h4 class="cfa-title">
                        <? echo $courses_title ?>
                    </h4>
                    <p class="description">
                        <? echo $courses_description ?>
                    </p>
                    <div class="key">
                        <div class="course-free-tag">
                            Free
                        </div>
                        <p class="key-text">
                            <? echo $free_tag_key ?>
                        </p>
                    </div>
                    <div class="key">
                        <div class="course-paid-tag">
                            paid
                        </div>
                        <p class="key-text">
                            <? echo $paid_tag_key ?>
                        </p>
                    </div>
                    <div class="content">
                        <?
                        if (!empty($courses)) {
                            foreach ($courses as $course) {
                                $title = $course['title'];
                                $provider = $this->concatenate_with_count($course['providers']);
                                $student_count = $course['students_count'];
                                $price = $course['price'];
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
                                        <div class="card-footer">
                                            <div class="student-count">
                                                <img alt="user" src="/wp-content/plugins/academy-africa/includes/assets/images/user.svg" />
                                                <span><? echo $student_count ?></span>
                                            </div>
                                            <div class="tag free">
                                                <? echo $price ?>
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
