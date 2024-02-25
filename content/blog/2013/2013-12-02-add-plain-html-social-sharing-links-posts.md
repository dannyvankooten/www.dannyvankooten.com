+++
title = "Add plain HTML social sharing links to your posts"
date = 2013-12-02 23:23:17
+++

I was going through the social sharing plugins for WordPress and found that most were either too complicated, bloated, poorly coded or ugly for my liking. 

I was looking to add simple sharing links for Twitter, Facebook and Google+ to my posts but I didn't want to add an extra 50 kb of scripts to my pages for a functionality 99% (let's be honest) of my visitors did <strong>not </strong>use.

Turns out, it is actually really easy to add sharing options to your posts without having to load any scripts. Twitter, Facebook and Google+ all provide a way to call their sharing functionality by linking to an URL and pass some parameters like the text or URL of the item to be shared.

<h3>HTML only sharing links</h3>
The HTML for the three sharing links is pretty simple. 

```html
<p class="social-sharing-links">
    <a href="https://twitter.com/intent/tweet/?text=YOUR_TEXT&url=YOUR_URL&via=YOUR_TWITTER" target="_blank">Share on Twitter</a>
    <a href="https://www.facebook.com/sharer/sharer.php?p[url]=YOUR_URL&p[title]=YOUR_TITLE" target="_blank" >Share on Facebook</a>
    <a href="https://plus.google.com/share?url=YOUR_URL" target="_blank" >Share on Google+</a>
</p>
```

The above HTML will render 3 default links so people can share on Twitter, Facebook or Google+.

<h3>Add links to every single post</h3>
Now, to automatically add the social sharing links to all of your single posts you can use the following code snippet. 

It will use the post title, text and permalink and use it in the URL parameters. <strong>No JavaScript or loads of CSS needed.</strong>

```php?start_inline=1
add_filter('the_content', 'my_add_social_sharing_links');

function my_add_social_sharing_links($content) {

    if(is_single() && get_post_type() == 'post') {

        $title = urlencode(get_the_title());
        $url = urlencode(get_permalink());
        $twitter_username = 'DannyvanKooten';

        ob_start();
        ?>
        
        <p class="social-sharing-links">
            <a href="https://twitter.com/intent/tweet/?text=<?php echo $title; ?>&url=<?php echo $url; ?>&via=<?php echo $twitter_username; ?>" target="_blank">Share on Twitter</a>
            <a href="https://www.facebook.com/sharer/sharer.php?s=100&p[url]=<?php echo $url; ?>&p[title]=<?php echo $title; ?>" target="_blank" >Share on Facebook</a>
            <a href="https://plus.google.com/share?url=<?php echo $url; ?>" target="_blank" >Share on Google+</a>
        </p>

        <?php
        $content .= ob_get_contents();
        ob_end_clean();
    }


    return $content;
}
```

You can of course modify the snippet and maybe add a few CSS rules to your theme stylesheet to make it look prettier.

I built a small plugin around this called <a href="https://www.dannyvankooten.com/wordpress-plugins/social-sharing-by-danny/" title="Social Sharing by Danny">Social Sharing by Danny</a>. Included are a few simple but pretty icons and a very small (under 500 bytes) JavaScript file which will make the links open in a pop-up window. Hope you like it!

Know of another simple and lightweight sharing plugin? Let me know please. :)