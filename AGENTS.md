# Repository Guidelines

Astro source of www.dannyvankooten.com, a static site migrated from WordPress (see the `wordpress-final` tag). README.md describes the content structure, images and deployment.

- Posts: `src/content/posts/{year}/{slug}.md(x)`, pages: `src/content/pages/{slug}.md(x)`. Use MDX for entries with images, so they are optimized.
- Theme: `src/layouts/`, `src/components/` and `src/styles/global.css`. Site-wide constants are in `src/consts.ts`.
- Redirects: add them to `redirects.mjs`. They are served by NGINX from the generated `dist/nginx-redirects.conf`.

## Testing

Run `npx astro check` and `npm run build`. The build reports SEO warnings (H1s, duplicate or overly long titles and descriptions, image alt text, broken internal links); don't add new ones.

## Commits

Use short, imperative commit messages, e.g. `fix margin for <hr>` or `docs: explain image directories`.

## Development

When starting the dev server, use background mode:

```
astro dev --background
```

Manage the background server with `astro dev stop`, `astro dev status`, and `astro dev logs`. `astro dev --background` reuses a dev server that is already running, and `astro dev stop` stops it, even if the user started it. Check `astro dev status` first and only stop a server you started.

## Documentation

Full documentation: https://docs.astro.build

Consult these guides before working on related tasks:

- [Adding pages, dynamic routes, or middleware](https://docs.astro.build/en/guides/routing/)
- [Working with Astro components](https://docs.astro.build/en/basics/astro-components/)
- [Using React, Vue, Svelte, or other framework components](https://docs.astro.build/en/guides/framework-components/)
- [Adding or managing content](https://docs.astro.build/en/guides/content-collections/)
- [Adding styles or using Tailwind](https://docs.astro.build/en/guides/styling/)
- [Supporting multiple languages](https://docs.astro.build/en/guides/internationalization/)
