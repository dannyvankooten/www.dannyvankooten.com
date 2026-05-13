ibericode mods
==============

A collection of lightweight WordPress plugins that we commonly use on our sites.

- Reject all WP Login attempts if submitted within 2.5 seconds of page load.
- Configure `wp_mail()` to use SMTP through a few PHP constants.
- Allow SVG uploads for administrators.
- Disable the `/wp-json/wp/v2/users` REST API endpoint.
- Set HTTP `Cache-Control` header on all safe requests for logged-out users.
- Adds `Robots: noindex` HTTP header to all non-singular pages (except the front page).
- Purge Bunny CDN Cache on `save_post`
- Automatically mark comments as spam through a collection of empirically discovered checks.

Some of these are simple no-ops if the relevant PHP constants are not set.

## Install

Download the plugin package from the [latest release here on GitHub](https://github.com/ibericode/ibericode-mods/releases/latest).

Go to **Plugins > Add Plugin > Upload Plugin** to install the plugin. 

Alternatively, download or clone this repository and place in `/wp-content/plugins/`.


## Configuring

### Email through SMTP

To configure WordPress to send emails via SMTP instead of the default `mail()` function, define the following constants in your `wp-config.php` file:

```php
define( 'SMTP_HOST', 'smtp.example.com' );
define( 'SMTP_USER', 'youremail@example.com' );
define( 'SMTP_PASSWORD', 'your_password' ); // Optional
define( 'SMTP_PORT', 587 ); // Optional
define( 'SMTP_ENCRYPTION', 'tls' ); // Optional, defaults to 'tls' (PHPMailer::ENCRYPTION_STARTTLS)
```

The plugin will automatically use `SMTP_USER` as the default "From" email address.

### Bunny CDN Purging

To automatically purge the Bunny CDN cache for a post's URL (and the sitemap) when it is saved or updated, define your Bunny API key in your `wp-config.php` file:

```php
define( 'BUNNY_API_KEY', 'your-bunny-cdn-api-key' );
```

## License

GPL v2 or later