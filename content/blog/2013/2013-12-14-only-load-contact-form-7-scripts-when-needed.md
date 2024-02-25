+++

title = "Only load Contact Form 7 scripts when needed"
date = 2013-12-14 18:33:20

+++

For the second year in a row, Elliot Richmond is hosting <a href="http://advent.squareonemd.co.uk/">WordPress Snippets Til Christmas</a>. The name says it pretty much: a useful WordPress snippet a day, until Christmas. Very useful, for both starters as experienced WordPress nerds.

I decided to get in on the fun and contributed <a href="http://advent.squareonemd.co.uk/prevent-loading-unnecessary-scripts-and-styles-in-wordpress/">a snippet explaining how to only load Contact Form 7 scripts and styles on pages that actually have a form in them</a>.

This is what the snippet does:
<ol>
	<li>It checks if the requested page is a post, page or attachment.</li>
	<li>Then, it checks if the post or page contains the <code>contact-form-7</code> shortcode.</li>
	<li>If either one of the above statements is not true, it will tell WordPress not to load the Contact Form 7 styles and scripts.</li>
</ol>

```php?start_inline=1
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

Also, if you're worrying about stuff like this, make sure you have proper caching set up. Caching, combining and minifying resources will give you a much bigger performance boost.

<h3>Plugin developers: load your scripts in the footer</h3>
In most cases, it makes sense to load your plugin scripts in the footer. This also has the benefit you can only add them to the footer if your plugin actually needs them. 

Just call <a href="https://codex.wordpress.org/Function_Reference/wp_enqueue_script">`wp_enqueue_scripts`</a> from your display function (your shortcode callback, template function, ...) and set the last parameter <code>$in_footer</code> to <code>true</code>. 

I usually register the script as early as possible so users have the chance to replace them, should they want to.

```php?start_inline=1
// on plugin initialization
wp_register_script( 'script-name', get_template_directory_uri() . '/js/script.js', array(), '1.0', true );

// your shortcode callback, widget display function, etc..
function my_display_function($attr = array(), $content = '') {
	wp_enqueue_script('script-name');
}
```

Did I miss anything? Don't hesitate to correct me in the comments. :-)