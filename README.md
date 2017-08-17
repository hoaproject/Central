<p align="center">
  <img src="https://static.hoa-project.net/Image/Hoa.svg" alt="Hoa" width="250px" />
</p>

---

<p align="center">
  <a href="https://travis-ci.org/hoaproject/Option"><img src="https://img.shields.io/travis/hoaproject/Option/master.svg" alt="Build status" /></a>
  <a href="https://coveralls.io/github/hoaproject/Option?branch=master"><img src="https://img.shields.io/coveralls/hoaproject/Option/master.svg" alt="Code coverage" /></a>
  <a href="https://packagist.org/packages/hoa/option"><img src="https://img.shields.io/packagist/dt/hoa/option.svg" alt="Packagist" /></a>
  <a href="https://hoa-project.net/LICENSE"><img src="https://img.shields.io/packagist/l/hoa/option.svg" alt="License" /></a>
</p>
<p align="center">
  Hoa is a <strong>modular</strong>, <strong>extensible</strong> and
  <strong>structured</strong> set of PHP libraries.<br />
  Moreover, Hoa aims at being a bridge between industrial and research worlds.
</p>

# Hoa\Option

[![Help on IRC](https://img.shields.io/badge/help-%23hoaproject-ff0066.svg)](https://webchat.freenode.net/?channels=#hoaproject)
[![Help on Gitter](https://img.shields.io/badge/help-gitter-ff0066.svg)](https://gitter.im/hoaproject/central)
[![Documentation](https://img.shields.io/badge/documentation-hack_book-ff0066.svg)](https://central.hoa-project.net/Documentation/Library/Option)
[![Board](https://img.shields.io/badge/organisation-board-ff0066.svg)](https://waffle.io/hoaproject/option)

This library is an implementation of the
famous
[`Option` polymorphic type](https://en.wikipedia.org/wiki/Option_type)
(also called `Maybe`). An `Option` represents an optional value, either
there is `Some` value, or there is `None` which is the equivalent of
`null`. This is a convenient and safe way to manipulate an optional
value.

[Learn more](https://central.hoa-project.net/Documentation/Library/Option).

## Installation

With [Composer](https://getcomposer.org/), to include this library into
your dependencies, you need to
require [`hoa/option`](https://packagist.org/packages/hoa/option):

```sh
$ composer require hoa/option '~1.0'
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

The following examples illustrate how to use the `Hoa\Option\Option` class.

### Build an optional value

There are two static methods to allocate an optional value:

  * `Hoa\Option\Option::some($value)` for some value,
  * `Hoa\Option\Option::none()` for no value.

Two functional aliases exist, respectively:

  * `Hoa\Option\Some($value)`, and
  * `Hoa\Option\None()`.

In the next examples, it is assumed that `use function
Hoa\Option\{Some, None}` is declared, so that we can use `Some` and
`None` directly.

### Basic operations

The `isSome` and `isNone` methods respectively return `true` if the
option contains some value or none:

```php
$x = Some(42);
$y = None();

assert($x->isSome());
assert($y->isNone());
```

The `unwrap` method returns the contained value if there is some, or
throws a `RuntimeException` if there is none:

```php
assert(Some(42)->unwrap() === 42);
assert(None()->unwrap()); // will throw a `RuntimeException`.
```

In general, because of the unexpected exception, its use is
discouraged. Prefer to use either: `expect`, `unwrapOr`, `isSome`, or
`isNone` for instance.

One common mistake is to think that it is required to extract/unwrap
the value from the option. Actually, the `Hoa\Option\Option` API is
designed to always manipulate an option without having the need to
extract its contained value. New options can be constructed or mapped,
see next sections.

The `unwrap` method can throw a `RuntimeException` with a default
message. To have a custom message, please use the `expect` method:

```php
$x = None();

assert($x->expect('Damn…') === 42);
```

The `unwrapOr` method returns the contained value if there is some, or
a default value if none:

```php
$x = Some(42);
$y = None();

assert($x->unwrapOr(153) === 42);
assert($y->unwrapOr(153) === 153);
```

The `unwrapOrElse` method returns the contained value if there is
some, or computes a default value from the given callable if none:

```php
$x = Some(42);
$y = None();

$else = function (): int { return 153; };

assert($x->unwrapOrElse($else) === 42);
assert($y->unwrapOrElse($else) === 153);
```

### Mappers

Mappers transform an option into another option by applying a callable
to the contained value if some.

```php
$x = Some('Hello, World!');
$y = None();

assert($x->map('strlen') == Some(13));
assert($x->map('strlen') == None());
```

The `mapOr` mapper transform an option into another option but use a
default value if there is no contained value:

```php
$x = None();

assert($x->mapOr('strlen', 0) == Some(0));
```

The result of `mapOr` is always an option with some value.

The `mapOrElse` mapper is similar to `mapOr` but computes the default
value from a callable:

```php
$x    = None();
$else = function (): int { return 0; };

assert($x->mapOrElse('strlen', $else) == Some(0));
```

### Boolean operations

It is possible to apply boolean operations on options. The `and`
method returns a none option if the current option has no value, otherwise it
returns the right option:

```php
assert(Some(42)->and(Some(153)) == Some(153));
assert(Some(42)->and(None())    == None());
assert(None()->and(Some(153))   == None());
```

The `andThen` method returns a none option if the current option has no value,
otherwise it returns a new option computed by a callable. Some
languages call this operation `flatmap`.

```php
$square = function (int $x): Option {
    return Some($x * $x);
};
$nop = function (): Option {
    return None();
};

assert(Some(2)->andThen($square)->andThen($square) == Some(16));
assert(Some(2)->andThen($nop)->andThen($square)    == None());
```

The `or` method returns the current option if it has some value, otherwise it
returns the right option:

```php
assert(Some(42)->or(Some(153)) == Some(42));
assert(None()->or(Some(153))   == Some(153));
```

Finally the `orElse` option returns the option if it has some value,
otherwise it returns a new option computed by a callable:

```php
$somebody = function (): Option {
    return Some('somebody');
};

assert(None()->orElse($somebody) == Some('somebody'));
```

## Documentation

The
[hack book of `Hoa\Option`](https://central.hoa-project.net/Documentation/Library/Option)
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
