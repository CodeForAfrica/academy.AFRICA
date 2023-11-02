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

    protected function register_controls()
    {
    }

    protected function render()
    {
        $settings = $this->get_settings_for_display();
?>
        <main class="body">
            <div class="sidebar" id="sidebar">
                <p class="filter-by">
                    Filter by:
                </p>
                <p class="filter-by-title">
                    Organization
                </p>
                <ul>
                    <li>
                        <label class="mui-checkbox">
                            <input type="checkbox">
                            <span class="checkmark"></span>
                            DW Akademie
                        </label>
                    </li>
                </ul>
                <ul>
                    <li>
                        <label class="mui-checkbox">
                            <input type="checkbox">
                            <span class="checkmark"></span>
                            Code for Africa
                        </label>
                    </li>
                </ul>
                <ul>
                    <li>
                        <label class="mui-checkbox">
                            <input type="checkbox">
                            <span class="checkmark"></span>
                            Institut Supérieur Des Sciences De L'information Et De La Communication (ISSIC)
                        </label>
                    </li>
                </ul>
            </div>
            <div class="courses-main">
                <section class="learning-pathways">
                    <h4 class="cfa-title">
                        Learning Pathways
                    </h4>
                    <p class="description">
                        Find out how you can enhance your skills and achieve mastery in specific disciplines within data
                        science and
                        technology.
                    </p>
                    <div class="content">
                        <div class="card">
                            <div class="course-card-pattern">
                                <div class="icon">
                                    <img src="/wp-content/plugins/academy-africa/includes/assets/images/sample-icon.svg" alt="sample-icon">
                                </div>
                            </div>
                            <div class="pathway-card-content">
                                <p class="pathway-name">
                                    Investigative Journalism
                                </p>
                                <p class="course-count">
                                    4 courses
                                </p>
                            </div>
                        </div>
                        <div class="card">
                            <div class="course-card-pattern">
                                <div class="icon">
                                    <img src="/wp-content/plugins/academy-africa/includes/assets/images/sample-icon.svg" alt="sample-icon">
                                </div>
                            </div>
                            <div class="pathway-card-content">
                                <p class="pathway-name">
                                    Investigative Journalism
                                </p>
                                <p class="course-count">
                                    4 courses
                                </p>
                            </div>
                        </div>
                        <div class="card">
                            <div class="course-card-pattern">
                                <div class="icon">
                                    <img src="/wp-content/plugins/academy-africa/includes/assets/images/sample-icon.svg" alt="sample-icon">
                                </div>
                            </div>
                            <div class="pathway-card-content">
                                <p class="pathway-name">
                                    Investigative Journalism
                                </p>
                                <p class="course-count">
                                    4 courses
                                </p>
                            </div>
                        </div>
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
                        All Courses
                    </h4>
                    <p class="description">
                        we are happy to say All courses are free to complete
                    </p>
                    <div class="key">
                        <div class="course-free-tag">
                            Free
                        </div>
                        <p class="key-text">
                            Download the certificate for free after completing the course
                        </p>
                    </div>
                    <div class="key">
                        <div class="course-paid-tag">
                            paid
                        </div>
                        <p class="key-text">
                            Download the certificate for free after completing the course
                        </p>
                    </div>
                    <div class="content">
                        <div class="card">
                            <img alt="course-logo" class="logo" src="/wp-content/plugins/academy-africa/includes/assets/images/course-image.png" />
                            <div class="card-content">
                                <div class="card-title">
                                    <p>
                                        Introduction to Data Visualisation with Flourish
                                    </p>
                                </div>
                                <p class="provider">
                                    by CODE FOR AFRICA + 1 more
                                </p>
                                <div class="card-footer">
                                    <div class="student-count">
                                        <img alt="user" src="/wp-content/plugins/academy-africa/includes/assets/images/user.svg" />
                                        <span>75</span>
                                    </div>
                                    <div class="tag free">
                                        Free
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <img alt="course-logo" class="logo" src="/wp-content/plugins/academy-africa/includes/assets/images/course-image.png" />
                            <div class="card-content">
                                <div class="card-title">
                                    <p>
                                        Introduction to Data Visualisation with Flourish
                                    </p>
                                </div>
                                <p class="provider">
                                    by CODE FOR AFRICA + 1 more
                                </p>
                                <div class="card-footer">
                                    <div class="student-count">
                                        <img alt="user" src="/wp-content/plugins/academy-africa/includes/assets/images/user.svg" />
                                        <span>75</span>
                                    </div>
                                    <div class="tag free">
                                        Free
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <img alt="course-logo" class="logo" src="/wp-content/plugins/academy-africa/includes/assets/images/course-image.png" />
                            <div class="card-content">
                                <div class="card-title">
                                    <p>
                                        Introduction to Data Visualisation with Flourish
                                    </p>
                                </div>
                                <p class="provider">
                                    by CODE FOR AFRICA + 1 more
                                </p>
                                <div class="card-footer">
                                    <div class="student-count">
                                        <img alt="user" src="/wp-content/plugins/academy-africa/includes/assets/images/user.svg" />
                                        <span>75</span>
                                    </div>
                                    <div class="tag free">
                                        Free
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <img alt="course-logo" class="logo" src="/wp-content/plugins/academy-africa/includes/assets/images/course-image.png" />
                            <div class="card-content">
                                <div class="card-title">
                                    <p>
                                        Introduction to Data Visualisation with Flourish
                                    </p>
                                </div>
                                <p class="provider">
                                    by CODE FOR AFRICA + 1 more
                                </p>
                                <div class="card-footer">
                                    <div class="student-count">
                                        <img alt="user" src="/wp-content/plugins/academy-africa/includes/assets/images/user.svg" />
                                        <span>75</span>
                                    </div>
                                    <div class="tag free">
                                        Free
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <img alt="course-logo" class="logo" src="/wp-content/plugins/academy-africa/includes/assets/images/course-image.png" />
                            <div class="card-content">
                                <div class="card-title">
                                    <p>
                                        Introduction to Data Visualisation with Flourish
                                    </p>
                                </div>
                                <p class="provider">
                                    by CODE FOR AFRICA + 1 more
                                </p>
                                <div class="card-footer">
                                    <div class="student-count">
                                        <img alt="user" src="/wp-content/plugins/academy-africa/includes/assets/images/user.svg" />
                                        <span>75</span>
                                    </div>
                                    <div class="tag free">
                                        Free
                                    </div>
                                </div>
                            </div>
                        </div>
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
