---
title: "Using Composer with a specific PHP version on Alpine Linux"
description: "Fixing Composer using the wrong PHP version on Alpine Linux due to a hardcoded shebang in the composer package."
date: "2025-09-10T00:00:00+00:00"
modified: "2026-05-14T20:49:35+02:00"
categories: ["Programming"]
---

While updating a few PHP applications to PHP 8.4, I ran into a Composer issue on Alpine Linux: Composer was using a different PHP binary than the one resolved by `/usr/bin/env php`.

If your `composer.json` requires a specific PHP version, this can result in an error like this even when the required PHP version is installed:

```txt
- Root composer.json requires php >=8.4 but your php version (8.3.24) does not satisfy that requirement.
```

The issue is that the [composer](https://pkgs.alpinelinux.org/package/edge/community/x86_64/composer) package in the Alpine Linux Package Repository depends on a hard-coded PHP version. The [APKBUILD file](https://gitlab.alpinelinux.org/alpine/aports/-/blob/f2f1500af08c9eec5be81ba7856671120b05a655/community/composer/APKBUILD#L53) creates `/usr/bin/composer` as a shell wrapper that calls that specific PHP binary instead of the one from `/usr/bin/env php`.

```sh
$ head /usr/bin/composer
#!/bin/sh

/usr/bin/php83 /usr/bin/composer.phar "$@"
```

You can confirm which PHP binary Composer is using by running `composer --version`. It prints both the Composer version and the PHP version behind it:

```sh
$ composer --version
Composer version 2.8.10 2025-07-10 19:08:33
PHP version 8.3.24 (/usr/bin/php83)
```

## How to fix Composer to your desired PHP version

Once you know Composer is using the wrong PHP binary, the fix is straightforward. You can either adjust the `/usr/bin/composer` wrapper or skip the Alpine package and install Composer manually.

### Point the wrapper at your desired PHP version

One option is to use [sed](https://linux.die.net/man/1/sed) to change the PHP binary used by `/usr/bin/composer`, so it runs whatever `/usr/bin/env php` resolves to.

```sh
$ sudo sed -i 's/php83/env php/g' /usr/bin/composer
$ composer --version
Composer version 2.8.10 2025-07-10 19:08:33
PHP version 8.4.11 (/usr/bin/php)
```

### Install Composer manually

Another option is to skip the package from the Alpine Package Repository and install Composer yourself using the [official installation instructions](https://getcomposer.org/download/).

```
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php -r "if (hash_file('sha384', 'composer-setup.php') === 'dac665fdc30fdd8ec78b38b9800061b4150413ff2e3b6f88543c636f7cd84f6db9189d43a81e5503cda447da73c7e5b6') { echo 'Installer verified'.PHP_EOL; } else { echo 'Installer corrupt'.PHP_EOL; unlink('composer-setup.php'); exit(1); }"
php composer-setup.php
php -r "unlink('composer-setup.php');"
sudo mv composer.phar /usr/bin/composer
```

After that, `composer --version` should show Composer running with the expected PHP version:

```sh
$ composer --version
Composer version 2.8.11 2025-08-21 11:29:39
PHP version 8.4.11 (/usr/bin/php)
```

With Composer using the correct PHP binary, Alpine Linux builds can use the intended PHP version without failing on a platform requirement mismatch.
