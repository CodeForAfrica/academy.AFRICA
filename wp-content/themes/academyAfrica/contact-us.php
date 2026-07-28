<?php
/*
Template Name: Contact Us
*/
require_once(ABSPATH . 'wp-load.php');
get_header();

$email_label = get_theme_mod('email_label', 'Email');
$name_label = get_theme_mod('name_label', 'Name');
$save_label = get_theme_mod('save_label', 'Submit');
?>
<div class="contact-us">
  <?php
  echo do_shortcode('[contact-form-7 id="9521ba1" title="Contact form 1"]');
  ?>
</div>
<?php
get_footer();
