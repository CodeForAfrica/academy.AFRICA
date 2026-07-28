<?php
/*
Template Name: Single Event
*/
get_header();
$back_text = "Back to All Events";
$resources_text = "Resources";
$register_text = "Register Here";
$or_title = "The Organisation";
$speaker_title = "The Speaker";
require_once __DIR__ . '/includes/utils/countries.php';
?>
<main id="content" class="single-page-event">
    <?php
    if (have_posts()):
        while (have_posts()):
            the_post();
            $post_array = get_post();
            $post_id = $post_array->ID;
            $raw_date = get_post_meta($post_id, 'date', true);
            $raw_time = get_post_meta($post_id, 'time', true);
            // Safe parsing — a missing/malformed date must not fatal the page (#57).
            $event_ts = academyafrica_event_timestamp($raw_date, $raw_time);
            $date = academyafrica_format_event_date($raw_date, 'Y-m-d');

            $registration_link = get_post_meta($post_id, 'registration_link', true);
            $is_past_event = ($event_ts !== null) && $event_ts < strtotime('today midnight -1 second');
            $post_title = $post_array->post_title;
            $post_content = $post_array->post_content;
            $featured_image_url = get_the_post_thumbnail_url($post_id, 'full');
            $is_virtual = get_post_meta($post_id, 'is_virtual', true) ? "&#x1F5A5;" : "";
            $speakers = get_field("speaker", $post_id) ?: [];
            $time = $raw_time . ' GMT +00:00';
            $language = get_post_meta($post_id, 'language', true);
            $organisations = get_field("organisations", $post_id) ?: [];
            $resources_field = get_field('resources', $post_id) ?: [];
            $resources = isset($resources_field['url']) ? $resources_field['url'] : null;
            $countries = get_field("countries", $post_id) ?: [];
    ?>
            <h1 class="cfa-title">
                <?php echo esc_html($post_title) ?>
            </h1>
            <div class="image-container">
                <img width="100%" class="featured-image" src="<?php echo esc_url($featured_image_url) ?>"
                    alt="<?php echo esc_attr($post_title) ?>">
            </div>
            <div class="details">
                <div class="custom-data">
                    <p class="speaker">
                        <?php echo isset($speaker) ? esc_html($speaker->display_name) : "" ?>
                    </p>
                    <div class="with-icons">
                        <img src="/wp-content/themes/academyAfrica/assets/images/icons/Type=calendar, Size=16, Color=Black.svg" alt="">
                        <p style="margin: 0" class="date">
                            <?php echo esc_html($date) ?>
                        </p>
                    </div>

                    <div class="with-icons">
                        <img src="/wp-content/themes/academyAfrica/assets/images/icons/Type=world, Size=16, Color=Black.svg" alt="">
                        <p style="margin: 0" class="language">
                            <?php echo esc_html($language) ?>
                        </p>
                    </div>
                    <div class="with-icons">
                        <img src="/wp-content/themes/academyAfrica/assets/images/icons/Type=location, Size=16, Color=Black.svg" alt="">
                        <p style="margin: 0" class="time">
                            <?php
                            if (is_array($countries)) {
                                foreach ($countries as $country) {
                                    if (is_array($country) && isset($country['value'])) {
                                        echo country_flag_emoji($country['value']);
                                    }
                                }
                            }
                            ?>
                        </p>
                    </div>
                </div>
                <div class="share">
                    <div style="margin-bottom: 20px">
                        <?php get_template_part('template-parts/social_share', 'template'); ?>
                    </div>
                    <?php
                    if ($is_past_event) {
                    ?>
                        <a href="<?php echo esc_url($resources) ?>" <?php echo $resources ? 'download' : '' ?>>
                            <button class="button resources">
                                <img src="/wp-content/themes/academyAfrica/assets/images/MOOCButton.svg" alt="">
                                <?php echo esc_html($resources_text) ?>
                            </button>
                        </a>
                    <?php
                    } else {
                    ?>
                        <a href="<?php echo esc_url($registration_link) ?>">
                            <button class="button cta signup-button">
                                <?php echo esc_html($register_text) ?>
                            </button>
                        </a>

                    <?php
                    }
                    ?>
                </div>
            </div>
            <hr class="divider">
            <p class="content">
                <?php echo wp_kses_post($post_content) ?>
            </p>
            <div class="linked-post">
                <h4 class="title"><?php echo esc_html($speaker_title) ?></h4>

                <?php
                if (isset($speakers) && is_array($speakers) && count($speakers) > 0) {
                    foreach ($speakers as $speaker) {
                        $sp_title = $speaker->post_title;
                        $avatar_url = get_the_post_thumbnail_url($speaker->ID, 'full');
                        $sp_desc = get_the_excerpt($speaker->ID);
                ?>
                        <img style="width: 100px; height: 100px; border-radius: 50%; object-fit: cover; margin: 0;" src="<?php echo esc_url($avatar_url) ?>" alt="<?php echo esc_attr($speaker->display_name) ?>" class="logo">
                        <p style="text-transform: capitalize; margin: 0" class="name">
                            <?php echo esc_html($sp_title) ?>
                        </p>
                        <p class="description" style="margin-bottom: 32px; margin-top: 16px;">
                            <?php echo esc_html($sp_desc) ?>
                        </p>
                <?php
                    }
                }
                ?>
            </div>
            <div class="linked-post">
                <?php
                if (isset($organisations) && is_array($organisations) && count($organisations) > 0):
                foreach ($organisations as $organisation) {
                    $org_title = $organisation->post_title;
                    $img = get_the_post_thumbnail_url($organisation->ID, 'full');
                    $desc = get_the_excerpt($organisation->ID);
                ?>
                    <h4 class="title"><?php echo esc_html($or_title) ?></h4>
                    <img src="<?php echo esc_url($img) ?>" alt="<?php echo esc_attr($org_title) ?>" style="height: 100px;" class="logo">
                    <p class="name">
                        <?php echo esc_html($org_title) ?>
                    </p>
                    <p class="description">
                        <?php echo esc_html($desc) ?>
                    </p>
                <?php
                }
                endif;
                ?>
            </div>
    <?php
        endwhile;
    endif;
    ?>
    <div class="back">
        <a href="javascript:history.go(-1)">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none">
                <path d="M10 4L6 8L10 12" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                    stroke-linejoin="round" />
            </svg>
            <?php echo esc_html($back_text) ?>
        </a>
    </div>
</main>

<?php
get_footer(); // Include the footer file
?>