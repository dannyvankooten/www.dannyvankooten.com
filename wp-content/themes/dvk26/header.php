<!doctype html>
<html <?php language_attributes(); ?>>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <?php wp_head(); ?>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet">
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
