<p align="center">
  <img src="https://static.hoa-project.net/Image/Hoa.svg" alt="Hoa" width="250px" />
</p>

---

<p align="center">
  <a href="https://travis-ci.org/hoaproject/worker"><img src="https://img.shields.io/travis/hoaproject/worker/master.svg" alt="Build status" /></a>
  <a href="https://coveralls.io/github/hoaproject/worker?branch=master"><img src="https://img.shields.io/coveralls/hoaproject/worker/master.svg" alt="Code coverage" /></a>
  <a href="https://packagist.org/packages/hoa/worker"><img src="https://img.shields.io/packagist/dt/hoa/worker.svg" alt="Packagist" /></a>
  <a href="https://hoa-project.net/LICENSE"><img src="https://img.shields.io/packagist/l/hoa/worker.svg" alt="License" /></a>
</p>
<p align="center">
  Hoa is a <strong>modular</strong>, <strong>extensible</strong> and
  <strong>structured</strong> set of PHP libraries.<br />
  Moreover, Hoa aims at being a bridge between industrial and research worlds.
</p>

# Hoa\Worker

[![Help on IRC](https://img.shields.io/badge/help-%23hoaproject-ff0066.svg)](https://webchat.freenode.net/?channels=#hoaproject)
[![Help on Gitter](https://img.shields.io/badge/help-gitter-ff0066.svg)](https://gitter.im/hoaproject/central)
[![Documentation](https://img.shields.io/badge/documentation-hack_book-ff0066.svg)](https://central.hoa-project.net/Documentation/Library/Worker)
[![Board](https://img.shields.io/badge/organisation-board-ff0066.svg)](https://waffle.io/hoaproject/worker)

This library allows to create shared workers in order to lift out some heavy and
blocking tasks.

[Learn more](https://central.hoa-project.net/Documentation/Library/Worker).

## Installation

With [Composer](https://getcomposer.org/), to include this library into
your dependencies, you need to
require [`hoa/worker`](https://packagist.org/packages/hoa/worker):

```sh
$ composer require hoa/worker '~3.0'
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

As a quick overview, we see how to create a worker and how to communicate with
it.

### Create a worker

First, we need to register the worker (i.e. creating a `.wid` file), called
`demorker`:

```php
if (false === Hoa\Worker\Run::widExists('demorker')) {
    Hoa\Worker\Run::register('demorker', 'tcp://127.0.0.1:123456');
}
```

Then, we start the worker (with a password) and we listen to messages:

```php
$worker = new Hoa\Worker\Backend\Shared('demorker', 'iamapassword');
$worker->on('message', function(Hoa\Event\Bucket $bucket) {
    $data = $bucket->getData();
    // compute $data['message'].
});
$worker->run();
```

The message indicates a task to do (sending an email, create some archives,
update the database, send some notifications…).

We are also able to manage all workers from a CLI.

### Communicate with a worker

Second, since the worker is running, we can communicate with it from our
application. Thus:

```php
$worker = new Hoa\Worker\Shared('demorker');
$worker->postMessage('mail gordon@freeman.hl Hello Gordon!');
```

We are able to send everything that can be serialized.

## Documentation

The
[hack book of `Hoa\Worker`](https://central.hoa-project.net/Documentation/Library/Worker)
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
