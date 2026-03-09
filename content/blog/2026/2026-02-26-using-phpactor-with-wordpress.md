+++
title = "Using phpactor as a language server for WordPress development"
+++

In this post I want to share how I use [phpactor](https://phpactor.readthedocs.io/en/master/) as a language server for WordPress development. Phpactor is a great tool for PHP development, and it can be used with various editors and IDEs that support the Language Server Protocol (LSP).

Phpactor will work great with modern PHP code as long as it either adheres to [PSR-4](https://www.php-fig.org/psr/psr-4/) or uses [Composer](https://getcomposer.org/) for its autoloader. WordPress, however, does neither, so by default phpactor will have some trouble discovering symbols from WordPress core. 

## Configuring phpactor to include WordPress core files

This is easily solved by setting the [indexer.include_patterns](https://phpactor.readthedocs.io/en/master/reference/configuration.html#indexer-include-patterns) configuration option to include the WordPress core files. You can do this by adding the following to your `.phpactor.json` configuration file:

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

Personally I expand on this by also explicitly excluding WordPress core and third-party plugins from phpactor's diagnostics through the [language_server.diagnostic_exclude_paths](https://phpactor.readthedocs.io/en/master/reference/configuration.html#language-server-diagnostic-exclude-paths) configuration setting.

```json
{
  "language_server.diagnostic_exclude_paths": [
    "wp-includes/**/*",
    "wp-admin/**/*",
    "wp-content/plugins/woocommerce/**/*"
  ]
}
```

With these two configuration options in place, phpactor will be able to discover symbols from WordPress core and provide accurate diagnostics for your own code, while ignoring any issues in the WordPress core or third-party plugins.

## Using WordPress stubs

Alternatively, you can use [stubs](https://phpactor.readthedocs.io/en/master/reference/stubs.html) to provide phpactor with the necessary information about WordPress core functions and classes. 

```sh
composer require --dev php-stubs/wordpress-stubs
```

This will install the [php-stubs/wordpress-stubs](https://github.com/php-stubs/wordpress-stubs) package. You can then configure phpactor to use these stubs by adding the following to your `.phpactor.json` configuration file:

```json
{
    "worse_reflection.additive_stubs": [
        "vendor/php-stubs/wordpress-stubs/wordpress-stubs.php"
    ]
}
```
