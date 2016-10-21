<p align="center">
  <img src="https://static.hoa-project.net/Image/Hoa.svg" alt="Hoa" width="250px" />
</p>

---

<p align="center">
  <a href="https://travis-ci.org/hoaproject/router"><img src="https://img.shields.io/travis/hoaproject/router/master.svg" alt="Build status" /></a>
  <a href="https://coveralls.io/github/hoaproject/router?branch=master"><img src="https://img.shields.io/coveralls/hoaproject/router/master.svg" alt="Code coverage" /></a>
  <a href="https://packagist.org/packages/hoa/router"><img src="https://img.shields.io/packagist/dt/hoa/router.svg" alt="Packagist" /></a>
  <a href="https://hoa-project.net/LICENSE"><img src="https://img.shields.io/packagist/l/hoa/router.svg" alt="License" /></a>
</p>
<p align="center">
  Hoa is a <strong>modular</strong>, <strong>extensible</strong> and
  <strong>structured</strong> set of PHP libraries.<br />
  Moreover, Hoa aims at being a bridge between industrial and research worlds.
</p>

# Hoa\Router

[![Help on IRC](https://img.shields.io/badge/help-%23hoaproject-ff0066.svg)](https://webchat.freenode.net/?channels=#hoaproject)
[![Help on Gitter](https://img.shields.io/badge/help-gitter-ff0066.svg)](https://gitter.im/hoaproject/central)
[![Documentation](https://img.shields.io/badge/documentation-hack_book-ff0066.svg)](https://central.hoa-project.net/Documentation/Library/Router)
[![Board](https://img.shields.io/badge/organisation-board-ff0066.svg)](https://waffle.io/hoaproject/router)

This library allows to find an appropriated route and extracts data from a
request. Conversely, given a route and data, this library is able to build a
request.

For now, we have two routers: HTTP (routes understand URI and subdomains) and
CLI (routes understand a full command-line).

[Learn more](https://central.hoa-project.net/Documentation/Library/Router).

## Installation

With [Composer](https://getcomposer.org/), to include this library into
your dependencies, you need to
require [`hoa/router`](https://packagist.org/packages/hoa/router):

```sh
$ composer require hoa/router '~3.0'
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

We propose a quick overview of two usages: in a HTTP context and in a CLI
context.

### HTTP

We consider the following routes:

  * `/hello`, only accessible with the `GET` and `POST` method;
  * `/bye`, only accessible with the `GET` method;
  * `/hello_<nick>` only accessible with the `GET` method.

There are different ways to declare routes but the more usual is as follows:

```php
$router = new Hoa\Router\Http();
$router
    ->get('u', '/hello', function () {
        echo 'world!', "\n";
    })
    ->post('v', '/hello', function (Array $_request) {
        echo $_request['a'] + $_request['b'], "\n";
    })
    ->get('w', '/bye', function () {
        echo 'ohh :-(', "\n";
    })
    ->get('x', '/hello_(?<nick>\w+)', function ($nick) {
        echo 'Welcome ', ucfirst($nick), '!', "\n";
    });
```

We can use a basic dispatcher to call automatically the associated callable of
the appropriated rule:

```php
$dispatcher = new Hoa\Dispatcher\Basic();
$dispatcher->dispatch($router);
```

Now, we will use [cURL](http://curl.haxx.se/) to test our program that listens
on `127.0.0.1:8888`:

```sh
$ curl 127.0.0.1:8888/hello
world!
$ curl -X POST -d a=3\&b=39 127.0.0.1:8888/hello
42
$ curl 127.0.0.1:8888/bye
ohh :-(
$ curl -X POST 127.0.0.1:8888/bye
// error
$ curl 127.0.0.1:8888/hello_gordon
Welcome Gordon!
$ curl 127.0.0.1:8888/hello_alyx
Welcome Alyx!
```

This simple API hides a modular mechanism that can be foreseen by typing
`print_r($router->getTheRule())`.

To unroute, i.e. make the opposite operation, we can do this:

```php
var_dump($router->unroute('x', array('nick' => 'gordon')));
// string(13) "/hello_gordon"
```

### CLI

We would like to recognize the following route `[<group>:]?<subcommand>
<options>` in the `Router.php` file:

```php
$router = new Hoa\Router\Cli();
$router->get(
    'g',
    '(?<group>\w+):(?<subcommand>\w+)(?<options>.*?)'
    function ($group, $subcommand, $options) {
        echo
            'Group     : ', $group, "\n",
            'Subcommand: ', $subcommand, "\n",
            'Options   : ', trim($options), "\n";
    }
);
```

We can use a basic dispatcher to call automatically the associated callable:

```php
$dispatcher = new Hoa\Dispatcher\Basic();
$dispatcher->dispatch($router);
```

And now, testing time:

```sh
$ php Router.php foo:bar --some options
Group     : foo
Subcommand: bar
Options   : --some options
```

The use of the [`Hoa\Console`
library](https://central.hoa-project.net/Resource/Library/Console) would be a
good idea to interprete the options and getting some comfortable services for
the terminal.

## Documentation

The
[hack book of `Hoa\Router`](https://central.hoa-project.net/Documentation/Library/Router)
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
