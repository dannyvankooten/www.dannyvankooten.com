# www.dannyvankooten.com (Astro)

Astro source files of www.dannyvankooten.com.

## Commands

| Command           | Action                                                    |
| :---------------- | :-------------------------------------------------------- |
| `npm run dev`     | Start the dev server at `localhost:4321`                  |
| `npm run build`   | Build the site to `./dist/`                               |
| `npm run preview` | Preview the build locally                                 |
| `npm run import`  | Re-import all posts, pages and images from WordPress      |
| `./deploy.sh`     | Build the site and upload `dist/` to the server           |

The WordPress version of this site is kept in Git history, at the `wordpress-final` tag.

`npm run import` only works while the WordPress site is still online. It overwrites `src/content/` and `src/assets/images/`, including any changes made after the migration.

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

## Deploying

`./deploy.sh` builds the site and uploads `dist/` to `/var/www/www.dannyvankooten.com/` on the server, deleting files there that are no longer in the build. It refuses to deploy uncommitted changes.

The build output is plain static files. Example NGINX server block:

```nginx
server {
    root /var/www/www.dannyvankooten.com;

    # 301 redirects, generated from redirects.mjs
    include /var/www/www.dannyvankooten.com/nginx-redirects.conf;

    error_page 404 /404.html;

    location / {
        try_files $uri $uri/ =404;
    }

    location /_astro/ {
        add_header Cache-Control "public, max-age=31536000, immutable";
    }
}
```

`redirects.mjs` is ported from WordPress's `wp-content/mu-plugins/redirects.php`. The dev server applies these redirects too, but the build doesn't write redirect pages, so in production only NGINX handles them.
