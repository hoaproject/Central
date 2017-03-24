<p align="center">
  <img src="https://static.hoa-project.net/Image/Hoa.svg" alt="Hoa" width="250px" />
</p>

---

<p align="center">
  <a href="https://travis-ci.org/hoaproject/Dispatcher"><img src="https://img.shields.io/travis/hoaproject/Dispatcher/master.svg" alt="Build status" /></a>
  <a href="https://coveralls.io/github/hoaproject/Dispatcher?branch=master"><img src="https://img.shields.io/coveralls/hoaproject/Dispatcher/master.svg" alt="Code coverage" /></a>
  <a href="https://packagist.org/packages/hoa/dispatcher"><img src="https://img.shields.io/packagist/dt/hoa/dispatcher.svg" alt="Packagist" /></a>
  <a href="https://hoa-project.net/LICENSE"><img src="https://img.shields.io/packagist/l/hoa/dispatcher.svg" alt="License" /></a>
</p>
<p align="center">
  Hoa is a <strong>modular</strong>, <strong>extensible</strong> and
  <strong>structured</strong> set of PHP libraries.<br />
  Moreover, Hoa aims at being a bridge between industrial and research worlds.
</p>

# Hoa\Dispatcher

[![Help on IRC](https://img.shields.io/badge/help-%23hoaproject-ff0066.svg)](https://webchat.freenode.net/?channels=#hoaproject)
[![Help on Gitter](https://img.shields.io/badge/help-gitter-ff0066.svg)](https://gitter.im/hoaproject/central)
[![Documentation](https://img.shields.io/badge/documentation-hack_book-ff0066.svg)](https://central.hoa-project.net/Documentation/Library/Dispatcher)
[![Board](https://img.shields.io/badge/organisation-board-ff0066.svg)](https://waffle.io/hoaproject/dispatcher)

This library dispatches a task defined by some data on a callable, or with the
appropriated vocabulary, on a controller and an action. It is often used in
conjunction with the [`Hoa\Router`
library](https://central.hoa-project.net/Resource/Library/Router) and the
[`Hoa\View` library](https://central.hoa-project.net/Resource/Library/View).

The link between libraries and the application is represented by a kit which
aggregates all important data, such as the dispatcher, the router, the view and
data associated to the view.

[Learn more](https://central.hoa-project.net/Documentation/Library/Dispatcher).

## Installation

With [Composer](https://getcomposer.org/), to include this library into
your dependencies, you need to
require [`hoa/dispatcher`](https://packagist.org/packages/hoa/dispatcher):

```sh
$ composer require hoa/dispatcher '~1.0'
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

We propose a quick overview of the basic dispatcher represented by the class
`Hoa\Dispatcher\Basic` which is able to dispatch a task on three kinds of
callables:

  * lambda function (as a controller, no action);
  * function (as a controller, no action);
  * class and method (respectively as a controller and an action).

We will focus on the last kind with this following example:

```php
$router = new Hoa\Router\Http();
$router->get('w', '/(?<controller>[^/]+)/(?<action>\w+)\.html');

$dispatcher = new Hoa\Dispatcher\Basic();
$dispatcher->dispatch($router);
```

By default, the controller will be `Application\Controller\<Controller>` and the
action will be `<Action>Action`. Thus, for the request `/Foo/Bar.html`, we will
call `Application\Controller\Foo::BarAction`.

It is possible to specify a different controller and action names if the request
is asynchronous. By default, only the action name is different with the value
`<Action>ActionAsync`.

With all kinds of callables, the basic dispatcher will distribute captured data
(with the `(?<name>…)` [PCRE](https://pcre.org/) syntax) on callable arguments
where the `name` matches the argument name. For example, with a rule such as
`'/hello_(?<nick>\w+)'`, if the callable has an argument named `$nick`, it will
receive the value `gordon` for the request `/hello_gordon`.

The kit is reachable through the `$_this` argument or `$this` variable if the
controller is a class that extends `Hoa\Dispatcher\Kit`. The kit propose four
elementary attributes, which are: `router`, `dispatcher`, `view` and `data`.

## Documentation

The
[hack book of `Hoa\Dispatcher`](https://central.hoa-project.net/Documentation/Library/Dispatcher)
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
