---
title: "Replacing annotations with PHP attributes – with Rector"
description: "Using Rector to automatically convert Doctrine annotations to native PHP 8 attributes in a Symfony project."
date: "2023-07-10T00:00:00+00:00"
modified: "2026-05-14T20:29:36+02:00"
categories: ["Programming"]
---

While updating a Symfony application to version 6.3, I worked through the deprecations that came with the upgrade. One of them was the move from Doctrine annotations to native [PHP attributes](https://www.php.net/manual/en/language.attributes.overview.php), which were introduced in PHP 8.

Converting every annotation by hand would have taken a few tedious hours. Fortunately, static analysis and automated refactoring tools have become very good in the PHP ecosystem.

## Rector for automated refactoring

[Rector](https://getrector.com/documentation) is a tool for automated refactoring of PHP code. It can handle a wide variety of language-level changes, and it also supports upgrades for several popular frameworks.

For example, using Rector to replace annotations with PHP 8 attributes was as simple as this:

1.  Install Rector.

```php
composer require rector/rector --dev
```

2.  Create a configuration file called `rector.php`.

```php
<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\CodeQuality\Rector\Class_\InlineConstructorDefaultToPropertyRector;
use Rector\Php80\Rector\Class_\AnnotationToAttributeRector;
use Rector\Php80\ValueObject\AnnotationToAttribute;

return static function (RectorConfig $rectorConfig): void {
    // Paths for Rector to act upon
    $rectorConfig->paths([
        __DIR__ . '/config',
        __DIR__ . '/public',
        __DIR__ . '/src',
        __DIR__ . '/tests',
    ]);

    // Additional configuration (Rector rules) go here
};
```

3.  Use the provided Symfony and Doctrine sets to automatically refactor `@Route` and `@ORM` annotations to attributes.

```php
$rectorConfig->sets([
    \Rector\Doctrine\Set\DoctrineSetList::ANNOTATIONS_TO_ATTRIBUTES,
    \Rector\Symfony\Set\SymfonySetList::ANNOTATIONS_TO_ATTRIBUTES,
    \Rector\Symfony\Set\SensiolabsSetList::ANNOTATIONS_TO_ATTRIBUTES,
]);
$rectorConfig->ruleWithConfiguration(AnnotationToAttributeRector::class, [
    new AnnotationToAttribute(\Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity::class),
    new AnnotationToAttribute(\Ibericode\Vat\Bundle\Validator\Constraints\VatNumber::class),
]);
```

4.  Preview the suggested changes by running Rector with the `--dry-run` option.

```
vendor/bin/rector process --dry-run
```

5.  Apply the changes.

```
vendor/bin/rector process
```

That was all it took. Rector saved several hours of repetitive work with a configuration that took only a few minutes to set up.

Rector also comes with [set lists](https://getrector.com/documentation/set-lists), which configure multiple rules for you. These are useful when upgrading to a new PHP version or adopting newer language features.

```
$rectorConfig->sets([\Rector\Set\ValueObject\LevelSetList::UP_TO_PHP_82]);
```

There are hundreds of available [Rector rules](https://getrector.com/documentation/rules-overview). If you are refactoring PHP code at the language or framework level, Rector is worth trying before doing the work manually.

Kudos to the Rector authors for building such a useful tool.
