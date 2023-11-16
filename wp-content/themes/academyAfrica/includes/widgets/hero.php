<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}


class Academy_Africa_Hero extends \Elementor\Widget_Base
{

    public function get_name()
    {
        return 'Hero';
    }

    public function get_style_depends()
    {
        return ['academy-africa-hero', 'academy-africa'];
    }

    public function get_title()
    {
        return esc_html__('Hero', 'elementor-hero-widget');
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
                'type' => \Elementor\Controls_Manager::WYSIWYG,
                'default' => __('Learn digital & data skills for social impact.', 'academy-africa'),
                'label_block' => true,
            ]
        );
        $this->add_control(
            'sign_up_label',
            [
                'label' => __('Sign up Label', 'academy-africa'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => __('SIGN UP', 'academy-africa'),
                'label_block' => true,
            ]
        );
        $this->add_control(
            'sign_up_link',
            [
                'label' => esc_html__('Sign up Link', 'academy-africa'),
                'type' => \Elementor\Controls_Manager::URL,
                'options' => ['url', 'custom_attributes'],
                'default' => [
                    'url' => '/signin',
                    'label' => 'Sign In'
                ],
                'label_block' => true,
            ]
        );
        $metrics = new \Elementor\Repeater();
        $metrics->add_control(
            'metric',
            [
                'label' => __('Metric', 'academy-africa'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'label_block' => true,
                'placeholder' => 'e.g 6500'
            ]
        );
        $metrics->add_control(
            'label',
            [
                'label' => __('label', 'academy-africa'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'label_block' => true,
                'placeholder' => 'e.g members'
            ]
        );
        $this->add_control(
            'metrics',
            [
                'label' => esc_html__('Metrics', 'academy-africa'),
                'type' => \Elementor\Controls_Manager::REPEATER,
                'fields' => $metrics->get_controls(),
                'title_field' => '{{{ label }}}',
            ]
        );
        $this->end_controls_section();
    }

    protected function render()
    {
        $settings = $this->get_settings_for_display();
        $title = $settings['title'];
        $sign_up_label = $settings['sign_up_label'];
        $sign_up_url = $settings['sign_up_link']['url'];
?>
        <div class="hero">
            <div class="content-parent">
                <div class="content">
                    <div class="title">
                        <div class="cfa-title" <?php echo $this->get_render_attribute_string('title'); ?>>
                            <? echo $title ?>
                        </div>
                    </div>
                    <a class="button cta large signup-button" href="<?php echo $sign_up_url; ?>">
                        <? echo $sign_up_label ?>
                    </a>
                </div>
                <div class="metrics-content">
                    <div class="metrics">
                        <?
                        if (!empty($settings['metrics'])) {
                            foreach ($settings['metrics'] as $item) {
                                $metric = esc_html($item['metric']);
                                $label = esc_html($item['label']);
                        ?>
                                <div class="metric">
                                    <h2 class="numbers">
                                        <? echo $metric ?>
                                    </h2>
                                    <p class="label">
                                        <? echo $label ?>
                                    </p>
                                </div>
                        <?
                            }
                        }
                        ?>
                    </div>
                </div>
                <!-- <img height="217" alt="mask" class="mask" src="<?php echo get_stylesheet_directory_uri() . '/assets/images/mask.png' ?>"> -->
            </div>
            <!-- <div class="content-parent">
                <div class="metrics-content">
                    <div class="metrics">
                        <?
                        if (!empty($settings['metrics'])) {
                            foreach ($settings['metrics'] as $item) {
                                $metric = esc_html($item['metric']);
                                $label = esc_html($item['label']);
                        ?>
                                <div class="metric">
                                    <h2 class="numbers">
                                        <? echo $metric ?>
                                    </h2>
                                    <p class="label">
                                        <? echo $label ?>
                                    </p>
                                </div>
                        <?
                            }
                        }
                        ?>
                    </div>
                </div>
                <div class="mask"></div>
            </div> -->
        </div>
<?
    }
}
