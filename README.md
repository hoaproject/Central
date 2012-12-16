![Hoa](http://static.hoa-project.net/Image/Hoa_small.png)

Hoa is a **modular**, **extensible** and **structured** set of PHP libraries.
Moreover, Hoa aims at being a bridge between industrial and research worlds.

# Hoa\Fastcgi

This library allows to manipulate the [FastCGI](http://fastcgi.com/) protocol,
which ensures the communication between a HTTP server and an external program
(such as PHP).

## Quick usage

As a quick overview, we propose to execute a PHP file through the FastCGI
protocol directly.

Before starting, we need to know that PHP proposes two tools that support
FastCGI: `php-cgi` and `php-fpm` (for
[FastCGI Process Manager](http://php.net/install.fpm)). We will use `php-cgi` in
local with the standard port `9000` in TCP:

    $ php-cgi -b 127.0.0.1:9000

First, we write the `Echo.php` file, the one we are likely to execute:

    <?php

    echo 'foobar';

Second, we need to open a connexion to the FastCGI server and send a query with
the following headers:

  * `SCRIPT_FILENAME` which represents the absolute path to the PHP file to
    execute;
  * `REQUEST_METHOD` which represents the HTTP method among `GET`, `POST`,
    `PUT`, `DELETE` etc.;
  * `REQUEST_URI` which represents the identifier of the resource we are trying
    to access.

Thus:

    $fastcgi = new Hoa\Fastcgi\Responder(
        new Hoa\Socket\Client('tcp://127.0.0.1:9000')
    );
    var_dump($fastcgi->send(array(
        'REQUEST_METHOD'  => 'GET',
        'REQUEST_URI'     => '/',
        'SCRIPT_FILENAME' => __DIR__ . DS . 'Echo.php'
    )));
    // string(6) "foobar"

We can get the headers from the executed file by calling the
`Hoa\Fastcgi\Responder::getResponseHeaders` method.

This is a good and fast way to execute PHP files (or other programs that support
FastCGI) without worry about binaries location, sub-shells calls, errors
handling etc.

## Documentation

Different documentations can be found on the website:
[http://hoa-project.net/](http://hoa-project.net/).

## License

Hoa is under the New BSD License (BSD-3-Clause). Please, see
[`LICENSE`](http://hoa-project.net/LICENSE).
