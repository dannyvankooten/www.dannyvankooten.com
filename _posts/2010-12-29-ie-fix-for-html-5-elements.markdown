---
layout: post
title: IE fix for HTML 5 elements
date: '2010-12-29 19:15:53'
tags:
- javascript
- html-5
---

<p>If any of you had a look in the source code of my new theme here on DannyvanKooten.com you might have noticed that i've used some new HTML 5 elements for the mark-up. Nothing fancy, just trying to add some more semantic value to the different parts of a WordPress theme. The sidebar for example is no longer wrapped in a div element but is now wrapped in an aside element.</p>

<p>HTML 5 is far from finished (for those of you that want to know how many days are left till the official release date: check out <a href="http://ishtml5readyyet.com/">ishtml5readyyet.com</a>, make sure that you check out the source code of the page too) and not all major (or less major) browsers support the new semantic elements yet. Internet Explorer for example has some problems seeing and thus styling the elements, resulting in a messed up layout. Luckily, there is an easy fix for this.</p>

<h2>IE fix for HTML 5 elements</h2>
<p>Because IE 8 and lower does not see the elements it cannot style the elements. Resolving this issue is easy, just create the elements using JavaScript. When you do this before the page is rendered, in the head section of your webpage, IE will have no problem styling our newly created elements. This is called the "HTML 5 shiv"</p>

<p>You can just use the usual JavaScript function createElement() to create the elements you've used, this goes as followed: </p>

```javascript
document.createElement("header"); // repeat for all used html5 elements
```

<p>That's one way to do it. An easier way is to use the HTML 5 shiv that's hosted on googlecode by Remy Sharp. I'm using it myself too. It's minified and wrapped in conditional tags so it's only a few bytes that have to be downloaded by browsers like IE 8 and below to be able to render the "new" elements. Just add the following script tags to your head section and all browsers should be fine rendering the "new" HTML 5 elements.</p>

```javascript
<!--[if IE]>
	<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
<![endif]-->
```

<p>The JS script uses conditionals itself too so if you leave out the if IE conditionals the script will still not be ran by FireFox 3+ or Webkit browsers. Happy HTML 5 theming!</p>