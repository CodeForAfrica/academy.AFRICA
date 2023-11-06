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
    <div class="mobile">
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
                <div class="icon search-icon">
                    <i class="fa-solid fa-magnifying-glass"></i>
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
            <!-- Search Bar -->
            <div class="search">
                <div class="input">
                    <input type="text" class="form-control" placeholder="Search" aria-label="Search" aria-describedby="button-addon2">
                    <button class="btn btn-outline-secondary" type="button" id="button-addon2">
                        <i class="fa-solid fa-magnifying-glass icon"></i>
                    </button>
                </div>
            </div>
            <!-- Menu -->
            <div class="menu">
                <div class="items">
                    <?php
                    foreach ($menu_items as $menu_item) {
                        $class = 'item';
                        if (count($menu_item['children']) > 0) {
                            $class .= ' parent';
                            echo "<div class='" . $class . "'>";
                            echo "<span class='collapsible'>" . $menu_item['title'] . "
                    <i class='fa fa-chevron-down icon close'></i>
                    <i class='fa fa-chevron-up icon open' style='display: none;'></i>
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
</nav>
<!-- Mobile drawer -->
<div class="drawer">
    <div class="menu">
        <div class="items">
            <?php
            foreach ($menu_items as $menu_item) {
                $class = 'item';
                if (count($menu_item['children']) > 0) {
                    $class .= ' parent';
                    echo "<div class='" . $class . "'>";
                    echo "<span class='collapsible'>" . $menu_item['title'] . "
                    <i class='fa fa-chevron-down icon close'></i>
                    <i class='fa fa-chevron-up icon open' style='display: none;'></i>
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
    <div class="input">
        <button class="btn search-btn" type="button" id="button-search">
            <i class="fa-solid fa-magnifying-glass icon"></i>
        </button>
        <input type="text" class="form-control" placeholder="Search" aria-label="Search" aria-describedby="button-addon2">
        <div class="search-close-btn">
            <i class="fa-solid fa-xmark"></i>
        </div>
    </div>
</div>