<?php

$learning_path_id = get_the_ID();

?>

<?php get_header(); ?>

<div class="ac-learning-path-container">
    <div class="ac-learning-path-container__title">
        <div class="ac-learning-path-container__title__text"><?php the_title(); ?></div>
    </div>
    <div class="ac-learning-path-container__content">
        <?php
        the_content();
        ?>
    </div>
</div>

<?php get_footer(); ?>