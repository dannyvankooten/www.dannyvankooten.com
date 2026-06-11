<?php

namespace ibericode;

use WP_Post;

// Prevent direct file access
defined('ABSPATH') or exit;

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

add_action('save_post', static function (int $post_id, WP_Post $post) {
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
    purge_cache_for_url(get_home_url());
}, 20, 2);
