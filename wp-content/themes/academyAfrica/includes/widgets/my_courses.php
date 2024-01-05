<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

use AcademyAfrica\Theme\Courses\CoursesFunctions;

class Academy_Africa_My_Courses extends \Elementor\Widget_Base
{

    public function get_name()
    {
        return 'My Courses';
    }

    public function get_style_depends()
    {
        return ['academy-africa-my-courses', 'academy-africa'];
    }

    public function get_script_depends()
    {
        return ['academy-africa_my_courses'];
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
        return [];
    }
    public function replace_course_info($input, $course_id)
    {
        $pattern = '/\[courseinfo\b/';
        $replacement = '[courseinfo course_id=".' . $course_id . '"';
        $output = preg_replace($pattern, $replacement, $input);

        return $output;
    }


    public function sort_params()
    {
        return array(
            "date-asc" => "GREATEST(ld_user_activity.activity_started, ld_user_activity.activity_completed) ASC",
            "" => "GREATEST(ld_user_activity.activity_started, ld_user_activity.activity_completed) DESC"
        );
    }

    public function get_completed_courses()
    {
        $user_id = get_current_user_id();
        $courses = learndash_user_get_enrolled_courses($user_id);
        $orgs = $this->get_query_param('organization');
        $instructors = $this->get_query_param('instructor');
        $sort = $this->get_query_param('sort')[0];
        $current_page = $this->get_query_param("page") ? $this->get_query_param("page") : 1;
        $user_id = get_current_user_id();
        $order_by = $this->sort_params()[$sort ?? 'date-desc'];
        $args = array(
            'post_types' => 'sfwd-courses',
            'activity_types' => 'course',
            'activity_status' => 'COMPLETED',
            'per_page' => '9',
            'paged' => $current_page,
            'organization' => $orgs,
            'instructor' => $instructors,
            "orderby_order" => $order_by,
            'user_ids' => array($user_id),
            'post_ids' => $courses,
        );
        return learndash_reports_get_activity($args);
    }

    public function get_enrolled_courses()
    {
        $orgs = $this->get_query_param('organization');
        $instructors = $this->get_query_param('instructor');
        $sort = $this->get_query_param('sort')[0];
        $current_page = $this->get_query_param("courses_page") ? $this->get_query_param("courses_page") : 1;
        $course_ids = learndash_user_get_enrolled_courses(get_current_user_id());
        $order_by = $this->sort_params()[$sort ?? 'date-desc'];
        $args = array(
            'post_types' => 'sfwd-courses',
            'activity_types' => 'course',
            'activity_status' => 'IN_PROGRESS',
            'organization' => $orgs,
            'instructor' => $instructors,
            "orderby_order" => $order_by,
            'per_page' => '9',
            'paged' => $current_page,
            'post_ids' => $course_ids,
            'user_ids' => array(get_current_user_id()),
        );
        return learndash_reports_get_activity($args);
    }

    protected function register_controls()
    {
    }

    protected function render()
    {
        $settings = $this->get_settings_for_display();
        $user_id = get_current_user_id();
        $filter_by = "Filter by:";
        $filter_options = CoursesFunctions::get_filter_by();
        $completed_courses = $this->get_completed_courses();
        $enrolled_courses = $this->get_enrolled_courses();
        $enrolled = $enrolled_courses["results"] ?? [];
        $free_tag_key = "Download the certificate for free after completing the course";
        $paid_tag_key = "Download the certificate for free after completing the course";
        $courses_title = "All Courses";
        $courses_description = "we are happy to say All courses are free to complete";
        $current_user = wp_get_current_user();
        $certificate_pagination = $completed_courses["pager"] ?? [];
        $my_courses_pagination = $enrolled_courses["pager"] ?? [];
        $sort = $this->get_query_param('sort');
        $sort_by = "Sort By";
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
        $user = array(
            "first_name" => get_user_meta($user_id, 'first_name', true),
            "last_name" => get_user_meta($user_id, 'last_name', true),
        );
        ?>
        <main class="body">
            <?php get_template_part('template-parts/filter_bar', 'template', [
                'filter_by' => $filter_by,
                'filter_options' => $filter_options,
                'sort_by' => $sort_by,
                'sort_options' => $sort_options,
                'sort' => $sort
            ]); ?>
            <div class="main" id="all-courses">
                <section class="incomplete-courses">
                    <h4 class="cfa-title">
                        Welcome <strong style="text-transform: capitalize;">
                            <? echo $current_user->display_name; ?>
                        </strong>
                    </h4>
                    <div class="filter-section">
                        <div class="sort">
                            <div class="label">
                                Sort by:
                            </div>
                            <select name="sort" id="sort" class="select" onchange="changeSort(this)">
                                <option value="newest">Most Recent</option>
                                <? $selected = $sort === "oldest" ? 'selected="selected"' : "" ?>
                                <option <? echo $selected ?> value="oldest">Oldest</option>
                            </select>
                        </div>
                        <div class="filter">
                            <button id="courses-mobile-filter" class="button primary filter-btn">
                                <svg width="17" height="16" viewBox="0 0 17 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <g clip-path="url(#clip0_11905_79908)">
                                        <path d="M15.1693 2H1.83594L7.16927 8.30667V12.6667L9.83594 14V8.30667L15.1693 2Z"
                                            stroke="#EFF0FD" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
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
                    <? if (!empty($enrolled)) {
                        ?>
                        <p class="description">
                            Complete your courses
                        </p>
                        <div class="content">
                            <?
                            foreach ($enrolled as $er) {
                                $course_id = $er->post_id;
                                $course = get_post($course_id);
                                $title = get_the_title($course);
                                $provider = get_the_author_meta('display_name', $course->post_author);
                                $course_link = get_permalink($course);

                                $image = get_the_post_thumbnail_url($course);
                                $atts = ['per_page' => '9',];
                                $progress = learndash_user_get_course_progress(get_current_user_id(), $course_id, 'legacy');
                                $completed = ((string) floor(($progress["completed"] / $progress["total"]) * 100)) . "%";
                                $lessons_count = $progress["total"];

                                ?>
                                <a href="<? echo $course_link ?>">
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
                                                by
                                                <? echo $provider ?>
                                            </p>
                                            <p class="lessons-count">
                                                <? echo $lessons_count ?> lessons
                                            </p>
                                            <div class="progress-bar">
                                                <div style="width: <? echo $completed ?>"></div>
                                            </div>
                                            <div class="card-footer">
                                                <p>Enrolled</p>
                                                <p>
                                                    <? echo $completed ?> Completed
                                                </p>
                                            </div>
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
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16"
                                            fill="none">
                                            <path d="M10 12L6 8L10 4" stroke="#616582" stroke-width="2" stroke-linecap="round"
                                                stroke-linejoin="round" />
                                        </svg>
                                    </a>
                                </li>

                                <?
                                $current_url_params = $_GET;
                                $current_page = (int) $current_url_params["courses_page"] ?? 1;
                                $next_page = $current_page + 1;
                                $previous_page = $current_page - 1;
                                $pr_2 = $previous_page - 1;
                                $next_2 = $next_page + 2;
                                ?>
                                <?php for ($i = 1; $i <= $my_courses_pagination['total_pages']; $i++): ?>
                                    <?
                                    $current_url_params["courses_page"] = $i;
                                    $new_url = add_query_arg($current_url_params, home_url($_SERVER['REQUEST_URI']));
                                    if ($i === $previous_page || $i === $next_page || $i === $my_courses_pagination['total_pages'] || $i === 1 || $i === $current_page) {
                                        ?>
                                        <li class="page-item"><a class="page-link" href="<? echo $new_url ?>">
                                                <?php echo $i; ?>
                                            </a></li>
                                        <?
                                    }
                                    if (($i === $next_2 && $next_2 < $my_courses_pagination['total_pages']) || $i === $pr_2 && $i > 1) {
                                        ?>
                                        <li style="margin-top: 6px">...</li>
                                        <?
                                    }

                                endfor; ?>

                                <!-- Next page link -->
                                <li class="page-item">
                                    <a class="page-link" href="#">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16"
                                            fill="none">
                                            <path d="M6 12L10 8L6 4" stroke="#616582" stroke-width="2" stroke-linecap="round"
                                                stroke-linejoin="round" />
                                        </svg>
                                    </a>
                                </li>

                            </ul>
                        </div>
                    <? } ?>
                </section>
                <? if (!empty($completed_courses['results'])) { ?>
                    <section class="your-certificates">
                        <h4 class="your-certificates-title">
                            Your Certificates
                        </h4>
                        <div class="content">
                            <?
                            if (!empty($completed_courses['results'])) {
                                foreach ($completed_courses['results'] as $course) {
                                    $course_id = $course->post_id;
                                    $certificate_id = learndash_get_setting($course_id, 'certificate');
                                    $cert_post = get_post($certificate_id);
                                    $title = get_the_title($course);
                                    $provider = get_the_author_meta('display_name', $course->post_author);
                                    $course_link = get_permalink($course_id);
                                    $progress = learndash_user_get_course_progress(get_current_user_id(), $course_id, 'legacy');
                                    $completed = floor(($progress["completed"] / $progress["total"]) * 100);
                                    $lessons_count = $progress["total"];
                                    $image = get_the_post_thumbnail_url($course);
                                    // $certificate_link = learndash_get_course_certificate_link($course_id, get_current_user_id());
                                    ?>

                                    <div class="cert-pdf" id="<? echo $course_id ?>">
                                        <? $cert_content = $this->replace_course_info($cert_post->post_content, $course_id) ?>
                                        <? echo do_shortcode($cert_content) ?>
                                    </div>
                                    <div class="card">
                                        <div class="course-card-pattern">
                                            <img src="<? echo $image ?>" alt="course-thumbnail">
                                        </div>
                                        <div class="card-content">
                                            <a href="<? echo $course_link ?>">
                                                <div class="card-title">
                                                    <p>
                                                        <? echo $title ?>
                                                    </p>
                                                </div>
                                            </a>
                                            <p class="provider">
                                                by
                                                <? echo $provider ?>
                                            </p>
                                            <p class="lessons-count">
                                                <? echo $lessons_count ?> lessons
                                            </p>
                                            <div class="completed-progress-bar">
                                            </div>
                                            <div class="card-footer">
                                                <p>Certificate Achieved</p>
                                                <div class="icons">
                                                    <img onclick="convertHTMLtoPDF('<? echo $course_id ?>', '<? echo $title ?>')"
                                                        src="/wp-content/plugins/academy-africa/includes/assets/images/download.svg"
                                                        style="cursor: pointer;" alt="download" />

                                                    <img src="/wp-content/plugins/academy-africa/includes/assets/images/share.svg"
                                                        alt="share" />
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
                            <ul class="pagination">

                                <!-- Previous page link -->
                                <li class="page-item">
                                    <a class="page-link" href="#">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16"
                                            fill="none">
                                            <path d="M10 12L6 8L10 4" stroke="#616582" stroke-width="2" stroke-linecap="round"
                                                stroke-linejoin="round" />
                                        </svg>
                                    </a>
                                </li>

                                <!-- Page links -->
                                <?php for ($i = 1; $i <= $certificate_pagination['total_pages']; $i++): ?>
                                    <?
                                    $current_url_params = $_GET;
                                    $current_url_params["courses_page"] = $i;
                                    $new_url = add_query_arg($current_url_params, home_url($_SERVER['REQUEST_URI']));
                                    ?>
                                    <li class="page-item"><a class="page-link" href="<? echo $new_url ?>">
                                            <?php echo $i; ?>
                                        </a></li>
                                <?php endfor; ?>

                                <!-- Next page link -->
                                <li class="page-item">
                                    <a class="page-link" href="#">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16"
                                            fill="none">
                                            <path d="M6 12L10 8L6 4" stroke="#616582" stroke-width="2" stroke-linecap="round"
                                                stroke-linejoin="round" />
                                        </svg>
                                    </a>
                                </li>

                            </ul>
                        </div>
                    </section>
                    <?

                }
                ?>
            </div>
            <script type="text/javascript">
                function convertHTMLtoPDF(id, courseTitle) {
                    const { jsPDF } = window.jspdf;
                    const element = document.getElementById(id);
                    if (element) {
                        let doc = new jsPDF('l', 'mm');
                        const pdfjs = element.querySelector("#certificate");
                        const width = doc.internal.pageSize.getWidth();
                        const height = doc.internal.pageSize.getHeight();
                        console.log({ width, height })
                        doc.html(pdfjs, {
                            callback: function (doc) {
                                doc.save(`<? echo $user['first_name'] . ' ' . $user['first_name'] ?> | ${courseTitle}.pdf`);
                            },
                            width: width,
                            height,
                            windowWidth: 891,
                            html2canvas: { scale: 0.954 },
                        });
                    }
                }            
            </script>
        </main>
        <?
    }
}
