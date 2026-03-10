<?php
$course_title = $args['course_title'];
$course_author = $args['course_author'];
$course_thumbnail = $args['course_thumbnail'];
$mooc_logo = get_stylesheet_directory_uri() . '/assets/images/mooc-logo-blue.svg';
$logo_url = $course_thumbnail ? $course_thumbnail : $mooc_logo;
$course_price = $args['course_price'];
$students = $args['students'];
$course_link = $args['course_link'];
$course_index = isset($args['course_index']) ? $args['course_index'] : uniqid();
?>

<a href="<? echo $course_link ?>" class="course-card" id="course-card-<? echo $course_index ?>">
    <div class="card">
        <div class="course-card-pattern">
            <img src="<? echo $logo_url
                        ?>"
                alt="course-thumbnail">
        </div>
        <div class="course-card-content">
            <p class="course-title">
                <? echo $course_title ?>
            </p>
            <div class="course-meta">
                <p class="course-author">
                    <?php echo esc_html(academyafrica_translate('By')); ?> <? echo $course_author ?>
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
<script>
    document.getElementById('course-card-<? echo $course_index ?>').addEventListener('click', function() {
        window.dataLayer = window.dataLayer || [];
        dataLayer.push({
            'event': 'course_card_click',
            'course_title': '<? echo $course_title ?>',
            'course_link': '<? echo $course_link ?>',
        });
        gtag('event', 'course_card_click', {
            'event_category': 'engagement',
            'event_label': '<? echo $course_title ?>',
            'course_link': '<? echo $course_link ?>'
        });
    });
</script>
