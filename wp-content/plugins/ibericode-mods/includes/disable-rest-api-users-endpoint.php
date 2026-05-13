<?php

// Prevent direct file access
defined('ABSPATH') or exit;

// Do not allow access to WordPress REST API for non-logged-in users
add_filter('rest_authentication_errors', function ($result) {
    if (is_wp_error($result)) {
        return $result;
    }

    $request_path = parse_url(urldecode($_SERVER['REQUEST_URI'] ?? ''), PHP_URL_PATH);
    $query_param = $_GET['rest_route'] ?? '';

    if (! is_user_logged_in() && (str_contains($request_path, '/wp-json/wp/v2/users') || $query_param === '/wp/v2/users')) {
        return new WP_Error(
            'rest_not_logged_in',
            'You are not currently logged in.',
            ['status' => 401]
        );
    }

    return $result;
});
