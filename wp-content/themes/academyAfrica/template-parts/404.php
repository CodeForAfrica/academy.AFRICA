<?php
namespace AcademyAfrica\Theme;

/**
 * The template for displaying 404 pages (not found).
 *
 * @package Academy Africa
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}
?>

<?php get_header(); ?>

<main id="content" class="site-main">

    <?php if (apply_filters('academy_africa_page_title', true)) : ?>
        <header class="page-header">
            <h1 class="entry-title"><?php echo esc_html__('Looks like you\'re Lost again.', 'academy-africa'); ?></h1>
        </header>
    <?php endif; ?>

    <div class="page-content">
        <p><?php echo esc_html__('No page in Academy Africa.', 'academy-africa'); ?></p>
    </div>

</main>

<?php get_footer(); ?>