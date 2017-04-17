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

I love Go. 

It's a joy to write Go code, the tooling is amazing and it's not only fast to develop in, the end result is usually crazy fast too. Just reading about [the purpose of the Go project](should be enough to convince most) sold me on the language. 

I think we'll see a crazy amount of people switching from dynamically typed languages like PHP, Python and JavaScript to Go in the next few years.

## Porting the codebase

Migrating the code to Go consisted mostly about getting the database interaction right & porting the Blade templates to something we could use in Go. 

ORM's are one thing that always end up getting in my way, so I went for a mockable data access layer and plain SQL this time. [Meddler](https://github.com/russross/meddler) was used to get rid of some of the boilerplate for scanning results into structs.

To support hierarchical templates with partials I open-sourced [grender](https://github.com/dannyvankooten/grender), a tiny wrapper around Go's standard html/template package. This allowed me to port the Blade template files to Go with relative ease, since I could use the same hierarchical structure and partials.

For integrating with Stripe there is the official [stripe-go](https://github.com/stripe/stripe-go) package. For Braintree there is the unofficial [braintree-go](https://github.com/lionelbarrow/braintree-go) package which was neglected for a little while but has received a lot of attention lately. Since there was no Go package to create and send invoices in Moneybird yet, I built and open-sourced [moneybird-go](https://github.com/dannyvankooten/moneybird-go).

## Comparing apples vs oranges

Since Go is a compiled language with a much better standard library than PHP, it is not really fair to compare the two languages like I am about to. That said, I thought it would be fun to share some numbers.

#### Performance

Some simple HTTP benchmarking was done using [wrk](https://github.com/wg/wrk), by pointing a request to the login page (using wrk's defaults).

```
wrk boxzilla-laravel.dev
```
The Laravel application was able to handle just over 250 requests per second.

```
wrk boxzilla-go.dev
```

Whoa. Close to 13.000 requests per second for the Go application.

#### Lines of code

```
find . -name '*.php' | xargs wc -l
```

The Laravel version consists of just over 450.000 lines of code.

```
find . -name '*.go' | xargs wc -l
```

The Go version on the other hand, consists of "just" 154.000 lines of code. 
