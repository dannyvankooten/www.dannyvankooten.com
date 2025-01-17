+++
title = "Building self-hosted and privacy-friendly website analytics. Again."
+++

<div class="alert" role="alert">TLDR: I am building a standalone version of Koko Analytics so that you can use it for non-WordPress sites. You can follow the project on GitHub here: <a href="https://github.com/koko-analytics/koko-analytics">github.com/koko-analytics/koko-analytics</a>.</div>

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

You can use either MySQL, PostgreSQL or SQLite as the database backend. You may wonder how this will possibly scale or why we're not choosing Clickhouse or DuckDB for the database. The answer is that Koko Analytics doesn't want to force you on certain hardware or spinning up a whole new database just for some metrics. By making some choices, we can make a traditional OLTP database perform really well:

- Daily granularity.
- Writing data to an temporary buffer file, then periodically aggregating this and only then persisting to the database (in bulk).
- Limiting the amount of metrics that Koko Analytics tracks: we think visitors, pageviews, referral traffic and custom events are most important for our target audience.


### Open-source licensing

The application itself is licensed under the [AGPL license](https://www.gnu.org/licenses/agpl-3.0.en.html).

To avoid issues with AGPL virality, the tracking snippet (which you include on the sites you would like to track and allows the project to track pages served from cache) is MIT licensed.

This mirrors the licensing used by established alternatives like [Matomo](https://matomo.org/licences/) and [Plausible](https://plausible.io/blog/open-source-licenses).

### Where we at?

Koko Analytics Standalone is functional already, but I still expect some things to change over the next few months.

We are about to start dogfooding it and hope to release a stable version some time during the second or third quarter of 2025.

If you think all this sounds interesting and would like to follow along, [come on over to GitHub and drop a star](https://github.com/koko-analytics/koko-analytics).
