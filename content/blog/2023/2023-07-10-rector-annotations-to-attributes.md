+++
title = "Replacing annotations with PHP attributes - with Rector" 
+++

Recently I was updating a Symfony application to version 6.3 and working my way through all deprecations. One of them was the move to native [PHP attributes](https://www.php.net/manual/en/language.attributes.overview.php) (introduced in PHP8) instead of annotations supported by the Doctrine Annotations library.

Doing this manually would cost quite a few tedious hours. Luckily, it's 2023 and static analysis tools have been getting better and better, at least in the PHP ecosystem. 

## Rector - automated refactoring

[Rector](https://getrector.com/documentation) is a tool for automated refactoring of PHP code. It can handle a wide variety of changes on a language level and also supports upgrades for some of the more popular frameworks.

For example, [having Rector replace all annotations with PHP8 attributes](https://getrector.com/blog/how-to-upgrade-annotations-to-attributes) was as simple as this:

1. Install Rector

```php
composer require rector/rector --dev
```

2. Create a configuration file called `rector.php`

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

3. Use the provided sets by Symfony & Doctrine to automatically refactor all `@Route` and `@ORM` annotations to attributes.

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

4. Preview the suggested changes by running Rector with the `--dry-run` option.

```
vendor/bin/rector process --dry-run
```

5. Apply the changes.

```
vendor/bin/rector process
```

That's all there is to it. Several tedious hours of work saved by a tool that just works and can be configured within minutes. So good!

Rector also comes with a thing called [set lists](https://getrector.com/documentation/set-lists) which automatically configure multiple rules for you. This can come in really handy if you want to upgrade to a new PHP level and use new language features.

```
$rectorConfig->sets([\Rector\Set\ValueObject\LevelSetList::UP_TO_PHP_82]);
```

There are hundreds of available [Rector rules](https://getrector.com/documentation/rules-overview) available. My guess is that if you're refactoring something on a language or framework level, Rector has you covered.

Kudos to the Rector authors for building such a great tool!