<?php get_header(); ?>
<main id="main-content" class="container site-content">
    <h1>Page not found</h1>
    <p>The requested URL <code><?php echo esc_html(rawurldecode(wp_unslash($_SERVER['REQUEST_URI'] ?? ''))); ?></code> was not found on this site.</p>
    <p><a href="/">&laquo; Back to homepage</a></p>
</main>
<?php get_footer(); ?>
