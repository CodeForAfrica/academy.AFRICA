<?php

namespace AcademyAfrica\Theme;

/**
 * The template for displaying 404 (not found) pages.
 *
 * @package Academy Africa
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}
?>
<?php
// template-parts/404 renders a complete document: get_header() and get_footer()
// emit no <main>, and the part opens and closes its own single
// <main id="content">. This wrapper adds no markup of its own.
get_template_part('template-parts/404', 'template');
?>