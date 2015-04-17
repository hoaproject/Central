![Hoa](http://static.hoa-project.net/Image/Hoa_small.png)

Hoa is a **modular**, **extensible** and **structured** set of PHP libraries.
Moreover, Hoa aims at being a bridge between industrial and research worlds.

# Hoa\Devtools ![state](http://central.hoa-project.net/State/Devtools)

This library contains several development tools. This is for developers or
maintainers. Sometimes it can be useful to users also, but in particular cases.

## Installation

With [Composer](http://getcomposer.org/), to include this library into your
dependencies, you need to require
[`hoa/devtools`](https://packagist.org/packages/hoa/devtools):

```json
{
    "require": {
        "hoa/devtools": "~0.0"
    }
}
```

Please, read the website to [get more informations about how to
install](http://hoa-project.net/Source.html).

## Quick usage

We propose a quick overview of some commands.

### `cs`

Fix coding style of Hoa. It embraces [PSR-1](http://www.php-fig.org/psr/psr-1/)
and [PSR-2](http://www.php-fig.org/psr/psr-2/), in addition to some extra
fixers.

```sh
$ hoa devtools:cs --diff .
```

Requires [PHP-CS-Fixer](https://github.com/FriendsOfPHP/PHP-CS-Fixer) to be
installed.

### `dependency`

Check the dependencies of a specific library.

```sh
$ hoa devtools:dependency console
Dependency for the library Console:
    • hoa/core => ~…
    • hoa/stream => ~…
    • hoa/string => ~…
```

### `diagnostic`

Help to write (and send) a diagnostic report. Very useful to help users.

```sh
$ hoa devtools:diagnostic --section bin
[bin]
self = "…/hoa"
hoa = "/usr/local/lib/Hoa.central"
php_dir = "…/bin"
php = "…/bin/php"
```

### `paste`

Paste something somewhere (by default, on `paste.hoa-project.net`).

```sh
$ echo 'foobar' | hoa devtools:paste
http://paste.hoa-project.net:80/<id>
```

### `requiresnapshot`

Check if a library requires a new snapshot or not.

```sh
$ hoa devtools:requiresnapshot console
A snapshot is required, since … days (tag …, … commits to publish)!
```

If yes, you are probably likely to use `hoa devtools:snapshot`.

### `state`

Get the state of a library.

```sh
$ hoa devtools:state core
finalized
```

## Documentation

Different documentations can be found on the website:
[http://hoa-project.net/](http://hoa-project.net/).

## License

Hoa is under the New BSD License (BSD-3-Clause). Please, see
[`LICENSE`](http://hoa-project.net/LICENSE).
