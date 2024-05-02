<?php
function get_icon($type){
    return get_stylesheet_directory_uri() . ('/assets/images/icons/Type=' . $type . ', Size=24, Color=Black.svg');
}
$url_to_share = isset($args["url"]) ? $args["url"] : the_permalink();
?>

<div style="display: flex;" class="share-menu">
<div style="color: #000; cursor: pointer; margin-right: 16px;" class="share-icon" onclick="toggleShareButtons()">
        <img class='icon-image' src="<? echo get_icon('share')?>" alt="Share">
    </div>
    <div class="share-icons hide">
        <!-- LinkedIn -->
        <a style="color: #000; margin-right: 8px;" href="https://www.linkedin.com/shareArticle?mini=true&url=<?php echo $url_to_share; ?>" target="_blank">
            <img class='icon-image' src="<? echo get_icon('linkedin')?>" alt="LinkedIn">
        </a>
        
        <!-- Twitter -->
        <a style="color: #000; margin-right: 8px;" href="https://twitter.com/intent/tweet?url=<?php echo $url_to_share; ?>" target="_blank">
            <img class='icon-image' src="<? echo get_icon('twitter')?>" alt="Twitter">
        </a>
        
        <!-- Facebook -->
        <a style="color: #000; margin-right: 8px;" href="https://www.facebook.com/sharer/sharer.php?u=<?php echo $url_to_share; ?>" target="_blank">
            <img class='icon-image' src="<? echo get_icon('facebook')?>" alt="Facebook">
        </a>
        
        <!-- Instagram -->
        <a style="color: #000; margin-right: 8px;" href="https://www.instagram.com/" target="_blank">
            <img class='icon-image' src="<? echo get_icon('instagram')?>" alt="Instagram">
        </a>
    </div>
</div>

<script>
function toggleShareButtons() {
    var shareButtons = document.querySelector('.share-icons');
    shareButtons.classList.toggle('hide');
}
</script>
