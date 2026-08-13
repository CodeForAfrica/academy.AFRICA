<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

$quizId    = get_the_ID();
$course_id = learndash_get_course_id($quizId);
$user_id   = get_current_user_id();

do_action('qm/start', 'quiz:init');

if (!$course_id) {
    do_action('qm/error', 'quiz.php: learndash_get_course_id() returned null for quiz_id={id}', ['id' => $quizId]);
}

$course_url = get_permalink($course_id);
$course     = get_post($course_id);
if (!$course) {
    do_action('qm/error', 'quiz.php: get_post() returned null for course_id={id}', ['id' => $course_id]);
}

// Lesson list — shared cache with course.php (user-agnostic, just count + structure)
$lessons = \AcademyAfrica\Theme\Cache\Cache::get('course_lessons_' . $course_id);
if (false === $lessons) {
    do_action('qm/start', 'quiz:fetch_lessons');
    $lessons = learndash_get_course_lessons_list($course_id, 0) ?: [];
    \AcademyAfrica\Theme\Cache\Cache::set('course_lessons_' . $course_id, $lessons, HOUR_IN_SECONDS);
    do_action('qm/stop', 'quiz:fetch_lessons');
    do_action('qm/debug', 'quiz:fetch_lessons: DB fetch {count} lessons for course {id}', [
        'count' => count($lessons),
        'id'    => $course_id,
    ]);
}

$is_quiz = get_post_type($quizId) === 'sfwd-quiz';

do_action('qm/stop', 'quiz:init');
?>

<style>
    .entry-title {
        display: none;
    }
</style>

<div class="sfwd-container quiz-page wysiwyg">
    <div class="sfwd-large-screen wysiwyg">
        <div class="content">
            <?php if ($is_quiz) : ?>
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
                        do_action('qm/start', 'quiz:course_content_shortcode');
                        $cc_cache_key = 'course_content_u' . $user_id . '_c' . $course_id;
                        $cc_output    = \AcademyAfrica\Theme\Cache\Cache::get($cc_cache_key);
                        if (false === $cc_output) {
                            $cc_output = do_shortcode('[course_content course_id="' . $course_id . '"]');
                            \AcademyAfrica\Theme\Cache\Cache::set($cc_cache_key, $cc_output, 5 * MINUTE_IN_SECONDS);
                        }
                        echo $cc_output;
                        do_action('qm/stop', 'quiz:course_content_shortcode');
                        ?>
                    </div>
                </div>
            <?php endif; ?>

            <div class="sfwd-lessons <?php echo !$is_quiz ? 'not-quiz' : ''; ?>">
                <?php if ($is_quiz) : ?>
                    <div class="sfwd-lessons__title">
                        <div class="sfwd-lessons__title__text"><?php the_title(); ?></div>
                    </div>
                <?php endif; ?>
                <div class="sfwd-lessons__content">
                    <?php
                    if ($show_content) :
                        learndash_get_template_part(
                            'modules/tabs.php',
                            [
                                'course_id' => $course_id,
                                'post_id'   => $quiz_post->ID,
                                'user_id'   => $user_id,
                                'content'   => $content,
                                'materials' => $materials,
                                'context'   => 'quiz',
                            ],
                            true
                        );

                        if ($attempts_left) :
                            do_action('learndash-quiz-actual-content-before', $quiz_post->ID, $course_id, $user_id);
                            echo $quiz_content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Post content
                            do_action('learndash-quiz-actual-content-after', $quiz_post->ID, $course_id, $user_id);
                        else :
                            do_action('learndash-quiz-attempts-alert-before', $quiz_post->ID, $course_id, $user_id);
                            // Branded in place of learndash_get_template_part('modules/alert.php', ...) —
                            // that component has no unscoped base styling here, so it rendered as
                            // plain, unstyled text.
                            academyafrica_render_ld_notice([
                                'type'    => 'warning',
                                'icon'    => 'alert',
                                'message' => sprintf(
                                    esc_html_x('You have already taken this %1$s %2$d time(s) and may not take it again.', 'placeholders: quiz, attempts count', 'learndash'),
                                    learndash_get_custom_label_lower('quiz'),
                                    $attempts_count
                                ),
                            ]);
                            do_action('learndash-quiz-attempts-alert-after', $quiz_post->ID, $course_id, $user_id);
                        endif;
                    endif;
                    ?>
                </div>
                <?php if ($is_quiz) : ?>
                    <div class="sfwd-lessons__footer">
                        <hr class="sfwd-lessons__navigation__divider" />
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
