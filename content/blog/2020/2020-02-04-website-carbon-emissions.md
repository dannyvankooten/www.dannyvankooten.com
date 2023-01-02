+++
title = "CO2 emissions on the web"
date = 2020-02-04

[extra]
image = "/media/2020/co2-emissions.jpg"
+++

I've spent the last month trying to reduce the carbon footprint of the websites I have (some) control over. When talking about this with other people they often look at me blankly before asking "aren't you taking this a little too far?".

The simple answer is no. In fact, it is probably the most effective use of my time when it comes to reducing carbon dioxide emissions. 

Just last week I reduced global emissions by an estimated 59.000 kg CO<sub>2</sub> per month by removing a 20 kB JavaScript dependency in [Mailchimp for WordPress](https://www.mc4wp.com/). There's no way I can have that kind of effect in other areas of my life.


### CO2 emissions from distributed code

All of [my WordPress plugins](/wordpress-plugins/) combined run on well over 2 million different websites, each website receiving who knows how many visitors. 

At an average energy expenditure of [0,5 kWh per GB <sup>1</sup>](#f1) of data transfer this means that every kB equals `0,5 kWh / 1.000.000 kB * 2.000.000 websites = 1 kWh` if each of these websites received exactly 1 visitor.

Let's assume the average website receives about 10.000 unique visitors per month and serves files from cache for returning visitors. The total amount of energy saved by shaving off a single kilobyte is then `1 kWh * 10.000 visitors = 10.000 kWh`.

10.000 kWh of energy produced by the [current European electricity grid](https://www.eea.europa.eu/data-and-maps/indicators/overview-of-the-electricity-production-2/assessment-4) equals about `10.000 * 0,295 = 2950 kg of CO2`.

> **Shaving off a single kilobyte in a file that is being loaded on 2 million websites reduces CO<sub>2</sub> emissions by an estimated 2950 kg per month.**

To put this into perspective, that is the same amount of CO<sub>2</sub> saved each month as:

- Driving my Toyota Yaris for 18.670 kilometers. ([158 g CO<sub>2</sub> per km](https://car-emissions.com/cars/index/toyota%20yaris%201.3%20vvt-i%20tr/))
- 5 flights from Amsterdam to New York. ([679 kg CO<sub>2</sub> per flight](https://www.costtotravel.com/flight/from-new-york-to-amsterdam))
- Eating 118 kg of beef ([25 kg CO<sub>2</sub> per kg of beef](https://eprints.lancs.ac.uk/79432/4/1_s2.0_S0959652616303584_main.pdf))

I already work from home, am a vegetarian and didn't take any flights in the last 3 years so it seems I am stuck trying to make the web more efficient.

### What can we do?

According to [httparchive.org](https://httparchive.org/reports/page-weight?start=earliest&end=latest), the average website on desktop is about 4 times as large as in 2010. On mobile, where data transfer is way more expensive in terms of energy usage, the numbers look even worse: from 200 kB up to a whopping 1,9 MB!

As web developers we have a responsibility to stop this madness. Did websites really get 4 times as good? Is this [motherfuckingwebsite.com](https://motherfuckingwebsite.com/) clocking in at 5 kB in total really that bad in comparison? I don't think so.

Whenever you are adding to a website, ask yourself: is this necessary? If not, consider leaving it out. 

Your content site probably [doesn't need JavaScript](https://github.com/you-dont-need/You-Dont-Need-Javascript). You probably [don't need a CSS framework](https://hacks.mozilla.org/2016/04/you-might-not-need-a-css-framework/). You certainly don't need a custom font. Use [responsive images](https://developer.mozilla.org/en-US/docs/Learn/HTML/Multimedia_and_embedding/Responsive_images). Extend your [HTTP cache](https://developers.google.com/web/fundamentals/performance/optimizing-content-efficiency/http-caching) lifetimes. Enable [gzip compression](http://nginx.org/en/docs/http/ngx_http_gzip_module.html#example). Use a [static site generator](https://www.staticgen.com/) or [wp2static.com](https://wp2static.com/) instead of dynamically generating each page on the fly. Consider ditching that third-party analytics service that you never look at anyway, especially if they also happen to sell ads. Run your website through [websitecarbon.com](https://www.websitecarbon.com/). Choose a [green web host](https://www.thegreenwebfoundation.org/).

I'm sorry if that turned into a bit of a rambling, but I hope you see where I am going with this.

Personally I constrained myself to not use more than 1 kB of CSS for the website you are reading this on. And I really liked making that work, it [sparked creativity](https://www.inc.com/thomas-oppong/for-a-more-creative-brain-embrace-constraints.html).

Let's do our share as web developers and stop bloating the web. 

---

<small>
 Energy costs of data transfer varies a lot depending on the type of network that is used. The range seems to be from 0,08 kWh per GB for fixed broadband connections to 37 kWh per GB for 2G networks.</small>

<small>I initially went with a global estimate of 2,9 kWh per GB in this post (the average cost per GB for 3G networks), but later changed it to 0,50 kWh per GB as I believe that is a better estimate for 2020 <sup>[2](#f2)</sup>. It's hard to come up with a good estimate that works globally, but I didn't mean for this post to be about exact numbers anyway.</small>

<small>
The most important thing I attempted to convey is that the choices we make in developing for the web have consequences that really add up at scale.
</small>

---

<small>**References**</small>

<small>
</small>

<small><sup id="f1">1</sup> Pihkola, H., Hongisto, M., Apilo, O., & Lasanen, M. (2018). Evaluating the energy consumption of mobile data transfer-from technology development to consumer behaviour and life cycle thinking. [https://doi.org/10.3390/su10072494](https://doi.org/10.3390/su10072494)
</small>

<small>
	<sup id="f2">2</sup>
	Aslan, Joshua & Mayers, Kieren & Koomey, Jonathan & France, Chris. (2017). Electricity Intensity of Internet Data Transmission: Untangling the Estimates: Electricity Intensity of Data Transmission. Journal of Industrial Ecology. [https://doi.org/10.1111/jiec.12630](https://doi.org/10.1111/jiec.12630) 
</small>
