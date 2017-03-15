<p align="center">
  <img src="https://static.hoa-project.net/Image/Hoa.svg" alt="Hoa" width="250px" />
</p>

---

<p align="center">
  <a href="https://travis-ci.org/hoaproject/Mime"><img src="https://img.shields.io/travis/hoaproject/Mime/master.svg" alt="Build status" /></a>
  <a href="https://coveralls.io/github/hoaproject/Mime?branch=master"><img src="https://img.shields.io/coveralls/hoaproject/Mime/master.svg" alt="Code coverage" /></a>
  <a href="https://packagist.org/packages/hoa/mime"><img src="https://img.shields.io/packagist/dt/hoa/mime.svg" alt="Packagist" /></a>
  <a href="https://hoa-project.net/LICENSE"><img src="https://img.shields.io/packagist/l/hoa/mime.svg" alt="License" /></a>
</p>
<p align="center">
  Hoa is a <strong>modular</strong>, <strong>extensible</strong> and
  <strong>structured</strong> set of PHP libraries.<br />
  Moreover, Hoa aims at being a bridge between industrial and research worlds.
</p>

# Hoa\Mime

[![Help on IRC](https://img.shields.io/badge/help-%23hoaproject-ff0066.svg)](https://webchat.freenode.net/?channels=#hoaproject)
[![Help on Gitter](https://img.shields.io/badge/help-gitter-ff0066.svg)](https://gitter.im/hoaproject/central)
[![Documentation](https://img.shields.io/badge/documentation-hack_book-ff0066.svg)](https://central.hoa-project.net/Documentation/Library/Mime)
[![Board](https://img.shields.io/badge/organisation-board-ff0066.svg)](https://waffle.io/hoaproject/mime)

This library allows to manipulate a MIME types database and get some related
informations about streams.

[Learn more](https://central.hoa-project.net/Documentation/Library/Mime).

## Installation

With [Composer](https://getcomposer.org/), to include this library into
your dependencies, you need to
require [`hoa/mime`](https://packagist.org/packages/hoa/mime):

```sh
$ composer require hoa/mime '~3.0'
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

As a quick overview, we will see how to get general and stream-related
informations.

### General informations

All we need is static methods `Hoa\Mime\Mime::getExtensionsFromMime` to get
extensions from a type and `Hoa\Mime\Mime::getMimeFromExtension` to get type
from an extension:

```php
print_r(Hoa\Mime\Mime::getExtensionsFromMime('text/html'));

/**
 * Will output:
 *     Array
 *     (
 *         [0] => html
 *         [1] => htm
 *     )
 */

var_dump(Hoa\Mime\Mime::getMimeFromExtension('webm'));

/**
 * Will output:
 *     string(10) "video/webm"
 */
```

By default, `Hoa\Mime\Mime` uses the `hoa://Library/Mime/Mime.types` file as
database. We can change this behavior by calling the `Hoa\Mime\Mime::compute`
before any computations:

```php
Hoa\Mime\Mime::compute('/etc/mime.types');
```

### Stream-related informations

By instanciating the `Hoa\Mime\Mime` class with a stream, we are able to get
some informations about the stream, such as its extension, others extensions,
type, etc. Thus:

```php
$type = new Hoa\Mime\Mime(new Hoa\File\Read('index.html'));

var_dump(
    $type->getExtension(),
    $type->getOtherExtensions(),
    $type->getMime(),
    $type->isExperimental()
);

/**
 * Will output:
 *     string(4) "html"
 *     array(1) {
 *       [0]=>
 *       string(3) "htm"
 *     }
 *     string(9) "text/html"
 *     bool(false)
 */
```

## Documentation

The
[hack book of `Hoa\Mime`](https://central.hoa-project.net/Documentation/Library/Mime) contains
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

## Related projects

The following projects are using this library:

  * [E-Conf](https://gitlab.com/econf/econf), E-Conf is a Conference
    Management System,
  * [sabre/katana](https://github.com/fruux/sabre-katana/), A contact,
    calendar, task list and file server.
