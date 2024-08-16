<?
function password_reset($user_email){
  // Check if the email address is valid
  if ( !is_email($user_email) ) {
      $url = add_query_arg(array(
          'action' => 'lostpassword',
          'error_message' => 'Invalid email address.'
      ), home_url('/login'));
      wp_redirect(home_url($url));
      exit;
  }

  // Retrieve user data by email
  $user = get_user_by('email', $user_email);

  // Check if the user exists
  if ( !$user ) {
      $url = add_query_arg(array(
          'action' => 'lostpassword',
          'error_message' => 'No user was found with that email address.'
      ), home_url('/login'));
      wp_redirect(home_url($url));
      exit;
  }

  // Generate the password reset key
  $reset_key = get_password_reset_key($user);
  if ( is_wp_error($reset_key) ) {
      $url = add_query_arg(array(
          'action' => 'lostpassword',
          'error_message' => 'An error occurred while generating the password reset key.'
      ), home_url('/login'));
      wp_redirect(home_url($url));
      exit;
  }

  $reset_url = network_site_url("wp-login.php?action=rp&key=$reset_key&login=" . rawurlencode($user->user_login), 'login');

  // Email subject
  $subject = '[academy.Africa] Password Reset';

  // Email message
  $message = "Hi <strong>".$user->display_name."</strong>,
      <p>
      Someone requested a password reset for your account.</p>
      <p>If this was a mistake, just ignore this email and nothing will happen.</p>
      <p>To reset your password, visit the following link:</p>
          <a href=".  $reset_url .">
          <button style=\"background: #004085;
          border: 1px solid #004085;
          margin-top: 16px;
          margin-bottom: 16px;
          padding: 8px 16px; 
          font-size: 14px; 
          line-height: 16px;
          color: #ffffff; 
          text-transform: uppercase;
          font-weight: 800;
          letter-spacing: 1.6px;
          display: inline-flex;
          align-items: center;
          justify-content: center;
          gap: 8px;
          cursor: pointer;
          font-family: 'Open Sans', sans-serif;
          border-radius: 0;\"
          onmouseover=\"this.style.background='#cce5ff'; this.style.color='#004085';\"
          onmouseout=\"this.style.background='#004085'; this.style.color='#ffffff';\">
          Reset Password
          </button>
          </a>
     
      <p style=\"margin-bottom: 0; margin-top: 10px\">
      Thank you,
      </p>
      <p>The academy.AFRICA Team</p>
      ";

  add_filter('wp_mail_content_type', 'set_html_content_type');
  $email_sent = wp_mail($user_email, $subject, $message);
  remove_filter('wp_mail_content_type', 'set_html_content_type');
  // Check if the email was sent successfully
  if ( $email_sent ) {
      $url = add_query_arg(array(
          'action' => 'lostpassword',
          'email_sent' => '1',
          'user_login' => $user_email,
      ), home_url('/login'));
      wp_redirect($url);
      exit;
  } else {
      wp_redirect(home_url('/login?action=lostpassword'));
  }
}
function fix_wpelogin($url)
{
    $url = add_query_arg('wpe-login', true, $url);
    $url = add_query_arg('email_sent', true, $url);
    return $url;
}
add_filter('lostpassword_url', 'fix_wpelogin');
?>
