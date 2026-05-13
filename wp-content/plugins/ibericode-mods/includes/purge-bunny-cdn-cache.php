<?php

namespace ibericode;

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

add_action('save_post', function ($post_id) {
    // No-op if BUNNY_API_KEY constant is not set
    if (! defined('BUNNY_API_KEY')) {
        return;
    }

    // Check if it's not an autosave
    if ((defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) || wp_is_post_autosave($post_id) || wp_is_post_revision($post_id)) {
        return;
    }


    $post_type = get_post_type($post_id);
    $post_type_object = $post_type ? get_post_type_object($post_type) : null;
    if (! $post_type_object || ! $post_type_object->public) {
        return;
    }

    $permalink = get_permalink($post_id);
    if (! $permalink) {
        return;
    }

    purge_cache_for_url($permalink);
});
