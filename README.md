![Hoa](http://static.hoa-project.net/Image/Hoa_small.png)

Hoa is a **modular**, **extensible** and **structured** set of PHP libraries.
Moreover, Hoa aims at being a bridge between industrial and research worlds.

# Hoa\Bench ![state](http://central.hoa-project.net/State/Bench)

This library allows to analyze performance of algorithms or programs by placing
some “marks” in the code. Furthermore, this library provides some
[DTrace](http://dtrace.org/guide/) programs.

## Installation

With [Composer](http://getcomposer.org/), to include this library into your
dependencies, you need to require
[`hoa/bench`](https://packagist.org/packages/hoa/bench):

```json
{
    "require": {
        "hoa/bench": "~2.0"
    }
}
```

Please, read the website to [get more informations about how to
install](http://hoa-project.net/Source.html).

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
$ exed=`hoa core:resolve hoa://Library/Bench/Dtrace/Execution.d --no-verbose`
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

Different documentations can be found on the website:
[http://hoa-project.net/](http://hoa-project.net/).

## License

Hoa is under the New BSD License (BSD-3-Clause). Please, see
[`LICENSE`](http://hoa-project.net/LICENSE).
