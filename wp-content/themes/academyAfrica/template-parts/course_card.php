<?php
$course_title = $args['course_title'];
$course_author = $args['course_author'];
$course_thumbnail = $args['course_thumbnail'];
$course_price = $args['course_price'];
$students = $args['students'];
$course_link = $args['course_link'];
?>

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
                    By <? echo $course_author ?>
                </p>
                <div class="course-details">
                    <div class="course-students">
                        <div class="icon">
                            <img src="<? echo get_stylesheet_directory_uri() ?>/assets/images/user.svg" alt="students">
                        </div>
                        <p class="value"><? echo $students ?></p>
                    </div>
                    <p class="course-price">
                        <? echo $course_price ?>
                    </p>
                </div>
            </div>
        </div>
    </div>
</a>