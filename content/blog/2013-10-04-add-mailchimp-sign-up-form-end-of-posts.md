+++
title = "Automatically add a Mailchimp sign-up form to all posts"
date = 2013-10-04 15:40:32
+++

When someone gets to the end of your post, they surely love what you have to say. It makes the end of your posts the perfect spot to ask your visitors to sign-up to your newsletter. It converts well and you're asking right when they're feeling great about your content.

In this post I'll show you a small snippet you can use on your WordPress website. This snippet will automatically add a sign-up form to the end of all your posts, without having to go through all of them individually (which is madness).

<strong>Requirements</strong>
<ul>
	<li><a href="https://wordpress.org/plugins/Mailchimp-for-wp/">Mailchimp for WordPress</a></li>
</ul>
There are various ways to go about this but the easiest way is to use a filter, like I did in the following code snippet. All you need to do is to add this snippet to your theme <strong>functions.php </strong>file (use the Theme Editor or FTP for this) and you're good to go.

```php?start_inline=1
/**
 * Adds a form to the end of all single posts
 * 
 * @param string $content
 * 
 * @return string $content
 */
function myprefix_add_form_to_posts( $content ) {
    
    // Change to ID of the form you want to add
    $form_id = 0;
    
    // Check if this is a single post. 
    if ( is_singular( 'post' ) ) {        
        $content .= mc4wp_get_form( $form_id );
    }
    
    // Returns the content.
    return $content;
}
 
add_filter( 'the_content', 'myprefix_add_form_to_posts' );
```

If you're using the premium version of <a title="Mailchimp for WordPress" href="https://www.mc4wp.com/">Mailchimp for WordPress</a>, don't forget to change `$form_id` into the ID of the sign-up form you wish to show.

<em>Please be careful implementing the above snippet if you're not comfortable with PHP and/or editing functions.php files. It's easy to break your website if you're not following the correct PHP syntax.</em>

Questions? Ask away in the comments!