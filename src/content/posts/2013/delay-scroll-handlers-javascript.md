---
title: "Debounce scroll and resize handlers in JavaScript"
description: "Why and how to debounce scroll and resize event handlers in JavaScript for better performance."
date: "2013-11-14T00:00:00+00:00"
modified: "2026-05-14T16:47:42+02:00"
categories: ["Web Performance"]
---

Scroll and resize handlers can have a large impact on page performance. These events can fire many times per second, so any expensive work inside the handler can quickly make scrolling feel slow or unresponsive.

I ran into this while working on a WordPress plugin that needed to detect when a visitor had scrolled far enough down the page. The check itself was simple, but running it on every scroll event was not the right approach.

```javascript
$(window).scroll(function() {
    console.log( "Firing!" );
});
```

The problem is that the browser may trigger this callback continuously while the user scrolls. Even a small amount of work can add up when it runs dozens of times in a short period. The same applies to other high-frequency events, including `resize`, `mousemove`, and `keypress`.

This is not only a theoretical issue. Twitter ran into similar performance problems with infinite scroll years ago, as described in John Resig’s article [Learning from Twitter](https://johnresig.com/blog/learning-from-twitter/). Their handler did too much work on every scroll event, which made the interface slower than it needed to be.

## Debounce the handler

A common solution is to debounce the handler. Instead of running the callback for every event, you wait until the event has stopped firing for a short period. This keeps the page responsive and reduces the number of times your real callback runs.

```javascript
var timer;

$(window).scroll(function() {
    if (timer) {
        window.clearTimeout(timer);
    }

    timer = window.setTimeout(function() {
        // Run the actual callback here.
        console.log( "Firing!" );
    }, 100);
});
```

In this example, the callback only runs after scrolling has paused for 100 milliseconds. If another scroll event fires before that delay has passed, the pending timeout is cleared and a new one is created.

This pattern works well when you only need the final state, such as checking where the visitor stopped scrolling or recalculating layout after the window has been resized.

## Cache values that do not change

You can usually improve performance further by moving stable values outside the handler. For example, if the window height and trigger point do not change during scrolling, calculate them once and reuse them.

```javascript
var timer;
var windowHeight = $(window).height();
var triggerHeight = 0.5 * windowHeight;

$(window).scroll(function() {
    if (timer) {
        window.clearTimeout(timer);
    }

    timer = window.setTimeout(function() {
        // This value changes while scrolling, so calculate it inside the callback.
        var y = $(window).scrollTop() + windowHeight;

        if (y > triggerHeight) {
            // Run your logic here.
        }
    }, 100);
});
```

Be careful not to cache values that can change. If your layout changes after images load, after fonts render, or when the viewport is resized, recalculate those values at the right time.

## Throttle when you need regular updates

Debouncing is useful when you want to run code after the event settles. If you need regular updates while the user is scrolling, use throttling instead. A throttled handler runs at a controlled interval, such as once every 100 milliseconds, instead of waiting until scrolling stops.

For many simple WordPress and jQuery use cases, a small debounce function is enough and avoids adding another dependency. The main goal is to keep expensive work out of the raw scroll or resize event.
