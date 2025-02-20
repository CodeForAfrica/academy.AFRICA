<?php
$message = $args['message'];
$type = $args['type'] ?? 'info';
$icon = '';
switch ($type) {
    case 'success':
        $icon = '<i class="fas fa-check-circle"></i>';
        break;
    case 'error':
        $icon = '<i class="fas fa-exclamation-circle"></i>';
        break;
    case 'warning':
        $icon = '<i class="fas fa-exclamation-triangle"></i>';
        break;
    default:
        $icon = '<i class="fas fa-info-circle"></i>';
}
?>
<div class="message-bar <?php echo "message-" . $type; ?>">
    <div class="message-bar-content">
        <?php echo $icon; ?>
        <p class="message-bar-text"><?php echo $message; ?></p>
    </div>
</div>
