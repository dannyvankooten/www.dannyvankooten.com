<?php get_header(); the_post(); ?>
<main class="container">
    <h1><?php the_title(); ?></h1>
    <?php the_content(); ?>
</main>
<?php get_footer(); ?>
