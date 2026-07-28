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
            "date-desc" => "GREATEST(ld_user_activity.activity_started, ld_user_activity.activity_completed) DESC"
        );
    }

    public function get_completed_courses()
    {
        $user_id = get_current_user_id();
        $courses = learndash_user_get_enrolled_courses($user_id);
        $orgs = $this->get_query_param('organization');
        $instructors = $this->get_query_param('instructor');
        $sort = $this->get_query_param('sort');
        $sort = !empty($sort) ? $sort[0] : 'date-desc';
        $current_page = $this->get_query_param("page");
        $current_page = !empty($current_page) ? $current_page[0] : 1;
        $user_id = get_current_user_id();
        $sort_params = $this->sort_params();
        $order_by = $sort_params[$sort] ?? $sort_params['date-desc'];
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
            'post__in' => $courses,
        );
        return learndash_reports_get_activity($args, $user_id);
    }

    public function get_enrolled_courses()
    {
        $orgs = $this->get_query_param('organization');
        $instructors = $this->get_query_param('instructor');
        $sort = $this->get_query_param('sort');
        $sort = !empty($sort) ? $sort[0] : 'date-desc';
        $current_page = $this->get_query_param("courses_page");
        $current_page = !empty($current_page) ? $current_page[0] : 1;
        $course_ids = learndash_user_get_enrolled_courses(get_current_user_id());
        $sort_params = $this->sort_params();
        $order_by = $sort_params[$sort] ?? $sort_params['date-desc'];
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

    protected function register_controls() {}

    protected function render()
    {
        wp_enqueue_script('canvas', 'https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js', [], ACADEMY_AFRICA_VERSION);
        wp_enqueue_script('jsPDF', 'https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js', [], ACADEMY_AFRICA_VERSION);
        wp_enqueue_script('html2pdf', 'https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js', [], ACADEMY_AFRICA_VERSION);

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
        // Only date ordering is offered here: My Courses lists come from
        // learndash_reports_get_activity(), which sorts by activity timestamps
        // (see sort_params()) and cannot order by course title. Offering a
        // name sort would produce an unsupported ordering.
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
            ]
        ];
        $user = array(
            "first_name" => get_user_meta($user_id, 'first_name', true),
            "last_name" => get_user_meta($user_id, 'last_name', true),
        );
?>
        <main class="body">
            <div class="desktop-only">
                <?php get_template_part('template-parts/filter_bar', 'template', [
                    'filter_by' => $filter_by,
                    'filter_options' => $filter_options,
                    'sort_by' => $sort_by,
                    'sort_options' => $sort_options,
                    'sort' => $sort
                ]); ?>
            </div>
            <div class="main" id="all-courses">
                <section class="incomplete-courses">
                    <h4 class="cfa-title">
                        Welcome <strong style="text-transform: capitalize;">
                            <?php echo $current_user->display_name; ?>
                        </strong>
                    </h4>
                    <div class="filter-by-language">
                        <div class="label">
                            <?php echo __('Choose a course Language:', 'academy-africa'); ?>
                        </div>
                        <div class="language-buttons">
                            <?php
                            $languages =  [
                                'all' => __('All', 'academy-africa'),
                                'English' => __('English', 'academy-africa'),
                                'French' => __('French', 'academy-africa'),
                                'Arabic' => __('Arabic', 'academy-africa'),
                            ];
                            foreach ($languages as $language_code => $language_name) {

                            ?>
                                <button id="<?php echo $language_code ?>" class="button medium ld-button"
                                    onclick="filterByLanguage('<?php echo $language_code; ?>')">
                                    <?php echo $language_name; ?>
                                </button>
                            <?php
                            }
                            ?>
                        </div>
                    </div>
                    <div class="mobile-only">
                        <?php get_template_part('template-parts/filter_bar', 'template', [
                            'filter_by' => $filter_by,
                            'filter_options' => $filter_options,
                            'sort_by' => $sort_by,
                            'sort_options' => $sort_options,
                            'sort' => $sort
                        ]); ?>

                    </div>
                    <div class="filter-section">
                        <div class="sort">
                            <div class="label">
                                <?php echo $sort_by ?>
                            </div>
                            <select name="sort" id="courses-sort" class="select" onchange="sortCourses(this)">
                                <?php
                                foreach ($sort_options as $key => $option) {
                                    $selected = $sort == $key ? "selected" : "";
                                ?>
                                    <option <?php echo $selected ?> value="<?php echo $key ?>"><?php echo $option["name"] ?></option>
                                <?php
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <?php if (!empty($enrolled)) {
                    ?>
                        <p class="description">
                            Complete your courses
                        </p>
                        <div class="content">
                            <?php
                            foreach ($enrolled as $er) {
                                $course_id = $er->post_id;
                                $course = get_post($course_id);
                                $title = get_the_title($course);
                                $authors = get_coauthors($course->ID);
                                if (!empty($authors)) {
                                    $first_name = get_the_author_meta('first_name', $authors[0]->ID);
                                    $last_name = get_the_author_meta('last_name', $authors[0]->ID);
                                    $provider = (!empty($first_name) && !empty($last_name)) ? $first_name . ' ' . $last_name : $authors[0]->display_name;
                                    if (count($authors) > 1) {
                                        $provider .= ' + ' . (count($authors) - 1) . ' more';
                                    }
                                } else {
                                    $provider = '';
                                }
                                $course_link = get_permalink($course);
                                $course_thumbnail = get_the_post_thumbnail_url($course);
                                $mooc_logo = get_stylesheet_directory_uri() . '/assets/images/mooc-logo-blue.svg';
                                $image = $course_thumbnail ? $course_thumbnail : $mooc_logo;
                                $atts = ['per_page' => '9',];
                                $progress = learndash_user_get_course_progress(get_current_user_id(), $course_id, 'legacy');
                                $completed_steps = (int) ($progress['completed'] ?? 0);
                                $total_steps = (int) ($progress['total'] ?? 0);
                                $completed = ((string) floor(($completed_steps / max($total_steps, 1)) * 100)) . "%";
                                $lessons_count = $total_steps;

                            ?>
                                <a href="<?php echo $course_link ?>">
                                    <div id="<?php echo $course_id ?>" class="card">
                                        <div class="course-card-pattern">
                                            <img src="<?php echo $image ?>" alt="course-thumbnail">
                                        </div>
                                        <div class="card-content">
                                            <div class="card-title">
                                                <p>
                                                    <?php echo $title ?>
                                                </p>
                                            </div>
                                            <p class="provider">
                                                by
                                                <?php echo $provider ?>
                                            </p>
                                            <p class="lessons-count">
                                                <?php echo $lessons_count ?> lessons
                                            </p>
                                            <div class="progress-bar">
                                                <div style="width: <?php echo $completed ?>"></div>
                                            </div>
                                            <div class="card-footer">
                                                <p>Enrolled</p>
                                                <p>
                                                    <?php echo $completed ?> Completed
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </a>

                            <?php
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

                                <?php
                                $current_url_params = $_GET;
                                $current_page = isset($current_url_params["courses_page"]) ? (int) $current_url_params["courses_page"] : 1;
                                $next_page = $current_page + 1;
                                $previous_page = $current_page - 1;
                                $pr_2 = $previous_page - 1;
                                $next_2 = $next_page + 2;
                                ?>
                                <?php for ($i = 1; $i <= ($my_courses_pagination['total_pages'] ?? 0); $i++) : ?>
                                    <?php
                                    $current_url_params["courses_page"] = $i;
                                    $new_url = add_query_arg($current_url_params, home_url($_SERVER['REQUEST_URI']));
                                    if ($i === $previous_page || $i === $next_page || $i === ($my_courses_pagination['total_pages'] ?? 0) || $i === 1 || $i === $current_page) {
                                    ?>
                                        <li class="page-item"><a class="page-link" href="<?php echo $new_url ?>">
                                                <?php echo $i; ?>
                                            </a></li>
                                    <?php
                                    }
                                    if (($i === $next_2 && $next_2 < ($my_courses_pagination['total_pages'] ?? 0)) || $i === $pr_2 && $i > 1) {
                                    ?>
                                        <li style="margin-top: 6px">...</li>
                                <?php
                                    }

                                endfor; ?>

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
                    <?php } ?>
                </section>
                <?php
                // Only completed courses that have a published certificate belong in
                // this section. Filter up front so the header never renders with zero
                // cards, and so the loop body can assume a valid cert (#46 review).
                $certificate_courses = array_filter(
                    $completed_courses['results'] ?? array(),
                    function ($course) {
                        return (bool) academyafrica_get_course_certificate_post($course->post_id);
                    }
                );
                if (!empty($certificate_courses)) { ?>
                    <section class="your-certificates">
                        <h4 class="your-certificates-title">
                            Your Certificates
                        </h4>
                        <div class="content">
                            <?php
                            foreach ($certificate_courses as $key => $course) {
                                    $course_id = $course->post_id;
                                    $cert_post = academyafrica_get_course_certificate_post($course_id);
                                    $title = get_the_title($course);
                                    $authors = get_coauthors($course_id);
                                    if (!empty($authors)) {
                                        $first_name = get_the_author_meta('first_name', $authors[0]->ID);
                                        $last_name = get_the_author_meta('last_name', $authors[0]->ID);
                                        $course_author = (!empty($first_name) && !empty($last_name)) ? $first_name . ' ' . $last_name : $authors[0]->display_name;
                                        if (count($authors) > 1) {
                                            $course_author .= ' + ' . (count($authors) - 1) . ' more';
                                        }
                                    } else {
                                        $course_author = '';
                                    }
                                    $course_link = add_query_arg("certificate", 1, get_permalink($course_id));
                                    $progress = learndash_user_get_course_progress(get_current_user_id(), $course_id, 'legacy');
                                    $completed_steps = (int) ($progress['completed'] ?? 0);
                                    $total_steps = (int) ($progress['total'] ?? 0);
                                    $completed = (int) floor(($completed_steps / max($total_steps, 1)) * 100);
                                    $lessons_count = $total_steps;
                                    $course_thumbnail = get_the_post_thumbnail_url($course);
                                    $mooc_logo = get_stylesheet_directory_uri() . '/assets/images/mooc-logo-blue.svg';
                                    $image = $course_thumbnail ? $course_thumbnail : $mooc_logo;
                                    // $certificate_link = learndash_get_course_certificate_link($course_id, get_current_user_id());
                            ?>

                                    <div class="cert-pdf" id="<?php echo $course_id ?>">
                                        <?php
                                        if ($cert_post instanceof WP_Post) {
                                            $cert_content = $this->replace_course_info($cert_post->post_content, $course_id);
                                            echo do_shortcode($cert_content);
                                        }
                                        ?>
                                    </div>
                                    <div>
                                        <div class="card">
                                            <div class="course-card-pattern">
                                                <img src="<?php echo $image ?>" alt="course-thumbnail">
                                            </div>
                                            <div class="card-content">
                                                <a href="<?php echo $course_link ?>">
                                                    <div class="card-title">
                                                        <p>
                                                            <?php echo $title ?>
                                                        </p>
                                                    </div>
                                                </a>
                                                <p class="provider">
                                                    by
                                                    <?php echo $course_author ?>
                                                </p>
                                                <p class="lessons-count">
                                                    <?php echo $lessons_count ?> lessons
                                                </p>
                                                <div class="completed-progress-bar">
                                                </div>
                                                <div class="card-footer">
                                                    <p>Certificate Achieved</p>
                                                    <div class="icons">
                                                        <?php
                                                        $cert = learndash_get_course_certificate_link($course_id);
                                                        ?>
                                                        <a href="<?php echo $cert ?>" download>
                                                            <img src="/wp-content/plugins/academy-africa/includes/assets/images/download.svg" style="cursor: pointer;" alt="download" />
                                                        </a>

                                                        <img src="/wp-content/plugins/academy-africa/includes/assets/images/share.svg" alt="share" />
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                    </div>

                            <?php
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
                                <?php for ($i = 1; $i <= ($certificate_pagination['total_pages'] ?? 0); $i++) : ?>
                                    <?php
                                    $current_url_params = $_GET;
                                    $current_url_params["courses_page"] = $i;
                                    $new_url = add_query_arg($current_url_params, home_url($_SERVER['REQUEST_URI']));
                                    ?>
                                    <li class="page-item"><a class="page-link" href="<?php echo $new_url ?>">
                                            <?php echo $i; ?>
                                        </a></li>
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
                    </section>
                <?php

                }
                ?>
            </div>
            <script type="text/javascript">
                function convertHTMLtoPDF(id, courseTitle) {
                    const {
                        jsPDF
                    } = window.jspdf;
                    const element = document.getElementById(id);
                    if (element) {
                        let doc = new jsPDF('l', 'mm');
                        const pdfjs = element.querySelector("#certificate");
                        const width = doc.internal.pageSize.getWidth();
                        const height = doc.internal.pageSize.getHeight();
                        doc.html(pdfjs, {
                            callback: function(doc) {
                                doc.save(`<?php echo $user['first_name'] . ' ' . $user['first_name'] ?> | ${courseTitle}.pdf`);
                            },
                            width: width,
                            height,
                            windowWidth: 891,
                            html2canvas: {
                                scale: 0.954
                            },
                        });
                    }
                }
            </script>
            <script>
                function filterByLanguage(language) {
                    if (language == 'all') {
                        language = '';
                    }
                    const urlParams = new URLSearchParams(window.location.search);
                    urlParams.set('language', language);
                    window.location.search = urlParams.toString();
                }
                document.addEventListener('DOMContentLoaded', function() {
                    const urlParams = new URLSearchParams(window.location.search);
                    const selectedLanguage = urlParams.get('language');
                    if (selectedLanguage) {
                        const button = document.getElementById(selectedLanguage);
                        if (button) {
                            button.classList.add('primary');
                        }
                    } else {
                        const allButton = document.getElementById('all');
                        if (allButton) {
                            allButton.classList.add('primary');
                        }
                    }
                });
            </script>
        </main>
<?php
    }
}
