<?php

// Prevent direct file access
defined('ABSPATH') or exit;

add_filter('upload_mimes', function (array $mime_types): array {
    if (current_user_can('manage_options')) {
        $mime_types['svg'] = 'image/svg+xml';
    }
    return $mime_types;
});
