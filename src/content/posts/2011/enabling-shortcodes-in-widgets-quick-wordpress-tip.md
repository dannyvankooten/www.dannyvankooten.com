---
title: "Enabling shortcodes in widgets, quick WordPress tip."
description: "A quick tip on enabling WordPress shortcodes in text widgets using a simple filter."
date: "2011-07-17T00:00:00+00:00"
modified: "2026-05-14T15:59:35+02:00"
categories: ["WordPress"]
---

Let’s say you’re developing a plugin that registers a shortcode so that a user can output some kind of form in his posts. Now, the user wants to output the form in one of his widget areas. One way to accomplish this would be to explicitly create a widget for it, add some fields like ‘text\_before’  and ‘text\_after’ and use that to output the form along with some text before and after the actual form.

An easier approach would be to just enable the shortcode so that your user can type some text in one of WordPress its default text widgets (or a [WYSIWYG Widget](https://wordpress.org/plugins/wysiwyg-widgets/), to make things a bit easier) and use the shortcode wherever he wants to output the form, or whatever it is your shortcode provides.

By default, shortcodes in text widgets are not enabled in WordPress. However, since the text from a text widget runs through a filter called ‘widget\_text’, enabling them would be pretty easy. In short, everything that’s needed is one simple line of code:

```php
add_filter('widget_text', 'do_shortcode');
```

Just using this line of code could lead to some problems though. Best thing would be to add a priority so that _do\_shortcode_ runs after `wpautop` (the function that converts linebreaks into paragraphs).

Looking at [wp-includes/shortcodes.php](https://core.trac.wordpress.org/browser/tags/3.2.1/wp-includes/shortcodes.php#L296) you’ll find that the default filter priority for shortcodes in posts is 11. We will use the same priority for our shortcodes inside text widgets as well.

To ensure that the output of our shortcode callback is not wrapped in paragraph tags, it is best to run one more function on the widget text, ‘shortcode\_unautop’.

```
add_filter( 'widget_text', 'shortcode_unautop');
add_filter( 'widget_text', 'do_shortcode', 11);
```

_Note: the above snippet goes into your theme’s _functions.php_ file._

Of course you can use the above snippet for pretty much every filter that’s out there, comment\_text, term\_description, manual filters, …

If you’re a plugin author and you have a custom widget, another way to enable shortcodes inside your widget is to run the content through the do\_shortcode function. So, let’s say your Widget’s content is stored inside a variable called $text, you’ll pass this variable as a parameter to the do\_shortcode function.

```
...
$output_text = do_shortcode($text);
echo $output_text;
...
```

Another, more flexible way would be to create a filter for your widget and hook the do\_shortcode function to that filter.

But, if enabling shortcodes is all that you’re looking for, the above snippet will do just fine.
