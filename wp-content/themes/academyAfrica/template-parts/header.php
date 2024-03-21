<?php

namespace AcademyAfrica\Theme;

require_once __DIR__ . '/../includes/utils/menus.php';

/**
 * The header for our theme
 *
 * This is the template that displays all of the <head> section and everything up until the <main id="main"> tag
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package AcademyAfrica
 */

use AcademyAfrica\Theme\Utils\MenuFunctions;

$menu_items = MenuFunctions::get_menu_items('menu-1');


?>
<nav class="header">
    <!-- Mobile Nav -->
    <div class="mobile" id="mobile-nav">
        <div class="nav">
            <div class="hamburger">
                <div class="icon open">
                    <i class="fa-solid fa-bars" id="open"></i>
                </div>
                <div class="icon close" style="display: none;">
                    <i class="fa-solid fa-xmark" id="close"></i>
                </div>
            </div>
            <div class="logo">
                <?php
                if (has_custom_logo()) {
                    echo "<div class='image'>";
                    the_custom_logo();
                    echo "</div>";
                }
                echo '<h1 class="site_name"><a href="' . esc_url(home_url('/')) . '">' . esc_html(get_bloginfo('name')) . '</a></h1>';
                ?>
            </div>
            <div class="search">
                <div class="button primary search-icon">
                    <i class="fa-solid fa-magnifying-glass icon"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Desktop Nav -->
    <div class="desktop">
        <div class="nav">
            <!-- Logo and site name -->
            <div class="logo">
                <?php
                if (has_custom_logo()) {
                    echo "<div class='image'>";
                    the_custom_logo();
                    echo "</div>";
                }
                echo '<h1 class="site_name"><a href="' . esc_url(home_url('/')) . '">' . esc_html(get_bloginfo('name')) . '</a></h1>';
                ?>
            </div>
            <div class='nav-actions'>
                <div class="search">
                    <?php get_search_form(); ?>
                </div>
                <div class="menu">
                    <div class="items">
                        <?php
                        foreach ($menu_items as $menu_item) {
                            $class = 'item';
                            $class .= ' ' . $menu_item["class"];
                            if (count($menu_item['children']) > 0) {
                                $class .= ' parent';
                                echo "<div class='" . $class . "'>";
                                echo
                                "<span class='collapsible'>" . $menu_item['title'] . "
                            </span>";
                                echo "<div class='children'>";
                                foreach ($menu_item['children'] as $child) {
                                    echo "
                            <a class='item' href='" . $child['url'] . "'>" . $child['title'] . "</a>
                            ";
                                }
                                echo "</div>";
                                echo "</div>";
                            } else {
                                echo "<a class='" . $class . "' href='" . $menu_item['url'] . "'>" . $menu_item['title'] . "</a>";
                            }
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>

    </div>


    <div style="display: none;">
        <!-- add user avatar if it exists -->
        <?php
        if (is_user_logged_in()) {
            $user = wp_get_current_user();
            $avatar = get_avatar_url($user->ID);
            if ($avatar) {
                echo "<img class='user-avatar' src='" . $avatar . "' alt='user avatar' />";
            } else {
                echo "<div class='user-avatar'>";
                $user = wp_get_current_user();
                $first_name = $user->first_name;
                $last_name = $user->last_name;
                $first_letter = substr($first_name, 0, 1);
                $second_letter = substr($last_name, 0, 1);
                echo "<span class='usernames'>" . $first_letter . $second_letter . "</span>";
                echo "</div>";
            }
        }
        ?>

    </div>
</nav>
<!-- Mobile drawer -->
<div class="drawer">
    <div class="menu">
        <div class="items">
            <?php
            foreach ($menu_items as $menu_item) {
                $class = 'item' . ' ' . $menu_item["class"];
                if (count($menu_item['children']) > 0) {
                    $class .= ' parent mobile';
                    echo "<div class='" . $class . "'>";
                    echo "<span class='collapsible'>" . $menu_item['title'] . "
                    </span>";
                    echo "<div class='children'>";
                    foreach ($menu_item['children'] as $child) {
                        echo "
                            <a class='item' href='" . $child['url'] . "'>" . $child['title'] . "</a>
                            ";
                    }
                    echo "</div>";
                    echo "</div>";
                } else {
                    echo "<a class='" . $class . "' href='" . $menu_item['url'] . "'>" . $menu_item['title'] . "</a>";
                }
            }
            ?>
        </div>
    </div>

</div>

<!-- Mobile Search -->
<div class="mobile-search">
    <div class="search">
        <?php get_search_form(); ?>
    </div>
</div>

<script>
    const signOutMenu = document.querySelectorAll("a[href='#sign-out']");
    // console.log(signOutMenu);
    Array.from(signOutMenu).forEach((element) => {
        element.addEventListener("click", function() {
            <?php
            $current_url = get_permalink();
            $logout_url = wp_logout_url(get_permalink());
            echo "window.location.href = '" . $logout_url . "'";
            ?>
        });
    });
</script>