+++

title = "Add a Mailchimp sign-up checkbox to Contact Form 7"
date = 2013-09-25 16:03:45

+++

ontact Form 7 is one of the most popular free contact form plugins for WordPress and Mailchimp is an enormously powerful email marketing tool. There are quite a few guides on the internet telling you how to make the two work together. Unfortunately, most of them seem to overcomplicate things or use improper and unmaintainable code.

This post will show you how to add a Mailchimp sign-up checkbox to your Contact Form 7 form without writing a single line of code.

<img class="alignright size-full wp-image-2191" alt="contact-form-7-example" src="https://res.cloudinary.com/dannyvankooten/image/upload/v1408704561/contact-form-7-example_rtkgiq.png" width="352" height="374" />

People who send you a message will be able to subscribe to your Mailchimp list(s) with ease. You can choose to have the checkbox pre-checked so it doesn't even require them to click.

**Requirements**
- [Contact Form 7](https://wordpress.org/plugins/contact-form-7/)
- [Mailchimp for WordPress Pro](https://www.mc4wp.com/) or even [the free version](https://wordpress.org/plugins/mailchimp-for-wp/)
- [A Mailchimp account](https://eepurl.com/FLbC9) (referral link, we'll both get $30 in so-called MonkeyRewards if you use this link to sign-up to Mailchimp)

### Adding the sign-up checkbox to your Contact Form 7 template
To add the newsletter sign-up checkbox to your contact form we can use a simple shortcode that comes with the Mailchimp for WordPress plugin when you have Contact Form 7 enabled.

```html
[mc4wp_checkbox "Your custom label text, asking visitors to subscribe."]
```

As you can see, I added a custom label text but this is completely optional.

<strong>That's it. We're done. No additional code is required. </strong>

The plugin will automatically find the email address of the person filling out your contact form and add it to the selected list(s) in *Mailchimp for WP > Checkboxes*.

### Sending more fields to Mailchimp
So your list has a required field which holds the first name of every subscriber? No problem. Just prefix the CF7 field name with `mc4wp-` and the plugin will send it to Mailchimp as a merge variable.

<em>Example CF7 mark-up for FNAME</em>
```
[text* mc4wp-FNAME]
```
The part after the dash should match the "merge tag" of the list field.

Let's do another example but this time for a list field called `WEBSITE`. This time, we'll be using the Contact Form 7 "Generate Tag" tool.

<img class="aligncenter size-full wp-image-2189" alt="Generating a Mailchimp website field using the CF7 tag wizard" src="https://res.cloudinary.com/dannyvankooten/image/upload/v1408704562/contact-form-7-generate-tag_ugaglb.png" width="615" height="390" />

Easy, right? It's a sure way to get more subscribers to your email lists as well. Want to see a live example? I am using it for my own <a href="https://www.mc4wp.com/contact/">contact form on the plugin site</a>.
