<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

class Academy_Africa_Connect_and_Collaborate extends \Elementor\Widget_Base
{

    public function get_name()
    {
        return 'Connect and Collaborate';
    }

    public function get_style_depends()
    {
        return ['academy-africa-connect'];
    }

    public function get_title()
    {
        return esc_html__('Connect and Collaborate');
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
                'label' => __('Content', 'academy-africa'),
            ]
        );
        $this->add_control(
            'title',
            [
                'label' => __('Title', 'academy-africa'),
                'type' => \Elementor\Controls_Manager::TEXTAREA,
                'default' => __('Connect & Collaborate', 'academy-africa'),
                'label_block' => true,
            ]
        );
        $this->add_control(
            'join_us_on_slack',
            [
                'label' => __('Join us on Slack', 'academy-africa'),
                'type' => \Elementor\Controls_Manager::TEXTAREA,
                'default' => __('Join us on Slack', 'academy-africa'),
                'label_block' => true,
            ]
        );
        $this->add_control(
            'share_text',
            [
                'label' => __('Share Text', 'academy-africa'),
                'type' => \Elementor\Controls_Manager::TEXTAREA,
                'default' => __('Share your work with colleagues in the same industry.', 'academy-africa'),
                'label_block' => true,
            ]
        );
        $this->add_control(
            'become_a_member_text',
            [
                'label' => __('Become a Member Text', 'academy-africa'),
                'type' => \Elementor\Controls_Manager::TEXTAREA,
                'default' => __('Become a member of academy.AFRICA', 'academy-africa'),
                'label_block' => true,
            ]
        );
        $this->add_control(
            'about_us_url',
            [
                'label' => esc_html__('About us', 'academy-africa'),
                'type' => \Elementor\Controls_Manager::URL,
                'options' => ['url', 'custom_attributes'],
                'default' => [
                    'url' => '/',
                ],
                'label_block' => true,
            ]
        );
        $this->end_controls_section();
    }
    protected function render()
    {
        $settings = $this->get_settings_for_display();
        $share_text = $settings["share_text"];
        $become_a_member_text = $settings["become_a_member_text"];
        $join_us_on_slack = $settings["join_us_on_slack"];
        $title = $settings["title"];
        $about_us_url = $settings["about_us_url"];
        ?>
        <div class="connect">
            <div class="cfa-title-centered">
                <h4>
                    <? echo $title ?>
                </h4>
            </div>
            <div class="content">
                <div class="center africa">
                    <img src="/wp-content/plugins/academy-africa/includes/assets/images/africa.png" alt="connect"
                        class="africa">
                </div>
                <div class="banner center">
                    <p class="share">
                        <? echo $share_text ?>
                    </p>
                    <p class="become-a-member">
                        <? echo $become_a_member_text ?>
                    </p>
                    <a href=src="<?php echo $about_us_url ?>" class="primary-button">
                        <? echo $join_us_on_slack ?>
                    </a>
                </div>
            </div>
        </div>
        <?
    }
}
