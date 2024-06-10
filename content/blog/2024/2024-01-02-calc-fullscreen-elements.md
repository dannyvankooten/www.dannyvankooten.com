+++
title = "Using CSS calc to make elements fullscreen"
+++

> **Heads up.** The styling of my website changed since writing this post, so the element may look different than intended.

This blog always had a pretty minimal amount of CSS, but somehow I still kicked off 2024 by throwing away all styles and starting over.

The constraints I set for myself were as follows:

- No use of CSS classes.
- No colors except for shades of black and white.
- A monospaced (and web-safe) font.
- A strict separation of content and presentation.

The latter one proved to be a bit of a problem when I decided I wanted to have fullscreen code blocks, like so:

<pre><code style="margin-left: min(-16px, (100vw - 72ch) / -2);
    margin-right: min(-16px, (100vw - 72ch) / -2);
    padding-left: max(16px, (100vw - 72ch) / 2);
    padding-right: max(16px, (100vw - 72ch) / 2);">See how this elements expands beyond the size of its container</code></pre>

Normally, I would accomplish such a thing by breaking out of the element that has a `max-width` rule applied to it and then re-apply the max-width rule to a child of the element I want to have full-width.

```
<div style="max-width: 820px; margin: 0 auto;">
    <p>Some centered text here.</p>
</div>
<pre>
    <code style="max-width: 820px; margin: 0 auto;">
        Centered code inside a fullscreen pre element.
    </code>
</pre>
<div style="max-width: 820px; margin: 0 auto;">
    <!-- continue container element... -->
    ...
```

But that would violate my last constraint, as this means I would have to modify the HTML of all of my posts containing code blocks just to attain some visual effect.

## CSS to the rescue

Luckily CSS support in browsers has not stood still these last few years. All major browsers now support [calc](https://developer.mozilla.org/en-US/docs/Web/CSS/calc) and [viewport units](https://developer.mozilla.org/en-US/docs/Learn/CSS/Building_blocks/Values_and_units#relative_length_units).

The idea is to set a negative margin on either side combined with a padding to keep the element's content aligned with the parent container.

The negative margin pulls the element to the side of the screen (overflowing its parent container), while the padding ensures the content still lines up with the element's siblings.

For an 800px container in a 1000px viewport, the CSS for this would look like this:

```css
pre {
    margin-left: -100px;
    margin-right: -100px;
    padding-left: 100px;
    padding-right: 100px;
}
```

To make this work with viewports of any size, we need to do some math based on `100vw` (100% of the viewport's width) and the size of our container.

```css
pre {
    margin-left: calc((100vw - 800px) / -2);
    margin-right: calc((100vw - 800px) / -2);
    padding-left: calc((100vw - 800px) / 2);
    padding-right: calc((100vw - 800px) / 2);
}
```

This sets the negative margin on either side to half of the difference between the viewport width and our container width.

This works! Except on screens smaller than our container size, there's no margin or padding because our calculation results in a positive value...

Let's use `min` and `max` to fix that:

```css
pre {
    margin-left: min(-1em, (100vw - 800px) / -2);
    margin-right: min(-1em, (100vw - 800px) / -2);
    padding-left: max(1em, (100vw - 800px) / 2);
    padding-right: max(1em, (100vw - 800px) / 2);
}
```

This ensures we always get a minimum negative margin / padding of `1em`, which matches the padding on our `<body>` element.

I was quite stoked to see that CSS could pull this off nowadays. In case you're interested, you can view the entire ~2 kB of not-minified CSS (don't worry, [compression is used](https://www.dannyvankooten.com/blog/2023/top-websites-not-using-compression/)) for this site by inspecting the source of this page.

Happy 2024!


### References

- [mdn: min() CSS function](https://developer.mozilla.org/en-US/docs/Web/CSS/min)
- [mdn: calc() CSS function](https://developer.mozilla.org/en-US/docs/Web/CSS/calc)
- [mdn: CSS values and units](https://developer.mozilla.org/en-US/docs/Learn/CSS/Building_blocks/Values_and_units)
- [caniuse: calc](https://caniuse.com/calc)
- [caniuse: viewport units](https://caniuse.com/viewport-units)
- [caniuse: min/max](https://caniuse.com/minmaxwh)
