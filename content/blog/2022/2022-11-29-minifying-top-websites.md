+++
title = "Minification and cache directives for the most popular websites of the internet"
+++

While comparing various minification tools recently I soon discovered that there are plenty of options available. 

Some minifiers focus on performance and only strip whitespace, remove comments (except for license notices) and maybe rename local variables to use shorter names. That usually accounts for the biggest reduction in size, but the same effect is usually already accomplishing by using [gzip compression](https://en.wikipedia.org/wiki/Gzip).

Other minifiers are more comprehensive and some even apply dead code elimination, which usually requires evaluating the source internally (and therefore is a lot slower).

For websites that are pushed into production, minification performance is usually less important and achieving the highest reduction in file size (using only safe minifications) is what counts.

From  my findings and [related benchmarks](https://github.com/privatenumber/minification-benchmarks), the best available minification tools right now for the usual web assets are:

- [Terser](https://github.com/terser/terser) for JS files. Terser is the successor of UglifyJS and is the default option in [webpack](https://webpack.js.org/guides/production/#minification).
- [LightningCSS](https://lightningcss.dev/) for CSS files. 
- [html-minifier-terser](https://github.com/terser/html-minifier-terser) for HTML files. This is a fork of [html-minifier](https://github.com/kangax/html-minifier) and also maintained by the Terser people.

Just for fun, I decided to pull in the most popular websites (by Alexa rank) and run them through these tools to see what potential savings there could be. 

The good news is that most websites are doing really well, as I was only able to shave off about 11 kilobytes on average. 

The bad news is that a really popular porn website out there with 45M monthly visitors is serving unminified JavaScript and thereby forcing each and every one of their visitors to download 122 kB more than strictly necessary! ;-)

### Minifying the most popular websites on the intermet

Using a list of the most popular websites out there, I fired up a Python script<sup><a href="#1">1</a></sup> to download the HTML for each homepage<sup>2</sup>. 

It then parsed the HTML to look for any stylesheets and scripts and downloaded these too. After running these files through through `html-minifier`, `lightningcss` and `terser` respectively, gzipped sizes were compared and written to a CSV for later analysis.

Only safe minification techniques were used, so more aggressive techniques that could affect functionality were omitted.

What follows is a summary of the results (in bytes):

<div style="overflow-x: scroll;">

|       |   html_savings |   css_savings |   js_savings |   combined_savings |
|:------|---------------:|--------------:|-------------:|-------------------:|
| count |         606    |       606     |        606   |              606   |
| mean  |        1693 |       889 |       8283 |            10864 |
| std   |        3447 |      3178  |      17124 |            18285 |
| min   |           0    |         0     |          0   |                0   |
| 25%   |         205    |         0     |        257 |             1232   |
| 50%   |         631    |        56   |       2083   |             4648 |
| 75%   |        2090 |       876     |       9708   |            14081 |
| max   |       58872    |     68793     |     158072   |           158345   |

</div>

On average, about 11 kB worth of data could be saved by using these minification tools instead of whatever these websites are using now.

Compared to what certain page builders are outputting nowadays, this is actually really good!

But then the websites using these page builders are not visited anywhere close to 87 billion times per month ([google.com](https://google.com), #1 on the list) or 187 million times per month ([washingtonpost.com](https://washingtonpost.com), #500). 

Anything multiplied by such gigantic numbers will amount to a lot. And this is only using safe minification techniques, so normally quite trivial to improve upon.

To better understand just how much data this might amount to in total, we have to look at cache lifetimes too.

### Cache lifetimes

While downloading the asset files, I inspected the HTTP headers for cache directives. The average time (in seconds) was taken across all of the assets that had either a `Cache-Control` or an `Expires` header, or `0` if the response included no such header.

|       |     expires (s) |   expires (h)   |
|:------|----------------:|----------------:|
| count |   499           |          499    |
| mean  |     3.0e+07 |         8328 |
| std   |     7.0e+07 |        19451  |
| min   |     0           |            0    |
| 10%   |   292           |            0    |
| 25%   | 70994           |           19  |
| 50%   |     2.6e+06   |          720    |
| 75%   |     2.7e+07 |         7533    |
| max   |     5.5e+08  |       153300    |

The median cache lifetime encountered was 1 month. 25% of websites asked the browser to cache their assets for 24 hours and 10% asked for just 5 minutes.

I think the above is quite good already. Even taking into account that the results might be underestimating things because it only looks at assets defined in the static HTML.

It shows that these popular websites are pretty much all applying best practices we've known for years: 

- [Use gzip compression](https://docs.nginx.com/nginx/admin-guide/web-server/compression/). Only a handful requests out of several thousand did not have gzip compression enabled for their responses<sup>3</sup>, and IIRC most of these were for error responses.
- Minify your assets in production. Across the top 500 websites, I was only able to shave off an average of 11 kilobytes per website.
- [Instruct the browser that your assets can be cached](https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Cache-Control) in between requests. Over 50% of these popular websites had an average cache directive of about 1 month.


### Energy cost of data transmission 

In 2021, [data transmission was good for about 1.4% of global electricy usage](https://www.iea.org/reports/data-centres-and-data-transmission-networks). Imagine what this number would be if we did not have gzip compression, browser caches and minification.

A few years ago I wrote about [CO2 emissions on the web](@/blog/2020/2020-02-04-website-carbon-emissions.md) where I went with an estimate of 0.5 kWh per GB of data transfered. Since then I've seen a lot of additional discussion about the energy cost of data transfer, with estimates still varying wildly.

The team behind [WebsiteCarbon.com estimate it](https://sustainablewebdesign.org/calculating-digital-emissions/) at about 0.8 kWh per GB while [other research](https://www.researchgate.net/figure/Trends-for-ICT-electric-power-overall-2030_fig5_342643762) estimates it closer to 0.1 kWh per GB for 2020. 

Whatever the actual number is, the good news is that data transmission still seems to be getting more efficient. Let's make sure these efficiency gains aren't negated because of [Jevon's paradox](https://en.wikipedia.org/wiki/Jevons_paradox), shall we?


---

<small id="1"><sup>1</sup> You can [find the code and results for this experiment here](https://git.sr.ht/~dvko/dannyvankooten.com/tree/master/code/minify-top-500-websites).</small>

<small><sup>2</sup> This approach ignores any dynamically inserted assets, because only assets linked from the static HTML are downloaded and evaluated. </small>

<small><sup>3</sup> gzip is probably the real hero of this story. It's mind boggling to think of how much data is saved because of this compression algorithm.</small>
