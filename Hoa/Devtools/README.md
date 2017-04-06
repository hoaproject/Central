<p align="center">
  <img src="https://static.hoa-project.net/Image/Hoa.svg" alt="Hoa" width="250px" />
</p>

---

<p align="center">
  <a href="https://travis-ci.org/hoaproject/devtools"><img src="https://img.shields.io/travis/hoaproject/devtools/master.svg" alt="Build status" /></a>
  <a href="https://coveralls.io/github/hoaproject/devtools?branch=master"><img src="https://img.shields.io/coveralls/hoaproject/devtools/master.svg" alt="Code coverage" /></a>
  <a href="https://packagist.org/packages/hoa/devtools"><img src="https://img.shields.io/packagist/dt/hoa/devtools.svg" alt="Packagist" /></a>
  <a href="https://hoa-project.net/LICENSE"><img src="https://img.shields.io/packagist/l/hoa/devtools.svg" alt="License" /></a>
</p>
<p align="center">
  Hoa is a <strong>modular</strong>, <strong>extensible</strong> and
  <strong>structured</strong> set of PHP libraries.<br />
  Moreover, Hoa aims at being a bridge between industrial and research worlds.
</p>

# Hoa\Devtools

[![Help on IRC](https://img.shields.io/badge/help-%23hoaproject-ff0066.svg)](https://webchat.freenode.net/?channels=#hoaproject)
[![Help on Gitter](https://img.shields.io/badge/help-gitter-ff0066.svg)](https://gitter.im/hoaproject/central)
[![Documentation](https://img.shields.io/badge/documentation-hack_book-ff0066.svg)](https://central.hoa-project.net/Documentation/Library/Devtools)
[![Board](https://img.shields.io/badge/organisation-board-ff0066.svg)](https://waffle.io/hoaproject/devtools)

This library contains several development tools. This is for developers or
maintainers. Sometimes it can be useful to users also, but in particular cases.

[Learn more](https://central.hoa-project.net/Documentation/Library/Devtools).

## Installation

With [Composer](https://getcomposer.org/), to include this library into
your dependencies, you need to
require [`hoa/devtools`](https://packagist.org/packages/hoa/devtools):

```sh
$ composer require hoa/devtools '~1.0'
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

We propose a quick overview of some commands.

### `cs`

Fix coding style of Hoa. It embraces [PSR-1](http://www.php-fig.org/psr/psr-1/)
and [PSR-2](http://www.php-fig.org/psr/psr-2/), in addition to some extra
fixers.

```sh
$ hoa devtools:cs --diff .
```

Requires [PHP-CS-Fixer](https://github.com/FriendsOfPHP/PHP-CS-Fixer) to be
installed.

### `dependency`

Check the dependencies of a specific library.

```sh
$ hoa devtools:dependency console
Dependency for the library Console:
    • hoa/consistency => …
    • hoa/event => …
    • hoa/exception => …
    • hoa/file => …
    • hoa/stream => …
    • hoa/ustring => …
```

### `diagnostic`

Help to write (and send) a diagnostic report. Very useful to help users.

```sh
$ hoa devtools:diagnostic --section bin
[bin]
self = "…/hoa"
hoa = "/usr/local/lib/Hoa.central"
php_dir = "…/bin"
php = "…/bin/php"
```

### `documentation`

Generate the documentation of all libraries installed:

```sh
$ hoa devtools:documentation
```

### `expandflexentities`

Expand entity names to ease auto-completion in IDE.

```sh
$ hoa devtools:expandflexentities
```

### `paste`

Paste something somewhere (by default, on `paste.hoa-project.net`).

```sh
$ echo 'foobar' | hoa devtools:paste
http://paste.hoa-project.net:80/<id>
```

### `requiresnapshot`

Check if a library requires a new snapshot or not.

```sh
$ hoa devtools:requiresnapshot console
A snapshot is required, since … days (tag …, … commits to publish)!
```

If yes, you are probably likely to use `hoa devtools:snapshot`.

### `state`

Get the state of a library.

```sh
$ hoa devtools:state core
finalized
```

## Documentation

The
[hack book of `Hoa\Devtools`](https://central.hoa-project.net/Documentation/Library/Devtools)
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
