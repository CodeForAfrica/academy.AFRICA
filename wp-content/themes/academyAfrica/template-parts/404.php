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
<?php
$status_code = 404;
$title = academyafrica_translate('PAGE NOT FOUND');
$description = academyafrica_translate('There seems to be an error on this page. Please contact us for more details');
$refresh = academyafrica_translate('Refresh');
$home = academyafrica_translate('Home');
?>
<main id="content" class="site-main" style="margin: 0;">

    <div class="error">
        <div></div>
        <div class="content">
            <p class="code">
                <?php echo $status_code ?>
            </p>
            <p class="text">
                <?php echo $title ?>
            </p>
            <p class="description">
                <?php echo $description ?>
            </p>
            <div class="actions">
                <a class="button" href="" onclick="location.reload();" data-analytics-action="refresh">
                    <?php echo $refresh ?>
                </a>
                <a class="button" href="/" data-analytics-action="home">
                    <?php echo $home ?>
                </a>
            </div>
        </div>

    </div>
    <script>
        // Track 404 page view and recovery actions
        window.dataLayer = window.dataLayer || [];
        var queryString = window.location.search ? window.location.search.substring(1) : '';
        var ua = navigator.userAgent || '';
        var deviceType = /Mobi|Android/i.test(ua) ? 'mobile' : (/iPad|Tablet/i.test(ua) ? 'tablet' : 'desktop');
        var browser = 'other';
        if (/Edg\//i.test(ua)) {
            browser = 'edge';
        } else if (/Chrome\//i.test(ua) && !/Edg\//i.test(ua)) {
            browser = 'chrome';
        } else if (/Safari\//i.test(ua) && !/Chrome\//i.test(ua)) {
            browser = 'safari';
        } else if (/Firefox\//i.test(ua)) {
            browser = 'firefox';
        }

        var notFoundPayload = {
            'page_title': '<?php echo $title ?>',
            'page_url': window.location.href,
            'status_code': '<?php echo $status_code ?>',
            'not_found_path': window.location.pathname + window.location.search + window.location.hash,
            'not_found_referrer': document.referrer || '',
            'query_string': queryString,
            'is_logged_in': <?php echo is_user_logged_in() ? 'true' : 'false'; ?>,
            'device_type': deviceType,
            'browser': browser
        };

        dataLayer.push(Object.assign({ 'event': 'page_not_found' }, notFoundPayload));
        if (typeof window.gtag === 'function') {
            window.gtag('event', 'page_not_found', notFoundPayload);
        }

        document.querySelectorAll('[data-analytics-action]').forEach(function (el) {
            el.addEventListener('click', function () {
                var action = el.getAttribute('data-analytics-action');
                var actionPayload = Object.assign({ 'not_found_action': action }, notFoundPayload);
                dataLayer.push(Object.assign({ 'event': 'page_not_found_action' }, actionPayload));
                if (typeof window.gtag === 'function') {
                    window.gtag('event', 'page_not_found_action', actionPayload);
                }
            });
        });
    </script>

</main>

<?php get_footer(); ?>
