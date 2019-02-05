---
layout: post
title: "From Go to PHP again"
date: '2019-02-04'
tags:
- symfony
- php
---

Remember when [we ditched Laravel for Golang](/laravel-to-golang/)?

Well, after 2 years on Go, our shop applications are powered by PHP again.

**Why?! You already said it was probably a bad business decision, and then you spend even more time on it?!** Well, yeah, several reasons actually.

<div class="well italic">
Side note: without the experience gained from our years on Go, I probably wouldn't have started <a href="https://usefathom.com/">Fathom Analytics</a>. So perhaps it wasn't such a bad business decision after all?
</div>

### PHP improved a lot

PHP improved a lot during the last 3 years. It added [scalar argument type declarations](https://secure.php.net/manual/en/functions.arguments.php#functions.arguments.type-declaration), [return type declarations](https://secure.php.net/manual/en/functions.returning-values.php#functions.returning-values.type-declaration), [multi-catch exceptions](https://wiki.php.net/rfc/multiple-catch), impressive [performance improvements](http://www.zend.com/en/resources/php7_infographic) and many more general improvements. 

### Symfony4 is a game changer

I've always been a big fan of [Symfony's compatibility promise](https://symfony.com/doc/current/contributing/code/bc.html) and they have the track record to prove they mean it.  

So when [Symfony4](https://symfony.com/4) was released a few months ago and I heard good things about it, I had to take it for a test drive by implementing a tiny part of our application in it.

Conclusion: it's great.

A lot of effort went into simplifying the setup, making it a lot faster to bootstrap a Symfony application with much less configuration. It's now rivaling Laravel's rapid development while at the same time encouraging best practices to ensure you don't shoot yourself in the foot. And [it performs really well](http://www.phpbenchmarks.com/en/).

It was relatively easy to port our old Laravel application to Symfony, implement some new features the Go version of our application offered and undo some of the shortcuts we took earlier (most of them because of Laravel's global helpers). 

A nice side effect is that we've managed to substantially increase our test coverage in the process.

![Symfony's debug bar](/media/2019/symfony-debug-bar.png)

Symfony's debug bar is an amazing tool. It shows you what happened during the journey from request to response, notifies you of warnings & deprecations and comes with a built-in profiler that you can easily hook into to benchmark parts of your own code.

![Symfony's profiler](/media/2019/symfony-profiler.jpg)

After learning [Symfony's Form component](https://symfony.com/doc/current/forms.html), we'd rather not go without it again. It makes it trivial to render an accessible form that can be re-used in several places, validating the form upon submit and then populating a PHP object from the form data safely.

```php?start_inline=1
$user = $this->getUser();
$form = $this->createForm(UserBillingInfoType::class, $user)
             ->handleRequest($request);

if ($form->isSubmitted() && $form->isValid()) {
    // $user is already populated with the form values at this point
    // we now it's valid, so we can update the database and be done with it
}
```

Doctrine is another piece of software that really improved our overall application. Your models ([entities](https://www.doctrine-project.org/projects/doctrine-orm/en/latest/reference/basic-mapping.html#basic-mapping)) are normal PHP classes and relations ([associations](https://www.doctrine-project.org/projects/doctrine-orm/en/latest/reference/working-with-associations.html#working-with-associations)) are normal references, making it easy to test your domain logic without having to worry about the database implementation.

```php?start_inline=1
$user = new User();
$user->addLicense(new License());
$manager->persist($user); // both user and its license will be saved
```

In Doctrine all operations are wrapped in a SQL transaction by default. That's a big plus for us as it guarantees atomicity, something that involved more work in Eloquent.

### Go is (still) great

Honestly, Go is great and its simplicity is refreshing. We would still pick it any day if we need a small API or backend for a single page application.

Our shops however are more monolithic, with a lot of server-side rendering going on. While that's certainly doable in Go (as the last 2 years proved), it's more maintainable for us to do it in PHP right now. 

### Preparing for a possible sale

One reason that I have not mentioned so far is that we've been approached by several companies interested to take over (part) of our business. 

They were a little surprised to hear our stack involved Golang and some flat out told us they'd prefer PHP, because that's what our actual products ([mc4wp.com](https://mc4wp.com/) & [boxzillaplugin.com](https://boxzillaplugin.com)) rely upon. And they're probably right.



