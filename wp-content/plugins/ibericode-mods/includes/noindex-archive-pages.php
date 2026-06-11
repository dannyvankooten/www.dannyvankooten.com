<?php

// Prevent direct file access
defined('ABSPATH') or exit;

add_filter('wp_robots', function (array $robots): array {
    
    if (did_action('wp') && !is_singular() && !is_front_page()) {
        $robots['noindex'] = true;
    }

    return $robots;
});
