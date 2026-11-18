/**
 * "Sign in with Google" buttons (includes/functions/google-login.php).
 *
 * The Log in with Google plugin protects its OAuth flow with a state token that
 * must match a cookie. On front-end pages it leaves setting that cookie to the
 * browser (so pages stay cacheable), the same way its own login.js does for its
 * button: on click, reuse or create the cookie and write its value into the
 * OAuth `state` before following the link.
 */
(function () {
  function randomToken() {
    var bytes = new Uint8Array(16);
    window.crypto.getRandomValues(bytes);
    return Array.from(bytes, function (b) {
      return ('0' + (b & 0xff).toString(16)).slice(-2);
    }).join('');
  }

  document.addEventListener('click', function (event) {
    var link = event.target && event.target.closest ? event.target.closest('a.aa-google-login') : null;
    if (!link || !link.href) {
      return;
    }

    try {
      var url = new URL(link.href);
      var state = url.searchParams.get('state');
      if (!state) {
        return;
      }

      var isHttps = window.location.protocol === 'https:';
      var cookieName = link.getAttribute('data-cookie-name') || (isHttps ? '__Host-lwg_state_token' : 'lwg_state_token');
      var match = document.cookie.match(new RegExp('(^|;\\s*)' + cookieName + '=([^;]*)'));
      var token = match ? match[2] : '';

      if (!token) {
        token = randomToken();
        var secure = isHttps || cookieName.indexOf('__Host-') === 0 ? '; Secure' : '';
        document.cookie = cookieName + '=' + token + '; Path=/; max-age=3600; SameSite=Lax' + secure;
      }

      var data = JSON.parse(atob(state));
      data.state_token = token;
      url.searchParams.set('state', btoa(JSON.stringify(data)));
      link.href = url.toString();
    } catch (e) {
      // Leave the link as-is; the plugin will report an expired session.
    }
  });
})();
