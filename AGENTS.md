# Repository Guidelines

## Project Structure & Module Organization

This is a WordPress site repository. The custom theme lives in `wp-content/themes/dvk26/`; edit templates such as `single.php`, `page.php`, `front-page.php`, `header.php`, `footer.php`, and shared behavior in `functions.php`. Custom site functionality is in `wp-content/plugins/ibericode-seo/`, `wp-content/plugins/ibericode-mods/`, and `wp-content/mu-plugins/`. Treat `wp-content/themes/twentytwentyfive/` and `wp-content/plugins/query-monitor/` as upstream or development dependencies unless a task explicitly targets them. Uploaded media and environment files are excluded from deploys.

## Build, Test, and Development Commands

- `wp server --host=localhost --port=8080`: run the site locally if WP-CLI is configured for this checkout.
- `scripts/pull`: pull the remote database and uploads into the local environment, rewrite URLs to `http://localhost:8080`, and reactivate Query Monitor.
- `scripts/deploy`: deploy the clean working tree to production with `rsync`; it aborts if there are uncommitted changes or WordPress core updates are pending.
- `composer install` in `wp-content/plugins/ibericode-mods/`: install plugin development dependencies.
- `vendor/bin/phpstan analyse` in `wp-content/plugins/ibericode-mods/`: run static analysis for that plugin.

## Coding Style & Naming Conventions

Use PHP 8.4-compatible WordPress code. Prefer small functions, WordPress hooks and filters, and escaped output (`esc_html`, `esc_attr`, `esc_url`) at render time. Existing custom theme functions use the `dvk26_` prefix; namespaced plugin functions in `ibericode-seo` live under `Ibericode\SEO`. Follow the surrounding file style for spacing, but keep new code readable with 4-space indentation in PHP and concise comments only where they clarify behavior.

## Testing Guidelines

There is no repository-wide automated test suite. For PHP changes, run `php -l path/to/file.php` on edited files and use PHPStan where available. For theme and SEO changes, verify key templates in a local WordPress instance and inspect generated HTML, metadata, redirects, and sitemap behavior as relevant.

## Commit & Pull Request Guidelines

Git history uses short, imperative messages, sometimes with conventional prefixes such as `docs:`, `feat:`, or `chore:`. Keep commits focused, for example `fix margin for <hr>` or `docs: add docblock for dvk26_get_asset_url function`. Pull requests should describe the visible change, list validation performed, link related issues when applicable, and include screenshots for front-end changes.

## Security & Configuration Tips

Do not commit `wp-config.php`, database dumps, uploads, logs, or secrets. Configuration such as SMTP credentials and Bunny CDN keys belongs in local or server config constants, not in repository files.
