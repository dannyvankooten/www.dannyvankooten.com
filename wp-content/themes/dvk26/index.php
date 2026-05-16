<?php get_header(); ?>
<main id="main-content" class="container">
    <?php if(is_search()) : ?>
        <h1>Search results for "<?php the_search_query(); ?>"</h1>
    <?php elseif (is_archive()) : ?>
        <h1><?php the_archive_title(); ?></h1>
        <p><?php the_archive_description(); ?></p>
    <?php endif; ?>

    <?php if (have_posts()) : ?>
        <section class="post-list">
            <ol>
        <?php while(have_posts()): the_post(); ?>
            <li><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></li>
        <?php endwhile; ?>
            </ol>
        </section>
        <?php the_posts_navigation(); ?>

    <?php else : ?>
        <p>No results.</p>
    <?php endif; ?>
</main>
<?php get_footer(); ?>
