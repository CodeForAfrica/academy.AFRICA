<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

$quizId = get_the_ID();
$course_id = learndash_get_course_id($quizId);

$course_url = get_permalink($course_id);
$course = get_post($course_id);
$lessons = learndash_get_course_lessons_list($course_id);
$parent_post = get_post_ancestors($quizId);
$post_type = get_post_type($quizId);
$is_quiz = $post_type == 'sfwd-quiz';
?>

<style>
    .entry-title {
        display: none;
    }
</style>

<div class="sfwd-container quiz-page wysiwyg">
    <!-- <div class='sfwd-small-screen'>
        <div class="title">
            <div class="cfa-title"><?php the_title(); ?></div>
            <h1>Quiz</h1>
        </div>
        <div class='helper'>
            <p> For best experience, please use a laptop or larger screen </p>
        </div>
    </div> -->
    <div class="sfwd-large-screen wysiwyg">
        <div class="content">
            <?
            if ($is_quiz) {
            ?>
                <div class="progress">
                    <div class="back-to-course">
                        <a href="<?php echo $course_url; ?>" class="link">
                            <svg width="8" height="14" viewBox="0 0 8 14" fill="none">
                                <path d="M7 13L1 6.93015L6.86175 1" stroke="#1F1F1F" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <div class="back-to-course__text">Back to Course Carriculum</div>
                        </a>
                    </div>
                    <div class="course-details">
                        <div class="course-title">
                            <?php echo $course->post_title; ?>
                        </div>
                        <div class="progress-bar">
                            <div class="lesson-count">
                                <?php echo count($lessons); ?> Lessons
                            </div>
                            <?php echo do_shortcode('[learndash_course_progress]'); ?>
                        </div>
                    </div>
                    <div class='course-carriculum'>
                        <?php echo do_shortcode('[course_content]'); ?>
                    </div>
                </div>
            <?
            }
            ?>
            <div class="sfwd-lessons <? echo !$is_quiz ? 'not-quiz' : ''; ?>">
                <?
                if ($is_quiz) {
                ?>
                    <div class="sfwd-lessons__title">
                        <div class="sfwd-lessons__title__text"><?php the_title(); ?></div>
                    </div>
                <?
                }
                ?>
                <div class="sfwd-lessons__content">
                    <?
                    if ($show_content) :

                        /**
                         * Content and/or tabs
                         */
                        learndash_get_template_part(
                            'modules/tabs.php',
                            array(
                                'course_id' => $course_id,
                                'post_id'   => $quiz_post->ID,
                                'user_id'   => $user_id,
                                'content'   => $content,
                                'materials' => $materials,
                                'context'   => 'quiz',
                            ),
                            true
                        );

                        if ($attempts_left) :

                            /**
                             * Fires before the actual quiz content (not WP_Editor content).
                             *
                             * @since 3.0.0
                             *
                             * @param int $quiz_id   Quiz ID.
                             * @param int $course_id Course ID.
                             * @param int $user_id   User ID.
                             */
                            do_action('learndash-quiz-actual-content-before', $quiz_post->ID, $course_id, $user_id);

                            echo $quiz_content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Post content

                            /**
                             * Fires after the actual quiz content (not WP_Editor content).
                             *
                             * @since 3.0.0
                             *
                             * @param int $quiz_id   Quiz ID.
                             * @param int $course_id Course ID.
                             * @param int $user_id   User ID.
                             */
                            do_action('learndash-quiz-actual-content-after', $quiz_post->ID, $course_id, $user_id);

                        else :

                            /**
                             * Display an alert
                             */

                            /**
                             * Fires before the quiz attempts alert.
                             *
                             * @since 3.0.0
                             *
                             * @param int $quiz_id   Quiz ID.
                             * @param int $course_id Course ID.
                             * @param int $user_id   User ID.
                             */
                            do_action('learndash-quiz-attempts-alert-before', $quiz_post->ID, $course_id, $user_id);

                            learndash_get_template_part(
                                'modules/alert.php',
                                array(
                                    'type'    => 'warning',
                                    'icon'    => 'alert',
                                    'message' => sprintf(
                                        // translators: placeholders: quiz, attempts count.
                                        esc_html_x('You have already taken this %1$s %2$d time(s) and may not take it again.', 'placeholders: quiz, attempts count', 'learndash'),
                                        learndash_get_custom_label_lower('quiz'),
                                        $attempts_count
                                    ),
                                ),
                                true
                            );

                            /**
                             * Fires after the quiz attempts alert.
                             *
                             * @since 3.0.0
                             *
                             * @param int $quiz_id   Quiz ID.
                             * @param int $course_id Course ID.
                             * @param int $user_id   User ID.
                             */
                            do_action('learndash-quiz-attempts-alert-after', $quiz_post->ID, $course_id, $user_id);

                        endif;
                    endif;
                    ?>
                </div>
                <?
                if ($is_quiz) {
                ?>
                    <div class="sfwd-lessons__footer">
                        <hr class="sfwd-lessons__navigation__divider" />
                    </div>
                <?
                }
                ?>
            </div>
        </div>
    </div>
</div>