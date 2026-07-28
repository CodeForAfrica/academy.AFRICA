<?php

namespace AcademyAfrica\Theme;

/**
 * The template for displaying the footer.
 *
 * Contains the body & html closing tags.
 *
 * @package Academy Africa
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}
?>
<?php
// template-parts/404 renders a complete document: it calls get_header()
// (opens <main>) and get_footer() (footer.php closes <main>, </body>, </html>).
get_template_part('template-parts/404', 'template');
?>