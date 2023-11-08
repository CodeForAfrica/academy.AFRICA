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
            'header_image',
            [
                'label' => __('Header Image', 'academy-africa'),
                'type' => \Elementor\Controls_Manager::MEDIA,
                'default' => [
                    'url' => \Elementor\Utils::get_placeholder_image_src(),
                ],
            ]
        );
        $this->add_control(
            'title',
            [
                'label' => __('Title', 'academy-africa'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => __('academy.AFRICA', 'academy-africa'),
                'label_block' => true,
            ]
        );
        $this->add_control(
            'headline',
            [
                'label' => __('Headline', 'academy-africa'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => __('Giving African newsrooms access to the world’s best digital experts and storytelling technologies.', 'academy-africa'),
                'label_block' => true,
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
        $title = $settings['title'];
        $content = $settings['content'];
        $headline = $settings['headline'];
        $header_image = $settings['header_image']['url'];
        $social_media_links = [
            [
                'link' => [
                    'url' => 'https://www.facebook.com/CodeForAfrica',
                ],
                'type' => 'facebook',
            ],
            [
                'link' => [
                    'url' => 'https://twitter.com/Code4Africa',
                ],
                'type' => 'twitter',
            ],
            [
                'link' => [
                    'url' => 'https://www.instagram.com/code4africa/',
                ],
                'type' => 'instagram',
            ],
            [
                'link' => [
                    'url' => 'https://www.linkedin.com/company/code-for-africa/',
                ],
                'type' => 'linkedin',
            ],
        ];
?>
        <div class="about-section">
            <div class="about-hero">
                <img src="<?php echo $header_image; ?>" alt="academy.AFRICA">
            </div>
            <div class="title">
                <h1 class="cfa-title"><?php echo $title; ?></h1>
            </div>
            <div class="headline">
                <h2 class="cfa-headline"><?php echo $headline; ?></h2>
            </div>
            <div class="share-menu">
                <div class="social-icons">
                    <?
                    if (!empty($social_media_links)) {
                        foreach ($social_media_links as $item) {
                            $link = esc_url($item['link']['url']);
                            $type = esc_html($item['type']);
                            $icon = get_stylesheet_directory_uri() . ('/assets/images/icons/Type=' . $type . ', Size=24, Color=CurrentColor.svg');
                            $image = "<img src='" . $icon . "' alt='" . $type . "' />";
                            echo '<a style="color: #000" href="' . $link . '" class="icon">' . $image . '</a>';
                        }
                    }
                    ?>
                </div>
            </div>
            <div class="content">
                <?php echo $content; ?>
            </div>
        </div>
<?
    }
}
