<?php
/*
Template Name: Page Content Template
*/
require_once(ABSPATH . 'wp-load.php');
get_header();

?>
<main class="default-page-content">
    <?php
    if (have_posts()) {
        while (have_posts()) {
            the_post();
            the_content();
        }
    } else {
        echo '<p>No content available.</p>';
    }
    ?>
</main>
<?php
get_footer();
