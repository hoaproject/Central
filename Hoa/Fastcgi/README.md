<p align="center">
  <img src="https://static.hoa-project.net/Image/Hoa.svg" alt="Hoa" width="250px" />
</p>

---

<p align="center">
  <a href="https://travis-ci.org/hoaproject/fastcgi"><img src="https://img.shields.io/travis/hoaproject/fastcgi/master.svg" alt="Build status" /></a>
  <a href="https://coveralls.io/github/hoaproject/fastcgi?branch=master"><img src="https://img.shields.io/coveralls/hoaproject/fastcgi/master.svg" alt="Code coverage" /></a>
  <a href="https://packagist.org/packages/hoa/fastcgi"><img src="https://img.shields.io/packagist/dt/hoa/fastcgi.svg" alt="Packagist" /></a>
  <a href="https://hoa-project.net/LICENSE"><img src="https://img.shields.io/packagist/l/hoa/fastcgi.svg" alt="License" /></a>
</p>
<p align="center">
  Hoa is a <strong>modular</strong>, <strong>extensible</strong> and
  <strong>structured</strong> set of PHP libraries.<br />
  Moreover, Hoa aims at being a bridge between industrial and research worlds.
</p>

# Hoa\Fastcgi

[![Help on IRC](https://img.shields.io/badge/help-%23hoaproject-ff0066.svg)](https://webchat.freenode.net/?channels=#hoaproject)
[![Help on Gitter](https://img.shields.io/badge/help-gitter-ff0066.svg)](https://gitter.im/hoaproject/central)
[![Documentation](https://img.shields.io/badge/documentation-hack_book-ff0066.svg)](https://central.hoa-project.net/Documentation/Library/Fastcgi)
[![Board](https://img.shields.io/badge/organisation-board-ff0066.svg)](https://waffle.io/hoaproject/fastcgi)

This library allows to manipulate the [FastCGI](http://fastcgi.com/) protocol,
which ensures the communication between a HTTP server and an external program
(such as PHP).

[Learn more](https://central.hoa-project.net/Documentation/Library/Fastcgi).

## Installation

With [Composer](https://getcomposer.org/), to include this library into
your dependencies, you need to
require [`hoa/fastcgi`](https://packagist.org/packages/hoa/fastcgi):

```sh
$ composer require hoa/fastcgi '~3.0'
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

As a quick overview, we propose to execute a PHP file through the FastCGI
protocol directly.

Before starting, we need to know that PHP proposes two tools that support
FastCGI: `php-cgi` and `php-fpm` (for
[FastCGI Process Manager](http://php.net/install.fpm)). We will use `php-cgi` in
local with the standard port `9000` in TCP:

```sh
$ php-cgi -b 127.0.0.1:9000
```

First, we write the `Echo.php` file, the one we are likely to execute:

```php
<?php

echo 'foobar';
```

Second, we need to open a connexion to the FastCGI server and send a query with
the following headers:

  * `SCRIPT_FILENAME` which represents the absolute path to the PHP file to
    execute;
  * `REQUEST_METHOD` which represents the HTTP method among `GET`, `POST`,
    `PUT`, `DELETE` etc.;
  * `REQUEST_URI` which represents the identifier of the resource we are trying
    to access.

Thus:

```php
$fastcgi = new Hoa\Fastcgi\Responder(
    new Hoa\Socket\Client('tcp://127.0.0.1:9000')
);
var_dump($fastcgi->send([
    'REQUEST_METHOD'  => 'GET',
    'REQUEST_URI'     => '/',
    'SCRIPT_FILENAME' => __DIR__ . DS . 'Echo.php'
]));
// string(6) "foobar"
```

We can get the headers from the executed file by calling the
`Hoa\Fastcgi\Responder::getResponseHeaders` method.

This is a good and fast way to execute PHP files (or other programs that support
FastCGI) without worry about binaries location, sub-shells calls, errors
handling etc.

## Documentation

The
[hack book of `Hoa\Fastcgi`](https://central.hoa-project.net/Documentation/Library/Fastcgi)
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
