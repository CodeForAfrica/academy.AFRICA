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
        $settings = $this->get_settings_for_display();
        $other_partners = $settings['partners'];
        $output = array();
        foreach ($other_partners as $key) {
            $new_array = array("name" => $key['title'], 'icon' => $key['image']['url'], 'url' => $key['url']);
            array_push($output, $new_array);
        }

        return $output;
    }

    public function get_other_partners()
    {
        $settings = $this->get_settings_for_display();
        $other_partners = $settings['other_partners'];
        $output = array();
        foreach ($other_partners as $key) {
            $new_array = array("name" => $key['title'], 'icon' => $key['image'], 'url' => $key['url']);
            array_push($output, $new_array);
        }
        return $output;
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
        $partner = new \Elementor\Repeater();
        $partner->add_control(
            'title',
            [
                'label' => __('Title', 'academy-africa'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'label_block' => true,
                'placeholder' => 'Title'
            ]
        );
        $partner->add_control(
            'url',
            [
                'label' => __('URL', 'academy-africa'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'label_block' => true,
            ]
        );
        $partner->add_control(
            'image',
            [
                'label' => __('Slider Image', 'academy-africa'),
                'type' => \Elementor\Controls_Manager::MEDIA,
                'label_block' => true,
            ]
        );
        $this->add_control(
            'partners',
            [
                'label' => esc_html__('Partners', 'academy-africa'),
                'type' => \Elementor\Controls_Manager::REPEATER,
                'fields' => $partner->get_controls(),
                'title_field' => '{{{ title }}}',
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
        $this->add_control(
            'other_partners',
            [
                'label' => esc_html__('Other Partners', 'academy-africa'),
                'type' => \Elementor\Controls_Manager::REPEATER,
                'fields' => $partner->get_controls(),
                'title_field' => '{{{ title }}}',
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
                            <a href="<? echo $url ?>" target="_blank" class="partner-link">
                                <img src="<? echo $icon ?>" alt="<? echo $name ?>" class="partner" />
                                <!-- <div class="partner" style="background: url(<? echo $icon ?>); background-size: cover; background-position: center; background-repeat: no-repeat; background-color: lightgray;"></div> -->
                            </a>
                            <?
                        }
                    }
                    ?>
                </div>
            </div>
            <hr />
            <div class="other-partners">
                <h1 class="other-partners-title">
                    <? echo $other_partners_title ?>
                </h1>
                <div class="other-partners-content">
                    <?
                    if (!empty($other_partners)) {
                        foreach ($other_partners as $partner) {
                            $icon = $partner["icon"];
                            $name = $partner["name"];
                            $url = $partner["url"];
                            ?>
                            <a href="<? echo $url ?>" target="_blank" class="partner-tag">
                                <div class="partner-tag-content">
                                    <? echo $name ?>
                                </div>
                            </a>
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
