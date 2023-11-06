<?php
/**
 * Elementor Test Widget.
 *
 * Elementor widget that inserts a Test widget.
 *
 * @since 1.0.0
 */
class Academy_Africa_Test_Widget extends \Elementor\Widget_Base {
    
        /**
        * Get widget name.
        *
        * Retrieve Test widget name.
        *
        * @since 1.0.0
        *
        * @access public
        *
        * @return string Test Widget Name.
        */
        public function get_name() {
            return 'test_widget';
        }
    
        /**
        * Get widget title.
        *
        * Retrieve Test widget title.
        *
        * @since 1.0.0
        *
        * @access public
        *
        * @return string Test Widget Title.
        */
        public function get_title() {
            return __( 'Test Widget', 'elementor-test-widget' );
        }
    
        /**
        * Get widget icon.
        *
        * Retrieve Test widget icon.
        *
        * @since 1.0.0
        *
        * @access public
        *
        * @return string Test Widget icon.
        */
        public function get_icon() {
            return 'fa fa-code';
        }
    
        /**
        * Get widget categories.
        *
        * Retrieve the list of categories the Test widget belongs to.
        *
        * @since 1.0.0
        *
        * @access public
        *
        * @return array Test widget categories.
        */
        public function get_categories() {
            return [ 'academy-africa' ];
        }
    
        /**
        * Register Test widget controls.
        *
        * Adds different input fields to allow the user to change and customize the widget settings.
        *
        * @since 1.0.0
        *
        * @access protected
        */
    
        protected function _register_controls() {
    
            $this->start_controls_section(
                'section_content',
                [
                    'label' => __( 'Content', 'elementor-test-widget' ),
                ]
            );
    
            $this->add_control(
                'title',
                [
                    'label' => __( 'Title', 'elementor-test-widget' ),
                    'type' => \Elementor\Controls_Manager::TEXT
                ]
            );
        }

        protected function render() {
            $settings = $this->get_settings_for_display();
            // echo '<h2>' . $settings['title'] . '</h2>';
            echo '<h2>Test Widget is placed here</h2>';
        }

        
}