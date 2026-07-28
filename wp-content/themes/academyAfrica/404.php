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
<?php get_template_part('template-parts/404', 'template'); ?>

</main><?php // close the single <main> opened via get_header() in template-parts/404 ?>

</body>

</html>