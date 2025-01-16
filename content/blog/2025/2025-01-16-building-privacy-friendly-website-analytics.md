+++
title = "Building self-hosted and privacy-friendly website analytics. Again."
+++

> TLDR: I am building a standalone version of Koko Analytics: https://github.com/koko-analytics/koko-analytics

The year is 2025 and I once again find myself building an open-source, self-hostable, privacy-friendly website analytics product.

By now I am starting to worry if I will ever be able to leave this topic alone. I mean, it was quite the rage [back in 2018](/blog/2018/reviving-ana-as-fathom/) but surely there are more than enough good solutions available now, well over 6 years later?

The one thing I have going for me is that I am not actually starting from scratch. Instead, I am porting over [Koko Analytics](https://www.kokoanalytics.com/) to a standalone version in modern PHP.

The WordPress plugin version is currently in active use on more than 50.000 sites according to [the statistics on WordPress.org](https://wordpress.org/plugins/koko-analytics/advanced/). Over the years, I have received several requests for a standalone version for being able to track non-WordPress sites. Having been there when I was initially involved with Fathom, I never really wanted to go there again.

However, a couple of weeks ago I found myself itching to get it done, rolled up my sleeves and started the work.

## Introducing Koko Analytics Standalone

I haven't really decided whether I want to recycle the Koko Analytics brandname or name it differently, but let's just roll with what we got for now.

You can follow the project on GitHub here: [github.com/koko-analytics/koko-analytics](https://github.com/koko-analytics/koko-analytics).

### Project goals

Goals for the project are to have a PHP based solution that uses a minimal amount of resources and can run on a wide range of hosting options.

You can use either MySQL, PostgreSQL or SQLite as the database backend. Yes, it would be faster to use an OLAP database engine like Clickhouse or DuckDB, but because Koko Analytics does a bunch of aggregation before data is persisted, we can use a more traditional database engine instead.

### Open-source licensing

The application itself is licensed under the AGPL license. Only the tracking snippet (which allows the project to work with pages served from cache) is MIT licensed.

This mirrors licensing used by alternatives like [Matomo](https://matomo.org/licences/) and [Plausible](https://plausible.io/blog/open-source-licenses).

### Where we at?

Koko Analytics Standalone is functional right now, but I still expect some things to change over the next few months. We are about to start dogfooding it and I hope to release an official first stable version some time during the second or third quarter of 2025.

If you think all this sounds interesting and would like to follow along, [come on over to GitHub and drop a star](https://github.com/koko-analytics/koko-analytics).
