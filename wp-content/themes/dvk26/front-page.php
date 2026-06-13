<?php get_header();
the_post(); ?>
<main id="main-content" class="container site-content">
    <article>
        <h1><?php the_title(); ?></h1>
        <?php the_content(); ?>
    </article>
        
    <section class="post-list">
<?php $posts_by_year = dvk26_get_posts_grouped_by_year(); ?>
<?php foreach ($posts_by_year as $year => $posts) : ?>
        <section>
            <h2><?php echo esc_html($year); ?></h2>
            <ol>
    <?php foreach ($posts as [$permalink, $title]) : ?>
                <li><a href="<?php echo esc_url($permalink); ?>"><?php echo esc_html($title); ?></a></li>
    <?php endforeach; ?>
            </ol>
        </section>
<?php endforeach; ?>
    </section>
</main>
<?php get_footer(); ?>
