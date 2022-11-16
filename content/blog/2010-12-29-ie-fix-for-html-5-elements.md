+++
title = "IE fix for HTML 5 elements"
date = 2010-12-29 19:15:53
+++

If you had a look at the source code of my site you might have noticed the use of some new HTML 5 elements. Nothing fancy, just trying to add some more semantic value to the different parts of a WordPress theme. The sidebar for example is no longer wrapped in a div element but is now wrapped in an `<aside>` element.

HTML 5 is far from finished (ishtml5readyyet.com, make sure to check the source too) and thus not all major browsers support the new element tags. Internet Explorer for example has problems seeing and thus styling the elements, resulting in a messed up layout. Luckily, there's an easy fix.

## IE fix for HTML 5 elements
Because IE 8 does not "understand" these new element tags, it can not style the elements. Resolving this issue is easy, just create the elements using JavaScript. When you do this before the page is rendered (in your site's `<head>`), IE will have no problem styling these HTML 5 elements.

You can just use the usual JavaScript function `createElement()` to create the elements, this goes as followed:

```javascript
document.createElement("header"); // repeat for all used html5 elements
```

That's one way to do it.

Another way is to use the HTML 5 shiv that's hosted on googlecode by Remy Sharp. It's minified and wrapped in conditional tags so it's only a few bytes that have to be downloaded by browsers like IE 8 and below to be able to render the "new" elements. Just add the following script tags to your head section and all browsers should be fine rendering the "new" HTML 5 elements.

```javascript
<!--[if IE]>
	<script src="https://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
<![endif]-->
```

The script uses conditionals itself too so if you leave out the IE conditionals the script will still not run for FireFox 3+ or WebKit browsers.

Happy HTML 5 theming!
