---
title: "AltoRouter, another PHP Routing class."
description: "Introducing AltoRouter, a lightweight PHP5.3+ routing class with named routes and URL type restrictions."
date: "2012-07-31T00:00:00+00:00"
modified: "2026-05-14T16:01:38+02:00"
categories: ["Programming"]
---

A few months earlier I blogged about developing [PHP Router](/blog/2011/php-routing-class-with-rest-routes/), a PHP5.3+ routing class with support for REST routing and reversed routing.

Well, last week I came across [klein.php](https://github.com/chriso/klein.php/), a lightweight PHP router developed by Chris O’Hara. I particularly liked the fact that it’s pretty easy to specify certain type restrictions for your URL parameters so I decided to create my own version of klein.php. The aim was to create something that worked in a similar way as my own PHP Router class, but with the ease of klein.php.

So, here it is: [AltoRouter](https://github.com/dannyvankooten/AltoRouter), a tiny routing class which will help you in your future PHP projects.

-   Usage of different HTTP Methods
-   Dynamic routing, which lets you capture certain URL segments as parameters
-   REST Routing
-   Reversed routing (generating URLs from route names with parameter values)
-   Wildcards
-   Parameter types
-   Custom regexes, if the built-in regular expressions aren’t suited for your project

AltoRouter is hosted on GitHub and also available as a [Composer package from Packagist](https://packagist.org/packages/altorouter/altorouter).

Feel free to take a look at the code or contribute to the routing class.
