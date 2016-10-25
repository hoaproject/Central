<p align="center">
  <img src="https://static.hoa-project.net/Image/Hoa.svg" alt="Hoa" width="250px" />
</p>

---

<p align="center">
  <a href="https://travis-ci.org/hoaproject/irc"><img src="https://img.shields.io/travis/hoaproject/irc/master.svg" alt="Build status" /></a>
  <a href="https://coveralls.io/github/hoaproject/irc?branch=master"><img src="https://img.shields.io/coveralls/hoaproject/irc/master.svg" alt="Code coverage" /></a>
  <a href="https://packagist.org/packages/hoa/irc"><img src="https://img.shields.io/packagist/dt/hoa/irc.svg" alt="Packagist" /></a>
  <a href="https://hoa-project.net/LICENSE"><img src="https://img.shields.io/packagist/l/hoa/irc.svg" alt="License" /></a>
</p>
<p align="center">
  Hoa is a <strong>modular</strong>, <strong>extensible</strong> and
  <strong>structured</strong> set of PHP libraries.<br />
  Moreover, Hoa aims at being a bridge between industrial and research worlds.
</p>

# Hoa\Irc

[![Help on IRC](https://img.shields.io/badge/help-%23hoaproject-ff0066.svg)](https://webchat.freenode.net/?channels=#hoaproject)
[![Help on Gitter](https://img.shields.io/badge/help-gitter-ff0066.svg)](https://gitter.im/hoaproject/central)
[![Documentation](https://img.shields.io/badge/documentation-hack_book-ff0066.svg)](https://central.hoa-project.net/Documentation/Library/Irc)
[![Board](https://img.shields.io/badge/organisation-board-ff0066.svg)](https://waffle.io/hoaproject/irc)

This library allows to write an IRC client, and interact through listeners and
simple methods.

[Learn more](https://central.hoa-project.net/Documentation/Library/Irc).

## Installation

With [Composer](https://getcomposer.org/), to include this library into
your dependencies, you need to
require [`hoa/irc`](https://packagist.org/packages/hoa/irc):

```sh
$ composer require hoa/irc '~0.0'
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

We propose a quick overview of a simple client that joins a channel and
interacts to mentions. Next, we will enhance this client with a WebSocket server
to receive external messages.

### Interact to mentions

The `Hoa\Irc\Client` proposes the following listeners: `open`, `join`,
`message`, `private-message`, `mention`, `other-message`, `ping`, `kick`,
`invite` and `error`.

In order to connect to an IRC server, we have to use a socket client, such as:

```php
$uri    = 'irc://chat.freenode.net';
$client = new Hoa\Irc\Client(new Hoa\Socket\Client($uri));
```

Then, we attach our listeners. When the connexion will be opened, we will join a
channel, for example `#hoaproject` with the `Gordon` username:

```php
$client->on('open', function (Hoa\Event\Bucket $bucket) {
    $bucket->getSource()->join('Gordon', '#hoaproject');

    return;
});
```

Next, when someone will mention `Gordon`, we will answer `What?`:

```php
$client->on('mention', function (Hoa\Event\Bucket $bucket) {
    $data    = $bucket->getData();
    $message = $data['message']; // do something with that.

    $bucket->getSource()->say(
        $data['from']['nick'] . ': What?'
    );

    return;
});
```

Finally, to run the client:

```php
$client->run();
```

### Include a WebSocket server

We can add a WebSocket server to receive external messages we will forward to
the IRC client. Thus, the beginning of our program will look like:

```php
$ircUri = 'irc://chat.freenode.net';
$wsUri  = 'ws://127.0.0.1:8889';

$group  = new Hoa\Socket\Connection\Group();
$client = new Hoa\Irc\Client(new Hoa\Socket\Client($ircUri));
$server = new Hoa\Websocket\Server(new Hoa\Socket\Server($wsUri));

$group[] = $server;
$group[] = $client;
```

Then, we will forward all messages received by the WebSocket server to the IRC
client:

```php
$server->on('message', function (Hoa\Event\Bucket $bucket) use ($client) {
    $data = $bucket->getData();
    $client->say($data['message']);

    return;
});
```

Finally, to run both the IRC client and WebSocket server:

```php
$group->run();
```

To send a message to the WebSocket server, we can use a WebSocket client in CLI:

```sh
$ echo 'foobar' | hoa websocket:client -s 127.0.0.1:8889
```

## Documentation

The
[hack book of `Hoa\Irc`](https://central.hoa-project.net/Documentation/Library/Irc)
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
