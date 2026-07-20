<?php

defined('ABSPATH') or exit;

// Do not allow access to WordPress REST API for logged-out users
add_filter('rest_authentication_errors', static function ($result) {
    if (is_wp_error($result)) {
        return $result;
    }

    if (is_user_logged_in()) {
        return $result;
    }

    return new WP_Error(
        'rest_not_logged_in',
        'You are not currently logged in.',
        ['status' => 401]
    );
});

// Do not advertise REST API for logged-out users
add_action('init', static function () {
    if (is_user_logged_in()) {
        return;
    }

    remove_action('xmlrpc_rsd_apis', 'rest_output_rsd');
    remove_action('wp_head', 'rest_output_link_wp_head', 10);
    remove_action('template_redirect', 'rest_output_link_header', 11);
}, 10, 0);
