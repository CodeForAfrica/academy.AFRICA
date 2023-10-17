<?php
class Academy_Africa_Hero extends \Elementor\Widget_Base {

	public function get_name() {
		return 'academy_africa_hero';
	}

	public function get_title() {
		return esc_html__( 'Hero Section', 'elementor-addon' );
	}

	public function get_icon() {
		return 'eicon-code';
	}

	public function get_categories() {
		return [ 'basic' ];
	}

	public function get_keywords() {
		return [ 'hello', 'world' ];
	}

	protected function render() {
		?>

		<p> Hello World </p>

		<?php
	}
}