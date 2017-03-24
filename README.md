<p align="center">
  <img src="https://static.hoa-project.net/Image/Hoa.svg" alt="Hoa" width="250px" />
</p>

---

<p align="center">
  <a href="https://travis-ci.org/hoaproject/Locale"><img src="https://img.shields.io/travis/hoaproject/Locale/master.svg" alt="Build status" /></a>
  <a href="https://coveralls.io/github/hoaproject/Locale?branch=master"><img src="https://img.shields.io/coveralls/hoaproject/Locale/master.svg" alt="Code coverage" /></a>
  <a href="https://packagist.org/packages/hoa/locale"><img src="https://img.shields.io/packagist/dt/hoa/locale.svg" alt="Packagist" /></a>
  <a href="https://hoa-project.net/LICENSE"><img src="https://img.shields.io/packagist/l/hoa/locale.svg" alt="License" /></a>
</p>
<p align="center">
  Hoa is a <strong>modular</strong>, <strong>extensible</strong> and
  <strong>structured</strong> set of PHP libraries.<br />
  Moreover, Hoa aims at being a bridge between industrial and research worlds.
</p>

# Hoa\Locale

[![Help on IRC](https://img.shields.io/badge/help-%23hoaproject-ff0066.svg)](https://webchat.freenode.net/?channels=#hoaproject)
[![Help on Gitter](https://img.shields.io/badge/help-gitter-ff0066.svg)](https://gitter.im/hoaproject/central)
[![Documentation](https://img.shields.io/badge/documentation-hack_book-ff0066.svg)](https://central.hoa-project.net/Documentation/Library/Locale)
[![Board](https://img.shields.io/badge/organisation-board-ff0066.svg)](https://waffle.io/hoaproject/locale)

This library allows to get the informations of the locale from the system, the
HTTP client or something else.

[Learn more](https://central.hoa-project.net/Documentation/Library/Locale).

## Installation

With [Composer](https://getcomposer.org/), to include this library into
your dependencies, you need to
require [`hoa/locale`](https://packagist.org/packages/hoa/locale):

```sh
$ composer require hoa/locale '~2.0'
```

For more installation procedures, please read [the Source
page](https://hoa-project.net/Source.html).

## Testing

Before running the test suites, the development dependencies must be installed:

```sh
$ composer install
```

Then, to run all the test suites:

```sh
$ vendor/bin/hoa test:run
```

For more information, please read the [contributor
guide](https://hoa-project.net/Literature/Contributor/Guide.html).

## Quick usage

We propose a quick overview to get the locale and related informations about an
HTTP client. Next, we will see the other localizers.

### Locale from an HTTP client

To get the locale from an HTTP client, we will use the
`Hoa\Locale\Localizer\Http` localizer. Then, we will print the result of the
following interesting methods:

  * `getLanguage` to get the language,
  * `getScript` to get the script,
  * `getRegion` to get the region,
  * `getVariants` to get variants of the locale.

Thus:

```php
$locale = new Hoa\Locale(new Hoa\Locale\Localizer\Http());

echo
    'language : ', $locale->getLanguage(), "\n",
    'script   : ', $locale->getScript(), "\n",
    'region   : ', $locale->getRegion(), "\n",
    'variant  : ', implode(', ', $locale->getVariants()), "\n";
```

For example, with the `Accept-Language` HTTP header set to
`zh-Hant-TW-xy-ab-123`, we will have:

```
language : zh
script   : Hant
region   : TW
variant  : xy, ab, 123
```

### Other localizers

So far, we also have the `Hoa\Locale\Localizer\System` to get the locale
informations from the system and `Hoa\Locale\Localizer\Coerce` to get them from
an arbitrary locale representation.

## Documentation

The
[hack book of `Hoa\Locale`](https://central.hoa-project.net/Documentation/Library/Locale)
contains detailed information about how to use this library and how it works.

To generate the documentation locally, execute the following commands:

```sh
$ composer require --dev hoa/devtools
$ vendor/bin/hoa devtools:documentation --open
```

More documentation can be found on the project's website:
[hoa-project.net](https://hoa-project.net/).

## Getting help

There are mainly two ways to get help:

  * On the [`#hoaproject`](https://webchat.freenode.net/?channels=#hoaproject)
    IRC channel,
  * On the forum at [users.hoa-project.net](https://users.hoa-project.net).

## Contribution

Do you want to contribute? Thanks! A detailed [contributor
guide](https://hoa-project.net/Literature/Contributor/Guide.html) explains
everything you need to know.

## License

Hoa is under the New BSD License (BSD-3-Clause). Please, see
[`LICENSE`](https://hoa-project.net/LICENSE) for details.
