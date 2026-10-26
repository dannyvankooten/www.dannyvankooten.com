---
title: "Add lightweight social sharing links to WordPress posts"
description: "Add fast, script-free social sharing links to WordPress posts, or use Social Sharing by Danny for a ready-made lightweight plugin."
date: "2013-12-02T00:00:00+00:00"
modified: "2026-05-17T08:13:20+02:00"
categories: ["Web Performance"]
---

Many WordPress social sharing plugins add more code than a simple share link needs. Extra scripts, stylesheets, tracking code, and remote assets can slow down pages for a feature that many visitors will never use.

If you only want a few share links below your posts, you can do that with plain HTML. Most social networks support share URLs that accept the page title and URL as query parameters. That means you can offer sharing options without loading a third-party JavaScript widget.

**Prefer a ready-made option?** I built a lightweight WordPress plugin for this approach: [Social Sharing by Danny](https://wordpress.org/plugins/dvk-social-sharing/). It adds simple sharing buttons without the heavy overhead common in many social sharing plugins.

## Use plain HTML share links

The basic idea is simple: create normal links that point to each network’s share endpoint. The links can include the current post URL and, where supported, the post title.

```
<p class="social-sharing-links">
    <a href="https://twitter.com/intent/tweet?text=YOUR_TITLE&url=YOUR_URL" target="_blank" rel="noopener">Share on X/Twitter</a>
    <a href="https://www.facebook.com/sharer/sharer.php?u=YOUR_URL" target="_blank" rel="noopener">Share on Facebook</a>
    <a href="https://www.linkedin.com/sharing/share-offsite/?url=YOUR_URL" target="_blank" rel="noopener">Share on LinkedIn</a>
</p>
```

This renders three regular links. There is no embedded widget and no external script to download. You can style the links in your theme stylesheet just like any other markup.

## Add the links to every single post

To add these links automatically below every blog post, hook into `the_content`. The snippet below appends a small block of sharing links on single posts only.

```
add_filter( 'the_content', 'my_add_social_sharing_links' );

function my_add_social_sharing_links( $content ) {
    if ( ! is_single() || 'post' !== get_post_type() ) {
        return $content;
    }

    $title = rawurlencode( get_the_title() );
    $url   = rawurlencode( get_permalink() );

    $links = sprintf(
        '<p class="social-sharing-links">
            <a href="https://twitter.com/intent/tweet?text=%1$s&url=%2$s" target="_blank" rel="noopener">Share on X/Twitter</a>
            <a href="https://www.facebook.com/sharer/sharer.php?u=%2$s" target="_blank" rel="noopener">Share on Facebook</a>
            <a href="https://www.linkedin.com/sharing/share-offsite/?url=%2$s" target="_blank" rel="noopener">Share on LinkedIn</a>
        </p>',
        esc_attr( $title ),
        esc_attr( $url )
    );

    return $content . $links;
}
```

The snippet uses the current post title and permalink, encodes them for use in a URL, and appends the links to the post content. It also avoids loading anything on archive pages, pages, or custom post types.

## Keep it lightweight

This approach is useful when you want sharing links without the cost of a larger plugin. You still get clear calls to action, but the browser only has to render a few links. If you want icons or a small popup window, you can add those yourself with minimal CSS and JavaScript.

If you would rather install a plugin instead of maintaining the snippet, use the Social Sharing by Danny plugin hosted on WordPress.org. The plugin packages this lightweight sharing approach with simple icons and a very small script for opening share links in a popup window.
