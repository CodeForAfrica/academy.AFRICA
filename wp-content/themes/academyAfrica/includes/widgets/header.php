<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}


class Academy_Africa_Header_Section extends \Elementor\Widget_Base
{

    public function get_name()
    {
        return 'header-section';
    }

    public function get_style_depends()
    {
        return ['academy-africa-header-section', 'academy-africa'];
    }

    public function get_title()
    {
        return esc_html__('Header Section', 'elementor-hero-widget');
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
        // Wheter to show the social media links
        $this->add_control(
            'show_social_media_links',
            [
                'label' => __('Show Social Media Links', 'academy-africa'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __('Show', 'academy-africa'),
                'label_off' => __('Hide', 'academy-africa'),
                'return_value' => 'yes',
                'default' => 'no',
            ]
        );
        $this->end_controls_section();
    }

    protected function render()
    {
        $settings = $this->get_settings_for_display();
        $title = $settings['title'];
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
        <div class="header-section">
            <div class="header-image">
                <img src="<?php echo $header_image; ?>" alt="academy.AFRICA">
            </div>
            <div class="header-content">
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
                                $icon = get_stylesheet_directory_uri() . ('/assets/images/icons/Type=' . $type . ', Size=24, Color=Black.svg');
                                $image = "<img src='" . $icon . "' alt='" . $type . "' />";
                                echo '<a style="color: #000" href="' . $link . '" class="icon">' . $image . '</a>';
                            }
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
<?
    }
}
