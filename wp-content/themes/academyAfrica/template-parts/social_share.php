<?php

$url_to_share = isset($args["url"]) ? $args["url"] : get_permalink(0);
$share_message = isset($args["message"]) ? $args["message"] : "Check out this link: $url_to_share";
?>
<div style="display: flex;" class="share-menu">
    <div style="color: #000; cursor: pointer; margin-right: 16px;" class="share-icon" onclick="toggleShareButtons()">
        <img class='icon-image' src="<? echo get_stylesheet_directory_uri() ?>/assets/images/icons/Type=share, Size=24, Color=Black.svg" alt="Share">
    </div>
    <div class="share-icons">
        <!-- LinkedIn -->
        <a id="share-btn-linkedin" style="color: #000; margin-right: 8px;" href="https://www.linkedin.com/shareArticle?mini=true&url=<?php echo $url_to_share; ?>&summary=<?php echo urlencode($share_message); ?>" target="_blank">
            <img class='icon-image' src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/icons/Type=linkedin, Size=24, Color=Black.svg" alt="LinkedIn">
        </a>

        <!-- Twitter -->
        <a id="share-btn-twitter" style="color: #000; margin-right: 8px;" href="https://twitter.com/intent/tweet?url=<?php echo $url_to_share; ?>&text=<?php echo urlencode($share_message); ?>" target="_blank">
            <img class='icon-image' src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/icons/Type=twitter, Size=24, Color=Black.svg" alt="Twitter">
        </a>

        <!-- Facebook -->
        <a id="share-btn-facebook" style="color: #000; margin-right: 8px;" href="https://www.facebook.com/sharer/sharer.php?u=<?php echo $url_to_share; ?>&quote=<?php echo urlencode($share_message); ?>" target="_blank">
            <img class='icon-image' src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/icons/Type=facebook, Size=24, Color=Black.svg" alt="Facebook">
        </a>

        <!-- Instagram -->
        <a id="share-btn-instagram" style="color: #000; margin-right: 8px;" href="https://www.instagram.com/" target="_blank">
            <img class='icon-image' src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/icons/Type=instagram, Size=24, Color=Black.svg" alt="Instagram">
        </a>
    </div>
</div>

<script>
    function toggleShareButtons() {
        var shareButtons = document.querySelector('.share-icons');
        shareButtons.classList.toggle('hide');
    }

    const shareButtons = ['share-btn-linkedin', 'share-btn-twitter', 'share-btn-facebook', 'share-btn-instagram'];
    shareButtons.forEach(buttonId => {
        const button = document.getElementById(buttonId);
        if (button) {
            button.addEventListener('click', function() {
                window.dataLayer = window.dataLayer || [];
                dataLayer.push({
                    'event': 'share_button_click',
                    "message": "<?php echo $share_message ?>",
                    'platform': buttonId.split('-')[2],
                    'url': "<?php echo $url_to_share ?>",
                });
                gtag('event', 'share_button_click', {
                    'event_category': 'engagement',
                    'event_label': buttonId.split('-')[2],
                    'message': "<?php echo $share_message ?>",
                    'url': "<?php echo $url_to_share ?>"
                });
            });
        }
    });

</script>
