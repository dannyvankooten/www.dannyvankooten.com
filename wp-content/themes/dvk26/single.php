<?php get_header(); the_post(); ?>
<main id="main-content" class="container site-content">
    <article <?php post_class('h-entry') ?>>
        <h1 class="p-name"><?php the_title(); ?></h1>
        <p class="text-sm">Published by <a class="p-author h-card" href="<?php echo esc_url(get_the_author_meta('url')); ?>" rel="author"><?php echo esc_html(get_the_author()); ?></a> on 
            <time class="dt-published" datetime="<?php echo esc_attr(get_the_date('Y-m-d')); ?>"><?php the_date(); ?></time>.
            <a class="u-url" style="display: none;" href="<?php the_permalink(); ?>">Permalink</a>
        </p>
        <div class="e-content mm">
            <?php the_content(); ?>
        </div>
    </article>
    <?php the_post_navigation(); ?>
</main>
<?php get_footer(); ?>
