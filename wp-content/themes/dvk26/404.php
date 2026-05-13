<?php get_header(); ?>
<div class="container">
    <?php dvk26_home_link(); ?>
    <h1>Page not found</h1>
    <p>The requested URL <code><?php echo esc_html($_SERVER['REQUEST_URI']) ?></code> was not found on this site.</p>
    <p><a href="/">&laquo; Back to homepage</a></p>
</div>
<?php get_footer(); ?>