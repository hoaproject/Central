<img src="https://static.hoa-project.net/Image/Hoa.svg" alt="Hoa" width="280px" />

[![Build status](https://img.shields.io/travis/hoaproject/heap/master.svg)](https://travis-ci.org/hoaproject/heap)
[![Coverage](https://img.shields.io/coveralls/hoaproject/heap/master.svg)](https://coveralls.io/github/hoaproject/heap?branch=master)
[![Packagist](https://img.shields.io/packagist/dt/hoa/heap.svg)](https://packagist.org/packages/hoa/heap)
[![License](https://img.shields.io/packagist/l/hoa/heap.svg)](https://hoa-project.net/LICENSE)

Hoa is a **modular**, **extensible** and **structured** set of PHP libraries.
Moreover, Hoa aims at being a bridge between industrial and research worlds.

# Hoa\Heap

[![Help on IRC](https://img.shields.io/badge/help-%23hoaproject-ff0066.svg)](https://webchat.freenode.net/?channels=#hoaproject)
[![Help on Gitter](https://img.shields.io/badge/help-gitter-ff0066.svg)](https://gitter.im/hoaproject/central)
[![Documentation](https://img.shields.io/badge/documentation-hack_book-ff0066.svg)](https://hoa-project.net/Literature/Hack/heap.html)
[![Board](https://img.shields.io/badge/organisation-board-ff0066.svg)](https://waffle.io/hoaproject/heap)

This library provides a set of advanced Heap can support *Scalar*, *Array*, *Object* or *Closure*
as item and not only Integer, as ordinal does.
The order of heap depends of priority parameter.

`Hoa\Heap\Min` and `Hoa\Heap\Max` class interpret priority by comparing items numerically.
But you are free to implement your own class if you want a different sort algorithm.

## :warning: Warning
The default iteration process do not dequeue the Heap as common usage.
You must use Generator methods `top` or `pop` for iterate on with remove item from heap.

## Installation

With [Composer](http://getcomposer.org/), to include this library into your
dependencies, you need to require
[`hoa/heap`](https://packagist.org/packages/hoa/heap):

```sh
$ composer require hoa/heap '~0.0'
```

For more installation procedures, please read [the Source
page](http://hoa-project.net/Source.html).

## Testing

Considering the library has been installed with Composer, the following
commands will run the test suites:

```sh
$ composer install
$ vendor/bin/hoa test:run
```

For more information, please consult the [contributor
guide](https://hoa-project.net/Literature/Contributor/Guide.html).

## Quick usage

As a quick overview, we propose to see a simple use case with
 a `Phone number`, This phone number must be sent to three methods 
 in a strict order, `Check`, `Transform`, `Format`.
 
 Let's assume we don't have access to iteration process.
 But we can sort in which orders our methods must be called for respect our process. 

### Register Callback

In first, we will create our callbacks process.

```php
require_once dirname(dirname(__DIR__)) . '/vendor/autoload.php';

// First method used to check if phone number is correct.
$check = function($phone) {
    if (1 !== preg_match('/^\+?[0-9]+$/', $phone)) {
        throw new \Exception('Phone number not conform.');
    }

    return $phone;
};

// Second method used to convert number into object.
$transform = function($phone) {
    return (object)[
        'prefix'  => '+33',
        'country' => 'France',
        'number'  => $phone,
    ];
};

// Third method used to display formatted number.
$format = function(\StdClass $phone) {
    return $phone->prefix
        . ' '
        . wordwrap($phone->number, 3, ' ', true)
    ;
};
```

### Create and fill Heap

Creation of our Heap with minimum priority Ascending ( lower called first ).

```php
$heap = new \Hoa\Heap\Min();

// Insert the callback method with the priority argument used for order Heap.
$heap->insert($transform, 20);
$heap->insert($check, 10);
$heap->insert($format, 30);


// Show the number of item in Heap.
var_dump(
    $heap->count()
);

/**
 * Will output:
 *     int(3)
 */
```

### Iteration Heap

Then we can iterate on our `Heap` with assurance of correct call order.
Spread your number into closure and have process mutation expected.

```php
// Phone number as expected by first callback.
$number = '123001234';

foreach ($heap as $closure) {
    try {
        // Mutation of number by closure, execute in the priority order expected.
        $number = $closure($number);
    } catch (\Exception $e) {
        break;
    }
}

// Finally, we can display our formatted number.
var_dump($number);

/**
 * Will output:
 *     string(15) "+33 123 001 234"
 */

```

## Documentation

The [hack book of
`Hoa\heap`](https://hoa-project.net/Literature/Hack/heap.html) contains
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

There are no related project registered, Let us know by opening issue
if you use it and want be listed!
