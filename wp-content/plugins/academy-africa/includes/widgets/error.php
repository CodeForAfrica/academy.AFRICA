<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

class Academy_Africa_Error extends \Elementor\Widget_Base
{
    public function get_name()
    {
        return 'Error';
    }

    public function get_style_depends()
    {
        return ['academy-africa-error'];
    }

    public function get_title()
    {
        return esc_html__('Error', 'elementor-error-widget');
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
            'status_code',
            [
                'label' => __('Status Code', 'academy-africa'),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'default' => __("500", 'academy-africa'),
            ]
        );
        $this->add_control(
            'title',
            [
                'label' => __('Title', 'academy-africa'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => __("INTERNAL SERVER ERROR", 'academy-africa'),
            ]
        );
        $this->add_control(
            'description',
            [
                'label' => __('Description', 'academy-africa'),
                'type' => \Elementor\Controls_Manager::TEXTAREA,
                'default' => __('Internal Server Error', 'academy-africa'),
            ]
        );

        $this->add_control(
            'refreshButtonLabel',
            [
                'label' => __('Refresh Button Label', 'academy-africa'),
                'type' => \Elementor\Controls_Manager::TEXTAREA,
                'default' => __('Refresh', 'academy-africa'),
            ]
        );

        $this->add_control(
            'homepageButtonLabel',
            [
                'label' => __('Back to Homepage Button Label', 'academy-africa'),
                'type' => \Elementor\Controls_Manager::TEXTAREA,
                'default' => __('Back to Homepage', 'academy-africa'),
            ]
        );

        $this->end_controls_section();
    }

    protected function render()
    {
        $settings = $this->get_settings_for_display();
        $description = $settings['description'];
        $status_code = $settings['status_code'];
        $title = $settings['title'];
        $refresh = $settings['refreshButtonLabel'];
        $home = $settings['homepageButtonLabel'];
?>
        <div class="error">
            <div></div>
            <div class="content">
                <p class="code" <?php echo $this->get_render_attribute_string('status_code'); ?>>
                    <? echo $status_code ?>
                </p>
                <p class="text" <?php echo $this->get_render_attribute_string('title'); ?>>
                    <? echo $title ?>
                </p>
                <p class="description" <?php echo $this->get_render_attribute_string('description'); ?>>
                    <? echo $description ?>
                </p>
                <div class="actions">
                    <a class="button" href="" onclick="location.reload();" <?php echo $this->get_render_attribute_string('refresh'); ?>>
                        <? echo $refresh ?>
                    </a>
                    <a class="button" href="/" <?php echo $this->get_render_attribute_string('home'); ?>>
                        <? echo $home ?>
                    </a>
                </div>
            </div>

        </div>
<?
    }
}
