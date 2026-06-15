<!doctype html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="preload" href="<?php echo esc_url(get_stylesheet_directory_uri()); ?>/assets/fonts/Inter-Regular.woff2" as="font" type="font/woff2" crossorigin>
    <?php wp_head(); ?>
    <meta name="theme-color" content="#030303">
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<header class="container site-header">
    <nav class="site-nav" aria-label="Primary">
        <a class="site-logo" href="<?php echo esc_url(home_url('/')); ?>" aria-label="<?php echo esc_attr(get_bloginfo('name')); ?>">
            <svg aria-hidden="true" focusable="false" viewBox="0 0 64 64" xmlns="http://www.w3.org/2000/svg"><text fill="currentColor" text-anchor="middle" x="32" y="50" font-family="Inter, sans-serif" font-size="50" font-weight="700">d</text></svg>
        </a>
        <?php
        $nav_items = array(
            'about'    => 'About',
            'projects' => 'Projects',
            'contact'  => 'Contact',
        );
        ?>
        <input class="site-nav-checkbox" type="checkbox" id="site-nav-toggle">
        <label class="site-nav-toggle" for="site-nav-toggle">
            <span class="screen-reader-text">Toggle navigation</span>
            <span aria-hidden="true"></span>
        </label>
        <ul class="site-nav-menu">
            <?php
            foreach ($nav_items as $slug => $label) :
                $is_current = is_page($slug);
                ?>
                <li>
                    <a href="<?php echo esc_url(home_url("/{$slug}/")); ?>"<?php echo $is_current ? ' aria-current="page"' : ''; ?>>
                        <?php echo esc_html($label); ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </nav>
</header>
