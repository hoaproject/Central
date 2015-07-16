![Hoa](http://static.hoa-project.net/Image/Hoa_small.png)

Hoa is a **modular**, **extensible** and **structured** set of PHP libraries.
Moreover, Hoa aims at being a bridge between industrial and research worlds.

# Hoa\Cli ![state](http://central.hoa-project.net/State/Cli)

This meta-library provides the `hoa` command line. This is a shell tool to
access to libraries' commands.

## Installation

With [Composer](http://getcomposer.org/), to include this library into your
dependencies, you need to require
[`hoa/cli`](https://packagist.org/packages/hoa/cli):

```json
{
    "require": {
        "hoa/cli": "~1.0"
    }
}
```

Please, read the website to [get more informations about how to
install](http://hoa-project.net/Source.html).

## Quick usage

Once installed, commands from libraries can be run with the following command
line pattern:

```sh
$ hoa <library-name>:<command-name> <options> <inputs>
```

Running `hoa` with no argument will list all the available commands with a small
description. Note: If the option `--no-verbose` is present, the list of commands
will not be formatted. Thus, used in conjunction with Zsh resources present in
[`Hoa\Devtools`](http://central.hoa-project.net/Resource/Library/Devtools/Resource/Zsh), you
will be able to auto-complete any commands from any libraries for free.

On every command, there is at least the `-h`, `--help` and `-?` options,
providing helps and usages.

To provide a command from a library, create a class inside the `Bin/` directory.
For instance, for a potential `Hoa\Foo` library, the `bar` command will be
described by the `Hoa\Foo\Bin\Bar` class, located inside the `Foo/Bin/bar.php`
file.

## Documentation

Different documentations can be found on the website:
[http://hoa-project.net/](http://hoa-project.net/).

## License

Hoa is under the New BSD License (BSD-3-Clause). Please, see
[`LICENSE`](http://hoa-project.net/LICENSE).
