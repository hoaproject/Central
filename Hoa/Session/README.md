![Hoa](http://static.hoa-project.net/Image/Hoa_small.png)

Hoa is a **modular**, **extensible** and **structured** set of PHP libraries.
Moreover, Hoa aims at being a bridge between industrial and research worlds.

# Hoa\Session ![state](http://central.hoa-project.net/State/Session)

This library allows to manipulate sessions easily by creating “namespaces”, i.e.
an entry in the `$_SESSION` global variable. It also allows to manipulate flash
sessions.

Each namespace has a profile. Amongst many things, it implies we can control
lifetime of each namespace individually. This library can be used in conjunction
of `ext/session` (especially to configure session).

## Installation

With [Composer](http://getcomposer.org/), to include this library into your
dependencies, you need to require
[`hoa/session`](https://packagist.org/packages/hoa/session):

```json
{
    "require": {
        "hoa/session": "~0.0"
    }
}
```

Please, read the website to [get more informations about how to
install](http://hoa-project.net/Source.html).

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
event('hoa://Event/Session/user:expired')
    ->attach(function (Hoa\Core\Event\Bucket $bucket) {
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

Different documentations can be found on the website:
[http://hoa-project.net/](http://hoa-project.net/).

## License

Hoa is under the New BSD License (BSD-3-Clause). Please, see
[`LICENSE`](http://hoa-project.net/LICENSE).
