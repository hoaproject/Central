<p align="center">
  <img src="https://static.hoa-project.net/Image/Hoa.svg" alt="Hoa" width="250px" />
</p>

---

<p align="center">
  <a href="https://travis-ci.org/hoaproject/Json"><img src="https://img.shields.io/travis/hoaproject/Json/master.svg" alt="Build status" /></a>
  <a href="https://coveralls.io/github/hoaproject/Json?branch=master"><img src="https://img.shields.io/coveralls/hoaproject/Json/master.svg" alt="Code coverage" /></a>
  <a href="https://packagist.org/packages/hoa/json"><img src="https://img.shields.io/packagist/dt/hoa/json.svg" alt="Packagist" /></a>
  <a href="https://hoa-project.net/LICENSE"><img src="https://img.shields.io/packagist/l/hoa/json.svg" alt="License" /></a>
</p>
<p align="center">
  Hoa is a <strong>modular</strong>, <strong>extensible</strong> and
  <strong>structured</strong> set of PHP libraries.<br />
  Moreover, Hoa aims at being a bridge between industrial and research worlds.
</p>

# Hoa\Json

[![Help on IRC](https://img.shields.io/badge/help-%23hoaproject-ff0066.svg)](https://webchat.freenode.net/?channels=#hoaproject)
[![Help on Gitter](https://img.shields.io/badge/help-gitter-ff0066.svg)](https://gitter.im/hoaproject/central)
[![Documentation](https://img.shields.io/badge/documentation-hack_book-ff0066.svg)](https://central.hoa-project.net/Documentation/Library/Json)
[![Board](https://img.shields.io/badge/organisation-board-ff0066.svg)](https://waffle.io/hoaproject/json)

This library provides only the grammar of JSON in the PP format (see [the
`Hoa\Compiler`
library](https://central.hoa-project.net/Resource/Library/Compiler)).

[Learn more](https://central.hoa-project.net/Documentation/Library/Json).

## Installation

With [Composer](https://getcomposer.org/), to include this library into
your dependencies, you need to
require [`hoa/json`](https://packagist.org/packages/hoa/json):

```sh
$ composer require hoa/json '~2.0'
```

For more installation procedures, please read [the Source
page](https://hoa-project.net/Source.html).

## Testing

Before running the json suites, the development dependencies must be installed:

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

As a quick overview, we will see how to use the grammar to parse JSON strings.

### Parse JSON strings

All we have to do is to use [the `Hoa\Compiler`
library](https://central.hoa-project.net/Resource/Library/Compiler). For
instance, in CLI:

```sh
$ echo '{"foo": 42, "bar": [1, [2, [3, 5], 8], 13]}' | hoa compiler:pp hoa://Library/Json/Grammar.pp 0 -v dump
>  #object
>  >  #pair
>  >  >  token(string:string, foo)
>  >  >  token(number, 42)
>  >  #pair
>  >  >  token(string:string, bar)
>  >  >  #array
>  >  >  >  token(number, 1)
>  >  >  >  #array
>  >  >  >  >  token(number, 2)
>  >  >  >  >  #array
>  >  >  >  >  >  token(number, 3)
>  >  >  >  >  >  token(number, 5)
>  >  >  >  >  token(number, 8)
>  >  >  >  token(number, 13)
```

## Documentation

The
[hack book of `Hoa\Json`](https://central.hoa-project.net/Documentation/Library/Json) contains
detailed information about how to use this library and how it works.

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
