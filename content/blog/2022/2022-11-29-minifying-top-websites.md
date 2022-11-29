+++
title = "Minifying the most popular websites on the internet"
+++

While comparing various minification tools recently I soon discovered that there are plenty of options available. 

Some minifiers focus on performance and only strip whitespace, remove comments (except for license notices) and maybe rename local variables to use shorter names. That usually accounts for the biggest reduction in size anyway, but the effect is dampened because pretty much any webserver already compresses static files with [gzip](https://en.wikipedia.org/wiki/Gzip) nowadays.

Other minifiers are more comprehensive and some even apply dead code elimination, which usually requires evaluating the source internally (and therefore is a lot slower).

For websites that are pushed into production, minification performance is usually less important and achieving the highest reduction in file size (using only safe minifications) is what counts.

From  my findings and [related benchmarks](https://github.com/privatenumber/minification-benchmarks), the best available minification tools right now for the usual web assets are:

- [Terser](https://github.com/terser/terser) for JS files. Terser is the successor of UglifyJS and is the default option in [webpack](https://webpack.js.org/guides/production/#minification).
- [LightningCSS](https://lightningcss.dev/) for CSS files. 
- [html-minifier-terser](https://github.com/terser/html-minifier-terser) for HTML files. This is a fork of [html-minifier](https://github.com/kangax/html-minifier) and also maintained by the Terser people.

Just for fun, I decided to pull in the top 500 websites by Alexa rank and run them through these tools to see what potential savings there could be. 

Using a list of the most popular websites out there, I fired up a Python script<sup><a href="#1">1</a></sup> to download the HTML for each homepage<sup>2</sup>. 

It then parsed the HTML to look for any stylesheets and scripts and downloaded these too. After running these files through through `html-minifier`, `lightningcss` and `terser` respectively, gzipped sizes were compared and written to a CSV for later analysis.

Only safe minification techniques were used, so more aggressive techniques that could affect functionality were omitted.

What follows is a summary of the results: 

<div style="overflow-x: scroll;">

|       |   html_savings |   css_savings |   js_savings |   combined |
|:------|---------------:|--------------:|-------------:|-------------------:|
| count |         500    |       500     |       500    |             500    |
| mean  |        1745.33 |       689.042 |      6731.67 |            9166.05 |
| std   |        4131.57 |      3376.42  |     13374.8  |           14939.5  |
| min   |         -17    |     -3701     |      -822    |               0    |
| 25%   |         202.75 |         0     |       128.75 |            1099.5  |
| 50%   |         684.5  |         0     |      1838.5  |            3799.5  |
| 75%   |        1799.25 |       635.75  |      7969.75 |           11336.8  |
| max   |       58872    |     68793     |    144248    |          150255    |

</div>

On average, about 9.2 kB worth of data could be saved by using these minification tools instead of whatever these websites are using now.

Compared to what certain page builders are outputting nowadays, this is actually really good!

But then the websites using these page builders are not visited anywhere close to 87 billion times per month ([google.com](https://google.com), #1 on the list) or 187 million times per month ([washingtonpost.com](https://washingtonpost.com), #500). 

Anything multiplied by such gigantic numbers will amount to a lot. To better understand just how much data this might amount to in total, we have to look at cache lifetimes too.

### Cache lifetimes

While downloading the asset files, I inspected the HTTP headers for cache directives. The average time (in seconds) was taken across all of the assets that had either a `Cache-Control` or an `Expires` header, or `0` if the response included no such header.

|       |     expires (s) |   expires (h)   |
|:------|----------------:|----------------:|
| count |   500           |          500    |
| mean  |     2.32891e+07 |         6469.19 |
| std   |     7.01929e+07 |        19498    |
| min   |     0           |            0    |
| 10%   |   296.4         |            0    |
| 25%   |  3342.25        |            1    |
| 50%   | 86400           |           24    |
| 75%   |     8.424e+06   |         2340    |
| 90%   |     3.1536e+07  |         8760    |
| max   |     3.65e+08    |       101389    |

The median cache lifetime encountered was 24 hours. 

25% of websites cached their assets for only an hour and 10% either did not set any cache directive or explicitly asked the browser to re-download the asset file.

### Energy use of data transfer

First, I think the above is quite good already. Even taking into account that the results might be underestimating things because it only looks at assets defined in the static HTML.

It shows that these popular websites are pretty much all applying best practices we've known for years: 

- Less than 4 requests out of several thousands did not have gzip enabled for their responses<sup>3</sup>.
- On average, only a few kilobytes worth of data could be saved by using better minification tools.
- Over 50% of requests had a cache directive of at least 1 day.

If I had to pick one thing that I would wish to see improved, it is the latter. Certainly a lot more of these assets could be cached for more than just a single day and then invalidated when needed using some sort of cache busting?

But, just to get some appreciation for the gigantic scale these websites are operating on, let's run some quick math on how much energy could be saved if all these websites applied the minification optimization described here.

As described, the median amount of data saved was only 4 kB. The median cache lifetime was 1 day. 

Assuming 1M unique visitors per day and a cache that is functioning optimally, this amounts to a total of 4 GB of data per day or 120 GB per month. Per website.

A few years ago I wrote about [CO2 emissions on the web](@/blog/2020/2020-02-04-website-carbon-emissions.md) where I went with an estimate of 0.5 kWh per GB of data transfered. Since then I've seen a lot of discussion about the energy use of data transfer, with estimates still varying wildly.

The team behind [WebsiteCarbon.com estimate it](https://sustainablewebdesign.org/calculating-digital-emissions/) at about 0.8 kWh per GB while [other research](https://www.researchgate.net/figure/Trends-for-ICT-electric-power-overall-2030_fig5_342643762) estimates it closer to 0.1 kWh per GB for 2020. 

Whatever the actual number is, the good news is that data transmission still seems to be getting more efficient by the year.

If we go with the lower estimate of 0.1 kWh per GB, the average top-500 website could save about 12 kWh of energy each month by applying better minification. 

And that's just for minifying some code. Now imagine if websites actually started shipping less bullshit?


---

<small id="1"><sup>1</sup> You can [find the code and results for this experiment here](/foobart).</small>

<small><sup>2</sup> This approach ignores any dynamically inserted assets, because only assets linked from the static HTML are downloaded and evaluated. </small>

<small><sup>3</sup> gzip is probably the real hero of this story. It's mind boggling to think of how much data is saved because of this compression algorithm.</small>
