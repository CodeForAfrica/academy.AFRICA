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
        $this->add_control(
            'mailchimp_embed_code',
            [
                'label' => __('Mailchimp Embed Code', 'academy-africa'),
                'type' => \Elementor\Controls_Manager::TEXTAREA,
                'description' => __('Paste the Mailchimp embed code here', 'academy-africa'),
                'default' => __('<div id="mc_embed_shell">
                <link href="//cdn-images.mailchimp.com/embedcode/classic-061523.css" rel="stylesheet" type="text/css">
            <style type="text/css">
                  #mc_embed_signup{background:#fff; false;clear:left; font:14px Helvetica,Arial,sans-serif; width: 600px;}
                  /* Add your own Mailchimp form style overrides in your site stylesheet or in this style block.
                     We recommend moving this block and the preceding CSS link to the HEAD of your HTML file. */
          </style>
          <div id="mc_embed_signup">
              <form action="https://codeforafrica.us6.list-manage.com/subscribe/post?u=65e5825507b3cec760f272e79&amp;id=3675122e9c&amp;f_id=00d8a3e2f0" method="post" id="mc-embedded-subscribe-form" name="mc-embedded-subscribe-form" class="validate" target="_blank">
                  <div id="mc_embed_signup_scroll"><h2>Join Our Slack</h2>
                      <div class="indicates-required"><span class="asterisk">*</span> indicates required</div>
                      <div class="mc-field-group"><label for="mce-EMAIL">Email Address <span class="asterisk">*</span></label><input type="email" name="EMAIL" class="required email" id="mce-EMAIL" required="" value=""></div><div class="mc-field-group"><label for="mce-FNAME">Full Name <span class="asterisk">*</span></label><input type="text" name="FNAME" class="required text" id="mce-FNAME" required="" value=""></div><div class="mc-field-group"><label for="mce-POSITION">Position </label><input type="text" name="POSITION" class=" text" id="mce-POSITION" value=""></div><div class="mc-field-group"><label for="mce-COMPANY">Company </label><input type="text" name="COMPANY" class=" text" id="mce-COMPANY" value=""></div>
                  <div id="mce-responses" class="clear">
                      <div class="response" id="mce-error-response" style="display: none;"></div>
                      <div class="response" id="mce-success-response" style="display: none;"></div>
                  </div><div aria-hidden="true" style="position: absolute; left: -5000px;"><input type="text" name="b_65e5825507b3cec760f272e79_3675122e9c" tabindex="-1" value=""></div><div class="clear"><input type="submit" name="subscribe" id="mc-embedded-subscribe" class="button" value="Subscribe"></div>
              </div>
          </form>
          </div>
          <script type="text/javascript" src="//s3.amazonaws.com/downloads.mailchimp.com/js/mc-validate.js"></script><script type="text/javascript">(function($) {window.fnames = new Array(); window.ftypes = new Array();fnames[0]=\'EMAIL\';ftypes[0]=\'email\';fnames[1]=\'FNAME\';ftypes[1]=\'text\';fnames[2]=\'POSITION\';ftypes[2]=\'text\';fnames[3]=\'COMPANY\';ftypes[3]=\'text\';}(jQuery));var $mcj = jQuery.noConflict(true);</script></div>
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
        $slack_url = $settings['slack_url'];
?>
        <div class="join-our-slack" id="join-our-slack">
            <div class="title">
                <h2 class="cfa-title"><?php echo $title; ?></h2>
            </div>
            <div class="content">
                <?php echo $content; ?>
            </div>
            <div class="form">
                <?php echo $settings['mailchimp_embed_code']; ?>
                <script>
                    const submit_btn = document.querySelector('#mc-embedded-subscribe');
                    if (submit_btn) {
                        submit_btn.value = 'Submit';
                    }
                </script>
            </div>
        </div>
<?
    }
}
