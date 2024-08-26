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
<main id="main" class="single-page-event">
    <?php
    if (have_posts()):
        while (have_posts()):
            the_post();
            $post_array = get_post();
            $post_id = $post_array->ID;
            $raw_date = get_post_meta($post_id, 'date', true);
            $date = date_format(date_create($raw_date), 'Y-m-d');
            $offset = "UTC";
            $raw_time = get_post_meta($post_id, 'time', true);

            $registration_link = get_post_meta($post_id, 'registration_link', true);
            $given_date_time = new DateTime($date . ' ' . $raw_time, new DateTimeZone($offset));
            $current_date_time = new DateTime("now", new DateTimeZone($offset));
            $is_past_event = $given_date_time < $current_date_time;
            $post_title = $post_array->post_title;
            $post_content = $post_array->post_content;
            $featured_image_url = get_the_post_thumbnail_url($post_id, 'full');
            $is_virtual = get_post_meta($post_id, 'is_virtual', true) ? "&#x1F5A5;" : "";
            $speakers = get_field("speaker", $post_id);
            $time = $raw_time . ' GMT +00:00';
            $language = get_post_meta($post_id, 'language', true);
            $organisations = get_field("organisations", $post_id);
            $resources = get_field('resources', $post_id)['url'];
            $countries = get_field("countries", $post_id);
    ?>
            <h1 class="cfa-title">
                <? echo $post_title ?>
            </h1>
            <div class="image-container">
                <img width="100%" class="featured-image" src="<? echo $featured_image_url ?>"
                    alt="<? echo $post_id ?>">
            </div>
            <div class="details">
                <div class="custom-data">
                    <p class="speaker">
                        <? echo isset($speaker) ? $speaker->display_name : "" ?>
                    </p>
                    <div class="with-icons">
                        <img src="/wp-content/themes/academyAfrica/assets/images/icons/Type=calendar, Size=16, Color=Black.svg" alt="">
                        <p style="margin: 0" class="date">
                            <? echo $date ?>
                        </p>
                    </div>

                    <div class="with-icons">
                        <img src="/wp-content/themes/academyAfrica/assets/images/icons/Type=world, Size=16, Color=Black.svg" alt="">
                        <p style="margin: 0" class="language">
                            <? echo $language ?>
                        </p>
                    </div>
                    <div class="with-icons">
                        <img src="/wp-content/themes/academyAfrica/assets/images/icons/Type=location, Size=16, Color=Black.svg" alt="">
                        <p style="margin: 0" class="time">
                            <?
                            if (isset($countries)) {
                                foreach ($countries as $country) {
                                    echo country_flag_emoji($country['value']);
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
                    <?
                    if ($is_past_event) {
                    ?>
                        <a href="<? echo $resources ?>" <? echo $resources ? 'download' : '' ?>>
                            <button class="button resources">
                                <img src="/wp-content/themes/academyAfrica/assets/images/MOOCButton.svg" alt="">
                                <? echo $resources_text ?>
                            </button>
                        </a>
                    <?
                    } else {
                    ?>
                        <a href="<? echo $registration_link ?>">
                            <button class="button cta signup-button">
                                <? echo $register_text ?>
                            </button>
                        </a>

                    <?
                    }
                    ?>
                </div>
            </div>
            <hr class="divider">
            <p class="content">
                <? echo $post_content ?>
            </p>
            <div class="linked-post">
                <h4 class="title"><? echo $speaker_title ?></h4>

                <?
                foreach ($speakers as $speaker) {
                    $sp_title = $speaker->post_title;
                    $avatar_url = get_the_post_thumbnail_url($speaker->ID, 'full');
                    $sp_desc = get_the_excerpt($speaker->ID);
                ?>
                    <img style="width: 100px; height: 100px; border-radius: 50%; object-fit: cover; margin: 0;" src="<? echo $avatar_url ?>" alt="<? echo $speaker->display_name ?>" class="logo">
                    <p style="text-transform: capitalize; margin: 0" class="name">
                        <? echo $sp_title ?>
                    </p>
                    <p class="description" style="margin-bottom: 32px; margin-top: 16px;">
                        <? echo $sp_desc ?>
                    </p>
                <?
                }
                ?>
            </div>
            <div class="linked-post">
                <?
                foreach ($organisations as $organisation) {
                    $org_title = $organisation->post_title;
                    $img = get_the_post_thumbnail_url($organisation->ID, 'full');
                    $desc = get_the_excerpt($organisation->ID);
                ?>
                    <h4 class="title"><? echo $or_title ?></h4>
                    <img src="<? echo $img ?>" alt="<? echo $org_title ?>" style="height: 100px;" class="logo">
                    <p class="name">
                        <? echo $org_title ?>
                    </p>
                    <p class="description">
                        <? echo $desc ?>
                    </p>
                <?
                }
                ?>
            </div>
    <?
        endwhile;
    endif;
    ?>
    <div class="back">
        <a href="javascript:history.go(-1)">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none">
                <path d="M10 4L6 8L10 12" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                    stroke-linejoin="round" />
            </svg>
            <? echo $back_text ?>
        </a>
    </div>
</main>

<?php
get_footer(); // Include the footer file
?>
