<p align="center">
  <img src="https://static.hoa-project.net/Image/Hoa.svg" alt="Hoa" width="250px" />
</p>

---

<p align="center">
  <a href="https://travis-ci.org/hoaproject/Socket"><img src="https://img.shields.io/travis/hoaproject/Socket/master.svg" alt="Build status" /></a>
  <a href="https://coveralls.io/github/hoaproject/Socket?branch=master"><img src="https://img.shields.io/coveralls/hoaproject/Socket/master.svg" alt="Code coverage" /></a>
  <a href="https://packagist.org/packages/hoa/socket"><img src="https://img.shields.io/packagist/dt/hoa/socket.svg" alt="Packagist" /></a>
  <a href="https://hoa-project.net/LICENSE"><img src="https://img.shields.io/packagist/l/hoa/socket.svg" alt="License" /></a>
</p>
<p align="center">
  Hoa is a <strong>modular</strong>, <strong>extensible</strong> and
  <strong>structured</strong> set of PHP libraries.<br />
  Moreover, Hoa aims at being a bridge between industrial and research worlds.
</p>

# Hoa\Socket

[![Help on IRC](https://img.shields.io/badge/help-%23hoaproject-ff0066.svg)](https://webchat.freenode.net/?channels=#hoaproject)
[![Help on Gitter](https://img.shields.io/badge/help-gitter-ff0066.svg)](https://gitter.im/hoaproject/central)
[![Documentation](https://img.shields.io/badge/documentation-hack_book-ff0066.svg)](https://central.hoa-project.net/Documentation/Library/Socket)
[![Board](https://img.shields.io/badge/organisation-board-ff0066.svg)](https://waffle.io/hoaproject/socket)

This library provides an abstract layer to build safe, fast and modular clients
and servers.

It represents a connection as a stream (please, see the [`Hoa\Stream`
library](http://central.hoa-project.net/Resource/Library/Stream)) that is used
to build clients and servers. A connection supports timeout, options, context,
encryption, remote informations etc. Such a connection, along with an abstract
connection handler, allows to embed and “merge” many connections inside the same
processus side-by-side.

[Learn more](https://central.hoa-project.net/Documentation/Library/Socket).

## Installation

With [Composer](https://getcomposer.org/), to include this library into
your dependencies, you need to
require [`hoa/socket`](https://packagist.org/packages/hoa/socket):

```sh
$ composer require hoa/socket '~1.0'
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

As a quick overview, we will look at creating a server and a client, and
introduce the respective API.

### A connection behind

Both server and client extend a connection, namely the
`Hoa\Socket\Connection\Connection` class, which is a stream represented by the
[`Hoa\Stream` library](http://central.hoa-project.net/Resource/Library/Stream).
This latter provides the common stream API whose the read and write methods
(from `Hoa\Stream\IStream\In`, `Hoa\Stream\IStream\Out` and also
`Hoa\Stream\IStream\Pathable`). Since it is also responsible of the connection,
we are able to manipulate the underlying socket resource, the timeout, the
different flags, the stream context, the encryption, the remote informations
etc.

To start a connection, we will use the `connect` method (the constructor does
not start the connection by itself). For a server, we will often prefer to use
the `connectAndWait` method (see bellow). To stop a connection, most of the
time, we will use the `disconnect` method.

A remote connection (a client for the server, a server for the client) is
represented by a node: an object that holds several informations about the
remote connection. The default node is `Hoa\Socket\Node` and can be easily
extended. To use a new node, we have to call the
`Hoa\Socket\Connection\Connection::setNodeName` method.

A connection needs a socket URI, represented by the `Hoa\Socket\Socket` class,
to know where to connect. This latter represents an IPv4 or IPv6 address, a
domain or a path (for Unix socket), along with the transport scheme (`tcp://`,
`udp://` etc.) and the port.

### Manipulating a server or a client

We will instanciate the `Hoa\Socket\Server` class and start a connection to
`tcp://127.0.0.1:4242`. Then, to select active nodes,
we will use the `Hoa\Socket\Connection\Connection::select` method that returns
an iterator.  Finally, we will read a line and write an uppercassed echo. Thus:

```php
$server = new Hoa\Socket\Server('tcp://127.0.0.1:4242');
$server->connectAndWait();

while (true) {
    foreach ($server->select() as $node) {
        $line = $server->readLine();

        if (empty($line)) {
            $server->disconnect();
            continue;
        }

        echo '< ', $line, "\n";
        $server->writeLine(strtoupper($line));
    }
}
```

And then, with `telnet`:

```sh
$ telnet 127.0.0.1 4242
Trying 127.0.0.1...
Connected to localhost.
Escape character is '^]'.
foobar
FOOBAR
hello world
HELLO WORLD
```

From the server, we will see:

```
< foobar
< hello world
```

To reproduce the same behavior with our own client, we will write (thanks to
`Hoa\Console\Readline\Readline`, please see the [`Hoa\Console`
library](http://central.hoa-project.net/Resource/Library/Console)):

```php
$client = new Hoa\Socket\Client('tcp://127.0.0.1:4242');
$client->connect();

$readline = new Hoa\Console\Readline\Readline();

while (true) {
    $line = $readline->readLine('> ');

    if ('quit' === $line) {
        break;
    }

    $client->writeLine($line);

    echo '< ', $client->readLine(), "\n";
}
```

Finally:

```sh
$ php Client.php
> foobar
< FOOBAR
> hello world
< HELLO WORLD
> quit
```

### Handle servers and clients

A connection has advanced operations but they are low-levels and not obvious.
Moreover, there is repetitive and not so trivial tasks that we need often, such
as broadcasting messages. The `Hoa\Socket\Connection\Handler` provides an easy
way to create and embed a very flexible server or client. (A good and complete
example is the [`Hoa\Websocket`
library](http://central.hoa-project.net/Resource/Library/Websocket)).

We will focus on a server. A server has the magic `run` method that starts an
infinite loop and make some computation on active nodes. This is basically the
`while (true)` in our previous examples. In addition, we would like to easily
send a message to a specific node, or send a message to all nodes except one.
The `Hoa\Socket\Connection\Handler` class asks the user to implement only two
methods: `_run` and `_send`, and provides the `run` method, along with `send`
and `broadcast`. Then, we no longer need to start the connection or to take care
about the implementation of different network topologies. All is managed by the
handler.  Thus:

```php
class MyServer extends Hoa\Socket\Connection\Handler
{
    protected function _run (Hoa\Socket\Node $node)
    {
        $connection = $node->getConnection();
        $line       = $connection->readLine();

        if (empty($line)) {
            $connection->disconnect();
            return;
        }

        echo '< ', $line, "\n";
        $this->send(strtoupper($line));

        return;
    }

    protected function _send ($message, Hoa\Socket\Node $node)
    {
        return $node->getConnection()->writeLine($message);
    }
}
```

And then, all we need to do is:

```php
$server = new MyServer(new Hoa\Socket\Server('tcp://127.0.0.1:4242'));
$server->run();
```

We see that the connection is embeded inside our server, and that all the logic
has been moved inside the `_run` method. If we change the call to `send` by
`broadcast`, we will see all connected clients receiving the message, something
like:

```php
        echo '< ', $line, "\n";
        $this->broadcast(strtoupper($line));
```

The `_send` method gives an implementation of “sending one message”, which is
the basis. Because the `_run` method does not start an infinite loop, we have
more flexibility (see the next section).

We can add listeners (please see the [`Hoa\Event`
library](http://central.hoa-project.net/Resource/Library/Event)) to
interact with the server, something like `$server->on('message', function ( … )
{ … });` etc.

### Merging connections

Another huge advantage of using handlers is that they can be used inside a
`Hoa\Socket\Connection\Group` object. The `run` method is an infinite loop, so
we are not able to run two servers side-by-side in the same process.
Fortunately, the `Hoa\Socket\Connection\Group` allows to “merge” connections
(this is an underlying feature of `Hoa\Socket\Connection\Connection` but a group
abstracts and manages all the complexity). Consequently, we are able to run
several servers and clients together, inside the same processus, at the same
time.

For example, we will run an instance of `Hoa\Irc\Client` (please, see the
[`Hoa\Irc` library](http://central.hoa-project.net/Resource/Library/Irc)) with a
`Hoa\Websocket\Server` (please, see the [`Hoa\Websocket`
library](http://central.hoa-project.net/Resource/Library/Websocket): all
messages received by the WebSocket server will be redirected on the IRC client.
Thus:

```php
$websocket = new Hoa\Websocket\Server(new Hoa\Socket\Server('tcp://…'));
$irc       = new Hoa\Irc\Client(new Hoa\Socket\Client('tcp://…'));
$group     = new Hoa\Socket\Connection\Group();
$group[]   = $websocket;
$group[]   = $irc;

$websocket->on(
    'message',
    function (Hoa\Event\Bucket $bucket) use ($irc) {
        $data = $bucket->getData();
        $irc->say($data['message']);

        return;
    }
);

// $irc->…

$group->run();
```

This is an illustration of the power provided by the `Hoa\Socket\Connection`
classes.

## Documentation

The
[hack book of `Hoa\Socket`](https://central.hoa-project.net/Documentation/Library/Socket) contains
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

The following projects are using this library:

  * [PHP School](https://www.phpschool.io/), A revolutionary new way
    to learn PHP.
