<p align="center">
  <img src="https://static.hoa-project.net/Image/Hoa.svg" alt="Hoa" width="250px" />
</p>

---

<p align="center">
  <a href="https://travis-ci.org/hoaproject/cli"><img src="https://img.shields.io/travis/hoaproject/cli/master.svg" alt="Build status" /></a>
  <a href="https://coveralls.io/github/hoaproject/cli?branch=master"><img src="https://img.shields.io/coveralls/hoaproject/cli/master.svg" alt="Code coverage" /></a>
  <a href="https://packagist.org/packages/hoa/cli"><img src="https://img.shields.io/packagist/dt/hoa/cli.svg" alt="Packagist" /></a>
  <a href="https://hoa-project.net/LICENSE"><img src="https://img.shields.io/packagist/l/hoa/cli.svg" alt="License" /></a>
</p>
<p align="center">
  Hoa is a <strong>modular</strong>, <strong>extensible</strong> and
  <strong>structured</strong> set of PHP libraries.<br />
  Moreover, Hoa aims at being a bridge between industrial and research worlds.
</p>

# Hoa\Cli

[![Help on IRC](https://img.shields.io/badge/help-%23hoaproject-ff0066.svg)](https://webchat.freenode.net/?channels=#hoaproject)
[![Help on Gitter](https://img.shields.io/badge/help-gitter-ff0066.svg)](https://gitter.im/hoaproject/central)
[![Documentation](https://img.shields.io/badge/documentation-hack_book-ff0066.svg)](https://central.hoa-project.net/Documentation/Library/Cli)
[![Board](https://img.shields.io/badge/organisation-board-ff0066.svg)](https://waffle.io/hoaproject/cli)

This meta-library provides the `hoa` command line. This is a shell tool to
access libraries' commands.

[Learn more](https://central.hoa-project.net/Documentation/Library/Cli).

## Installation

With [Composer](https://getcomposer.org/), to include this library into
your dependencies, you need to
require [`hoa/cli`](https://packagist.org/packages/hoa/cli):

```sh
$ composer require hoa/cli '~3.0'
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

Once installed, commands from libraries can be run with the following command
line pattern:

```sh
$ hoa <library-name>:<command-name> <options> <inputs>
```

Running `hoa` with no argument will list all the available commands with a small
description. Note: If the option `--no-verbose` is present, the list of commands
will not be formatted. Thus, used in conjunction with [Zsh
resources](https://central.hoa-project.net/Resource/Contributions/Zsh/Hoa), you
will be able to auto-complete any commands from any libraries for free.

On every command, there is at least the `-h`, `--help` and `-?` options,
providing helps and usages.

To provide a command from a library, create a class inside the `Bin/` directory.
For instance, for a potential `Hoa\Foo` library, the `bar` command will be
described by the `Hoa\Foo\Bin\Bar` class, located inside the `Foo/Bin/Bar.php`
file.

## Documentation

The
[hack book of `Hoa\Cli`](https://central.hoa-project.net/Documentation/Library/Cli)
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
