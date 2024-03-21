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
    if(have_posts()):
        while(have_posts()):
            the_post();
            $post_array = get_post();
            $post_id = $post_array->ID;
            $raw_date = get_post_meta($post_id, 'date', true);
            $date = date_format(date_create($raw_date), 'Y-m-d');
            $offset = get_timezones()[get_post_meta($post_id, 'timezone', true)] ?? "+0:00";
            $raw_time = get_post_meta($post_id, 'time', true);
            $registration_link = get_post_meta($post_id, 'registration_link', true);
            $given_date_time = new DateTime($date.' '.$raw_time, new DateTimeZone($offset));
            $current_date_time = new DateTime("now", new DateTimeZone($offset));
            $is_past_event = $given_date_time < $current_date_time;
            $post_title = $post_array->post_title;
            $post_content = $post_array->post_content;
            $featured_image_url = get_the_post_thumbnail_url($post_id, 'full');
            $is_virtual = get_post_meta($post_id, 'is_virtual', true) ? "&#x1F5A5;" : "";
            $speaker = get_userdata(get_post_meta($post_id, 'speaker', true));
            $time = str_replace("Africa/", "", $raw_time.' '.get_post_meta($post_id, 'timezone', true));
            $language = get_post_meta($post_id, 'language', true);
            $organisations = get_field("organisations", $post_id);
            $resources = get_field('resources')['url'];
            ?>
            <script>
                // console.log(<? echo json_encode($post_array) ?>)
            </script>
            <h1 class="cfa-title">
                <? echo $post_title ?>
            </h1>
            <div class="image-container">
                <img width="100%" height="200px" class="featured-image" src="<? echo $featured_image_url ?>"
                    alt="<? echo $featured_image_url ?>">
            </div>
            <div class="details">
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


                </div>
                <div class="share">
                    <div style="margin-bottom: 20px">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <rect x="0.05" y="0.05" width="23.9" height="23.9" stroke="black" stroke-opacity="0.1"
                                stroke-width="0.1" />
                            <path
                                d="M18 8C19.6569 8 21 6.65685 21 5C21 3.34315 19.6569 2 18 2C16.3431 2 15 3.34315 15 5C15 6.65685 16.3431 8 18 8Z"
                                stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            <path
                                d="M6 15C7.65685 15 9 13.6569 9 12C9 10.3431 7.65685 9 6 9C4.34315 9 3 10.3431 3 12C3 13.6569 4.34315 15 6 15Z"
                                stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            <path
                                d="M18 22C19.6569 22 21 20.6569 21 19C21 17.3431 19.6569 16 18 16C16.3431 16 15 17.3431 15 19C15 20.6569 16.3431 22 18 22Z"
                                stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M8.58984 13.5117L15.4198 17.4917" stroke="black" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" />
                            <path d="M15.4098 6.51172L8.58984 10.4917" stroke="black" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" />
                        </svg>
                    </div>
                    <?
                    if($is_past_event) {
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
                <h4 class="title">The speaker</h4>
                <?
                $user_id = get_post_meta($post_id, 'speaker', true);
                $biography = get_the_author_meta('description', $user_id);
                $avatar_url = get_avatar_url($user_id, array('size' => 100, 'default' => 'gravatar_default')).'?nocache='.time();
                ?>
                <img src="<? echo $avatar_url ?>" alt="<? echo $speaker->display_name ?>" class="logo">
                <p class="name">
                    <? echo $speaker->display_name ?>
                </p>
                <p class="description">
                    <? echo $biography ?>
                </p>
            </div>
            <div class="linked-post">
                <?
                foreach($organisations as $organisation) {
                    $org_title = $organisation->post_title;
                    $img = get_the_post_thumbnail_url($organisation->ID);
                    $desc = get_the_excerpt($organisation->ID);
                    ?>
                    <h4 class="title">The Organisation</h4>
                    <img src="<? echo $img ?>" alt="<? echo $org_title ?>" class="logo">
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
    <script>
        // console.log(<?php echo json_encode($organisations) ?>);
    </script>
</main>

<?php
get_footer(); // Include the footer file
?>
