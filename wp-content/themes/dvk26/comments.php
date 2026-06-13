<?php
if (post_password_required()) {
    return;
}

if (have_comments() || comments_open()) :
    ?>
<section id="comments" class="comments-area mm">
    <?php if (have_comments()) : ?>
        <h2 class="comments-title m0">
            <?php
            printf(
                esc_html(_n('%s comment', '%s comments', get_comments_number(), 'dvk26')),
                esc_html(number_format_i18n(get_comments_number()))
            );
            ?>
        </h2>

        <ol class="comment-list">
            <?php
            wp_list_comments([
                'style'      => 'ol',
                'short_ping' => true,
                'avatar_size' => 40,
            ]);
            ?>
        </ol>

        <?php the_comments_navigation(); ?>
    <?php endif; ?>

    <?php
    if (comments_open()) {
        comment_form();
    }
    ?>
</section>
<?php endif; ?>
