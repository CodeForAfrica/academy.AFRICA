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
<main class="contact-us">
<?
if ($_SERVER['REQUEST_METHOD'] === 'POST' && (isset($_POST['action']) && $_POST['action'] === 'contact-us')) {
  $name = $_POST['user_name'];
  $email = $_POST['user_email'];
  $description = $_POST['description'];

  $to = 'academy@codeforafrica.org';
  $subject = 'Contact Us [' . $name . ']( ' . $email . ')';
  $body = $description;

  $headers = array(
    'From: ' . $name . ' <' . $email . '>',
    'Content-Type: text/html'
  );

  $sent = wp_mail($to, $subject, $body, $headers);

  if ($sent) {
    ?>
                <p style="margin-top: 16px;" class="description">We've sent an email with your message to Academy Team. They will reach out to your email address shortly.</p>

    <?;
  } else {
    ?>
    <p style="margin-top: 16px;" class="description">Failed to send your email</p>
    <button>Try again</button>
    <?;
  }
} else {
  ?>
<h4 class="cfa-title">Contact Us</h4>
<form method="post" action="" enctype="multipart/form-data">
    <div class="input">
      <label for="user_name">
        <?php echo $name_label; ?>
      </label>
      <input type="text" name="user_name" required id="user_name">
    </div>
    <div class="input">
      <label for="user_email">
        <?php echo $email_label; ?>
      </label>
      <input type="email" name="user_email" value="<?php echo $user_email; ?>" id="user_email">
    </div>

    <label for="description">Message</label><br>
    <textarea name="description" id="description" rows="5" cols="30" required></textarea><br><br>
    <input type="hidden" name="action" value="contact-us">
    <div style="text-align: right;">
      <style>
        .gglcptch_recaptcha {
    display: flex;
    justify-content: flex-end;
  }
        </style>
    <? echo do_shortcode('[bws_google_captcha]') ?>
    <script>
      function submit(event) {
        
        var captchaResponse = grecaptcha.getResponse();
        if (captchaResponse.length === 0) {
          event.preventDefault();
          alert('Please complete the CAPTCHA');
        }
      }
    </script>
    </div>
    <div class="submit-area">
                <button type="submit" class="button primary">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="17" viewBox="0 0 16 17" fill="none">
                        <path d="M12.6667 14.5H3.33333C2.97971 14.5 2.64057 14.3595 2.39052 14.1095C2.14048 13.8594 2 13.5203 2 13.1667V3.83333C2 3.47971 2.14048 3.14057 2.39052 2.89052C2.64057 2.64048 2.97971 2.5 3.33333 2.5H10.6667L14 5.83333V13.1667C14 13.5203 13.8595 13.8594 13.6095 14.1095C13.3594 14.3595 13.0203 14.5 12.6667 14.5Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M11.3346 14.4993V9.16602H4.66797V14.4993" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M4.66797 2.5V5.83333H10.0013" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    <? echo $save_label ?>
                </button>
            </div>
  </form>

  <?
}
?>
</main>
<?php get_footer(); ?>
