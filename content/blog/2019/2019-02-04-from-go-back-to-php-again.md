+++
title = "Moving from Go to PHP again"
date = 2019-02-04
+++

Remember when [I ditched Laravel for Golang](@/blog/2017/2017-04-17-laravel-to-golang.md)?

Well, after 2 years on Go, our shop applications are powered by PHP again.

**Why?! You already said it was probably a bad business decision, and then you spend even more time on it?!** Well, yeah, several reasons actually.

### PHP improved a lot

PHP improved a lot during the last 3 years. It added [scalar argument type declarations](https://secure.php.net/manual/en/functions.arguments.php#functions.arguments.type-declaration), [return type declarations](https://secure.php.net/manual/en/functions.returning-values.php#functions.returning-values.type-declaration), [multi-catch exceptions](https://wiki.php.net/rfc/multiple-catch), impressive [performance improvements](https://www.zend.com/en/resources/php7_infographic) and many more general improvements. 

### Symfony4 is a game changer

I've always been a big fan of [Symfony's compatibility promise](https://symfony.com/doc/current/contributing/code/bc.html) and their impressive 13-year track record proves they mean it.  

So when [Symfony4](https://symfony.com/4) was released and I heard good things about it, I took it for a test drive by implementing a tiny part of our application in it.

Conclusion: it's great. Really, really great.

A lot of effort went into simplifying the setup, making it a lot faster to bootstrap a Symfony application with much less work required configuring bundles. It's now rivaling Laravel's rapid development while at the same time encouraging decent development practices to ensure you don't shoot yourself in the foot. And [it performs really well](http://www.phpbenchmarks.com/en/).

It was relatively easy to port our old Laravel application to Symfony, implement some new features the Go version of our application offered and undo some of the shortcuts I took earlier (most of them because of Laravel's global helpers). 

A nice side effect is that I've managed to substantially increase our test coverage in the process. Writing the same application in terms of functionality for a ~~second~~ third time really helps in that regard.

![Symfony's debug bar](/media/2019/symfony-debug-bar.png)

Symfony's debug bar is an amazing tool. It shows you what happened during the journey from request to response, notifies you of warnings & deprecations and comes with a built-in profiler that you can easily hook into to benchmark parts of your own code.

![Symfony's profiler](/media/2019/symfony-profiler.jpg)

After learning [Symfony's Form component](https://symfony.com/doc/current/forms.html), I'd rather not go without it again. It makes it trivial to render an accessible form that can be re-used in several places, validating the form upon submit and then populating a PHP object from the form data safely.

```php?start_inline=1
$user = $this->getUser();
$form = $this->createForm(UserBillingInfoType::class, $user)
             ->handleRequest($request);

if ($form->isSubmitted() && $form->isValid()) {
    // $user is already populated with the form values at this point
    // it's valid, so we can update the database and redirect the user now
}
```

Doctrine is another piece of software that really improved our overall application. Your models ([entities](https://www.doctrine-project.org/projects/doctrine-orm/en/latest/reference/basic-mapping.html#basic-mapping)) are normal PHP classes and relations ([associations](https://www.doctrine-project.org/projects/doctrine-orm/en/latest/reference/working-with-associations.html#working-with-associations)) are normal references, making it easy to test your domain logic without having to worry about the database implementation.

```php?start_inline=1
$user = new User();
$user->addLicense(new License());
$manager->persist($user); // both user and its license will be saved
```

In Doctrine all operations are wrapped in a SQL transaction by default. That's a big plus for me as it guarantees atomicity, which involved more work to get right in Eloquent.

### Go is (still) great

Honestly, Go is great. Its simplicity is refreshing and you can't get anywhere near that kind of performance using PHP <sup class="muted">1</sup>. I would still pick it if we need a small API or something that requires high throughput.

Our shops however are more monolithic with a lot of server-side rendering. While that's certainly doable in Go (as the last 2 years proved), it's more maintainable for us to do it in PHP right now. 

> Side note: without the experience gained from our years on Go, I probably wouldn't have started [Fathom](@/blog/2018/2018-05-12-reviving-ana-as-fathom.md). So perhaps it wasn't such a bad business decision after all?

### Making the correct business decision

One reason not mentioned so far is that over the last year or so, I've been approached by several companies interested to take over one of our products. 

They were a little surprised to hear our stack involved Golang and some flat out told us they'd prefer PHP, because that's what most of our products ([mc4wp.com](https://www.mc4wp.com/), [boxzillaplugin.com](https://boxzillaplugin.com) and [htmlformsplugin.com](https://www.htmlformsplugin.com/)) rely upon. And I don't blame them.


<hr />
<p><small><sup>1</sup> Just for fun, I compared apples and oranges again by benchmarking the login page (which doesn't hit any database) for both application versions using <a href="https://www.joedog.org/siege-home/">Siege</a>.</small></p>

<p><small>The Symfony application (PHP 7.3, OPcache enabled, optimized autoloader) handles about 1470 req/s. The Go application (compiled using Go v1.11) averages about 18600 req/s.</small></p>


