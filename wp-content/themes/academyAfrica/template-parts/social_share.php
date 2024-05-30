<?php

$url_to_share = isset($args["url"]) ? $args["url"] : get_permalink(0);
?>
<div style="display: flex;" class="share-menu">
<div style="color: #000; cursor: pointer; margin-right: 16px;" class="share-icon" onclick="toggleShareButtons()">
        <img class='icon-image' src="<? echo get_stylesheet_directory_uri() ?>/assets/images/icons/Type=share, Size=24, Color=Black.svg" alt="Share">
    </div>
    <div class="share-icons">
        <!-- LinkedIn -->
        <a style="color: #000; margin-right: 8px;" href="https://www.linkedin.com/shareArticle?mini=true&url=<?php echo $url_to_share; ?>" target="_blank">
            <img class='icon-image' src="<? echo get_stylesheet_directory_uri() ?>/assets/images/icons/Type=linkedin, Size=24, Color=Black.svg" alt="LinkedIn">
        </a>
        
        <!-- Twitter -->
        <a style="color: #000; margin-right: 8px;" href="https://twitter.com/intent/tweet?url=<?php echo $url_to_share; ?>" target="_blank">
            <img class='icon-image' src="<? echo get_stylesheet_directory_uri() ?>/assets/images/icons/Type=twitter, Size=24, Color=Black.svg" alt="Twitter">
        </a>
        
        <!-- Facebook -->
        <a style="color: #000; margin-right: 8px;" href="https://www.facebook.com/sharer/sharer.php?u=<?php echo $url_to_share; ?>" target="_blank">
            <img class='icon-image' src="<? echo get_stylesheet_directory_uri() ?>/assets/images/icons/Type=facebook, Size=24, Color=Black.svg" alt="Facebook">
        </a>
        
        <!-- Instagram -->
        <a style="color: #000; margin-right: 8px;" href="https://www.instagram.com/" target="_blank">
            <img class='icon-image' src="<? echo get_stylesheet_directory_uri() ?>/assets/images/icons/Type=instagram, Size=24, Color=Black.svg" alt="Instagram">
        </a>
    </div>
</div>

<script>
function toggleShareButtons() {
    var shareButtons = document.querySelector('.share-icons');
    shareButtons.classList.toggle('hide');
}
</script>
