---
layout: post
title: Moving from PHP (Laravel) to Go
date: '2017-04-17 17:57:00'
tags:
- go
- laravel
---

Earlier this year, I made an arguably bad business decision. I decided to rewrite the Laravel application powering [Boxzilla](https://platform.boxzillaplugin.com/) in [Go](https://golang.org/). 

No regrets though. 

<img style="height: 400px; width: auto; margin-left: 40px;" class="pull-right small-margin" src="/media/2017-04-boxzilla-platform.jpg">

Just a few weeks later I was deploying the Go application. Building it was the most fun I had in months, I learned a ton and the end result is a huge improvement over the old application. Better performance, easier deployments and higher test coverage. 

The application is a fairly straightforward database driven API & account area where users can log-in to download the product, view their invoices or update their payment method.

[Stripe](https://stripe.com/) and [Braintree](https://www.braintreepayments.com/) are used to accept subscription payments. Invoices are handled using [MoneyBird](https://www.moneybird.com/) and some transactional emails are sent using [Mailgun](https://www.mailgun.com/).

While Laravel worked well enough for this, some things always felt overcomplicated to me. And what's with releasing a new "major" version every few months? I'd be fine if the newer versions contained significant improvements, but a lot of times it just felt like minor naming & directory structure changes to me. 

## Why Go?

Last year I've been moving several services over to Go, so I wasn't completely new to the language. As a developer selling WordPress based products, part of my job is working in an ancient tech stack that is mostly focused on the end user.

If I were an employee, I'd simply apply for a new job to make up for this lack of sexy tech. Being the boss, I owe it to myself to make my day-to-day work fun and not just chase more immediate $$$. If revenue allows (and it does), why not have a little fun?

It's a joy to write Go code, the tooling is amazing and it's not only fast to develop in, the end result is usually crazy fast too. Just reading about [the purpose of the Go project](should be enough to convince most) sold me on the language. 

I think we'll see a crazy amount of people switching from dynamically typed languages like PHP, Python and JavaScript to Go in the next few years.

## Porting the codebase

Migrating the code to Go consisted mostly about getting the database interaction right & porting the Blade templates to something we could use in Go. 

ORM's are one thing that always end up getting in my way, so I went for a mockable data access layer and plain SQL queries. [Meddler](https://github.com/russross/meddler) was used to get rid of some of the boilerplate for scanning query results into structs.

To support hierarchical templates and partials I open-sourced [grender](https://github.com/dannyvankooten/grender), a tiny wrapper around Go's standard html/template package. This allowed me to port the Blade template files to Go with relative ease, since I could use the same hierarchical structure and partial templates.

For integrating with Stripe there is the official [stripe-go](https://github.com/stripe/stripe-go) package. For Braintree there is the unofficial [braintree-go](https://github.com/lionelbarrow/braintree-go) package, which was neglected for a little while but received some renewed attention lately. Since there was no Go package to manage invoices in Moneybird yet, I built and open-sourced [moneybird-go](https://github.com/dannyvankooten/moneybird-go).

## Comparing apples vs oranges

Since Go is a compiled language with a much better standard library than PHP, it is not really fair to compare the two languages like I am about to. That said, I thought it would be fun to share some numbers.

#### Performance

[wrk](https://github.com/wg/wrk) was used to perform some simple HTTP benchmarks for both applications returning the HTML for the login page.

| | Concurrency | Avg. latency  | Req / sec   | Transfer / sec  |
|---|---|---|---|---|
| Laravel | 1  | 120.88ms | 59.81 | 403.47KB |
| Laravel | 100 | 385.88ms | 264.57 | 1.74MB |
| Go | 1 | 325.72us | 7365.48 | 34.27MB |
| Go | 100 | 11.63ms | 19967.31 | 92.91MB |
| Go | 200 | 37.68ms | 22653.22 | 105.41MB | 

Unfortunately, the Laravel application kept falling over once I increased the number of concurrent "users" past 100.

[NetData](https://my-netdata.io/) provided the following graphs to see how the server was holding up under all this load. 

**Go with 100 concurrent connections**
![Go with 100 concurrent connections](/media/2017-benchmark-go-c100.jpg)

**Laravel with 100 concurrent connections**
![Laravel with 100 concurrent connections](/media/2017-benchmark-laravel-c100.jpg)

Please note that I ran the benchmark from the same machine as the applications were running on, so this heavily influences both graphs. 

#### Lines of code

Let's compare the lines of code in both applications, including all vendored dependencies.

```
find . -name '*.php' | xargs wc -l
```

The Laravel version consists of just over 450.000 lines of code. This is excluding any development dependencies which are needed to run tests etc.

```
find . -name '*.go' | xargs wc -l
```

The Go version on the other hand consists of 154.000 lines of code. That's one third of the code for exactly the same functionality.

#### Test coverage

Testing is a first class citizen in Go, and test files live right next to the actual source files. 

```
license.go
license_test.go
subscription.go
subscription_test.go
```

This makes it incredibly convenient to apply test driven development. 

In our Laravel application we mostly had integration tests that checked whether the request handlers returned a proper response. Overall test coverage was quite low, mostly due to tight coupling which in turn was mostly my fault. Writing the same application a second time really helps here too.

## TLDR

Did something you should never do: rewrote an application in a different language because I felt like it. Had lots of fun and got a much smaller & faster application in return. 



