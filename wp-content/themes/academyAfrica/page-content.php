<?php
/*
Template Name: Page Content
*/
get_header();

?>
<main id="content" class="about-section">
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
</main>
<?php
get_footer();
