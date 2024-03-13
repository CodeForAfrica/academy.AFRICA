<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}


class Academy_Africa_About_Section extends \Elementor\Widget_Base
{

    public function get_name()
    {
        return 'about-section';
    }

    public function get_style_depends()
    {
        return ['academy-africa-about-section', 'academy-africa'];
    }

    public function get_title()
    {
        return esc_html__('About Section', 'elementor-hero-widget');
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
            'content',
            [
                'label' => __('Content', 'academy-africa'),
                'type' => \Elementor\Controls_Manager::WYSIWYG,
                'default' => __('
                    <p>The Future Is Here</p>
                    <p>African newsrooms often feel left behind.</p>
                    <p>Access to the tools and know-how to leapfrog into digital journalism is often limited to only a few newsrooms.</p>
                ', 'academy-africa'),
                'label_block' => true,
            ]
        );
        $this->end_controls_section();
    }

    protected function render()
    {
        $settings = $this->get_settings_for_display();
        $content = $settings['content'];
?>
        <div class="about-section">
            <div class="content wysiwyg">
                <?php echo $content; ?>
            </div>
        </div>
<?
    }
}
