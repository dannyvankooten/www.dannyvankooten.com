+++
title = "HTML compression on popular websites"
+++

> **TLDR:** I grabbed a list of the 10.000 most popular domains on the internet, downloaded their homepage and checked for compression techniques. Surprisingly, quite a few of them (~8%) are not applying any kind of compression at all with some of them leaving terabytes of potential monthly data savings on the table ($$$ + a slower website).
>
> Some notable entries that caught my eye are the websites of the US Department of State, multiple country specific branches of Lidl, the Python programming language, Klarna and Zapier.

At its core, a website is just a collection of text, image and video files.

The text files include the actual content of the site (HTML), how this content should be displayed (CSS) and any dynamic code that should run in your browser (JavaScript).

All of these text files are highly suitable for lossless compression, which replaces repeated text patterns with a pointer to the previous occurrence of the pattern. As a result, larger text files result in higher compression ratios.
Compression ratios of well over 80% are not uncommon, especially for files well over 100 kB.

All things considered, compression is a net win for everyone involved:

- As a website owner, your bill for outgoing network traffic is lower.
- As a website visitor, you're downloading fewer data so the website loads faster.

Gzip and Brotli are the 2 most commonly used compression algorithms used on the web right now.
Both strike a nice balance between compression rates and performance and most web server software (like Nginx or Caddy) has support for these built-in.

You would think any website with millions of unique per visitors per month would be using compression, right? Let's find out!

## 10.000 HTTP requests later

I grabbed a list of the top 10.000 domains on the internet (according to their Alexa rank) and then made an HTTP request to each of them, discarding any error responses or requests that failed to return a response within 10 seconds.

The `User-Agent` HTTP header was set to match that of my browser. `Accept-Encoding` was set to `br, gzip, deflate`, again matching that of my browser.

To get the amount of bytes that could be saved by applying compression, I ran the response body through Golang's [compress/gzip](https://pkg.go.dev/compress/gzip) using the default compression level.

<style>
.bar-chart > div {
    display: inline-block;
    box-sizing: border-box;
    text-align: center;
    color: white;
}
.legend {
    margin-top: 1em;
    list-style: none;
    padding: 0;
}
.legend span {
    display: inline-block;
    width: 40px;
    height: 20px;
    vertical-align: middle;
}
</style>

---

#### Successful requests

Of all 10000 HTTP requests, just under 7900 managed to return a successful HTML response in under 10 seconds.

<div class="bar-chart">
    <div style="width: 78%; background: #3d834e;">7885</div><div style="background:#da4c95; width: 22%">2015</div>
</div>

<ul class="legend">
    <li><span style="background:#3d834e;"></span> HTTP 200 OK</li>
    <li><span style="background:#da4c95;"></span> Non-OK response / request timed out</li>
</ul>

---

#### Compression

Of these 7900 HTML responses, about 8% did not apply any compression.

<div class="bar-chart">
    <div style="width: 63.8%; background: #3d834e;">5028</div><div style="background:#da4c95; width: 27.7%">2190</div><div style="background:#536DFE; width: 8.4%">663</div><div style="background:#FF1744; width: 0.1%">4</div>
</div>

<ul class="legend">
    <li><span style="background:#3d834e;"></span> Gzip</li>
    <li><span style="background:#da4c95;"></span> Brotli</li>
    <li><span style="background:#536DFE;"></span> None</li>
    <li><span style="background:#FF1744;"></span> Deflate</li>
</ul>

On average, about 55 kB of data transfer could be saved by enabling compression on these HTML responses.

---

## Notable sites not using compression

[Lidl.cz](https://www.lidl.cz/), the Czech branch of a popular supermarket chain here in Europe, topped the list by shipping 1.65 MB of uncompressed HTML.
Applying gzip compression at compression level 2 would reduce this to just under 200 kB, a 90% reduction.

Another interesting site that caught my eye was [Python.org](https://python.org/).
Apparently the [sample nginx configuration provided by Heroku](https://github.com/heroku/heroku-buildpack-nginx/pull/86/commits/458bc2e997825abd802ff49d5c9f0b4e01c55815)
was missing the `gzip_proxied any` setting to allow applying compression to proxied responses.

This was addressed back in 2021, but not merged upstream in the Python website codebase. Hopefully once [my PR](https://github.com/python/pythondotorg/pull/2334) gets merged, it will be!

Other popular websites that I was surprised to see were [klarna.com](https://www.klarna.com), [zapier.com](https://zapier.com/) and [state.gov](https://www.state.gov/).

You can browse the [full list of websites not applying any compression here](/2023/sites-without-compression.html).

### Running the math on data transfer that could be avoided

Take [tmz.com](https://www.tmz.com/) as an example, #2 on the list:

- They could save 665 kB on their homepage's HTML by applying compression.
- [similarweb estimates their monthly traffic at about 60M](https://www.similarweb.com/website/tmz.com/).
- The cache lifetime on their HTML responses is set to only 60 seconds, so a substantial amount of traffic will be downloading the HTML over and over again.

Multiplying 665 kilobytes by 10 million already gets you to about **6.14 terabytes of unnecessary data transfer**.

That's 6.14 terabytes of data transmission that could not exist by adding a single line to a configuration file somewhere:

```
gzip on
```

If you happen to know anyone working at [these websites](/2023/sites-without-compression.html), please inform them of this opportunity. There's no good reason to not be using compression so this is likely due to an oversight or misconfiguration on their part.

---

The code for this experiment is up on GitHub here: [dannyvankooten/top-websites-compression](https://github.com/dannyvankooten/top-websites-compression).
