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

// Get current language from Polylang (defaults to 'en' if Polylang not active)
$current_language = function_exists('pll_current_language') ? pll_current_language() : 'en';

$menu_items = MenuFunctions::get_menu_items('menu-2', $current_language);


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

// Use Polylang for translatable secondary menu labels
$secondary_menus = [
    [
        'url' => '/privacy-policy',
        'label' => academyafrica_translate('Imprint'),
    ],
    [
        'url' => '/privacy-policy',
        'label' => academyafrica_translate('Privacy'),
    ]
];

// Query footer by current language
$search = array(
    'post_type' => 'footer',
    'posts_per_page' => -1,
    'lang' => $current_language,
);

$custom_posts = get_posts($search);

// Fallback to English if no footer found for current language
if (empty($custom_posts)) {
    $search['lang'] = 'en';
    $custom_posts = get_posts($search);
}

// A footer post may not exist (e.g. none published for this language and no
// English fallback). Guard against it so the site chrome renders an intentional
// empty state instead of emitting warnings on a null post.
$footer = !empty($custom_posts) ? $custom_posts[0] : null;

if ($footer instanceof \WP_Post) {
    $logo             = get_post_meta($footer->ID, 'logo', true);
    $site_description = get_post_meta($footer->ID, 'site_description', true);
    $stay_in_touch    = get_post_meta($footer->ID, 'stay_in_touch', true);
    $secondary_links  = get_post_meta($footer->ID, 'secondary_links', true);
    $newsletter       = get_post_meta($footer->ID, 'newsletter', true);
    $newsletter_title = get_post_meta($footer->ID, 'newsletter_title', true);
} else {
    $logo = $site_description = $stay_in_touch = $secondary_links = $newsletter = $newsletter_title = '';
}

// wp_get_attachment_image_src() returns false for an empty/invalid attachment;
// guard the [0] offset so we never index a bool.
$logo_src      = $logo ? wp_get_attachment_image_src($logo, 100) : false;
$thumbnail_url = is_array($logo_src) ? $logo_src[0] : '';

?>
<footer class="footer-wrapper">
    <div class="root">
        <div class="item">
            <div class="site-description">
                <img height="110" width="250"
                    src="<?php echo $thumbnail_url ?>" alt=<?php echo get_bloginfo('name'); ?> class="logo">
                <p class="description">
                    <?php echo $site_description ?>
                </p>
                <div class="footer-connect">
                    <span style="white-space: nowrap;">
                        <?php echo $stay_in_touch ?>
                    </span>
                    <div class="social-icons">
                        <?php
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
                <?php
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
                    <?php echo $newsletter_title ?>
                </p>
                <div>
                    <?php echo $newsletter ?>
                </div>
            </div>
        </div>
    </div>
</footer>
