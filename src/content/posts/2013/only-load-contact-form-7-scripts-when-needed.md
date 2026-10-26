---
title: "Only load Contact Form 7 scripts when needed"
description: "A WordPress snippet to only load Contact Form 7 scripts and styles on pages that actually contain a form."
date: "2013-12-14T00:00:00+00:00"
modified: "2026-05-14T14:15:10+02:00"
categories: ["WordPress"]
---

> **Update, 2026:** This post was written in 2013. The general idea still applies: avoid loading plugin assets on pages that do not need them. The original links are dead, and modern WordPress supports script loading strategies like `defer` through `wp_enqueue_script()`. Treat the snippet below as historical context, not as current best practice.

For the second year in a row, [Elliot Richmond](https://elliottrichmond.co.uk/) is hosting WordPress Snippets Til Christmas. The name says it pretty much: a useful WordPress snippet a day, until Christmas. Very useful, for both starters as experienced WordPress nerds.

I decided to get in on the fun and contributed a snippet explaining how to only load Contact Form 7 scripts and styles on pages that actually have a form in them.

This is what the snippet does:

1.  It checks if the requested page is a post, page or attachment.
2.  Then, it checks if the post or page contains the `contact-form-7` shortcode.
3.  If either one of the above statements is not true, it will tell WordPress not to load the Contact Form 7 styles and scripts.

```
function dvk_dequeue_scripts() {

    $load_scripts = false;

    if( is_singular() ) {
        $post = get_post();

        if( has_shortcode($post->post_content, 'contact-form-7') ) {
            $load_scripts = true;
        }

    }

    if( ! $load_scripts ) {
        wp_dequeue_script( 'contact-form-7' );
        wp_dequeue_style( 'contact-form-7' );
    }

}

add_action( 'wp_enqueue_scripts', 'dvk_dequeue_scripts', 99 );
```

Be careful when implementing a snippet like this. If you (or your client) show a contact form using a template function or widget, the form will be missing its scripts and styles.

Also, if you’re worrying about stuff like this, make sure you have proper caching set up. Caching, combining and minifying resources will give you a much bigger performance boost.

### Plugin developers: load assets only when needed

In 2013, my advice was to load scripts in the footer. That was a reasonable default at the time, but modern WordPress gives plugin developers better tools.

Since WordPress 6.3, wp\_enqueue\_script() accepts an args array where you can pass a loading strategy such as defer or async. For most plugin JavaScript that does not need to block rendering, defer is a better default than only moving the script to the footer.

```
wp_enqueue_script(
    'script-name',
    plugin_dir_url( __FILE__ ) . 'js/script.js',
    array(),
    '1.0.0',
    array(
        'strategy'  => 'defer',
        'in_footer' => true,
    )
);
```

The harder part is still deciding when to enqueue the asset. If the script is only needed when a shortcode, block, or template tag renders something, enqueue it from that rendering path instead of loading it globally.

What did I miss? Let me know by leaving a comment on this post.
