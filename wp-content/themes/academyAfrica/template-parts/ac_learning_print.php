<?php
$learning_path_title = $args['learning_path_title'];
$learning_path_excerpt = $args['learning_path_excerpt'];
$courses = $args['courses'];
$content = $args['content'];

?>

<link rel="stylesheet" href="<? echo get_stylesheet_directory_uri() ?>/assets/css/dist/print/ac_learning_print.css">
<div class="pdf-template">
    <div class="template">
        <div class="template__title">
            <div class="template__title__image">
                <img src="/wp-content/themes/academyAfrica/assets/images/mask.svg" class="image">
            </div>
            <div class="template__title__text">
                <div class="cfa-title">
                    <? echo $learning_path_title ?>
                </div>
                <div class="cfa-excerpt">
                    <? echo $learning_path_excerpt ?>
                </div>
            </div>
        </div>
        <div class="template__content">
            <div class="courses">
                <div class="title">
                    Take the courses
                </div>
                <div class="content">
                    <? echo $content ?>
                </div>
                <div class="list">
                    <?php
                    $counter = 0;
                    foreach ($courses as $course_index =>  $course) : ?>
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
                        // If counter is 4 or a multiple of 5 after the 4th, insert a page break
                        if ($counter == 4 || ($counter > 4 && ($counter - 4) % 5 == 0)) {
                            echo '<div class="pagebreak"></div>';
                        }
                        $counter++;
                        ?>
                        <div class="individual-course">
                            <div class="course-index">
                                <? echo $course_index + 1 ?>
                            </div>
                            <div class="course">
                                <?php get_template_part(
                                    'template-parts/course_card',
                                    'template',
                                    [
                                        'course_title' => $course_title,
                                        'course_author' => $course_author,
                                        'course_thumbnail' => $course_thumbnail,
                                        'course_link' => $course_link,
                                        'course_price' => $course_price,
                                        'students' => $students_count
                                    ]
                                ); ?>
                                <div class="extra-course-details">
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
    </div>

</div>