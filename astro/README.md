# www.dannyvankooten.com (Astro)

Static version of www.dannyvankooten.com, migrated from WordPress. The theme is a port of `wp-content/themes/dvk26`.

## Commands

| Command           | Action                                                    |
| :---------------- | :-------------------------------------------------------- |
| `npm run dev`     | Start the dev server at `localhost:4321`                  |
| `npm run build`   | Build the site to `./dist/`                               |
| `npm run preview` | Preview the build locally                                 |
| `npm run import`  | Re-import all posts, pages and uploads from the live site |

## Content

- `src/content/posts/{year}/{slug}.md` is served at `/blog/{year}/{slug}/`.
- `src/content/pages/{slug}.md` is served at `/{slug}/`. `index.md` is the front page.
- `src/assets/images/blog/{year}/{slug}/` and `src/assets/images/pages/{slug}/` hold the images of each post or page.

Frontmatter is validated in `src/content.config.ts`. Without a `description`, the first 30 words of the content are used, as on the WordPress site.

### Images

Entries with images are MDX (`.mdx`). Markdown images in MDX are rendered by `src/components/ContentImage.astro`, which uses `<Picture>` to generate AVIF and WebP at 1x and 2x the content width. SVGs and (animated) GIFs keep their format. Plain `.md` files can't use components, so their images would not be optimized.

```mdx
![Alt text](../../../assets/images/blog/2026/my-post/screenshot.png)

<figure>

![Alt text](../../../assets/images/blog/2026/my-post/photo.jpg)

<figcaption>A caption</figcaption>
</figure>
```

Raw HTML isn't allowed in MDX. Imported HTML that Markdown can't express is wrapped in `<Fragment set:html={`…`} />`.

## Importing from WordPress

`scripts/import-wordpress.mjs` overwrites `src/content/` and `src/assets/images/` with the current content of the live site:

- Bunny CDN ignores query strings, so items are found through the WordPress sitemaps and fetched from the REST API by ID.
- Posts that Bunny Shield blocks on the REST API are read from their HTML page instead.
- HTML that Markdown can't express (MathML, forms, scripts, styled elements, figures with captions) is kept as raw HTML.

## Deploying

The build output in `dist/` is plain static files. Example NGINX server block:

```nginx
server {
    root /var/www/dannyvankooten.com/dist;

    # 301 redirects, generated from redirects.mjs
    include /var/www/dannyvankooten.com/dist/nginx-redirects.conf;

    error_page 404 /404.html;

    location / {
        try_files $uri $uri/ =404;
    }

    location /_astro/ {
        add_header Cache-Control "public, max-age=31536000, immutable";
    }
}
```

`redirects.mjs` is ported from `wp-content/mu-plugins/redirects.php`. Astro also turns it into `<meta http-equiv="refresh">` pages, which only matter when NGINX doesn't handle the redirect.
