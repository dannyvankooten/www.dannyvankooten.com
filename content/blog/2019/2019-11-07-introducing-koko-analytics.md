+++
title = "Introducing Koko Analytics"
date = 2019-11-07
+++

After [stepping down from Fathom](@/blog/2019/2019-03-18-stepping-down-fathom-maintainer.md) earlier this year, I was happy working on [Mailchimp for WordPress](https://www.mc4wp.com/) for a good few months 
before realising that I was still thinking about how to make web analytics more private.

It dawned on me that part of why I was building Fathom in Go was because I wanted a break from WordPress and because I deemed it necessary to achieve good enough performance. 

That last part might still hold true, but when choosing not to keep track of bounce rates and the time a visitor spends on a page, things become much simpler.

Add to that the following facts and an idea was born:

- Adding a third-party service to your site to keep track of your visitors will never be as private as a self-hosted service.
- The majority of WordPress users will never self-host their analytics if it's not as easy as installing and activating a plugin.
- [WordPress powers 34.9% of the internet](https://w3techs.com/technologies/details/cm-wordpress/all/all). That's 34.9% of the internet owning their data, despite usually not being a developer themselves.

That's why I set out to built [Koko Analytics](https://www.kokoanalytics.com/), a privacy-friendly analytics plugin for WordPress that does not use any external services.

![Koko Analytics dashboard](/media/2019/koko-analytics-dashboard.png)

### Metrics

Koko Analytics currently keeps track of the following metrics: 

- Total site visitors
- Total site pageviews
- (Unique) pageviews for posts, pages, products, etc.
- Referrers (including a built-in blacklist to filter referrer spam)

The nice thing about running inside of WordPress is that it gives the software first-hand knowledge about what's being tracked and allows it to offer seamless integrations, like built-in event tracking for leaving comments or any of the popular form plugins.

### Performance

Most likely, you won't even notice that Koko Analytics is there. Even when your site is getting hammered by a sudden burst of traffic.

To achieve this, the plugin uses an append-only buffer file in which pageviews are temporarily stored until they are aggregated using a background process that runs every 60 seconds.

In my tests it was able to handle well over 15.000 requests per second, meaning you don't have to worry about being on the first page of Hacker News. [PHP has really come a long way](@/blog/2019/2019-02-04-from-go-back-to-php-again.md) in the last few years.

### Downloading the plugin 

To make sure as many people as possible have access to Koko Analytics and any improvements made by me or others, the plugin is GPLv3 licensed and available for free download.

As of yesterday, you can [download Koko Analytics from WordPress.org](https://wordpress.org/plugins/koko-analytics) or [contribute to it on GitHub](https://github.com/ibericode/koko-analytics).

If you're running Koko Analytics on your WordPress site then please don't hesitate to [let me know](/contact/) and share your thoughts on how we can make it better. 

And definitely consider [leaving a plugin review on WordPress.org](https://wordpress.org/support/plugin/koko-analytics/reviews/#new-post), because as you can see we desperately need some.

