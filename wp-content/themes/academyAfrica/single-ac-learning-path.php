<?php

/**
 * Template Name: Learning Path
 * Template Post Type: ac-learning-path
 */

$learning_path_id = get_the_ID();
$learning_path_title = get_the_title();
$learning_path_excerpt = get_the_excerpt();
$courses = get_field('courses', $learning_path_id) ?: [];
?>

<?php get_header(); ?>


<div class="ac-learning-path-container">
    <div id='learning-path'>
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
                    <?php the_content() ?>
                </div>
                <div class="list">
                    <?php foreach ($courses as $course_index =>  $course) : ?>
                        <?php
                        $course_thumbnail = get_the_post_thumbnail_url($course);
                        $course_title = $course->post_title;
                        $course_excerpt = $course->post_excerpt;
                        $course_link = get_permalink($course->ID);
                        $course_author = get_the_author_meta('display_name', $course->post_author);
                        $course_meta = get_post_meta($course->ID, 'sfwd-courses', true);
                        $course_price = isset($course_meta['sfwd-courses_course_price']) ? $course_meta['sfwd-courses_course_price'] : 0;
                        $course_price = $course_price == 0 ? "Free" : $course_price;
                        $students_count = academyafrica_count_students($course->ID);
                        ?>
                        <div class="individual-course">
                            <div class="course-index">
                                <?php echo $course_index + 1 ?>
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
                                    <a href="<?php echo esc_url($course_link) ?>" class="course-title">
                                        <?php echo esc_html($course_title) ?>
                                    </a>
                                    <div class="course-excerpt">
                                        <?php echo esc_html($course_excerpt) ?>
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
    <div id="learning">
        <?php get_template_part('template-parts/ac_learning_print', 'template', [
            'learning_path_title' => $learning_path_title,
            'learning_path_excerpt' => $learning_path_excerpt,
            'courses' => $courses,
            'content' => get_the_content()
        ]); ?>
    </div>
    <div class="ac-learning-path-container__download">
        <button class="button primary large" onclick="downloadSingleLearningPath()">
            <i class="fa-solid fa-download icon"></i>
            Download as PDF
        </button>
    </div>
</div>

<script type="text/javascript">
    function downloadSingleLearningPath() {
        var printSource = document.getElementById('learning');
        if (!printSource) return;
        var printTemplate = printSource.innerHTML;

        var printWindow = window.open('', '', '');
        if (!printWindow) return;
        printWindow.document.write('<!DOCTYPE html>');
        printWindow.document.write('<html><head>');
        printWindow.document.write('<title><?php echo esc_js($learning_path_title); ?></title>');
        printWindow.document.write('</head><body>');
        printWindow.document.write(printTemplate);
        printWindow.document.write('</body></html>');
        printWindow.document.close(); // necessary for IE >= 10
        printWindow.focus(); // necessary for IE >= 10

        printWindow.onload = function() {
            printWindow.print();
        };
    }

    window.dataLayer = window.dataLayer || [];
    dataLayer.push({
        'event': 'learning_path_print',
        'page_title': '<?php echo esc_js($learning_path_title) ?>',
        'page_url': window.location.href,
    });
    typeof window.gtag === 'function' && gtag('event', 'learning_path_print', {
        'page_title': '<?php echo esc_js($learning_path_title) ?>',
        'page_location': window.location.href,
    });
</script>

<?php get_footer(); ?>
