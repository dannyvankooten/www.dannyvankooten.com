+++
title = "Version 2.0 of Mailchimp for WordPress"
date = 2014-04-30 13:17:30
+++

Almost a year after its initial release (June 2013) and close to 300.000 downloads later, I just pushed version 2.0 of <a href="https://wordpress.org/plugins/Mailchimp-for-wp/">Mailchimp for WordPress to the WordPress.org plugin repository</a>. Although the update doesn't come with too many changes or new features it brings some cool new features to the plugin.
<h3>Support for CAPTCHA fields</h3>
The biggest new feature is the ability to include (mathematical) Captcha fields in your sign-up forms. The option to include a Captcha field will only be visible if you're running the <a href="https://wordpress.org/plugins/captcha/">Captcha plugin by BestWebSoft</a>.

If you're running the Captcha plugin, using the <code>[captcha]</code> shortcode in your form mark-up is the only thing that's needed to render the Captcha field. The plugin will take care of validation and showing any possible error messages.
<h3><img class="size-full wp-image-4075 aligncenter" src="https://res.cloudinary.com/dannyvankooten/image/upload/v1408704525/captcha-demo_dzr074.jpg" alt="captcha-demo" width="452" height="249" /></h3>
<h3></h3>
<h3>Translated settings pages</h3>
Previously the only translatable part were the actual sign-up forms and checkboxes. In version 2.0, all text on the Mailchimp for WordPress settings pages makes use of WordPress' translation functions which allows translation to the language of your WP installation.

<img class="aligncenter size-full wp-image-4076" src="https://res.cloudinary.com/dannyvankooten/image/upload/v1408704524/License-API-Settings-Mailchimp-for-WP-Pro-_-WP-Latest-_-WordPress-2014-04-30-13-14-09-2014-04-30-13-14-57_gpuonn.jpg" alt="Dutch settings page" width="613" height="279" />

As of now, the plugin only comes with English (default) and Dutch language files. If you're good at <a href="https://codex.wordpress.org/Translating_WordPress">translating</a>, feel free to send in your translation files so I can include them in the plugin. <a href="https://plugins.svn.wordpress.org/mailchimp-for-wp/trunk/languages/">You can download the latest .po files here.</a>
<h3>Other changes</h3>
<ul>
	<li>Stylesheets are no longer combined and served through a <code>.php</code> file but are now served as static (and minified) CSS. This allows the use of a plugin like W3 Total Cache to combine the various stylesheets loaded by Mailchimp for WP.</li>
	<li>You can now use TAB indentation in the form mark-up, which helps keeping your mark-up looking nice and tidy.</li>
	<li>The anti-SPAM honeypot is now added to sign-up checkboxes as well, reducing spam sign-ups even more.</li>
	<li>Major code improvement: better object-oriented code, class documentation (PHPDocs) and so forth.</li>
</ul>
<h3>Premium plugin</h3>
In case you don't know, <a href="https://www.mc4wp.com/">Mailchimp for WordPress has a premium version</a> which comes with some very useful improvements over the free version. For example, using the Pro version you can create multiple sign-up forms where each form can be totally different and subscribe people to one or multiple of your Mailchimp lists.

All new features and changes described in this post are (obviously) also included in the Pro version of the plugin.
<h3>GitHub project</h3>
Although not directly related to the 2.0 release, the <a href="https://github.com/ibericode/mailchimp-for-wordpress">Mailchimp for WordPress project is now on GitHub</a>. If you would like to follow development of the plugin (or even contribute some code yourself), feel free to drop by.

Found a bug? Please <a href="https://github.com/ibericode/mailchimp-for-wordpress/issues">raise an issue on GitHub.</a>