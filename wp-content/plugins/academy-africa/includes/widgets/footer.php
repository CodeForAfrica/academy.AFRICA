<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


class Academy_Africa_Footer extends \Elementor\Widget_Base {
	
	public function get_name() {
		return 'footer';
	}

	public function get_title() {
		return esc_html__( 'footer', 'elementor-footer-widget' );
	}

	public function get_icon() {
		return 'eicon-code';
	}

	public function get_categories()
    {
        return ['academy-africa'];
    }

	public function get_keywords() {
		return [ ];
	}
	protected function register_controls() {
	}
	protected function render() {

		?>
		<footer class="root">
        <div class="item">
            <div class="site-description">
                <img src="https://cfa.dev.codeforafrica.org/media/cfalogobw.svg" alt="logo" />
                <p class="description">
                    Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the
                    industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type
                    and
                    scrambled it to make a type specimen book. It has survived not only five centuries, but also the
                    leap
                    into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with
                    the
                    release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop
                    publishing
                    software like Aldus PageMaker including versions of Lorem Ipsum.
                </p>
                <div class="connect">
                    <p>Stay in Touch</p>
                </div>
            </div>
        </div>
        <div class="item">
            <div class="links">
                <a class="primary">
                    Connect
                </a>
                <a href="#" class="primary">
                    Courses
                </a>
                <a href="#" class="primary">
                    Events
                </a>
                <a href="#" class="primary">
                    Blog
                </a>
                <a href="#" class="primary">
                    About us
                </a>
                <a href="#" class="primary">
                    Sign In
                </a>
                <a class="secondary first">
                    Imprint
                </a>
                <a class="secondary">
                    Privacy Policy
                </a>
            </div>
        </div>
        <div class="item">
            <div class="embed">
                <P class="title">
                    Subscribe to the Code for Africa newsletter
                </P>
                <!-- Begin Mailchimp Signup Form -->

                <div id="mc_embed_signup">
                    <form
                        action="https://twitter.us6.list-manage.com/subscribe/post?u=65e5825507b3cec760f272e79&amp;id=c2ff751541"
                        method="post" id="mc-embedded-subscribe-form" name="mc-embedded-subscribe-form" class="validate"
                        target="_blank" novalidate="">
                        <div id="mc_embed_signup_scroll">
                            <label for="MERGE1">Name</label>
                            <input type="text" name="MERGE1" id="MERGE1" size="25" value="" placeholder="Your name">
                            <label for="mce-EMAIL">Email</label>
                            <input type="email" value="" placeholder="example@email.com" name="EMAIL" class="email"
                                id="mce-EMAIL" required="">
                            <!-- real people should not fill this in and expect good things - do not remove this or risk form bot signups-->
                            <div style="position: absolute; left: -5000px;" aria-hidden="true"><input type="text"
                                    name="b_65e5825507b3cec760f272e79_c2ff751541" tabindex="-1" value=""></div>
                            <div class="clear"><input type="submit" value="Sign up" id="mc-embedded-subscribe"
                                    class="button"></div>
                        </div>
                    </form>
                </div>

                <!--End mc_embed_signup-->
            </div>
        </div>
    </footer>
		<?php

	}

}