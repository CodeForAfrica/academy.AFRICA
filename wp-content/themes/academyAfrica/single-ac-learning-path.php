<?php

$learning_path_id = get_the_ID();
$learning_path_title = get_the_title();
$learning_path_excerpt = get_the_excerpt();
$courses = get_field('courses', $learning_path_id);
?>

<?php get_header(); ?>

<div class="ac-learning-path-container">
    <div class="ac-learning-path-container__title">
        <div class="ac-learning-path-container__title__image">
            <img src="/wp-content/themes/academyAfrica/assets/images/mask.svg" class="image">
        </div>
        <div class="ac-learning-path-container__title__text">
            <div class="cfa-title">
                <?php the_title(); ?>
            </div>
            <div class="cfa-excerpt">
                <?php the_excerpt(); ?>
            </div>
        </div>
    </div>
    <div class="ac-learning-path-container__content">
        <div class="courses">
            <div class="title">
                Take the courses
            </div>
            <div class="content">
                <? the_content() ?>
            </div>
            <div class="list">
                <?php foreach ($courses as $course_index =>  $course) : ?>
                    <?
                    $course_thumbnail = get_the_post_thumbnail_url($course);
                    $course_title = $course->post_title;
                    $course_excerpt = $course->post_excerpt;
                    $course_link = get_permalink($course->ID);
                    $course_author = get_the_author_meta('display_name', $course->post_author);
                    $course_meta = get_post_meta($course->ID);
                    $course_price = $course_meta['sfwd-courses_course_price'];
                    $course_price = $course_price == 0 ? "Free" : $course_price;
                    $students_count = learndash_course_grid_count_students($course->ID);
                    ?>
                    <div class="individual-course">
                        <div class="course-index">
                            <? echo $course_index + 1 ?>
                        </div>
                        <div class="course">
                            <a href="<? echo $course_link ?>" class="course-card">
                                <div class="card">
                                    <div class="course-card-pattern">
                                        <img src="<? echo $course_thumbnail ?>" alt="course-thumbnail">
                                    </div>
                                    <div class="course-card-content">
                                        <p class="course-title">
                                            <? echo $course_title ?>
                                        </p>
                                        <div class="course-meta">
                                            <p class="course-author">
                                                By
                                                <? echo $course_author ?>
                                            </p>
                                            <div class="course-details">
                                                <div class="course-students">
                                                    <div class="icon"></div>
                                                    <p class="value">
                                                        <? echo $students_count ?>
                                                    </p>
                                                </div>
                                                <p class="course-price">
                                                    <? echo $course_price ?>
                                                </p>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </a>
                            <div class="course-details">
                                <a href="<? echo $course_link ?>" class="course-title">
                                    <? echo $course_title ?>
                                </a>
                                <div class="course-excerpt">
                                    <? echo $course_excerpt ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr class="divider">
                <?php endforeach; ?>
            </div>

        </div>
    </div>
    <div class="ac-learning-path-container__download">
        <button class="button primary large">
            <i class="fa-solid fa-download icon"></i>
            Download as PDF
        </button>
    </div>
</div>

<?php get_footer(); ?>