<!doctype html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="preload" href="<?php echo get_stylesheet_directory_uri(); ?>/assets/fonts/Inter-Regular.woff2" as="font" type="font/woff2" crossorigin>
    <link rel="stylesheet" href="<?php echo esc_url(dvk26_get_asset_url('assets/fonts/inter.css')); ?>">
    <link rel="stylesheet" href="<?php echo esc_url(dvk26_get_asset_url('style.css')); ?>">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<header class="container">
    <a class="site-logo" href="<?php echo esc_url(home_url('/')); ?>" aria-label="<?php echo esc_attr(get_bloginfo('name')); ?>">
        <svg aria-hidden="true" focusable="false" viewBox="0 0 64 64" xmlns="http://www.w3.org/2000/svg">
            <text fill="currentColor" text-anchor="middle" x="32" y="50" font-family="Inter, sans-serif" font-size="50" font-weight="700">d</text>
        </svg>
    </a>
</header>
