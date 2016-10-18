<p align="center">
  <img src="https://static.hoa-project.net/Image/Hoa.svg" alt="Hoa" width="250px" />
</p>

---

<p align="center">
  <a href="https://travis-ci.org/hoaproject/dns"><img src="https://img.shields.io/travis/hoaproject/dns/master.svg" alt="Build status" /></a>
  <a href="https://coveralls.io/github/hoaproject/dns?branch=master"><img src="https://img.shields.io/coveralls/hoaproject/dns/master.svg" alt="Code coverage" /></a>
  <a href="https://packagist.org/packages/hoa/dns"><img src="https://img.shields.io/packagist/dt/hoa/dns.svg" alt="Packagist" /></a>
  <a href="https://hoa-project.net/LICENSE"><img src="https://img.shields.io/packagist/l/hoa/dns.svg" alt="License" /></a>
</p>
<p align="center">
  Hoa is a <strong>modular</strong>, <strong>extensible</strong> and
  <strong>structured</strong> set of PHP libraries.<br />
  Moreover, Hoa aims at being a bridge between industrial and research worlds.
</p>

# Hoa\Dns

[![Help on IRC](https://img.shields.io/badge/help-%23hoaproject-ff0066.svg)](https://webchat.freenode.net/?channels=#hoaproject)
[![Help on Gitter](https://img.shields.io/badge/help-gitter-ff0066.svg)](https://gitter.im/hoaproject/central)
[![Documentation](https://img.shields.io/badge/documentation-hack_book-ff0066.svg)](https://central.hoa-project.net/Documentation/Library/Dns)
[![Board](https://img.shields.io/badge/organisation-board-ff0066.svg)](https://waffle.io/hoaproject/dns)

This library allows to create a domain name resolver.

[Learn more](https://central.hoa-project.net/Documentation/Library/Dns).

## Installation

With [Composer](https://getcomposer.org/), to include this library into
your dependencies, you need to
require [`hoa/dns`](https://packagist.org/packages/hoa/dns):

```sh
$ composer require hoa/dns '~3.0'
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

As a quick overview, we propose to create our own resolution server for the top
level domain `.hoa`. We start by modifying the local resolver in order to add a
new resolution host: ours.

### Specify a resolution server

On Mac OS X, the simplest way is to write in `/etc/resolver/hoa` the following
declarations:

```
nameserver 127.0.0.1
port 57005
```
 
On Linux, we will use [DNSMasq](http://thekelleys.org.uk/dnsmasq/doc.html)
(often already installed). Then, we edit the file `/etc/dnsmasq.conf` by adding:
 
```
server=/hoa/127.0.0.1#57005
```

And do not forget to restart:

```sh
$ sudo /etc/init.d/dnsmasq restart
 * Restarting DNS forwarder and DHCP server dnsmasq    [OK]
```

For Windows, it is more complicated. You should read the documentation.

### Create a resolution server

Well, now, we will create our resolution server that will listen
`127.0.0.1:57005` (`57005` = `0xDEAD`) in UDP. Thus, in the `Resolution.php`
file:

```php
$dns = new Hoa\Dns\Resolver(
    new Hoa\Socket\Server('udp://127.0.0.1:57005')
);
$dns->on('query', function (Hoa\Event\Bucket $bucket) {
    $data = $bucket->getData();

    echo
        'Resolving domain ', $data['domain'],
        ' of type ', $data['type'], "\n";

    return '127.0.0.1';
});
$dns->run();
```

All query for the top level domain `.hoa` will be resolved to `127.0.0.1` (note:
we do not look at the type, which should be `A` or `AAAA` respectively for IPv4
and IPv6).

Finally, let say we have a HTTP server that runs on `127.0.0.1:8888` and the
index responds `yeah \o/`, then we start our resolver:

```sh
$ php Resolver.php
```

And we make an HTTP request on `foo.hoa` (that will be resolve to `127.0.0.1`):

```sh
$ curl foo.hoa --verbose
* About to connect() to foo.hoa port 80 (#0)
*   Trying 127.0.0.1... connected
* Connected to foo.hoa (127.0.0.1) port 80 (#0)
> GET / HTTP/1.1
> User-Agent: curl/a.b.c (…) libcurl/d.e.f
> OpenSSL/g.h.i zlib/j.k.l
> Host: foo.hoa:80
> Accept: */*
>
< HTTP/1.1 200 OK
< Date: …
< Server: …
< Content-Type: text/html
< Content-Length: 8
<
yeah \o/
* Connection #0 to host foo.hoa left intact
* Closing connection #0
```

We see that `foo.hoa` is resolved to `127.0.0.1`!

## Documentation

The
[hack book of `Hoa\Dns`](https://central.hoa-project.net/Documentation/Library/Dns) contains
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

  * [ec2dns](https://github.com/fruux/ec2dns), ec2dns is a set of
    command line tools that makes it easy to display public hostnames
    of EC2 instances and ssh into them via their tag name.
