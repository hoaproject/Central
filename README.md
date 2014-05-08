![Hoa](http://static.hoa-project.net/Image/Hoa_small.png)

Hoa is a **modular**, **extensible** and **structured** set of PHP libraries.
Moreover, Hoa aims at being a bridge between industrial and research worlds.

# Hoa\Test ![state](http://central.hoa-project.net/State/Test)

This library provides tools to create and run tests of Hoa.

In each library, a `Test/` directory contains test suites. So far, only unit
tests are supported. They are written with [atoum](http://atoum.org/).

## Quick usage

As a quick overview, we see how to write and execute a unit test for the
`Hoa\Foo` library.

First, let's create the `Hoa/Foo/Test/Unit/Bar.php` file, that contains:

```php
namespace Hoa\Foo\Test\Unit {

class Bar extends \Hoa\Test\Unit\Suite {

    public function caseBaz ( ) {

        $this->integer(7 * 3 * 2)->isEqualTo(42);
    }
}

}
```

A class represents a test suite (that extends the `Hoa\Test\Unit\Suite` class).
A method represents a test case, where its name must be prefixed by `case`.

Second, and last, we will use the `hoa test:run` script to execute this test. We
will execute all the test suites of a library with the `--library` option. Thus:

```sh
$ hoa test:run --library Foo
```

## Environment variables

  * `HOA_ATOUM_BIN`: this variable represents the path to the atoum binary,
  * `HOA_ATOUM_PRASPEL_EXTENSION`: this variable indicates the root of the
    `Atoum\PraspelExtension` library (do not forget the trailing `/`!).

## Documentation

Different documentations can be found on the website:
[http://hoa-project.net/](http://hoa-project.net/).

## License

Hoa is under the New BSD License (BSD-3-Clause). Please, see
[`LICENSE`](http://hoa-project.net/LICENSE).
