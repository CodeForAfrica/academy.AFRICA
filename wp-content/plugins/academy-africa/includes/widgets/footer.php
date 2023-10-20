<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}


class Academy_Africa_Footer extends \Elementor\Widget_Base
{

    public function get_name()
    {
        return 'footer';
    }

    public function get_style_depends()
    {
        return ['academy-africa-footer'];
    }

    public function get_title()
    {
        return esc_html__('footer', 'elementor-footer-widget');
    }

    public function get_icon()
    {
        return 'eicon-code';
    }

    public function get_categories()
    {
        return ['academy-africa'];
    }

    protected function register_controls()
    {
        $this->start_controls_section(
            'section_header',
            [
                'label' => __('Header', 'academy-africa'),
            ]
        );
        $this->add_control(
            'logo',
            [
                'label' => esc_html__('Choose Image', 'academy-africa'),
                'type' => \Elementor\Controls_Manager::MEDIA,
                'default' => [
                    'url' => wp_get_attachment_image_src($this->get_custom_logo()[0], 'full'),
                ],
            ]
        );
        $this->add_control(
            'site_description',
            [
                'label' => __('Site Description', 'academy-africa'),
                'type' => \Elementor\Controls_Manager::TEXTAREA,
                'default' => __('', 'academy-africa'),
            ]
        );

        $this->add_control(
            'stay_in_touch_text',
            [
                'label' => __('Stay in Touch', 'academy-africa'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => __('STAY IN TOUCH', 'academy-africa'),
            ]
        );
        $links = new \Elementor\Repeater();

        $links->add_control(
            'label',
            [
                'label' => esc_html__('Label', 'academy-africa'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => 'Link Label',
            ]
        );

        $links->add_control(
            'page_link',
            [
                'label' => esc_html__('Page Link', 'academy-africa'),
                'type' => \Elementor\Controls_Manager::URL,
                'default' => [
                    'url' => '',
                ],
            ]
        );

        $social_media = new \Elementor\Repeater();
        $social_media->add_control(
            'type',
            [
                'label' => esc_html__('Type', 'academy-africa'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'github',
                'options' => [
                    'facebook' => esc_html__('Facebook', 'academy-africa'),
                    'github' => esc_html__('Github', 'academy-africa'),
                    'slack' => esc_html__('Slack', 'academy-africa'),
                    'linkedin' => esc_html__('LinkedIn', 'academy-africa'),
                    'instagram' => esc_html__('Instagram', 'academy-africa'),
                    'twitter' => esc_html__('Twitter', 'academy-africa'),
                ]
            ]
        );

        $social_media->add_control(
            'link',
            [
                'label' => esc_html__('Page Link', 'academy-africa'),
                'type' => \Elementor\Controls_Manager::URL,
                'default' => [
                    'url' => '',
                ],
            ]
        );

        $this->add_control(
            'social_media_links',
            [
                'label' => esc_html__('Social Media Links', 'academy-africa'),
                'type' => \Elementor\Controls_Manager::REPEATER,
                'fields' => $social_media->get_controls(),
                'title_field' => '{{{ type }}}',
            ]
        );

        $this->add_control(
            'primary_links',
            [
                'label' => esc_html__('Primary Links', 'academy-africa'),
                'type' => \Elementor\Controls_Manager::REPEATER,
                'fields' => $links->get_controls(),
                'title_field' => '{{{ label }}}',
            ]
        );

        $this->add_control(
            'secondary_links',
            [
                'label' => esc_html__('Primary Links', 'academy-africa'),
                'type' => \Elementor\Controls_Manager::REPEATER,
                'fields' => $links->get_controls(),
                'title_field' => '{{{ label }}}',
            ]
        );
        $this->add_control(
            'newsletter_signup_text',
            [
                'label' => __('Sign in to our Newslettter', 'academy-africa'),
                'type' => \Elementor\Controls_Manager::TEXTAREA,
                'default' => __('Subscribe to the Code for Africa newsletter', 'academy-africa'),
            ]
        );
        $this->add_control(
            'newsletter_embed_code',
            [
                'label' => __('Newsletter embed code', 'academy-africa'),
                'type' => \Elementor\Controls_Manager::CODE,
            ]
        );
        $this->end_controls_section();
    }

    function get_menu_items($name)
    {
        $list = wp_get_nav_menu_items($name);
        $menu_items = [];
        foreach ($list as $item) {
            $menu_items[] = [
                'title' => $item->title,
                'url' => $item->url,
                'id' => $item->ID,
                'parent_id' => $item->menu_item_parent,
            ];
        }
        $menu = [];
        foreach ($menu_items as $item) {
            if ($item['parent_id'] == 0) {
                $menu[$item['id']] = $item;
            } else {
                $menu[$item['parent_id']]['children'][] = $item;
            }
        }
        return $menu;
    }

    function get_custom_logo()
    {
        $custom_logo_id = get_theme_mod('custom_logo');
        $image = wp_get_attachment_image($custom_logo_id, 'full');
        return $image;
    }


    protected function render()
    {
        $settings = $this->get_settings_for_display();
        $newsletter_embed_code = $settings['newsletter_embed_code'];
        $site_description = $settings['site_description'];
        $stay_in_touch_text = $settings['stay_in_touch_text'];
        $image_url = isset($settings['logo']['url']) ? esc_url($settings['logo']['url']) : '';

?>
        <footer class="root">
            <div class="item">
                <div class="site-description">
                    <img height="110" width="250" src="<?php echo $image_url ?>" alt=<?php echo get_bloginfo('name'); ?> class="logo">
                    <p class="description" <?php echo $this->get_render_attribute_string('site_description'); ?>>
                        <? echo $site_description ?>
                    </p>
                    <div class="connect">
                        <span style="white-space: nowrap;" <?php echo $this->get_render_attribute_string('stay_in_touch_text'); ?>>
                            <? echo $stay_in_touch_text ?>
                        </span>
                        <?
                        if (!empty($settings['social_media_links'])) {
                            foreach ($settings['social_media_links'] as $item) {
                                $link = esc_url($item['link']['url']);
                                $type = esc_html($item['type']);
                                $icon = dirname(plugin_dir_url(__FILE__)) . ('/assets/images/icons/Type=' . $type . ', Size=24, Color=CurrentColor.svg');
                                $content = file_get_contents($icon);
                                echo '<a style="color: #fff" href="' . $link . '" class="icon">' . $content . '</a>';
                            }
                        }
                        ?>
                    </div>
                </div>
            </div>
            <div class="item">
                <div class="links">
                    <?
                    if (!empty($settings['primary_links'])) {
                        foreach ($settings['primary_links'] as $item) {
                            $page_link = esc_url($item['page_link']['url']);
                            $label = esc_html($item['label']);
                            echo '<a href="' . $page_link . '" class="primary">' . $label . '</a>';
                        }
                    }
                    if (!empty($settings['secondary_links'])) {
                        foreach ($settings['secondary_links'] as $item) {
                            $page_link = esc_url($item['page_link']['url']);
                            $label = esc_html($item['label']);
                            echo '<a href="' . $page_link . '" class="secondary">' . $label . '</a>';
                        }
                    }
                    ?>
                </div>
            </div>
            <div class="item">
                <div class="embed">
                    <p class="title" <?php echo $this->get_render_attribute_string('newsletter_signup_text'); ?>>
                        <?php echo $settings['newsletter_signup_text']; ?>
                    </p>
                    <div <?php echo $this->get_render_attribute_string('newsletter_embed_code'); ?>>
                        <?php echo $newsletter_embed_code; ?>
                    </div>
                </div>
            </div>
        </footer>
<?php

    }
}
