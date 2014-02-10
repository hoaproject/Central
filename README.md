![Hoa](http://static.hoa-project.net/Image/Hoa_small.png)

Hoa is a **modular**, **extensible** and **structured** set of PHP libraries.
Moreover, Hoa aims at being a bridge between industrial and research worlds.

# Hoa\Dns ![state](http://central.hoa-project.net/State/Dns)

This library allows to resolve domain name according to their types.

## Quick usage

As a quick overview, we propose to create our own resolution server for the top
level domain `.hoa`. We start by modifying the local resolver in order to add a
new resolution host: ours.

### Specify a resolution server

On Mac OS X, the simplest way is to write in `/etc/resolver/hoa` the following
declarations:

    nameserver 127.0.0.1
    port 57005
 
On Linux, we will use [DNSMasq](http://thekelleys.org.uk/dnsmasq/doc.html)
(often already installed). Then, we edit the file `/etc/dnsmasq.conf` by adding:
 
    server=/hoa/127.0.0.1#57005

And do not forget to restart:

    $ sudo /etc/init.d/dnsmasq restart
     * Restarting DNS forwarder and DHCP server dnsmasq    [OK]

For Windows, it is more complicated. You should read the documentation.

### Create a resolution server

Well, now, we will create our resolution server that will listen
`127.0.0.1:57005` (`57005` = `0xDEAD`) in UDP. Thus, in the `Resolution.php`
file:

    $dns = new Hoa\Dns\Dns(
        new Hoa\Socket\Server('udp://127.0.0.1:57005')
    );
    $dns->on('query', function ( Hoa\Core\Event\Bucket $bucket ) {

        $data = $bucket->getData();
        echo 'Resolving domain ', $data['domain'],
             ' of type ', $data['type'], "\n";

        return '127.0.0.1';
    });
    $dns->run();

All query for the top level domain `.hoa` will be resolved to `127.0.0.1` (note:
we do not look at the type, which should be `A` or `AAAA` respectively for IPv4
and IPv6).

Finally, let say we have a HTTP server that runs on `127.0.0.1:8888` and the
index responds `yeah \o/`, then we start our resolver:

    $ php Resolver.php

And we make a HTTP request on `foo.hoa` (that will be resolve to `127.0.0.1`):

    $ curl foo.hoa --verbose
    * About to connect() to foo.hoa port 8888 (#0)
    *   Trying 127.0.0.1... connected
    * Connected to foo.hoa (127.0.0.1) port 8888 (#0)
    > GET / HTTP/1.1
    > User-Agent: curl/a.b.c (…) libcurl/d.e.f
    > OpenSSL/g.h.i zlib/j.k.l
    > Host: foo.hoa:8888
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

We see that `foo.hoa` is resolved to `127.0.0.1`!

## Documentation

Different documentations can be found on the website:
[http://hoa-project.net/](http://hoa-project.net/).

## License

Hoa is under the New BSD License (BSD-3-Clause). Please, see
[`LICENSE`](http://hoa-project.net/LICENSE).
