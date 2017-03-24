<p align="center">
  <img src="https://static.hoa-project.net/Image/Hoa.svg" alt="Hoa" width="250px" />
</p>

---

<p align="center">
  <a href="https://travis-ci.org/hoaproject/Zombie"><img src="https://img.shields.io/travis/hoaproject/Zombie/master.svg" alt="Build status" /></a>
  <a href="https://coveralls.io/github/hoaproject/Zombie?branch=master"><img src="https://img.shields.io/coveralls/hoaproject/Zombie/master.svg" alt="Code coverage" /></a>
  <a href="https://packagist.org/packages/hoa/zombie"><img src="https://img.shields.io/packagist/dt/hoa/zombie.svg" alt="Packagist" /></a>
  <a href="https://hoa-project.net/LICENSE"><img src="https://img.shields.io/packagist/l/hoa/zombie.svg" alt="License" /></a>
</p>
<p align="center">
  Hoa is a <strong>modular</strong>, <strong>extensible</strong> and
  <strong>structured</strong> set of PHP libraries.<br />
  Moreover, Hoa aims at being a bridge between industrial and research worlds.
</p>

# Hoa\Zombie

[![Help on IRC](https://img.shields.io/badge/help-%23hoaproject-ff0066.svg)](https://webchat.freenode.net/?channels=#hoaproject)
[![Help on Gitter](https://img.shields.io/badge/help-gitter-ff0066.svg)](https://gitter.im/hoaproject/central)
[![Documentation](https://img.shields.io/badge/documentation-hack_book-ff0066.svg)](https://central.hoa-project.net/Documentation/Library/Zombie)
[![Board](https://img.shields.io/badge/organisation-board-ff0066.svg)](https://waffle.io/hoaproject/zombie)

This library allows to transform a process into a zombie: not alive, nor dead!

This is possible only if the program is running behind
[PHP-FPM](http://php.net/install.fpm) (which manages processes for us).

[Learn more](https://central.hoa-project.net/Documentation/Library/Zombie).

## Installation

With [Composer](https://getcomposer.org/), to include this library into
your dependencies, you need to
require [`hoa/zombie`](https://packagist.org/packages/hoa/zombie):

```sh
$ composer require hoa/zombie '~3.0'
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

To create a zombie, all we have to do is to call the `Hoa\Zombie\Zombie::fork`
method. To kill a zombie, we have the choice between different weapons:

  * `Hoa\Zombie\Zombie::decapitate`, *ziip*;
  * `Hoa\Zombie\Zombie::bludgeon`, *tap tap*;
  * `Hoa\Zombie\Zombie::burn`, if you are cold;
  * `Hoa\Zombie\Zombie::explode`, *boom*;
  * `Hoa\Zombie\Zombie::cutOff`, sausage?

All these methods have been proven. Thus:

```php
// I'm alive!
Hoa\Zombie\Zombie::fork();
// I'm a zombie!
Hoa\Zombie\Zombie::decapitate();
// I'm dead…
```

But we have to run the script behind FastCGI, that is why we will use the
[`Hoa\Fastcgi` library](https://central.hoa-project.net/Resource/Library/Fastcgi) in the
following example.

In the `Zombie.php` file, we write the following instructions:

```php
echo 'I guess I am sick…', "\n";
Hoa\Zombie\Zombie::fork();

// Do whatever you want here, e.g.:
sleep(10);
file_put_contents(
    __DIR__ . DS . 'AMessage',
    'Hello from after-life… or somewhere about!'
);
Hoa\Zombie\Zombie::decapitate();
```

Then, in the `Run.php` file, we write:

```php
$fastcgi = new Hoa\Fastcgi\Responder(
    new Hoa\Socket\Client('tcp://127.0.0.1:9000')
);
echo $fastcgi->send([
    'GATEWAY_INTERFACE' => 'FastCGI/1.0',
    'REQUEST_METHOD'    => 'GET',
    'SCRIPT_FILENAME'   => __DIR__ . DS . 'Zombie.php'
]);
```

And finally, we can test:

```sh
$ php-fpm -d listen=127.0.0.1:9000
$ php Run.php
I guess I am sick…
```

And 10 seconds after, we will see the `AMessage` file appear with the content:
*Hello from after-life… or somewhere about!*.

## Documentation

The
[hack book of `Hoa\Zombie`](https://central.hoa-project.net/Documentation/Library/Zombie)
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
