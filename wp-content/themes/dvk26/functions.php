<?php

global $content_width;
if (! isset($content_width)) {
    $content_width = 540;
}

add_filter('xmlrpc_enabled', '__return_false');

add_action('after_setup_theme', function() {
    add_theme_support( 'html5', array( 'comment-list', 'comment-form', 'search-form', 'gallery', 'caption', 'style', 'script' ) );
    add_theme_support( 'title-tag' );
    add_theme_support( 'automatic-feed-links' );
    add_theme_support( 'responsive-embeds' );
});

add_action('wp_enqueue_scripts', function() {
    $stylesheet = get_stylesheet_directory() . '/style.css';
    wp_enqueue_style('theme', get_stylesheet_uri(), [], filemtime($stylesheet));
}, -10);

function dvk26_home_link() {
    ?>
    <a class="site-logo" href="<?php echo esc_url(home_url('/')); ?>" aria-label="Danny van Kooten homepage">
        <svg aria-hidden="true" focusable="false" viewBox="0 0 64 64" xmlns="http://www.w3.org/2000/svg">
            <text fill="currentColor" text-anchor="middle" x="32" y="50" font-family="Inter, sans-serif" font-size="50" font-weight="700">d</text>
        </svg>
    </a>
    <?php
}

function dvk26_get_posts_grouped_by_year(): array {
    $posts_by_year = [];

    foreach (get_posts(['numberposts' => -1]) as $post) {
        $year = get_the_date('Y', $post);
        $posts_by_year[$year][] = [ get_the_permalink($post), get_the_title($post) ];
    }

    return $posts_by_year;
}
