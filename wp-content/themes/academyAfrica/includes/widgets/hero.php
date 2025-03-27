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

    public function get_verified_users()
    {
        $args = [
            'meta_key' => 'is_verified',
            'meta_value' => '1'
        ];
        $verified_users = get_users($args);

        return $verified_users;
    }

    public function get_verified_users_count()
    {
        $verified_users = $this->get_verified_users();
        return count($verified_users);
    }

    public function get_courses_count()
    {
        $all_courses = get_posts([
            'post_type' => 'sfwd-courses',
            'post_status' => 'publish',
            'numberposts' => -1,
            'fields' => 'ids'
        ]);
        return count($all_courses);
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
        $metrics = !empty($settings['metrics']) ? $settings['metrics'] : array();

        array_unshift($metrics, [
            'metric' => $this->get_courses_count(),
            'label' => 'Courses'
        ]);
        array_unshift($metrics, [
            'metric' => $this->get_verified_users_count(),
            'label' => 'Members'
        ]);
?>
        <script>
            console.log(<? echo json_encode($this->get_verified_users()) ?>);
        </script>
        <?
        ?>
        <div class="hero">
            <div class="background-image"></div>
            <div class="content-parent">
                <div class="content">
                    <div class="title">
                        <div class="cfa-title" <?php echo $this->get_render_attribute_string('title'); ?>>
                            <? echo $title ?>
                        </div>
                    </div>
                    <?
                    if (!is_user_logged_in()) {
                    ?>
                        <button class="button cta large signup-button" onclick="register()">
                            <? echo $sign_up_label ?>
                        </button>
                    <?
                    }
                    ?>
                </div>
                <div class="metrics-content">
                    <div class="metrics">
                        <?
                        if (!empty($metrics)) {
                            foreach ($metrics as $item) {
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
            </div>
            <div class="mask-image">
                <img alt="mask" class="mask" src="<?php echo get_stylesheet_directory_uri() . '/assets/images/mask.svg' ?>">
            </div>
            <script>
                function register() {
                    window.location.href = "/login?action=register"
                }
            </script>
        </div>
<?
    }
}
