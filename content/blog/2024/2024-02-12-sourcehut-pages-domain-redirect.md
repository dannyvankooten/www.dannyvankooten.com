+++
title = "sourcehut pages: redirect (sub)domain"
+++

Last weekend I moved this site over to [sourcehut pages](https://srht.site/). Deployment was a lot quicker than through GitHub pages and the SSL certificate was issued within seconds of me pushing the tarball to the sourcehut servers.

One thing that took me a while to figure out was how to set-up a redirect from my apex domain to the `www.` variant. I'm sharing my approach here in the hope others can find it more easily:

Ideally this would be a HTTP redirect, but this does not seem possible right now so instead we can use HTML and JS. The gist of the idea is as follows:

- Ensure the [DNS](https://srht.site/custom-domains) of both domains is pointing to sourcehut's records.
- Upload two HTML files to the domain we want to redirect from.
- Use `<meta http-equiv="refresh" content="0; redirect-to-domain.com">` to redirect the `/` homepage.
- Use `window.location.href = "redirect-to-domain.com"` in a 404 handler to redirect all other pages.


### Redirecting the homepage

Create a filed called `index.html` with the following file contents:

```filename
index.html
```
```html
<meta http-equiv="refresh" content="0; url=https://www.dannyvankooten.com/">
```

### Redirecting while retaining the request URL

Create a file called `404.html` with the following file contents:

```filename
404.html
```
```html
<script>window.location.href = 'https://www.dannyvankooten.com' + window.location.pathname;</script>
```

Create another file called `config.json` with the following file contents:

```filename
config.json
```
```json
{
	"notFound": "/404.html"
}
```

Then, create a tar archive and use [hut](https://sr.ht/~emersion/hut/) to publish to the domain you would like to redirect while specifying the file we just created through the `--site-config` argument:

```sh
tar -C . -cvz . > site.tar.gz
hut pages publish --site-config=config.json -d dannyvankooten.com site.tar.gz
```

That's it. Any URL on `dannyvankooten.com` will now result in loading the specified 404 page, which in turn will redirect you to `www.dannyvankooten.com` with the same path.

PS. Note that you still want to ensure your HTML contains a `<link rel="canonical" href="https://example.com/dresses/green-dresses">` tag pointing to the [canonical source](https://developers.google.com/search/docs/crawling-indexing/canonicalization) of each of your pages.
