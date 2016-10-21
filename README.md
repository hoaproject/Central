<p align="center">
  <img src="https://static.hoa-project.net/Image/Hoa.svg" alt="Hoa" width="250px" />
</p>

---

<p align="center">
  <a href="https://travis-ci.org/hoaproject/session"><img src="https://img.shields.io/travis/hoaproject/session/master.svg" alt="Build status" /></a>
  <a href="https://coveralls.io/github/hoaproject/session?branch=master"><img src="https://img.shields.io/coveralls/hoaproject/session/master.svg" alt="Code coverage" /></a>
  <a href="https://packagist.org/packages/hoa/session"><img src="https://img.shields.io/packagist/dt/hoa/session.svg" alt="Packagist" /></a>
  <a href="https://hoa-project.net/LICENSE"><img src="https://img.shields.io/packagist/l/hoa/session.svg" alt="License" /></a>
</p>
<p align="center">
  Hoa is a <strong>modular</strong>, <strong>extensible</strong> and
  <strong>structured</strong> set of PHP libraries.<br />
  Moreover, Hoa aims at being a bridge between industrial and research worlds.
</p>

# Hoa\Session

[![Help on IRC](https://img.shields.io/badge/help-%23hoaproject-ff0066.svg)](https://webchat.freenode.net/?channels=#hoaproject)
[![Help on Gitter](https://img.shields.io/badge/help-gitter-ff0066.svg)](https://gitter.im/hoaproject/central)
[![Documentation](https://img.shields.io/badge/documentation-hack_book-ff0066.svg)](https://central.hoa-project.net/Documentation/Library/Session)
[![Board](https://img.shields.io/badge/organisation-board-ff0066.svg)](https://waffle.io/hoaproject/session)

This library allows to manipulate sessions easily by creating “namespaces”, i.e.
an entry in the `$_SESSION` global variable. It also allows to manipulate flash
sessions.

Each namespace has a profile. Amongst many things, it implies we can control
lifetime of each namespace individually. This library can be used in conjunction
of `ext/session` (especially to configure session).

[Learn more](https://central.hoa-project.net/Documentation/Library/Session).

## Installation

With [Composer](https://getcomposer.org/), to include this library into
your dependencies, you need to
require [`hoa/session`](https://packagist.org/packages/hoa/session):

```sh
$ composer require hoa/session '~0.0'
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

We propose a quick overview of three usages: manipulating a namespace,
expiration handling and destructing a namespace.

### Manipulating a namespace

We will start a `user` namespace. If this namespace is empty, it implies two
things: either the session is newly created or it has expired. In all cases, we
print `first time` and save the current time in `foo`. And if the namespace is
not empty, we print `other times` and dump the saved time. Thus:

```php
$user = new Hoa\Session\Session('user');

if ($user->isEmpty()) {
    echo 'first time', "\n";
    $user['foo'] = time();
} else {
    echo 'other times', "\n";
    var_dump($user['foo']);
}
```

First execution will print `first time` and next ones will print `other times`
followed by a timestamp.

No need to start the session, it will be done automatically. But if we want to,
we can use the `session_start` function or the `Hoa\Session\Session::start`
static method, which is more sophisticated.

### Expiration handling

If a namespace expires before the session, either an event is fired on the
channel `hoa://Event/Session/<namespace>:expired` or an exception
`Hoa\Session\Exception\Expired` is thrown (if no callable listens the channel).
We can test if the namespace is expired with the help of the
`Hoa\Session\Session::isExpired` method or we can declare the namespace as
expired with the help of the `Hoa\Session\Session::hasExpired` method (this
method will throw an exception or fire an event).

Considering the previous example, we will declare the namespace as expired when
the session is empty:

```php
Hoa\Event\Event::getEvent('hoa://Event/Session/user:expired')
    ->attach(function (Hoa\Event\Bucket $bucket) {
        echo 'expired', "\n";
    });

$user = new Hoa\Session\Session('user');

if ($user->isEmpty()) {
    $user->hasExpired();
    echo 'first time', "\n";
    $user['foo'] = time();
} else {
    echo 'other times', "\n";
    var_dump($user['foo']);
}
```

First execution will print `expired` and `first time`, and next ones will print
`other times` followed by a timestamp.

If an empty session does not imply an expiration (depending of our workflow and
configuration), we could avoid the call to `$user->hasExpired()`: the test is
automatically done during the construction of the namespace.

We can modify the lifetime by using the `Hoa\Session\Session::rememberMe`
method; for example:

```php
$user->rememberMe('+1 day');
```

And we can also forget the namespace:

```php
$user->forgetMe();
```

### Destructing a namespace

Destructing a namespace uses the `Hoa\Session\Session::delete` method:

```php
$user = new Hoa\Session\Session('user');
$user['foo'] = 'bar';
$user->delete();
```

Stored data in the namespace are **lost** because we explicitly delete the
namespace.

If we want to destroy the session (including all namespaces and cookie), we
could use the `Hoa\Session\Session::destroy` static method. Again, no need to
previously start the session manually if it is not done.

## Documentation

The
[hack book of `Hoa\Session`](https://central.hoa-project.net/Documentation/Library/Session)
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
