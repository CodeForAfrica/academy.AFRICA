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
<main>
	<?php get_template_part('template-parts/footer', 'template'); ?>
</main>

<?php wp_footer(); ?>

</body>

</html>