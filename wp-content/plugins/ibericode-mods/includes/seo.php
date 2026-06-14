<?php

// Prevent direct file access
defined('ABSPATH') or exit;

// remove Yoast SEO HTML comments
add_filter('wpseo_debug_markers', '__return_false');

// noindex archive pages
add_filter('wp_robots', static function (array $robots): array {
    if (did_action('wp') && !is_singular() && !is_front_page()) {
        $robots['noindex'] = true;
    }

    return $robots;
});

if ('production' !== wp_get_environment_type()) {
    // Block crawling.
    add_filter('robots_txt', static function(string $output, bool $public) {
        $output = '# Crawling is blocked for non-production environment' . PHP_EOL;
        $output .= 'User-agent: *' . PHP_EOL;
        $output .= 'Disallow: /' . PHP_EOL;;
        return $output;
    }, 1000, 2);

    // Enable "Discourage search engines from indexing this site" option.
    add_filter('pre_option_blog_public', '__return_zero', 999);
}
