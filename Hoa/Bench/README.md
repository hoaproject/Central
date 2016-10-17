<p align="center">
  <img src="https://static.hoa-project.net/Image/Hoa.svg" alt="Hoa" width="250px" />
</p>

---

<p align="center">
  <a href="https://travis-ci.org/hoaproject/bench"><img src="https://img.shields.io/travis/hoaproject/bench/master.svg" alt="Build status" /></a>
  <a href="https://coveralls.io/github/hoaproject/bench?branch=master"><img src="https://img.shields.io/coveralls/hoaproject/bench/master.svg" alt="Code coverage" /></a>
  <a href="https://packagist.org/packages/hoa/bench"><img src="https://img.shields.io/packagist/dt/hoa/bench.svg" alt="Packagist" /></a>
  <a href="https://hoa-project.net/LICENSE"><img src="https://img.shields.io/packagist/l/hoa/bench.svg" alt="License" /></a>
</p>
<p align="center">
  Hoa is a <strong>modular</strong>, <strong>extensible</strong> and
  <strong>structured</strong> set of PHP libraries.<br />
  Moreover, Hoa aims at being a bridge between industrial and research worlds.
</p>

# Hoa\Bench

[![Help on IRC](https://img.shields.io/badge/help-%23hoaproject-ff0066.svg)](https://webchat.freenode.net/?channels=#hoaproject)
[![Help on Gitter](https://img.shields.io/badge/help-gitter-ff0066.svg)](https://gitter.im/hoaproject/central)
[![Documentation](https://img.shields.io/badge/documentation-hack_book-ff0066.svg)](https://central.hoa-project.net/Documentation/Library/Bench)
[![Board](https://img.shields.io/badge/organisation-board-ff0066.svg)](https://waffle.io/hoaproject/bench)

This library allows to analyze performance of algorithms or programs by placing
some “marks” in the code. Furthermore, this library provides some
[DTrace](http://dtrace.org/guide/) programs.

[Learn more](https://central.hoa-project.net/Documentation/Library/Bench).

## Installation

With [Composer](https://getcomposer.org/), to include this library into
your dependencies, you need to
require [`hoa/bench`](https://packagist.org/packages/hoa/bench):

```sh
$ composer require hoa/bench '~3.0'
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

We propose a quick overview of two usages: The library itself and one DTrace
program.

### Benchmark

All we have to do is to place different marks in the code. A mark can be
started, paused, stopped and reset. The class `Hoa\Bench\Bench` proposes a quick
statistic graph that could be helpful:

```php
$bench = new Hoa\Bench\Bench();

// Start two marks: “one” and “two”.
$bench->one->start();
$bench->two->start();

usleep(50000);

// Stop the mark “two” and start the mark “three”.
$bench->two->stop();
$bench->three->start();

usleep(25000);

// Stop all marks.
$bench->three->stop();
$bench->one->stop();

// Print statistics.
echo $bench;

/**
 * Will output:
 *     __global__  ||||||||||||||||||||||||||||||||||||||||||||||||||||    77ms, 100.0%
 *     one         ||||||||||||||||||||||||||||||||||||||||||||||||||||    77ms,  99.8%
 *     two         ||||||||||||||||||||||||||||||||||                      51ms,  65.9%
 *     three       ||||||||||||||||||                                      26ms,  33.9%
 */
```

More operations are available, such as iterating over all marks, deleting a
mark, filters marks etc.

### DTrace

An interesting DTrace program is `hoa://Library/Bench/Dtrace/Execution.d` that
shows the call trace, errors and exceptions during an execution. For example, if
we consider the `Dtrace.php` file that contains the following code:

```php
<?php

function f() { g(); h(); }
function g() { h();      }
function h() {           }

f();
```

Then, we can run DTrace like this:

```sh
$ exed=`hoa protocol:resolve hoa://Library/Bench/Dtrace/Execution.d`
$ sudo $exed -c "php Dtrace.php"
Request start
     2ms ➜ f()        …/Dtrace.php:007
    37ms   ➜ g()      …/Dtrace.php:003
    26ms     ➜ h()    …/Dtrace.php:004
    28ms     ← h()
    37ms   ← g()
    44ms   ➜ h()      …/Dtrace.php:003
    25ms   ← h()
    30ms ← f()
Request end
```

Another program shows statistics about an execution: Each function that has been
called, how many times, how long the execution has taken etc.

## Documentation

The
[hack book of `Hoa\Bench`](https://central.hoa-project.net/Documentation/Library/Bench)
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

## Related projects

The following projects are using this library:

  * [Symfony Bench Bundle](https://central.hoa-project.net/Resource/Contributions/Symfony/BenchBundle),
    The `Hoa\Bench` Symfony 2 bundle.
