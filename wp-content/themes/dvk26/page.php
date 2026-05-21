<?php get_header(); the_post(); ?>
<main id="main-content" class="container site-content">
    <h1><?php the_title(); ?></h1>
    <?php the_content(); ?>
</main>
<?php get_footer(); ?>
