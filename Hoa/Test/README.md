<p align="center">
  <img src="https://static.hoa-project.net/Image/Hoa.svg" alt="Hoa" width="250px" />
</p>

---

<p align="center">
  <a href="https://travis-ci.org/hoaproject/test"><img src="https://img.shields.io/travis/hoaproject/test/master.svg" alt="Build status" /></a>
  <a href="https://coveralls.io/github/hoaproject/test?branch=master"><img src="https://img.shields.io/coveralls/hoaproject/test/master.svg" alt="Code coverage" /></a>
  <a href="https://packagist.org/packages/hoa/test"><img src="https://img.shields.io/packagist/dt/hoa/test.svg" alt="Packagist" /></a>
  <a href="https://hoa-project.net/LICENSE"><img src="https://img.shields.io/packagist/l/hoa/test.svg" alt="License" /></a>
</p>
<p align="center">
  Hoa is a <strong>modular</strong>, <strong>extensible</strong> and
  <strong>structured</strong> set of PHP libraries.<br />
  Moreover, Hoa aims at being a bridge between industrial and research worlds.
</p>

# Hoa\Test

[![Help on IRC](https://img.shields.io/badge/help-%23hoaproject-ff0066.svg)](https://webchat.freenode.net/?channels=#hoaproject)
[![Help on Gitter](https://img.shields.io/badge/help-gitter-ff0066.svg)](https://gitter.im/hoaproject/central)
[![Documentation](https://img.shields.io/badge/documentation-hack_book-ff0066.svg)](https://central.hoa-project.net/Documentation/Library/Test)
[![Board](https://img.shields.io/badge/organisation-board-ff0066.svg)](https://waffle.io/hoaproject/test)

This library provides tools to create and run tests for Hoa libraries.

In each library, a `Test/` directory contains test suites. They are
written with [atoum](http://atoum.org/).

[Learn more](https://central.hoa-project.net/Documentation/Library/Test).

## Installation

With [Composer](https://getcomposer.org/), to include this library into
your dependencies, you need to
require [`hoa/test`](https://packagist.org/packages/hoa/test):

```sh
$ composer require hoa/test '~2.0'
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

As a quick overview, we see how to execute, write and generate unit tests. Let
`Hoa\Foo` be a library.

### Execute tests

To execute some tests, we will use the `hoa test:run` command. We have several
options to select a set of tests:

  * `-f`/`--files` to select files,
  * `-d`/`--directories` to select directories,
  * `-n`/`--namespaces` to select classes in some namespaces,
  * `-l`/`--libraries` to select all tests of some libraries,
  * `-a`/`--all` to select all tests of all libraries.

Most of the time, we will run all tests of a library, and then all the tests of
all libraries. Thus:

```sh
$ hoa test:run --libraries Foo
# do something
$ hoa test:run --all
```

### Manual unit tests

First, let's create the `Hoa/Foo/Test/Unit/Bar.php` file, that contains:

```php
namespace Hoa\Foo\Test\Unit;

class Bar extends \Hoa\Test\Unit\Suite
{
    public function caseBaz()
    {
        $this->integer(7 * 3 * 2)->isEqualTo(42);
    }
}
```

A class represents a test suite (that extends the `Hoa\Test\Unit\Suite` class).
A method represents a test case, where its name must be prefixed by `case`.

The `Hoa\Test` library enables the [Praspel extension for
atoum](http://central.hoa-project.net/Resource/Contributions/Atoum/PraspelExtension).
Consequently, we have the `realdom`, `sample`, `sampleMany` etc. asserters to
automatically generate data.

### Automatically generate unit tests

Thanks to [Praspel](http://central.hoa-project.net/Resource/Library/Praspel), we
are able to automatically generate test suites. Those test suites are compiled
into executable test suites written with atoum's API with the help of the
[Praspel extension for
atoum](http://central.hoa-project.net/Resource/Contributions/Atoum/PraspelExtension).

Let `Hoa\Foo\Baz` be the following class:

```php
namespace Hoa\Foo;

class Baz
{
    /**
     * @requires x: /foo.+ba[rz]/;
     * @ensures  \result: true;
     */
    public function qux()
    {
        // …
    }
}
```

Then, to automatically generate a test suite, we will use the `hoa
test:generate` command. It has the following options:

  * `-c`/`--classes` to generate tests of some classes,
  * `-n`/`--namespaces` to generate tests of all classes in some namespaces,
  * `-d`/`--dry-run` to generate tests but output them instead of save them.

The dry-run mode is very helpful. We encourage you to often generate tests with
this option to see what happens. This option is also helpful when having some
errors.

Thus, to automatically generate tests of the `Hoa\Foo\Baz` class, we will make:

```sh
$ hoa test:generate --classes Hoa.Foo.Baz
```

`Hoa.Foo.Baz` is equivalent to `Hoa\\Foo\\Baz`, it avoids to escape backslashes.
Then to execute this test suite, nothing new:

```sh
$ hoa test:run --libraries Foo
```

or

```sh
$ hoa test:run --directories Test/Praspel/
```

to only run the test suite generated by the Praspel tools.

## Environment variables

  * `HOA_ATOUM_BIN`: This variable represents the path to the atoum binary.

## Documentation

The
[hack book of `Hoa\Test`](https://central.hoa-project.net/Documentation/Library/Test) contains
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
