<?php
/*
Template Name: Single Event
*/
get_header();
$back_text = "Back to All Events";
$resources_text = "Resources";
$register_text = "Register Here";
?>
<main id="main" class="single-page-event">
    <?php
    if (have_posts()) :

        // The WordPress Loop
        while (have_posts()) :
            the_post();

            $post_array = get_post();
            $raw_date = get_post_meta($post_id, 'date', true);
            $date = date_format(date_create($raw_date), 'Y-m-d');
            $offset = get_timezones()[get_post_meta($post_id, 'timezone', true)] ?? "+0:00";
            $raw_time = get_post_meta($post_id, 'time', true);
            $given_date_time = new DateTime($date . ' ' . $raw_time, new DateTimeZone($offset));
            $current_date_time = new DateTime("now", new DateTimeZone($offset));
            $is_past_event = $given_date_time < $current_date_time;
            $post_id = $post_array->ID;
            $post_title = $post_array->post_title;
            $post_content = $post_array->post_content;
            $featured_image_url = get_the_post_thumbnail_url($post_id, 'full');
            $is_virtual = get_post_meta($post_id, 'is_virtual', true) ? "&#x1F5A5;" : "";
            $speaker = get_userdata(get_post_meta($post_id, 'speaker', true));
            $time = str_replace("Africa/", "", $raw_time . ' ' . get_post_meta($post_id, 'timezone', true));
            $language = get_post_meta($post_id, 'language', true);
            $organisations = get_field("organisations", $post_id);
            $resources = get_field('resources')['url'];
    ?>
            <h1 class="cfa-title">
                <? echo $post_title ?>
            </h1>
            <div class="image-container">
                <img width="100%" height="200px" class="featured-image" src="<? echo $featured_image_url ?>" alt="<? echo $featured_image_url ?>">
            </div>
            <div class="custom-data">
                <p class="speaker">
                    <? echo $speaker->display_name ?>
                </p>
                <p class="date">
                    <? echo $date ?>
                </p>
                <p class="time">
                    <? echo $time ?>
                </p>
                <p class="language">
                    <? echo $language ?>
                </p>
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
                    <button class="button cta signup-button">
                        <? echo $register_text ?> </button>
                <?
                }
                ?>

            </div>
            <hr class="divider">
            <p class="content">
                <? echo $post_content ?>
            </p>
            <div class="linked-post">
                <h4 class="title">The speaker</h4>
                <?
                $user_id = get_post_meta($post_id, 'speaker', true);
                $biography = get_the_author_meta('description', $user_id);
                $avatar_url = get_avatar_url($user_id, array('size' => 100));
                ?>
                <img src="<? echo $avatar_url ?>" alt="<? echo $speaker->display_name ?>" class="logo">
                <p class="name"><? echo $speaker->display_name ?></p>
                <p class="description">
                    <? echo $biography ?>
                </p>
            </div>
            <div class="linked-post">
                <?
                foreach ($organisations as $organisation) {
                    $org_title = $organisation->post_title;
                    $img = get_the_post_thumbnail_url($organisation->ID);
                    $desc = get_the_excerpt($organisation->ID);
                ?>
                    <h4 class="title">The Organisation</h4>
                    <img src="<? echo $img ?>" alt="<? echo $org_title ?>" class="logo">
                    <p class="name"><? echo $org_title ?></p>
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
                <path d="M10 4L6 8L10 12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
            </svg><? echo $back_text ?>
        </a>
    </div>
    <script>
        console.log(<?php echo json_encode($organisations) ?>);
    </script>
</main>

<?php
get_footer(); // Include the footer file
?>