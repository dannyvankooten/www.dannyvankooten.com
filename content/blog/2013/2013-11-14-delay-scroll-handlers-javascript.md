+++

title = "Delay scroll (and resize) handlers in JavaScript"
date = 2013-11-14 16:34:02

+++

While working on my latest [WordPress plugin](@/wordpress-plugins.md) I had to attach a handler to the scroll event of the browser window to determine whether a user scrolled down far enough. While the logic for this is pretty simple, I had to put some thought into handling this the correct way.

Directly attaching a handler to the scroll event is considered bad practice because (depending on the browser) this event can fire <strong>many times within a few milliseconds</strong>. I did some testing and just logging a message in the scroll handler caused my debugger to crash in Internet Explorer. The sames goes for other events like mousemove, keypress and resize.

```javascript
$(window).scroll(function() {
	console.log( "Firing!" );
});
```

Even the developers at <a href="https://johnresig.com/blog/learning-from-twitter/">Twitter had an issue with scroll event handlers</a> in the past. They were using infinite scroll and attached their handler directly to the scroll event using jQuery. On top of that, they were running jQuery selector queries <em>every time</em> the event handler fired (without caching the results). Enough to make the scrolling experience extremely slow and the site  unresponsive.

<h3>The solution: delaying (or polling) your handlers</h3>
There is a simple solution to this problem: just run your event handlers with a slight delay. This will dramatically cut down on the times your actual callback function is fired. There are multiple ways to go about this but I find a simple timeout the simplest as it requires minimal extra code.

```javascript
var timer;

$(window).scroll(function() {
	if(timer) {
		window.clearTimeout(timer);
	}

	timer = window.setTimeout(function() {
		// actual callback
		console.log( "Firing!" );
	}, 100);
});
```

This will set a timeout of 100 ms and only then run the actual callback function. If the scroll event is fired again and the 100 ms have not passed yet, it will clear the pending timeout and set a new one. This increased my script performance by about 100% in IE, as my handler was called 10 times less. The same logic can be applied to other events like <em>resize</em> as well.

<h3>Cache your variables</h3>
Another optimization you can do is to move as many code outside the callback function as possible. For the scroll triggered boxes plugin, I had to calculate the window height and the height at which a box should be triggered. These heights do not change between firing handlers so I moved it outside the callback.

```javascript
var timer;
var windowHeight = $(window).height();
var triggerHeight = 0.5 * windowHeight;

$(window).scroll(function() {
	if(timer) {
		window.clearTimeout(timer);
	}

	timer = window.setTimeout(function() {

		// this variable changes between callbacks, so we can't cache it
		var y = $(window).scrollTop() + windowHeight;
 
	    if(y > triggerHeight) {
	        // ...
	    }

		
	}, 100);
});
```

Hope this saves someone a headache! There are various throttle or debounce plugins available for jQuery that do the same thing but in my opinion you don't really need another dependency for a simple thing as this. As you can see from my code examples, we just added throttling with just 3 or 4 lines of code.
