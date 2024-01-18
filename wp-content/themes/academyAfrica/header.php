<?php

namespace AcademyAfrica\Theme;

/*
* The template for displaying header.
 *
 * @package Academy Africa
 */

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}

$viewport_content = apply_filters('hello_elementor_viewport_content', 'width=device-width, initial-scale=1');
$enable_skip_link = apply_filters('hello_elementor_enable_skip_link', true);
$skip_link_url = apply_filters('hello_elementor_skip_link_url', '#content');
?>
<!doctype html>
<html <?php language_attributes(); ?>>

<head>
	<meta charset="<?php bloginfo('charset'); ?>">
	<meta name="viewport" content="<?php echo esc_attr($viewport_content); ?>">
	<link rel="profile" href="https://gmpg.org/xfn/11">
	<link rel="stylesheet" type="text/css" media="print" href="<? echo get_stylesheet_directory_uri() ?>/assets/css/dist/print/ac_learning_print.css" >
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
	<main>
		<?php wp_body_open(); ?>

		<?php if ($enable_skip_link) { ?>
			<a class="skip-link screen-reader-text" href="<?php echo esc_url($skip_link_url); ?>"><?php echo esc_html__('Skip to content', 'hello-elementor'); ?></a>
		<?php } ?>

		<!-- get header template -->

		<?php get_template_part('template-parts/header', 'template'); ?>

		<!-- Register template -->
		<?php get_template_part('template-parts/register', 'template'); ?>

		<!-- Login template -->
		<?php get_template_part('template-parts/login', 'template'); ?>