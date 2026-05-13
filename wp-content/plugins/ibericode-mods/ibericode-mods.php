<?php

/*
Plugin Name: ibericode mods
Description: a collection of commonly needed WordPresss modifications or enhancements
Author: Danny van Kooten
Version: 1.0
Author URI: https://www.dannyvankooten.com/
Requires at least: 6.5
Requires PHP: 8.3 
License: GPL v2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Update URI: https://www.ibericode.com/
*/

if (PHP_VERSION_ID < 80300) {
    return;
}

if (! defined('ABSPATH')) {
    return;
}

require __DIR__ . '/includes/allow-svg-uploads.php';
require __DIR__ . '/includes/noindex-archive-pages.php';
require __DIR__ . '/includes/disable-rest-api-users-endpoint.php';
require __DIR__ . '/includes/purge-bunny-cdn-cache.php';
require __DIR__ . '/includes/set-cache-headers.php';
require __DIR__ . '/includes/protect-wp-login.php';
require __DIR__ . '/includes/smtp-mailer.php';
require __DIR__ . '/includes/stop-comment-spam.php';
require __DIR__ . '/includes/disable-xmlrpc.php';
require __DIR__ . '/includes/remove-yoast-seo-comments.php';