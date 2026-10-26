---
title: "CO2 emissions on the web"
description: "How optimizing code used on millions of websites can reduce global CO2 emissions by tens of thousands of kilograms per month."
date: "2020-02-04T00:00:00+00:00"
modified: "2026-05-14T20:44:30+02:00"
image: "../../../assets/images/blog/2020/website-carbon-emissions/co2-emissions.jpg"
categories: ["Web Performance"]
---

I’ve spent the last month trying to reduce the carbon footprint of the websites I have some control over. When I talk about this with other people, they often look at me blankly before asking, “aren’t you taking this a little too far?”

The simple answer is no. In fact, it is one of the most effective uses of my time when it comes to reducing carbon dioxide emissions.

<p>Just last week I reduced global emissions by an estimated 59.000 kg CO<sub>2</sub> per month by removing a 20 kB JavaScript dependency in <a href="https://www.mc4wp.com/">Mailchimp for WordPress</a>. I cannot have that kind of effect in other areas of my life.</p>

### CO2 emissions from distributed code

All of [my WordPress plugins](/wordpress-plugins/) combined run on well over 2 million different websites, and each website receives an unknown number of visitors.

<p>At an average energy expenditure of <a href="#f1">0,5 kWh per GB <sup>1</sup></a> of data transfer, every kB equals <code>0,5 kWh / 1.000.000 kB * 2.000.000 websites = 1 kWh</code> if each of these websites received exactly 1 visitor.</p>

Let’s assume the average website receives about 10.000 unique visitors per month and serves files from cache for returning visitors. The total amount of energy saved by shaving off a single kilobyte is then `1 kWh * 10.000 visitors = 10.000 kWh`.

10.000 kWh of energy produced by the [current European electricity grid](https://ourworldindata.org/grapher/carbon-intensity-electricity) equals about `10.000 * 0,295 = 2950 kg of CO2`.

> <p><strong>Shaving off a single kilobyte in a file loaded on 2 million websites reduces CO<sub>2</sub> emissions by an estimated 2950 kg per month.</strong></p>

<p>To put this into perspective, that is roughly the same amount of CO<sub>2</sub> saved each month as:</p>

-   Driving my Toyota Yaris for 18.670 kilometers. (158 g CO<sub>2</sub> per km)
-   5 flights from Amsterdam to New York. (679 kg CO<sub>2</sub> per flight)
-   Eating 118 kg of beef. ([25 kg CO<sub>2</sub> per kg of beef](https://ourworldindata.org/food-choice-vs-eating-local))

I already work from home, eat meat only once or twice per week, and didn’t take any flights in the last 3 years, so it seems I am stuck trying to make the web more efficient.

### What can we do?

According to [httparchive.org](https://httparchive.org/reports/page-weight?start=earliest&end=latest), the average desktop website is about 4 times as large as it was in 2010. On mobile, where data transfer is much more expensive in terms of energy usage, the numbers look even worse: from 200 kB up to 1,9 MB.

As web developers, we have a responsibility to stop this madness. Did websites really get 4 times as good? Is this [motherfuckingwebsite.com](https://motherfuckingwebsite.com/) clocking in at 5 kB in total really that bad in comparison? I don’t think so.

Whenever you add something to a website, ask yourself: is this necessary? If not, consider leaving it out.

Your blog or marketing site probably [doesn’t need a JavaScript framework](https://github.com/you-dont-need/You-Dont-Need-Javascript). You probably [don’t need a CSS framework](https://hacks.mozilla.org/2016/04/you-might-not-need-a-css-framework/). Serve [responsive images](https://developer.mozilla.org/en-US/docs/Learn/HTML/Multimedia_and_embedding/Responsive_images). Extend your [HTTP cache](https://web.dev/articles/http-cache) lifetimes and use cache busting techniques. Enable [compression](https://developer.mozilla.org/en-US/docs/Web/HTTP/Guides/Compression). Consider replacing that heavyweight third-party analytics tool with [Koko Analytics](https://www.kokoanalytics.com/). Choose a [green web host](https://www.thegreenwebfoundation.org/).

That is a long list, but it points in one direction: make websites more efficient and less wasteful.

Personally, I limited myself to 1 kB of CSS for the website you are reading this on. I enjoyed making that work; the constraint [sparked creativity](https://www.inc.com/thomas-oppong/for-a-more-creative-brain-embrace-constraints.html).

Let’s do our share as web developers and stop bloating the web.

---

<p><small>Energy costs of data transfer vary a lot depending on the type of network that is used. The range seems to be from 0,08 kWh per GB for fixed broadband connections to 37 kWh per GB for 2G networks.</small></p>

<p><small>I initially went with a global estimate of 2,9 kWh per GB in this post (the average cost per GB for 3G networks), but later changed it to 0,50 kWh per GB as I believe that is a better estimate for 2020 <sup><a href="#f2">2</a></sup>. It’s hard to come up with a good estimate that works globally, but I didn’t mean for this post to be about exact numbers anyway.</small></p>

<p><small>The most important thing I attempted to convey is that the choices we make in developing for the web have consequences that really add up at scale.</small></p>

---

<p><small><strong>References</strong></small></p>

<p><small><sup id="f1">1</sup> Pihkola, H., Hongisto, M., Apilo, O., &amp; Lasanen, M. (2018). Evaluating the energy consumption of mobile data transfer-from technology development to consumer behaviour and life cycle thinking. <a href="https://doi.org/10.3390/su10072494">https://doi.org/10.3390/su10072494</a></small></p>

<p><small><sup id="f2">2</sup> Aslan, Joshua &amp; Mayers, Kieren &amp; Koomey, Jonathan &amp; France, Chris. (2017). Electricity Intensity of Internet Data Transmission: Untangling the Estimates: Electricity Intensity of Data Transmission. Journal of Industrial Ecology. <a href="https://doi.org/10.1111/jiec.12630">https://doi.org/10.1111/jiec.12630</a></small></p>
