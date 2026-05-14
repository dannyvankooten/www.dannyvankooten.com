<?php

global $content_width;
if (! isset($content_width)) {
    $content_width = 540;
}

function dvk26_get_asset_url(string $filename): string {
    $file = get_stylesheet_directory() . '/' . $filename;
    $url = get_stylesheet_directory_uri() . '/' . $filename;
    $time = filemtime($file) ?: 0;
    return $url . "?v=" . $time;
}

// load theme stylesheet
add_action('wp_enqueue_scripts', function() {
    $stylesheet = get_stylesheet_directory() . '/style.css';
    wp_enqueue_style('theme', get_stylesheet_uri(), [], filemtime($stylesheet));

    $stylesheet = get_stylesheet_directory() . '/assets/fonts/inter.css';
    wp_enqueue_style('inter', get_stylesheet_uri(), [], filemtime($stylesheet));
}, -10);

// declare theme support, remove some stuff
add_action('after_setup_theme', function() {
    add_theme_support( 'html5', array( 'comment-list', 'comment-form', 'search-form', 'gallery', 'caption', 'style', 'script' ) );
    add_theme_support( 'title-tag' );
    add_theme_support( 'automatic-feed-links' );
    add_theme_support( 'responsive-embeds' );

    // remove emoji scripts and styles
    remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
    remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
    remove_action( 'admin_print_styles', 'print_emoji_styles' );
    remove_action( 'wp_print_styles', 'print_emoji_styles' );

    // remove XMLRPC EditUri
    remove_action('wp_head', 'rsd_link');

    // remove shortlink 
    remove_action('wp_head', 'wp_shortlink_wp_head', 10);

    // remove comments feed
    add_filter( 'feed_links_show_comments_feed', '__return_false' );

    // instruct Yoast SEO to use a default Open Graph image
    add_filter('wpseo_opengraph_image', function($image) {
        return $image ?: dvk26_default_og_image_url();
    });
    add_filter('wpseo_twitter_image', function($image) {
        return $image ?: dvk26_default_og_image_url();
    });
});

// disable XMLRPC
add_filter('xmlrpc_enabled', '__return_false');

// remove the X-Pingback HTTP header
add_filter('wp_headers', function($headers) {
    if (isset($headers['X-Pingback'])) {
        unset($headers['X-Pingback']);
    }
    return $headers;
});

function dvk26_default_og_image_url(): string {
    return get_theme_file_uri('/assets/images/og-default.png');
}



function dvk26_get_posts_grouped_by_year(): array {
    $posts_by_year = [];

    foreach (get_posts(['numberposts' => -1]) as $post) {
        $year = get_the_date('Y', $post);
        $posts_by_year[$year][] = [ get_the_permalink($post), get_the_title($post) ];
    }

    return $posts_by_year;
}
