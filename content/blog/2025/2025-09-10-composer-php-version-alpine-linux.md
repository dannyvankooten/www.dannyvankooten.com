+++
title = "Using Composer with a specific PHP version on Alpine Linux"
+++

While updating some PHP applications to version 8.4 lately I ran into an issue with Composer on Alpine Linux using a different PHP version than what `/usr/bin/env php` resolved to.

If your `composer.json` has a platform requirement for a specific PHP version this will then result in errors like this, despite having the required PHP version installed:

```txt
- Root composer.json requires php >=8.4 but your php version (8.3.24) does not satisfy that requirement.
```

The issue is that the [composer](https://pkgs.alpinelinux.org/package/edge/community/x86_64/composer) package in the Alpine Linux Package Repository has a dependency on a hard-coded PHP version. The [APKBUILD file](https://gitlab.alpinelinux.org/alpine/aports/-/blob/f2f1500af08c9eec5be81ba7856671120b05a655/community/composer/APKBUILD#L53) creates a file `/usr/bin/composer` with a shebang that points to this hard-coded PHP version instead of the one from `/usr/bin/env php`.

```sh
$ head /usr/bin/composer
#!/bin/sh

/usr/bin/php83 /usr/bin/composer.phar "$@"
```

You can confirm this by running `composer --version` which will print both the Composer version as the PHP version it is using:

```sh
$ composer --version
Composer version 2.8.10 2025-07-10 19:08:33
PHP version 8.3.24 (/usr/bin/php83)
```

## How to fix Composer to your desired PHP version

Now we know the problem, addressing is straightforward. We can either modify the `/usr/bin/composer` file to point to our desired PHP binary or we can install Composer manually following the installation instructions on its website.

### Replace the shebang with a more portable version

Let's use [sed](https://linux.die.net/man/1/sed) to modify the shebang line in `/usr/bin/composer` to point to whatever `/usr/bin/env php` resolves to.

```sh
$ sudo sed -i 's/php83/env php/g' /usr/bin/composer
$ composer --version
Composer version 2.8.10 2025-07-10 19:08:33
PHP version 8.4.11 (/usr/bin/php)
```

### Install Composer manually

Alternatively, we can simply install Composer ourselves following the [installation instructions from its website](https://getcomposer.org/download/).

```
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php -r "if (hash_file('sha384', 'composer-setup.php') === 'dac665fdc30fdd8ec78b38b9800061b4150413ff2e3b6f88543c636f7cd84f6db9189d43a81e5503cda447da73c7e5b6') { echo 'Installer verified'.PHP_EOL; } else { echo 'Installer corrupt'.PHP_EOL; unlink('composer-setup.php'); exit(1); }"
php composer-setup.php
php -r "unlink('composer-setup.php');"
sudo mv composer.phar /usr/bin/composer
```

Now, running `composer --version` outputs the following:

```sh
$ composer --version
Composer version 2.8.11 2025-08-21 11:29:39
PHP version 8.4.11 (/usr/bin/php)
```

Now we can use any PHP version in our Alpine Linux builds without Composer complaining about the platform requirement not being satisfied. Big success!
