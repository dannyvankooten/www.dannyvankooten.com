<?php

global $content_width;
if (! isset($content_width)) {
    $content_width = 540;
}

/**
 * @return string Absolute URL with cache-bust based on the last file modification time
 */
function dvk26_get_asset_url(string $filename): string
{
    $file = get_stylesheet_directory() . "/{$filename}";
    $time = filemtime($file) ?: 0;
    return get_stylesheet_directory_uri() . "/{$filename}?v={$time}";
}

// declare theme support, remove some stuff
add_action('after_setup_theme', static function () {
    add_theme_support('html5', array( 'comment-list', 'comment-form', 'search-form', 'gallery', 'caption', 'style', 'script' ));
    add_theme_support('title-tag');
    add_theme_support('automatic-feed-links');
    add_theme_support('responsive-embeds');

    // remove emoji scripts and styles
    remove_action('wp_head', 'print_emoji_detection_script', 7);
    remove_action('admin_print_scripts', 'print_emoji_detection_script');
    remove_action('admin_print_styles', 'print_emoji_styles');
    remove_action('wp_print_styles', 'print_emoji_styles');

    // remove XMLRPC EditUri
    remove_action('wp_head', 'rsd_link');

    // remove shortlink
    remove_action('wp_head', 'wp_shortlink_wp_head', 10);

    // remove comments feed
    add_filter('feed_links_show_comments_feed', '__return_false');

    // Tells WP to load editor styles into the block editor iframe
    // and scope them under .editor-styles-wrapper.
    add_theme_support('editor-styles');

    // Path is relative to the theme root.
    add_editor_style('style.css');
});

// disable XMLRPC
add_filter('xmlrpc_enabled', '__return_false');

// remove the X-Pingback HTTP header
add_filter('wp_headers', static function (array $headers) {
    if (isset($headers['X-Pingback'])) {
        unset($headers['X-Pingback']);
    }
    return $headers;
});

function dvk26_default_og_image_url(): string
{
    return get_theme_file_uri('/assets/images/og-default.png');
}

function dvk26_get_posts_grouped_by_year(): array
{
    $posts_by_year = [];

    foreach (get_posts(['numberposts' => -1]) as $post) {
        $year = get_the_date('Y', $post);
        $posts_by_year[$year][] = [ get_the_permalink($post), get_the_title($post) ];
    }

    return $posts_by_year;
}
