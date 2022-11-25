+++
title = "AltoRouter, another PHP Routing class."
date = 2012-07-31 19:07:20
+++

A few months earlier I blogged about developing [PHP Router](@/blog/2011/2011-11-30-php-routing-class-with-rest-routes.md), a PHP5.3+ routing class with support for REST routing and reversed routing. 

Well, last week I came across <a href="https://github.com/chriso/klein.php/">klein.php</a>, a lightweight PHP router developed by Chris O'Hara. I particularly liked the fact that it's pretty easy to specify certain type restrictions for your URL parameters so I decided to create my own version of klein.php. The aim was to create something that worked in a similar way as my own PHP Router class, but with the easiness of klein.php. 

So, here it is: <a href="https://github.com/dannyvankooten/AltoRouter">AltoRouter</a>, a tiny routing class which will help you in your future PHP projects.

<ul>
<li>Usage of different HTTP Methods</li>
<li>Dynamic routing, which lets you capture certain URL segments as parameters</li>
<li>REST Routing</li>
<li>Reversed routing (generating URLs from route names with parameter values)</li>
<li>Wildcards</li>
<li>Parameter types</li>
<li>Custom regexes, if the built-in regular expressions aren't suited for your project</li>
</ul>

<a href="https://github.com/dannyvankooten/AltoRouter">AltoRouter is hosted on GitHub</a> and also available as a <a href="https://packagist.org/packages/altorouter/altorouter">Composer package from Packagist</a>. Feel free to take a look at the code or contribute to the routing class.

<strong>Update (April 2014):</strong> <a href="https://longren.io/basic-routing-in-php-with-altorouter/">Tyler Longren (a contributor to the project) wrote a great "getting started" post for AltoRouter</a>.

**Update (October 2014):** [AltoRouter](https://altorouter.com/) now has its own site containing detailed usage instructions.

