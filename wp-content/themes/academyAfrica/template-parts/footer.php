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

$menu_items = MenuFunctions::get_menu_items('menu-2');

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
]


    ?>

<footer class="footer-wrapper">
    <div class="root">
        <div class="item">
            <div class="site-description">
                <img height="110" width="250"
                    src="<?php echo get_stylesheet_directory_uri() . '/assets/images/cfa_logo.svg' ?>" alt=<?php echo get_bloginfo('name'); ?> class="logo">
                <p class="description">
                    This site is a project of Code for Africa, the continent's largest network of civic technology and
                    data journalism labs. All content is released under a Creative Commons Attribution Licence. Reuse it
                    to help empower your own community.
                </p>
                <div class="footer-connect">
                    <span style="white-space: nowrap;">
                        Stay in touch
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
                    Subscribe to the Code for Africa newsletter
                </p>
                <div>
                    <div id="mc_embed_signup">
                        <form
                            action="https://twitter.us6.list-manage.com/subscribe/post?u=65e5825507b3cec760f272e79&amp;id=c2ff751541"
                            method="post" id="mc-embedded-subscribe-form" name="mc-embedded-subscribe-form"
                            class="validate" target="_blank" novalidate="">
                            <div id="mc_embed_signup_scroll">
                                <label for="MERGE1">Name</label>
                                <input type="text" name="MERGE1" id="MERGE1" size="25" value="" placeholder="Your name">
                                <label for="mce-EMAIL">Email</label>
                                <input type="email" value="" placeholder="example@email.com" name="EMAIL" class="email"
                                    id="mce-EMAIL" required="">
                                <!-- real people should not fill this in and expect good things - do not remove this or risk form bot signups-->
                                <div style="position: absolute; left: -5000px;" aria-hidden="true"><input type="text"
                                        name="b_65e5825507b3cec760f272e79_c2ff751541" tabindex="-1" value=""></div>
                                <div class="clear"><input type="submit" value="Sign up" id="mc-embedded-subscribe"
                                        class="button"></div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>