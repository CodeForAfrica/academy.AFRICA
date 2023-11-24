<?php
/*
Template Name: Single Event
*/
get_header();

?>
<main id="main" class="single-page-event">
    <?php
    if (have_posts()) :

        // The WordPress Loop
        while (have_posts()) :
            the_post();

            $post_array = get_post();

            $post_id = $post_array->ID;
            $post_title = $post_array->post_title;
            $post_content = $post_array->post_content;
            $featured_image_url = get_the_post_thumbnail_url($post_id, 'full');
            $is_virtual = get_post_meta($post_id, 'is_virtual', true) ? "&#x1F5A5;" : "";
            $user_data = get_userdata(get_post_meta($post_id, 'speaker', true));
            $speaker = $user_data->display_name;
            $raw_date = get_post_meta($post_id, 'date', true);
            $date = date_format(date_create($raw_date), 'd/m/Y');
            $time = str_replace("Africa/", "", get_post_meta($post_id, 'time', true) . ' ' . get_post_meta($post_id, 'timezone', true));
            $language = get_post_meta($post_id, 'language', true);
    ?>
            <h1 class="cfa-title">
                <? echo $post_title ?>
            </h1>
            <div class="image-container">
                <img width="100%" height="200px" class="featured-image" src="<? echo $featured_image_url ?>" alt="<? echo $featured_image_url ?>">
            </div>
            <div class="custom-data">
                <p class="speaker">
                    <? echo $speaker ?>
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
                <button class="button cta signup-button">
                    Register Here
                </button>
            </div>
            <hr class="divider">
            <p class="content">
                <? echo $post_content ?>
            </p>
            <div class="linked-post">
                <h4 class="title">The speaker</h4>
                <img src="https://academyafridev.wpenginepowered.com/wp-content/uploads/2023/11/Strathmore_Uni-1.png" alt="" class="logo">
                <p class="name"><? echo $speaker ?></p>
                <p class="description">
                    About the instructor: Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam eu turpis molestie, dictum est a, mattis tellus. Sed dignissim, metus nec fringilla accumsan, risus sem sollicitudin lacus, ut interdum tellus elit sed risus. Maecenas eget condimentum velit, sit amet feugiat lectus. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Praesent auctor purus luctus enim egestas, ac scelerisque ante pulvinar. Donec ut rhoncus ex. Suspendisse ac rhoncus nisl, eu tempor urna. Curabitur vel bibendum lorem. Morbi convallis convallis diam sit amet lacinia. Aliquam in elementum tellus.
                </p>
            </div>
            <div class="linked-post">
                <h4 class="title">The Organisation</h4>
                <img src="https://academyafridev.wpenginepowered.com/wp-content/uploads/2023/11/Strathmore_Uni-1.png" alt="" class="logo">
                <p class="name"><? echo $speaker ?></p>
                <p class="description">
                    About the instructor: Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam eu turpis molestie, dictum est a, mattis tellus. Sed dignissim, metus nec fringilla accumsan, risus sem sollicitudin lacus, ut interdum tellus elit sed risus. Maecenas eget condimentum velit, sit amet feugiat lectus. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Praesent auctor purus luctus enim egestas, ac scelerisque ante pulvinar. Donec ut rhoncus ex. Suspendisse ac rhoncus nisl, eu tempor urna. Curabitur vel bibendum lorem. Morbi convallis convallis diam sit amet lacinia. Aliquam in elementum tellus.
                </p>
            </div>
    <?
        endwhile;
    endif;
    ?>
    <script>
        console.log(<?php echo json_encode($post_array) ?>);
    </script>
</main>

<?php
get_footer(); // Include the footer file
?>