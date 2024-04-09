<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

$lesson_id = get_the_ID();
$course_id = learndash_get_course_id($lesson_id);

$course_url = get_permalink($course_id);
$course = get_post($course_id);
$lessons = learndash_get_course_lessons_list($course_id);
$topic = get_post($lesson_id);
$has_assignments = learndash_lesson_hasassignments($topic);

?>


<style>
    .entry-title {
        display: none;
    }
</style>


<div class="sfwd-container wysiwyg">
    <!-- <div class='sfwd-small-screen'>
        <div class="title">
            <div class="cfa-title"><?php the_title(); ?></div>
        </div>
        <div class='helper'>
            <p> For best experience, please use a laptop or larger screen </p>
        </div>
    </div> -->
    <div class="sfwd-large-screen">
        <div class="content">
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
            <div class="sfwd-lessons">
                <div class="sfwd-lessons__navigation">
                    <?php
                    $previous_lesson = learndash_previous_post_link(url: true);
                    if ($previous_lesson) {
                        echo "<div class='sfwd-lessons__navigation__previous nav-link'>";
                        echo "<a href='$previous_lesson' class='link'>";
                        echo "<div class='sfwd-lessons__navigation__previous__text'>Previous</div>";
                        echo "</a>";
                        echo "</div>";
                    }
                    ?>
                    <?php
                    $next_lesson = learndash_next_post_link(url: true);
                    if ($next_lesson) {
                        echo "<div class='sfwd-lessons__navigation__next nav-link'>";
                        echo "<a href='$next_lesson' class='link'>";
                        echo "<div class='sfwd-lessons__navigation__next__text'>Next</div>";
                        echo "</a>";
                        echo "</div>";
                    }
                    ?>
                </div>
                <div class="sfwd-lessons__title">
                    <div class="sfwd-lessons__title__text"><?php the_title(); ?></div>
                </div>
                <div class="sfwd-lessons__content">
                    <?php
                    echo do_shortcode($topic->post_content);
                    ?>
                </div>
                <? if ($has_assignments) { ?>
                    <div class="assignment-upload">
                        <div class="sfwd-lessons__content">
                            <?php
                            learndash_get_template_part(
                                'assignment/listing.php',
                                array(
                                    'course_step_post' => $topic,
                                    'user_id'          => $user_id,
                                    'course_id'        => $course_id,
                                ),
                                true
                            );
                            ?>
                        </div>
                    </div>
                <? } ?>
                <div class="sfwd-lessons__footer">
                    <hr class="sfwd-lessons__footer__divider" />
                    <?
                    $complete_button = learndash_mark_complete($topic);
                    if ($complete_button) {
                        echo "<div class='sfwd-lessons__footer__complete'>";
                        echo $complete_button;
                        echo "</div>";
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>