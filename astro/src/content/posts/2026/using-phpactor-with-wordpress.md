---
title: "Using phpactor as a language server for WordPress development"
description: "Set up phpactor for WordPress development by indexing WordPress core files or using WordPress stubs while keeping diagnostics focused on your own code."
date: "2026-02-26T00:00:00+00:00"
modified: "2026-05-14T20:26:04+02:00"
categories: ["WordPress"]
---

This post shows how to use [phpactor](https://phpactor.readthedocs.io/en/master/) as a language server for WordPress development. Phpactor is a strong tool for PHP development, and it works with editors and IDEs that support the Language Server Protocol (LSP).

Phpactor works well with modern PHP code when the project either follows [PSR-4](https://www.php-fig.org/psr/psr-4/) or uses [Composer](https://getcomposer.org/) for autoloading. WordPress does not use either approach, so phpactor can have trouble discovering symbols from WordPress core by default.

## Configuring phpactor to include WordPress core files

You can solve this by setting the [indexer.include\_patterns](https://phpactor.readthedocs.io/en/master/reference/configuration.html#indexer-include-patterns) configuration option to include WordPress core files. Add the following to your `.phpactor.json` configuration file:

```json
{
    "indexer.include_patterns": [
        "wp-includes/**/*.php",
        "wp-admin/**/*.php",
        "wp-content/plugins/**/*.php",
        "wp-content/themes/**/*.php"
    ]
}
```

I also exclude WordPress core and third-party plugins from phpactor’s diagnostics through the [language\_server.diagnostic\_exclude\_paths](https://phpactor.readthedocs.io/en/master/reference/configuration.html#language-server-diagnostic-exclude-paths) configuration setting.

```json
{
  "language_server.diagnostic_exclude_paths": [
    "wp-includes/**/*",
    "wp-admin/**/*",
    "wp-content/plugins/woocommerce/**/*"
  ]
}
```

With these two configuration options in place, phpactor can discover symbols from WordPress core and provide accurate diagnostics for your own code while ignoring issues in WordPress core or third-party plugins.

## Using WordPress stubs

Alternatively, you can use [stubs](https://phpactor.readthedocs.io/en/master/reference/stubs.html) to give phpactor the information it needs about WordPress core functions and classes.

```sh
composer require --dev php-stubs/wordpress-stubs
```

This installs the [php-stubs/wordpress-stubs](https://github.com/php-stubs/wordpress-stubs) package. You can then configure phpactor to use these stubs by adding the following to your `.phpactor.json` configuration file:

```json
{
    "worse_reflection.additive_stubs": [
        "vendor/php-stubs/wordpress-stubs/wordpress-stubs.php"
    ]
}
```

Both approaches make phpactor more useful in WordPress projects. Indexing core files gives phpactor more project context, while stubs provide a lightweight way to expose WordPress functions and classes to the language server.
