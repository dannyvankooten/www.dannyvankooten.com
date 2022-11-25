+++
title = "Introducing HTML Forms for WordPress"
date = 2017-11-01 
+++

Our most popular plugin [Mailchimp for WordPress](https://www.mc4wp.com/) was released in 2013. Inspired by [Contact Form 7](https://wordpress.org/plugins/contact-form-7/) we built it in such a way that you could define the fields for your sign-up forms in native HTML. This has worked very well over the last 4 years.

We kept getting questions for that type of form functionality without the Mailchimp part though. CF7 still ships its own templating language, after all.

Last week, we finally gave in and submitted [HTML Forms](https://www.htmlformsplugin.com/) to the WordPress.org plugin repository. 

Allow me to introduce you to a form plugin where you have full control over the frontend output.

**You supply the form fields in normal HTML. The plugin takes care of validating & processing the form.**

We really like this approach as it allows for great performance & flexibility while still saving you tons of time. In fact, we find creating a form this way is much faster than interacting with a drag and drop interface.

### How HTML Forms works

Upon saving a form the plugin parses the field HTML (just as a browser would) and stores pieces of meta-data so it knows what to expect when that form is submitted.

You can configure each form to run several user-defined actions for each successful form submission, like sending an email notification or triggering a webhook.

Here's a quick screenshot of what editing a form in HTML Forms looks like.

[![Screenshot of HTML Forms](/media/2017/html-forms.png)](/media/2017/html-forms.png)

Check out the [HTML Forms features](https://www.htmlformsplugin.com/features/) page for a more complete list of features.

##### Wrapping up

Next time you're in need of a contact form for your WordPress site, please consider HTML Forms. And [let me know](/contact/) if you have any suggestions or thoughts. I'd love to hear them!
