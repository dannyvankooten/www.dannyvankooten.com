+++
title = "WordPress plugin structure, don't load unnecessary code"
date = 2013-11-20 12:00:58
+++

When developing WordPress plugins it is important to decide whether code needs to be loaded or not as early in the request lifecycle as possible. Why bother loading unnecessary code and hooking into actions and filters that will never run for the request being made? Having said that, it is also important to keep your code structured and not go overboard with this.

<h2>Conditional loading of code</h2>
What worked well for me so far is to split my code up into several files and load each of these files based on simple conditionals. While this might not do that much in terms of performance this will also help you in the long run by forcing a certain structure in your code.

Let's start with a no-brainer: admin section specific code versus public or frontend section specific code.

<h4>Do not load admin code in the public area</h4>
It makes no sense to load code that hooks into admin specific hooks for requests to the front page of a website. So let's split up our code into two files:

<ul>
	<li><code>public.php</code>, containing all public or frontend code, like shortcode callbacks and <code>wp_enqueue_scripts</code> hooks.</li>
	<li><code>admin.php</code>, containing all admin section code, like settings pages.</li>
</ul>

We can then load each of these files based on the outcome of the <code>is_admin()</code> function, which will return true if the URL begin requested is in the admin section.

```php?start_inline=1
define( 'MYPLUGIN_DIR', plugin_dir_path( __FILE__ ) );

// load code that ALWAYS needs to be loaded
// (registering post types, widgets, etc..)
require MYPLUGIN_DIR . 'includes/init.php'; 

if( ! is_admin() ) {
	// load code that only needs to be loaded for the public section
	// (stylesheets, shortcode callbacks, template functions, etc..)
	require MYPLUGIN_DIR . 'includes/public.php';
} else {
	// load code that is only needed in the admin section
	// (setting pages, admin menu, etc..)
	require MYPLUGIN_DIR . 'includes/admin.php';
}
```

<h4>AJAX requests</h4>
We can add one more conditional to the snippet above to differentiate between AJAX requests and regular (non-AJAX) requests. We can do this by checking for a constant named <code>DOING_AJAX</code>. 

For AJAX requests, <code>is_admin()</code> will always return <code>true</code>, which makes it unnecessary to check for AJAX in requests for the public section. My complete initializing code usually looks something like this.

```php?start_inline=1
define( 'MYPLUGIN_DIR';, plugin_dir_path( __FILE__ ) );

// all requests
require MYPLUGIN_DIR . 'includes/init.php';

if ( !is_admin() ) {
	// public sectionrequests
	require MYPLUGIN_DIR . 'includes/public.php';
} else if ( defined( 'DOING_AJAX' ) &amp;&amp; DOING_AJAX ) {
		// AJAX requests
		require MYPLUGIN_DIR . 'includes/ajax.php';
} else {
	// admin section requests
	require MYPLUGIN_DIR . 'includes/admin.php';
}
```



Combining these 3 simple conditionals will keep the footprint of your plugin for each request as low as possible and forces you to follow a certain code structure, which you will benefit from in the long run.

Additions or suggestions? Please leave them in the comments. :-) 
