<?php

namespace AcademyAfrica\Theme;

require_once __DIR__ . '/../includes/utils/menus.php';


/**
 * The template for displaying the footer.
 *
 * Contains the body & html closing tags.
 *
 * @package AcademyAfrica
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

use AcademyAfrica\Theme\Utils\MenuFunctions;

$menu_items = MenuFunctions::get_menu_items('menu-1');

// social media links array
$social_media_links = [
    [
        'link' => [
            'url' => 'https://www.facebook.com/CodeForAfrica',
        ],
        'type' => 'facebook',
    ],
    [
        'link' => [
            'url' => 'https://twitter.com/Code4Africa',
        ],
        'type' => 'twitter',
    ],
    [
        'link' => [
            'url' => 'https://www.instagram.com/code4africa/',
        ],
        'type' => 'instagram',
    ],
    [
        'link' => [
            'url' => 'https://www.linkedin.com/company/code-for-africa/',
        ],
        'type' => 'linkedin',
    ],
];

$secondary_menus = [
    [
        'url' => 'https://codeforafrica.org/',
        'label' => 'Imprint',
    ],
    [
        'url' => 'https://codeforafrica.org/',
        'label' => 'Privacy',
    ]
    ];

$search = array(
    'post_type' => 'footer',
    'posts_per_page' => -1,
    'language' => 'en',
);

$custom_posts = get_posts($search);
$footer = $custom_posts[0];
$logo = get_post_meta($footer->ID, 'logo', true);
$thumbnail_url = wp_get_attachment_image_src( $logo, 100 )[0];
$site_description = get_post_meta($footer->ID, 'site_description', true);
$stay_in_touch = get_post_meta($footer->ID, 'stay_in_touch', true);
$secondary_links = get_post_meta($footer->ID, 'secondary_links', true);
$newsletter = get_post_meta($footer->ID, 'newsletter', true);
$newsletter_title = get_post_meta($footer->ID, 'newsletter_title', true);
$locations = get_nav_menu_locations();

    ?>
    <script>
        console.log(<? echo json_encode($locations)?>)
    </script>
<footer class="footer-wrapper">
    <div class="root">
        <div class="item">
            <div class="site-description">
                <img height="110" width="250"
                    src="<?php echo $thumbnail_url ?>" alt=<?php echo get_bloginfo('name'); ?> class="logo">
                <p class="description">
                <? echo $site_description?>
                </p>
                <div class="footer-connect">
                    <span style="white-space: nowrap;">
                    <? echo $stay_in_touch?>
                    </span>
                    <div class="social-icons">
                        <?
                        if (!empty($social_media_links)) {
                            foreach ($social_media_links as $item) {
                                $link = esc_url($item['link']['url']);
                                $type = esc_html($item['type']);
                                $icon = get_stylesheet_directory_uri() . ('/assets/images/icons/Type=' . $type . ', Size=24, Color=CurrentColor.svg');
                                $image = "<img src='" . $icon . "' alt='" . $type . "' />";
                                echo '<a style="color: #fff" href="' . $link . '" class="icon">' . $image . '</a>';
                            }
                        }
                        ?>
                    </div>

                </div>
            </div>
        </div>
        <div class="item">
            <div class="links">
                <?
                if (!empty($menu_items)) {
                    foreach ($menu_items as $item) {
                        $page_link = esc_url($item['url']);
                        $label = esc_html($item['title']);
                        echo '<a href="' . $page_link . '" class="primary">' . $label . '</a>';
                    }
                }
                if (!empty($secondary_menus)) {
                    foreach ($secondary_menus as $item) {
                        $page_link = esc_url($item['url']);
                        $label = esc_html($item['label']);
                        echo '<a href="' . $page_link . '" class="secondary">' . $label . '</a>';
                    }
                }
                ?>
            </div>
        </div>
        <div class="item">
            <div class="embed">
                <p class="title">
                    <? echo $newsletter_title?>
                </p>
                <div>
                <? echo $newsletter?>
                </div>
            </div>
        </div>
    </div>
</footer>
