<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

class Academy_Africa_Partners extends \Elementor\Widget_Base
{

    public function get_name()
    {
        return 'Partners';
    }

    public function get_style_depends()
    {
        return ['academy-africa-partners', 'academy-africa-other-partners'];
    }

    public function get_title()
    {
        return esc_html__('Partners');
    }

    public function get_icon()
    {
        return 'eicon-code';
    }

    public function get_categories()
    {
        return ['academy-africa'];
    }

    public function get_our_partners()
    {
        return [
            [
                "name" => "Google News Initiative",
                "url" => "/",
                "icon" => get_stylesheet_directory_uri() . '/assets/images/gni.png'
            ],
            [
                "name" => "World Bank",
                "url" => "/",
                "icon" => get_stylesheet_directory_uri() . '/assets/images/world_bank.png'
            ],
            [
                "name" => "Dw",
                "url" => "/",
                "icon" => get_stylesheet_directory_uri() . '/assets/images/dw.png'
            ], [
                "name" => "bbc",
                "url" => "/",
                "icon" => get_stylesheet_directory_uri() . '/assets/images/bbc.png'
            ], [
                "name" => "icfj",
                "url" => "/",
                "icon" => get_stylesheet_directory_uri() . '/assets/images/icfj.png'
            ], [
                "name" => "nor_cap",
                "url" => "/",
                "icon" => get_stylesheet_directory_uri() . '/assets/images/nor_cap.png'
            ]
        ];
    }

    public function get_other_partners()
    {
        return [[
            "name" => "🇰🇪 Aga Khan University",
            "url" => "/",
            "icon" => "/wp-content/plugins/academy-africa/includes/assets/images/gni.png"
        ], [
            "name" => "🇳🇬 pan-atlantic university",
            "url" => "/",
            "icon" => "/wp-content/plugins/academy-africa/includes/assets/images/dw.png"
        ], [
            "name" => "🇰🇪 Daystar University",
            "url" => "/",
            "icon" => "/wp-content/plugins/academy-africa/includes/assets/images/gni.png"
        ], [
            "name" => "🇰🇪 Strathmore University",
            "url" => "/",
            "icon" => "/wp-content/plugins/academy-africa/includes/assets/images/gni.png"
        ], [
            "name" => "🇿🇦 University of johannesburg",
            "url" => "/",
            "icon" => "/wp-content/plugins/academy-africa/includes/assets/images/nor_cap.png"
        ], [
            "name" => "🇸🇳 Institut Supérieur Des Sciences De L'information Et De La Communication (ISSIC)",
            "url" => "/",
            "icon" => "/wp-content/plugins/academy-africa/includes/assets/images/icfj.png"
        ]];
    }

    protected function register_controls()
    {
        $this->start_controls_section(
            'section_header',
            [
                'label' => __('Content', 'academy-africa'),
            ]
        );
        $this->add_control(
            'title',
            [
                'label' => __('Title', 'academy-africa'),
                'type' => \Elementor\Controls_Manager::TEXTAREA,
                'default' => __('Our Partners', 'academy-africa'),
                'label_block' => true,
            ]
        );
        $this->add_control(
            'other_partners_title',
            [
                'label' => __('Other Partners Title', 'academy-africa'),
                'type' => \Elementor\Controls_Manager::TEXTAREA,
                'default' => __('Academic Partners', 'academy-africa'),
                'label_block' => true,
            ]
        );
        $this->end_controls_section();
    }
    protected function render()
    {
        $settings = $this->get_settings_for_display();
        $other_partners_title = $settings["other_partners_title"];
        $title = $settings["title"];
        $our_partners = $this->get_our_partners();
        $other_partners = $this->get_other_partners();
?>
        <div class="patners-root">
            <div class="partners">
                <div class="title">
                    <h4 class="cfa-title">
                        <? echo $title ?>
                    </h4>
                </div>
                <div class="partner-content">
                    <?
                    if (!empty($our_partners)) {
                        foreach ($our_partners as $partner) {
                            $icon = $partner["icon"];
                            $name = $partner["name"];
                            $url = $partner["url"];
                    ?>
                            <a href="<? echo $url ?>">
                                <div class="partner" style="background: url(<? echo $icon ?>); background-size: cover; background-position: center; background-repeat: no-repeat; background-color: lightgray;"></div>
                            </a>
                    <?
                        }
                    }
                    ?>
                </div>
            </div>
            <hr />
            <div class="other-partners">
                <h1 class="other-partners-title"><? echo $other_partners_title ?></h1>
                <div class="other-partners-content">
                    <?
                    if (!empty($other_partners)) {
                        foreach ($other_partners as $partner) {
                            $icon = $partner["icon"];
                            $name = $partner["name"];
                            $url = $partner["url"];
                    ?>
                            <div class="partner-tag">
                                <div class="partner-tag-content">
                                    <? echo $name ?>
                                </div>
                            </div>
                    <?
                        }
                    }
                    ?>
                </div>
            </div>
        </div>

<?
    }
}
