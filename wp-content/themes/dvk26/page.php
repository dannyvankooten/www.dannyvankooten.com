<?php get_header(); the_post(); ?>
<div class="container">
    <?php dvk26_home_link(); ?>
    <h1><?php the_title(); ?></h1>
    <?php the_content(); ?>
</div>
<?php get_footer(); ?>
