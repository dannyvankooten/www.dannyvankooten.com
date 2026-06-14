<?php

namespace ibericode;

use WP_Post;


add_filter('wp_headers', static function (array $headers) {
    if (WP_DEBUG || isset($headers['Cache-Control']) || is_admin()) {
        return $headers;
    }

    // only set cache-headers on safe HTTP methods
    $method = $_SERVER['REQUEST_METHOD'] ?? 'POST';
    if ($method !== 'GET' && $method !== 'HEAD') {
        return $headers;
    }

    $url = trim($_SERVER['REQUEST_URI'] ?? '');

    // never set cache headers for logged-in users
    if (is_user_logged_in()) {
        $headers['Cache-Control'] = 'must-revalidate, max-age=0, private';

        // cache 404 pages for 1 hour (shared) or 5 minutes (browser)
    } elseif (is_404()) {
        $headers['Cache-Control'] = 'public, s-max-age=3600, max-age=300';

        // cache feeds and XML files (ie sitemap) for 1 day (shared) or 1 hour (browser)
    } elseif (is_feed() || str_ends_with($url, '.xml')) {
        $headers['Cache-Control'] = 'public, s-max-age=86400, max-age=3600';

        // cache all other pages for 30 days (shared) or 1 hour (browser)
    } else {
        $headers['Cache-Control'] = 'public, s-max-age=2592000, max-age=3600';
    }

    return $headers;
});


function purge_cache_for_url(string $url)
{
    $request_url = 'https://api.bunny.net/purge?url=' . urlencode($url) . '&async=true';

    $response = wp_remote_post($request_url, [
        'timeout' => 10,
        'redirection' => 5,
        'headers' => [
            'AccessKey' => constant('BUNNY_API_KEY'),
        ],
    ]);

    if (is_wp_error($response)) {
        error_log('Error purging Bunny CDN cache: ' . $response->get_error_message());
        return;
    }

    $status_code = wp_remote_retrieve_response_code($response);
    if ($status_code < 200 || $status_code >= 300) {
        error_log('Error purging Bunny CDN cache: HTTP ' . $status_code);
    }
}

add_action('save_post', static function (int $post_id, WP_Post $post, $update) {
    // No-op if BUNNY_API_KEY constant is not set
    if (! defined('BUNNY_API_KEY')) {
        return;
    }

    // Check if it's not an autosave
    if (wp_is_post_autosave($post_id) || wp_is_post_revision($post_id)) {
        return;
    }

    // Check if post is viewable
    if (false === is_post_publicly_viewable($post)) {
        return;
    }

    $permalink = get_permalink($post_id);
    if (! $permalink) {
        return;
    }

    purge_cache_for_url($permalink);

    // if this is a new post, purge home page for discoverability
    if (! $update) {
        purge_cache_for_url(get_home_url());
    }
}, 20, 3);
