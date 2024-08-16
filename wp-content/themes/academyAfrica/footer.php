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
<?php get_template_part('template-parts/footer', 'template'); ?>

<?php wp_footer(); ?>
<script>
	
		window.addEventListener("load", function(){
			if(window.error){
        const element = document.getElementById("error_message");
        if(element){
            element.className = "error_message";
            element.innerText = window.error;
        }
    }
		})
</script>
</body>

</html>
