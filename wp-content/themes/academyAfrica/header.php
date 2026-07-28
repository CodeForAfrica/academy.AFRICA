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
	<link href="https://fonts.googleapis.com/icon?family=Material+Icons"
		rel="stylesheet">
	<meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests" />
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
	<?php wp_body_open(); ?>

	<?php if ($enable_skip_link) { ?>
		<a class="skip-link screen-reader-text" href="<?php echo esc_url($skip_link_url); ?>"><?php echo esc_html__('Skip to content', 'hello-elementor'); ?></a>
	<?php } ?>

	<!-- site navigation — kept outside <main> -->

	<?php get_template_part('template-parts/header', 'template'); ?>

	<?php // Single <main> landmark per document. Page templates and widgets render
	// their content inside this element (they no longer open their own <main>).
	// Closed in footer.php, and in 404.php which doesn't call get_footer(). ?>
	<main id="content">
