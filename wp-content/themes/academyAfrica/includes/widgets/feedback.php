<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

class Academy_Africa_User_Feedback extends \Elementor\Widget_Base
{

    public function get_name()
    {
        return 'User Feedback';
    }

    public function get_style_depends()
    {
        return ['academy-africa-feedback'];
    }

    public function get_title()
    {
        return esc_html__('Feedback');
    }

    public function get_icon()
    {
        return 'eicon-code';
    }

    public function get_categories()
    {
        return ['academy-africa'];
    }

    public function get_user_feedback()
    {
        $settings = $this->get_settings_for_display();
        $testimonials = $settings["testimonials"];
        $output = array();
        foreach ($testimonials as $testimonial) {
            $item = array(
                "name" => $testimonial["name"],
                "role" => $testimonial["role"],
                "company" => $testimonial["company"],
                "description" => $testimonial["description"],
                "logo" => $testimonial["logo"]["url"]
            );
            $output[] = $item;
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
                'default' => __('What members have to say', 'academy-africa'),
                'label_block' => true,
            ]
        );
        $testimonial = new \Elementor\Repeater();
        $testimonial->add_control(
            'logo',
            [
                'label' => __('Logo', 'academy-africa'),
                'type' => \Elementor\Controls_Manager::MEDIA,
                'default' => [
                    'url' => \Elementor\Utils::get_placeholder_image_src(),
                ],
            ]
        );
        $testimonial->add_control(
            'name',
            [
                'label' => __('Name', 'academy-africa'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'label_block' => true,
                'placeholder' => 'Name'
            ]
        );
        $testimonial->add_control(
            'role',
            [
                'label' => __('Role', 'academy-africa'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'label_block' => true,
                'placeholder' => 'Role'
            ]
        );
        $testimonial->add_control(
            'company',
            [
                'label' => __('Company', 'academy-africa'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'label_block' => true,
                'placeholder' => 'Company'
            ]
        );
        $testimonial->add_control(
            'description',
            [
                'label' => __('Description', 'academy-africa'),
                'type' => \Elementor\Controls_Manager::TEXTAREA,
                'label_block' => true,
            ]
        );
        $this->add_control(
            'testimonials',
            [
                'label' => esc_html__('Testimonials', 'academy-africa'),
                'type' => \Elementor\Controls_Manager::REPEATER,
                'fields' => $testimonial->get_controls(),
                'title_field' => '{{{ name }}}',
            ]
        );
        $this->end_controls_section();
    }
    protected function render()
    {
        $settings = $this->get_settings_for_display();
        $title = $settings["title"];
        $testimonials = $settings["testimonials"];
        $feedback = $this->get_user_feedback();
?>
        <div class="feedback">
            <div class="title">
                <h4 class="cfa-title">
                    <? echo $title ?>
                </h4>
            </div>
            <div class="content">
                <?
                if (!empty($feedback)) {
                    foreach ($feedback as $item) {
                        $name = $item["name"];
                        $role = $item["role"];
                        $company = $item["company"];
                        $description = $item["description"];
                        $logo = $item["logo"]
                ?>
                        <div class="card">
                            <div class="content-card">
                                <div class="user">
                                    <img src="<? echo $logo ?>" alt="user" class="avatar">
                                    <div class="name-role">
                                        <p class="name">
                                            <? echo $name ?>
                                        </p>
                                        <p class="role">
                                            <? echo $role ?>
                                        </p>
                                        <p class="role">
                                            <? echo $company ?>
                                        </p>
                                    </div>
                                </div>
                                <div class="description">
                                    <? echo $description ?>
                                </div>
                            </div>
                        </div>
                <?
                    }
                }
                ?>
            </div>
        </div>
<?
    }
}
