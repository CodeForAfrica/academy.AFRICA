<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}


class Academy_Africa_Join_Our_Slack extends \Elementor\Widget_Base
{

    public function get_name()
    {
        return 'join-our-slack';
    }

    public function get_style_depends()
    {
        return ['academy-africa-join-our-slack', 'academy-africa'];
    }

    public function get_title()
    {
        return esc_html__('Join Our Slack', 'elementor-hero-widget');
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
                'default' => __('Join our slack community.', 'academy-africa'),
                'label_block' => true,
            ]
        );
        $this->add_control(
            'content',
            [
                'label' => __('Content', 'academy-africa'),
                'type' => \Elementor\Controls_Manager::WYSIWYG,
                'default' => __('<p>This is an instruction of how to join the slack community</p>', 'academy-africa'),
                'label_block' => true,
            ]
        );
        // Slack URL control
        $this->add_control(
            'slack_url',
            [
                'label' => __('Slack URL', 'academy-africa'),
                'type' => \Elementor\Controls_Manager::URL,
                'description' => __('Enter the URL to your slack workspace', 'academy-africa'),
                'default' => [
                    'url' => 'https://academy-africa.slack.com/',
                    'is_external' => true,
                    'nofollow' => false,
                ],
                'label_block' => true,
            ]
        );
        $this->end_controls_section();
    }

    protected function render()
    {
        $settings = $this->get_settings_for_display();
        $title = $settings['title'];
        $content = $settings['content'];
        $slack_url = $settings['slack_url'];
?>
        <div class="join-our-slack">
            <div class="title">
                <h2 class="cfa-title"><?php echo $title; ?></h2>
            </div>
            <div class="content">
                <?php echo $content; ?>
            </div>
            <div class="form">
                <form action="<?php echo $slack_url['url']; ?>" method="GET">
                    <div class="input-group">
                        <label for="name">Full Name</label>
                        <input type="text" name="name" id="name" placeholder="Enter your name" required>
                    </div>
                    <div class="input-group">
                        <label for="email">Email</label>
                        <input type="email" name="email" id="email" placeholder="Enter your email">
                    </div>
                    <div class="input-group">
                        <label for="position">Position</label>
                        <input type="text" name="position" id="position" placeholder="Enter your position">
                    </div>
                    <div class="input-group">
                        <label for="company">Company</label>
                        <input type="text" name="company" id="company" placeholder="Enter your company">
                    </div>
                    <div class="input-group">
                        <button type="submit" class="button primary small">Submit</button>
                    </div>
                </form>
            </div>
        </div>
<?
    }
}
