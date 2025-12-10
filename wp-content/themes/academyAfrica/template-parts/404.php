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
<?
$status_code = 404;
$title = "Page not found -";
$description = "There seems to be an error on this page. Please contact us for more details";
$refresh = "Refresh";
$home = "Home";
?>
<main id="content" class="site-main" style="margin: 0;">

    <div class="error">
        <div></div>
        <div class="content">
            <p class="code">
                <? echo $status_code ?>
            </p>
            <p class="text">
                <? echo $title ?>
            </p>
            <p class="description">
                <? echo $description ?>
            </p>
            <div class="actions">
                <a class="button" href="" onclick="location.reload();">
                    <? echo $refresh ?>
                </a>
                <a class="button" href="/">
                    <? echo $home ?>
                </a>
            </div>
        </div>

    </div>
    <script>
        // Track page view event
        window.dataLayer = window.dataLayer || [];
        dataLayer.push({
            'event': 'page_view',
            'page_title': '<?php echo $title ?>',
            'page_url': window.location.href,
            'status_code': '<?php echo $status_code ?>'
        });
        gtag('event', 'page_view', {
            'page_title': '<?php echo $title ?>',
            'page_location': window.location.href,
            'status_code': '<?php echo $status_code ?>'
        });
    </script>

</main>

<?php get_footer(); ?>
