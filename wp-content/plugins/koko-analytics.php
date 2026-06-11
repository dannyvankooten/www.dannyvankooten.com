<?php

/**
* Plugin Name: Koko Analytics Standalone
* Update URI: https://standalone.kokoanalytics.com/
*/

add_action('wp_footer', function() {
    if (WP_DEBUG || is_user_logged_in()) {
        return;
    }

    ?>
<!-- Koko Analytics v2.4.0 - https://www.kokoanalytics.com/ -->
<script>
(function(o, c) {
  window[o] = c;
  var s = document.createElement('script');
  s.defer = true;
  s.src = [c.url, '/', o, '.js'].join('');
  document.body.appendChild(s);
})('ka', {
  url: 'https://standalone.kokoanalytics.com',
  domain: 'dannyvankooten.com'
})
</script>
<?php
});
