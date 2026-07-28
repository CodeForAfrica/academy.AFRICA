<?php
/*
Template Name: Page Content
*/
require_once(ABSPATH . 'wp-load.php');
get_header();

?>
<div class="about-section">
    <div class="content">
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
    </div>
</div>
<?php
get_footer();
