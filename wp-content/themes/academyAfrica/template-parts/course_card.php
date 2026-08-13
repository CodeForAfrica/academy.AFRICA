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
$course_id = $args['course_id'] ?? 0;
// A password-protected course otherwise looks identical to an open one until
// the visitor clicks through, which reads as a broken link rather than a
// deliberate restriction — so flag it on the card itself.
$is_locked = $course_id && post_password_required($course_id);
?>

<a href="<?php echo $course_link ?>" class="course-card<?php echo $is_locked ? ' course-card--locked' : ''; ?>" id="course-card-<?php echo $course_index ?>">
    <div class="card">
        <div class="course-card-pattern">
            <?php if ($is_locked) : ?>
                <span class="course-card-lock" aria-label="<?php echo esc_attr(function_exists('pll__') ? pll__('Password protected') : 'Password protected'); ?>">
                    <?php academyafrica_render_lock_icon(); ?>
                </span>
            <?php endif; ?>
            <img src="<?php echo $logo_url
                        ?>"
                alt="course-thumbnail">
        </div>
        <div class="course-card-content">
            <p class="course-title">
                <?php echo $course_title ?>
            </p>
            <div class="course-meta">
                <p class="course-author">
                    <?php echo esc_html(academyafrica_translate('By')); ?> <?php echo $course_author ?>
                </p>
                <div class="course-details">
                    <div class="course-students">
                        <div class="icon">
                            <img src="<?php echo get_stylesheet_directory_uri() ?>/assets/images/user.svg" alt="students">
                        </div>
                        <p class="value"><?php echo $students ?></p>
                    </div>
                    <p class="course-price">
                        <?php echo $course_price ?>
                    </p>
                </div>
            </div>
        </div>
    </div>
</a>
<script>
    document.getElementById('course-card-<?php echo $course_index ?>')?.addEventListener('click', function() {
        window.dataLayer = window.dataLayer || [];
        dataLayer.push({
            'event': 'course_card_click',
            'course_title': <?php echo wp_json_encode($course_title) ?>,
            'course_link': <?php echo wp_json_encode($course_link) ?>,
        });
        typeof window.gtag === 'function' && gtag('event', 'course_card_click', {
            'event_category': 'engagement',
            'event_label': <?php echo wp_json_encode($course_title) ?>,
            'course_link': <?php echo wp_json_encode($course_link) ?>
        });
    });
</script>
