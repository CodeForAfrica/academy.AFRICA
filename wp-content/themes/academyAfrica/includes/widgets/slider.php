<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}


class Academy_Africa_Slider  extends \Elementor\Widget_Base
{

    public function get_name()
    {
        return 'slider';
    }

    public function get_style_depends()
    {
        return ['academy-africa-slider', 'academy-africa'];
    }

    public function get_title()
    {
        return esc_html__('Slider', 'elementor-faq-widget');
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
            'title',
            [
                'label' => __('Title', 'academy-africa'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => __('FAQ\'s', 'academy-africa'),
                'label_block' => true,
            ]
        );
        $faqs = new \Elementor\Repeater();
        $faqs->add_control(
            'title',
            [
                'label' => __('Title', 'academy-africa'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'label_block' => true,
                'placeholder' => 'FAQ Title'
            ]
        );
        $faqs->add_control(
            'content',
            [
                'label' => __('content', 'academy-africa'),
                'type' => \Elementor\Controls_Manager::WYSIWYG,
                'label_block' => true,
            ]
        );
        $this->add_control(
            'faqs',
            [
                'label' => esc_html__('FAQ\'s', 'academy-africa'),
                'type' => \Elementor\Controls_Manager::REPEATER,
                'fields' => $faqs->get_controls(),
                'title_field' => '{{{ title }}}',
            ]
        );
        $this->end_controls_section();
    }


    protected function render()
    {
        $settings = $this->get_settings_for_display();
        $this->add_inline_editing_attributes('title', 'none');
?>
        <div class="slider-root">
            <h1>Slider</h1>
        </div>
<?
    }
}
