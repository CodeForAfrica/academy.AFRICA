<?php


?>

<nav class="header">
    <!-- Mobile Nav -->
    <div class="header__mobile">
        <div class="header__mobile__menu">
            <div class="header__mobile__menu__icon">
                <i class="fa-solid fa-bars" id="open"></i>
            </div>
            <div class="header__mobile__menu__close">
                <i class="fa-solid fa-xmark" id="close"></i>
            </div>
        </div>
        <div class="header__mobile__logo">
            <?php
            if (has_custom_logo()) {
                echo "<div class='header__mobile__logo__image'>";
                the_custom_logo();
                echo "</div>";
            }
            echo '<h1 class="header__mobile__logo__site_name"><a href="' . esc_url(home_url('/')) . '">' . esc_html(get_bloginfo('name')) . '</a></h1>';
            ?>
        </div>
        <div class="header__mobile__search">
            <div class="header__mobile__search__icon">
                <i class="fa-solid fa-magnifying-glass"></i>
            </div>
        </div>
    </div>
</nav>