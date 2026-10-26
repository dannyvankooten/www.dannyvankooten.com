---
title: "CDN URL rewriting plugin for WordPress"
description: "A lightweight WordPress plugin for rewriting asset URLs to serve them from a CDN."
date: "2015-05-22T00:00:00+00:00"
modified: "2026-05-13T15:16:51+02:00"
categories: ["WordPress"]
---

To make sure the [Mailchimp for WordPress](https://www.mc4wp.com/) site loads as fast as possible throughout the world we recently started using Amazon Cloudfront, a Content Delivery Network, for serving all public assets (CSS, JavaScript and image files).

Configuring WordPress to use a CDN seemed like a straightforward and simple enough process to me. Looking at the available plugins however, nothing really seemed to work in the way I expected it to.

I was looking for something lightweight that only replaced certain URL’s on the public section of a WordPress site, but everything I found did way more than that, was poorly coded or simply broken. That’s why I decided to write my own little CDN plugin for WordPress.

### WordPress CDN Loader, a microplugin.

In the last few months, I’ve been creating more and more **#microplugins** which do one thing and do it well. Since others might benefit from a simple CDN URL rewriter as well, I made the CDN Loader plugin for WordPress publicly available on GitHub.

We’re using it ourselves with Amazon Cloudfront (which is free for low-bandwidth sites) but you can configure it with any CDN service. Configuring the plugin is very straightforward, although the plugin does not come with a dedicated settings page as I figured that would be overkill for the one setting it actually requires: your CDN URL.

### Installing the CDN Loader

To use the plugin on your own site, take the following steps.

1.  First, install the plugin from GitHub: [dannyvankooten/wp-cdn-loader](https://github.com/dannyvankooten/wp-cdn-loader)
2.  Configure your CDN URL in `wp-config.php` by defining the following PHP constant.

```
define( 'DVK_CDN_URL', '//xxxxxx.cloudfront.net' );
```

That’s all there is to it – you’re all set now!

I hope you enjoy the lightweight solution, we’ve been using it on our own sites for a few months now.
