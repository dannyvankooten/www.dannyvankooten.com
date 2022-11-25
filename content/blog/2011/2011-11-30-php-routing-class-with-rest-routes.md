+++

title = "PHP Routing Class which supports REST routing"
date = 2011-11-30 22:04:10

+++

In the last few months I've been looking into all the different PHP frameworks and how they glue things together. One of the most important things in every framework is the way it handles routes. I've been developing Ruby on Rails applications extensively last year and I really like the way Rails handles it's routes. That's why I've decided to start programming a Routing class that mimics Rails routing functionality.

I've released this PHP Routing class on Github <a href="https://github.com/dannyvankooten/PHP-Router">here</a>, so you can follow and contribute to it's development progress.
<h4>What does this PHP Routing class do?</h4>
In short, this class helps you route URL's to their corresponding Controller and action methods. You can use different HTTP methods to fully benefit from using REST / resourceful routes. Also, setting up dynamic URL's that pass parameters to their corresponding action are no problem.
<ul>
	<li>REST Routing</li>
	<li>Use different HTTP methods for same URL's</li>
	<li>Dynamic URL segments</li>
	<li>Reversed routing, create URL from route</li>
</ul>
The class is fairly simple and lightweight, it won't do any unnecessary processing. Have a look at the following snippet taken from the example file to get a better understanding on how to use this class in your projects.

```php?start_inline=1
$r = new Router();

// maps / to controller "users" and method "index".
$r->match("/","users#index");

// maps /user/5 to controller "users", method "show" with parameter "id" => 5
$r->match("/user/:id","users#show");

// maps POST request to /users/ to controller "users" and method "create"
$r->match("/users","users#create",array("via" => "post"));

// maps GET /users/5/edit to controller "users", method "edit" with parameters "id" => 5 and saves route as a named route.
$r->match("/users/:id/edit","users#edit",array("via" => "get", "as" => "user_edit_page"));

// echoes /users/5/edit
echo $r->reverse("user_edit_page",array("id" => "5"));

// maps multiple routes
// GET /users will map to users#index
// GET /users/5 will map to users#show
$r->resources("users",array("only" => "index,show"));

if($r->hasRoute()) {
    // extract controller, action and parameters.
    extract($r->getRoute());
?>   
<h1>Route found!</h1>
<pre>
    Controller: 	<?php echo $controller; ?>
    Action: 		<?php echo $action; ?>
    Params: 		<?php var_dump($params); ?>
</pre><?php

} else {
    ?><h1>No route found.</h1><?php
}
```

If you're using this class and experience any issues, please post them using GitHub's issue tracker. Also, feel free to geek around and fork the project to improve this class.

<a href="https://github.com/dannyvankooten/PHP-Router">Watch PHP Routing Class on Github!</a>

_**Update (July 2012):** If you like PHP Router you might also like [AltoRouter](@/blog/2012/2012-07-31-altorouter-php-routing-class.md)._