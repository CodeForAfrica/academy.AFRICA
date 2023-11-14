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
        return [
            [
                "name" => "Sakina Salem",
                "role" => "Senior product Manager",
                "company" => "Code For Africa",
                "description" => "The Drone Journalism course enhanced my abilities to capture interesting moments. I won an award for the images used in the Sucked Dry Project by InfoNile"
            ], [
                "name" => "Sakina Salem",
                "role" => "Senior product Manager",
                "company" => "Code For Africa",
                "description" => "The Drone Journalism course enhanced my abilities to capture interesting moments. I won an award for the images used in the Sucked Dry Project by InfoNile"
            ],
            [
                "name" => "Sakina Salem",
                "role" => "Senior product Manager",
                "company" => "Code For Africa",
                "description" => "The Drone Journalism course enhanced my abilities to capture interesting moments. I won an award for the images used in the Sucked Dry Project by InfoNile"
            ]
        ];
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
        $this->end_controls_section();
    }
    protected function render()
    {
        $settings = $this->get_settings_for_display();
        $title = $settings["title"];
        $feedback = $this->get_user_feedback();
?>
        <div class="feedback">
            <div class="cfa-title">
                <h4>
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
                ?>
                        <div class="card">
                            <div class="content-card">
                                <div class="user">
                                    <img src="<? echo get_stylesheet_directory_uri() . '/assets/images/avatar.png' ?>" alt="user" class="avatar">
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
