<?php

/*
* Template Name: Single Lesson
* Template Post Type: sfwd-lessons
*/

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

$lesson_id = get_the_ID();
$course_id = learndash_get_course_id($lesson_id);
$user_id   = get_current_user_id();

do_action('qm/start', 'lesson:init');

if (!$course_id) {
    do_action('qm/error', 'lesson.php: learndash_get_course_id() returned null for lesson_id={id}', ['id' => $lesson_id]);
}

$cs_cache_key  = 'course_status_u' . $user_id . '_c' . $course_id;
$course_status = \AcademyAfrica\Theme\Cache\Cache::get($cs_cache_key);
if (false === $course_status) {
    $course_status = learndash_course_status($course_id);
    \AcademyAfrica\Theme\Cache\Cache::set($cs_cache_key, $course_status, 5 * MINUTE_IN_SECONDS);
}
$course_url    = get_permalink($course_id);
$course        = get_post($course_id);
if (!$course) {
    do_action('qm/error', 'lesson.php: get_post() returned null for course_id={id}', ['id' => $course_id]);
}

// Lesson list — shared cache with course.php (user-agnostic, just count + structure)
$lessons = \AcademyAfrica\Theme\Cache\Cache::get('course_lessons_' . $course_id);
if (false === $lessons) {
    do_action('qm/start', 'lesson:fetch_lessons');
    $lessons = learndash_get_course_lessons_list($course_id, 0) ?: [];
    \AcademyAfrica\Theme\Cache\Cache::set('course_lessons_' . $course_id, $lessons, HOUR_IN_SECONDS);
    do_action('qm/stop', 'lesson:fetch_lessons');
    do_action('qm/debug', 'lesson:fetch_lessons: DB fetch {count} lessons for course {id}', [
        'count' => count($lessons),
        'id'    => $course_id,
    ]);
}

$lesson          = get_post($lesson_id);
$has_assignments = learndash_lesson_hasassignments($lesson);
$topics          = learndash_get_topic_list($lesson_id, $course_id) ?: [];

do_action('qm/stop', 'lesson:init');
?>

<style>
    .entry-title {
        display: none;
    }
</style>

<div class="sfwd-container wysiwyg">
    <div class="sfwd-large-screen">
        <div class="content">
            <div class="progress">
                <div class="back-to-course">
                    <a href="<?php echo esc_url($course_url); ?>" class="link">
                        <svg width="8" height="14" viewBox="0 0 8 14" fill="none">
                            <path d="M7 13L1 6.93015L6.86175 1" stroke="#1F1F1F" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        <div class="back-to-course__text">Back to Course Curriculum</div>
                    </a>
                </div>
                <div class="course-details">
                    <div class="course-title">
                        <?php echo esc_html($course->post_title); ?>
                    </div>
                    <div class="progress-bar">
                        <div class="lesson-count">
                            <?php echo count($lessons); ?> Lessons
                        </div>
                        <?php
                        $cp_cache_key = 'course_progress_u' . $user_id . '_c' . $course_id;
                        $cp_output    = \AcademyAfrica\Theme\Cache\Cache::get($cp_cache_key);
                        if (false === $cp_output) {
                            $cp_output = do_shortcode('[learndash_course_progress]');
                            \AcademyAfrica\Theme\Cache\Cache::set($cp_cache_key, $cp_output, 5 * MINUTE_IN_SECONDS);
                        }
                        echo $cp_output;
                        ?>
                    </div>
                </div>
                <div class='course-carriculum'>
                    <?php
                    // Cache per user per course — LearnDash updates completion marks via
                    // AJAX so page reloads don't need a fresh render on every request.
                    do_action('qm/start', 'lesson:course_content_shortcode');
                    $cc_cache_key = 'course_content_u' . $user_id . '_c' . $course_id;
                    $cc_output    = \AcademyAfrica\Theme\Cache\Cache::get($cc_cache_key);
                    if (false === $cc_output) {
                        $cc_output = do_shortcode('[course_content course_id="' . $course_id . '"]');
                        \AcademyAfrica\Theme\Cache\Cache::set($cc_cache_key, $cc_output, 5 * MINUTE_IN_SECONDS);
                    }
                    echo $cc_output;
                    do_action('qm/stop', 'lesson:course_content_shortcode');
                    ?>
                </div>
            </div>
            <div class="sfwd-lessons">
                <div class="sfwd-lessons__navigation">
                    <?php
                    $previous_lesson = learndash_previous_post_link(url: true);
                    if ($previous_lesson) {
                        echo "<div class='sfwd-lessons__navigation__previous nav-link'>";
                        echo "<a href='" . esc_url($previous_lesson) . "' class='link'>";
                        echo "<div class='sfwd-lessons__navigation__previous__text'>Previous</div>";
                        echo "</a></div>";
                    }
                    $next_lesson = learndash_next_post_link(url: true);
                    if ($next_lesson) {
                        echo "<div class='sfwd-lessons__navigation__next nav-link'>";
                        echo "<a href='" . esc_url($next_lesson) . "' class='link'>";
                        echo "<div class='sfwd-lessons__navigation__next__text'>Next Lesson</div>";
                        echo "</a></div>";
                    }
                    ?>
                </div>
                <div class="sfwd-lessons__title">
                    <div class="sfwd-lessons__title__text"><?php the_title(); ?></div>
                </div>
                <div class="sfwd-lessons__content">
                    <?php echo do_shortcode($lesson->post_content); ?>
                </div>
                <?php if ($has_assignments) : ?>
                    <div class="assignment-upload">
                        <div class="sfwd-lessons__content">
                            <?php
                            learndash_get_template_part(
                                'assignment/listing.php',
                                [
                                    'course_step_post' => $lesson,
                                    'user_id'          => $user_id,
                                    'course_id'        => $course_id,
                                ],
                                true
                            );
                            ?>
                        </div>
                    </div>
                <?php endif; ?>
                <div class="sfwd-lessons__footer">
                    <hr class="sfwd-lessons__navigation__divider" />
                    <?php
                    if (!is_array($topics)) {
                        $complete_button = learndash_mark_complete($lesson);
                        if ($complete_button) {
                            echo "<div class='sfwd-lessons__footer__complete'>" . $complete_button . "</div>";
                        }
                        if ($course_status == 'Completed') {
                            echo "<div class='sfwd-lessons__footer__certificate'>";
                            echo "<a href='" . esc_url($course_url . '?certificate=true') . "' class='certificate_download'>Download Certificate</a>";
                            echo "</div>";
                        }
                    ?>
                        <script>
                            document.querySelector('.sfwd-mark-complete').addEventListener('submit', function(e) {
                                var url = e.target.action.split('#')[0];
                                e.target.action = url;
                            });
                        </script>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>
