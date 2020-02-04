---
layout: post
title: 'Carbon emissions on the web'
date: '2020-02-04'
tags:
- carbon
- web development
---

I've spent the last month trying to reduce the carbon footprint of the websites I have (some) control over. When talking about this with other people they often look at me blankly before asking "aren't you taking this a little too far?".

The simple answer is no. In fact, it is probably the most effective use of my time when it comes to reducing carbon emissions by a long shot. 

Just last week I reduced global carbon emissions by about 340.000 kg per month by removing a 20 kB JavaScript dependency in [Mailchimp for WordPress](https://www.mc4wp.com/). There's no way I can have that kind of an effect in other areas of my life.

### Carbon emissions from distributed code

All of [my WordPress plugins](/wordpress-plugins/) combined run on well over 2 million different websites, each website receiving who knows how many visitors. 

At an energy expenditure of [2,9 kWh per GB](https://www.researchgate.net/publication/326470455_Evaluating_the_Energy_Consumption_of_Mobile_Data_Transfer-From_Technology_Development_to_Consumer_Behaviour_and_Life_Cycle_Thinking) <sup>1</sup> this means that every kB saved equals an energy reduction of about `2,9 kWh / 1.000.000 kB * 2.000.000 websites = 5,8 kWh` if each of these websites received exactly 1 visitor.

Let's assume the average website receives about 10.000 unique visitors per month and serves static assets from cache for returning visitors. The total amount of energy saved by shaving off a single kilobyte is then `5,8 kWh * 10.000 visitors = 58.000 kWh`.

58.000 kWh of energy produced by the [current European electricity grid](https://www.eea.europa.eu/data-and-maps/indicators/overview-of-the-electricity-production-2/assessment-4) equals about `58.000 * 0,295 = 17.110 kg of CO2`.

> **Shaving off a single kilobyte in a plugin running on 2 million websites reduces CO<sub>2</sub> emissions by 17.110 kg per month!**

To put this into perspective, that is the same amount of CO<sub>2</sub> as:

- Driving my Toyota Yaris for 114.000 kilometers. ([158 g CO<sub>2</sub> per km](https://car-emissions.com/cars/index/toyota%20yaris%201.3%20vvt-i%20tr/))
- 25 flights from Amsterdam to New York. ([679 kg CO<sub>2</sub> per flight](https://www.costtotravel.com/flight/from-new-york-to-amsterdam))
- Eating 684 kg of beef ([25 kg CO<sub>2</sub> per kg of beef](https://eprints.lancs.ac.uk/79432/4/1_s2.0_S0959652616303584_main.pdf))

I do not see myself reducing my personal beef intake with 684 kg per month, so instead I try to make my website and distributed plugins as efficient as possible. 

### What can we do?

According to [httparchive.org](https://httparchive.org/reports/page-weight?start=earliest&end=latest), the average website on desktop is about 4 times as large as in 2010. On mobile the numbers look even worse, from 200 kB up to a whopping 1,9 MB!

As web developers we have a responsibility to stop this madness. Did websites really get 4 times as good? Is this [motherfuckingwebsite.com](https://motherfuckingwebsite.com/) clocking in at 5 kB total really that bad in comparison? I don't think so.

Whenever you are adding to a website, ask yourself: is this necessary? If not, consider leaving it out. 

Your content site probably [doesn't need JavaScript](https://github.com/you-dont-need/You-Dont-Need-Javascript). You probably [don't need a CSS framework](https://hacks.mozilla.org/2016/04/you-might-not-need-a-css-framework/). You don't need a custom font, especially not one served from Google's servers. Use [responsive images](https://developer.mozilla.org/en-US/docs/Learn/HTML/Multimedia_and_embedding/Responsive_images). Choose a [green web host](https://www.thegreenwebfoundation.org/). Consider ditching that third-party analytics service that you never look at anyway, especially if they also sell ads. Run your website through [websitecarbon.com](https://www.websitecarbon.com/). Extend your [HTTP cache](https://developers.google.com/web/fundamentals/performance/optimizing-content-efficiency/http-caching) lifetimes. Use a [static site generator](https://www.staticgen.com/) or [wp2static.com](https://wp2static.com/) instead of dynamically generating each page on the fly, despite never changing.

I'm sorry if that turned into a bit of a rambling, but I hope you see where I am going with this. Personally I really enjoyed forcing myself not to use more than 1 kB of CSS for the website you are reading this on. It [sparked creativity](https://www.inc.com/thomas-oppong/for-a-more-creative-brain-embrace-constraints.html).

Let's do our share as web developers and stop bloating the web. 


<small>
<sup>1</sup> Energy expenditure numbers vary a lot depending on the network you are using. "Using data volumes from the year 2010 Malmodin and colleagues estimated electricity consumption per data volume as follows: 0.08 kWh/gigabyte for averaged fixed broadband access
network, compared to 2.9 kWh/gigabyte for average 3G mobile broadband access network and 37 kWh/gigabyte for average 2G mobile communication." [doi:10.3390/su10072494](https://cris.vtt.fi/en/publications/evaluating-the-energy-consumption-of-mobile-data-transfer-from-te)
</small>