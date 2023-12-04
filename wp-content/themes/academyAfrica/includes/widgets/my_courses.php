<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

use AcademyAfrica\Theme\Courses\CoursesFunctions;

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

    public function concatenate_with_count($array)
    {
        $count = count($array);
        return $count === 0 ? '' : ($count === 1 ? $array[0] : $array[0] . ' + ' . ($count - 1) . ' more');
    }
    public function get_query_param($param)
    {
        if (isset($_GET[$param])) {
            if ($_GET[$param]) {
                return explode(",", $_GET[$param]);
            }
        }
    }
    public function get_completed_courses()
    {
        $user_id = get_current_user_id();
        $courses = learndash_user_get_enrolled_courses( $user_id );
        $orgs = $this->get_query_param('organization');
        $instructors = $this->get_query_param('instructor');
        $sort = $this->get_query_param('sort');
        $current_page = $this->get_query_param("page") ? $this->get_query_param("page") : 1;
        $user_id = get_current_user_id();

        $args = array(
            'post_types'      => 'sfwd-courses',
            'activity_types'  => 'course',
            'activity_status' => 'IN_PROGRESS',
            'per_page' => '9',
            'paged' => $current_page,
            'organization' => $orgs,
            'instructor' => $instructors,
            'sort' => $sort,
            'user_ids' => array($user_id),
            'post_ids' => $courses,
        );
        return learndash_reports_get_activity( $args );
    }

    public function get_enrolled_courses()
    {
        $orgs = $this->get_query_param('organization');
        $instructors = $this->get_query_param('instructor');
        $sort = $this->get_query_param('sort');
        $current_page = $this->get_query_param("page") ? $this->get_query_param("page") : 1;
        $targs = array(
            'organization' => $orgs,
            'instructor' => $instructors,
            'sort' => $sort,
            'posts_per_page' => "9",
            'paged' => $current_page,
            'nopaging'  => false,
        );
        return learndash_user_get_enrolled_courses(get_current_user_id(), $targs, false);
    }

    protected function register_controls()
    {
    }

    protected function render()
    {
        $settings = $this->get_settings_for_display();
        $filter_by = "Filter by:";
        $filter_options = CoursesFunctions::get_filter_by();
        $completed_courses = $this->get_completed_courses();
        $enrolled = $this->get_enrolled_courses();
        $free_tag_key = "Download the certificate for free after completing the course";
        $paid_tag_key = "Download the certificate for free after completing the course";
        $courses_title = "All Courses";
        $courses_description = "we are happy to say All courses are free to complete";
        $current_user = wp_get_current_user();
        $updated = learndash_process_mark_complete($current_user->ID, 102266, false, 102266);
        
?>
        <main class="body">
            <script>
                console.log(<? echo json_encode($completed_courses["results"]) ?>, "<? echo $updated ?>")
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
                                                <input type="checkbox" onclick="filterCourses(this, '<? echo $item["name"] ?>', '<? echo $option->name ?>')" value="<? echo $option->id ?>" name="<? echo $item["name"] . '-' . $option->name ?>">
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
            </aside>
            <div class="main">
                <section class="incomplete-courses">
                    <h4 class="cfa-title">
                        Welcome <strong><? echo $current_user->display_name; ?></strong>
                    </h4>
                    <div class="filter-section">
                        <div class="sort">
                            <div class="label">
                                Sort by:
                            </div>
                            <select name="sort" id="sort" class="select">
                                <option value="newest">Newest</option>
                                <option value="oldest">Oldest</option>
                                <option value="price">Price</option>
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
                    <p class="description">
                        Complete your courses
                    </p>
                    <div class="content">
                        <?
                        if (!empty($enrolled)) {
                            foreach ($enrolled as $course_id) {
                                $course = get_post($course_id);
                                $title = get_the_title($course);
                                $provider = get_the_author_meta('display_name', $course->post_author);
                                $course_link = get_permalink($course);
                                $lessons_count = count(learndash_get_course_lessons_list($course_id));
                                $image = get_the_post_thumbnail_url($course);
                                $atts = ['per_page' => '9',];
                                $progress = learndash_user_get_course_progress(get_current_user_id(),  $course_id,  'legacy');
                                $completed = ($progress["completed"] / $progress["total"]) * 100 . "%";
                                $certificate = learndash_get_course_certificate_link($course_id,  get_current_user_id());

                                // ld_update_course_access($current_user->ID, get_the_ID($course));
                                // $course_attrs = CoursesFunctions::get_post_attr($course, $atts);
                                // extract($course_attrs);
                        ?>
                                <div id="<? echo $course_id ?>" class="card">
                                <div class="course-card-pattern">
                                            <img src="<? echo $image ?>" alt="course-thumbnail">
                                        </div>
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
                        if (!empty($completed_courses['results'])) {
                            foreach ($completed_courses['results'] as $course) {
                                $course_id = $course->post_id;
                                $title = get_the_title($course);
                                $provider = get_the_author_meta('display_name', $course->post_author);
                                $course_link = get_permalink($course);
                                $progress = learndash_user_get_course_progress(get_current_user_id(),  $course_id,  'legacy');
                                $completed = ($progress["completed"] / $progress["total"]) * 100 . "%";
                                $lessons_count = $progress["total"];
                                $image = get_the_post_thumbnail_url($course);
                                $cert = learndash_certificate_details($course_id, get_current_user_id())
                        ?>
                        
                                <a href="<? echo $course_link ?>" class="course-card">
                                    <div class="card">                        
                                    <div class="course-card-pattern">
                                            <img src="<? echo $image ?>" alt="course-thumbnail">
                                        </div>
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
                                </a>
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
