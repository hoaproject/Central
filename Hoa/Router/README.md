![Hoa](http://static.hoa-project.net/Image/Hoa_small.png)

Hoa is a **modular**, **extensible** and **structured** set of PHP libraries.
Moreover, Hoa aims at being a bridge between industrial and research worlds.

# Hoa\Router ![state](http://central.hoa-project.net/State/Router)

This library allows to find an appropriated route and extracts data from a
request. Conversely, given a route and data, this library is able to build a
request.

For now, we have two routers: HTTP (routes understand URI and subdomains) and
CLI (routes understand a full command-line).

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
    ->get('u', '/hello', function ( ) {

        echo 'world!', "\n";
    })
    ->post('v', '/hello', function ( Array $_request ) {

        echo $_request['a'] + $_request['b'], "\n";
    })
    ->get('w', '/bye', function ( ) {

        echo 'ohh :-(', "\n";
    })
    ->get('x', '/hello_(?<nick>\w+)', function ( $nick ) {

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
    function ( $group, $subcommand, $options ) {

        echo 'Group     : ', $group, "\n",
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

The use of `Hoa\Console` would be a good idea to interprete the options and
getting some confortable services for the terminal.

## Documentation

Different documentations can be found on the website:
[http://hoa-project.net/](http://hoa-project.net/).

## License

Hoa is under the New BSD License (BSD-3-Clause). Please, see
[`LICENSE`](http://hoa-project.net/LICENSE).
