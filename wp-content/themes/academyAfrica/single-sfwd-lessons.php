<?php


?>

<?php get_header(); ?>

<div class="sfwd-container">
    <div class='sfwd-small-screen'>
        <div class="title">
            <div class="cfa-title"><?php the_title(); ?></div>
        </div>
        <div class='helper'>
            <p> For best experience, please use a laptop or larger screen </p>
        </div>
    </div>
    <div class="sfwd-large-screen">
        <div class="sfwd-lessons">
            <div class="sfwd-lessons__title">
                <div class="sfwd-lessons__title__text"><?php the_title(); ?></div>
            </div>
            <div class="sfwd-lessons__content">
                <?php
                if (have_posts()) :
                    while (have_posts()) : the_post();
                        the_content();
                    endwhile;
                endif;
                ?>
            </div>
        </div>
    </div>
</div>

<?php get_footer(); ?>