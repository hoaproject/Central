<p align="center">
  <img src="https://static.hoa-project.net/Image/Hoa.svg" alt="Hoa" width="250px" />
</p>

---

<p align="center">
  <a href="https://travis-ci.org/hoaproject/praspel"><img src="https://img.shields.io/travis/hoaproject/praspel/master.svg" alt="Build status" /></a>
  <a href="https://coveralls.io/github/hoaproject/praspel?branch=master"><img src="https://img.shields.io/coveralls/hoaproject/praspel/master.svg" alt="Code coverage" /></a>
  <a href="https://packagist.org/packages/hoa/praspel"><img src="https://img.shields.io/packagist/dt/hoa/praspel.svg" alt="Packagist" /></a>
  <a href="https://hoa-project.net/LICENSE"><img src="https://img.shields.io/packagist/l/hoa/praspel.svg" alt="License" /></a>
</p>
<p align="center">
  Hoa is a <strong>modular</strong>, <strong>extensible</strong> and
  <strong>structured</strong> set of PHP libraries.<br />
  Moreover, Hoa aims at being a bridge between industrial and research worlds.
</p>

# Hoa\Praspel

[![Help on IRC](https://img.shields.io/badge/help-%23hoaproject-ff0066.svg)](https://webchat.freenode.net/?channels=#hoaproject)
[![Help on Gitter](https://img.shields.io/badge/help-gitter-ff0066.svg)](https://gitter.im/hoaproject/central)
[![Documentation](https://img.shields.io/badge/documentation-hack_book-ff0066.svg)](https://central.hoa-project.net/Documentation/Library/Praspel)
[![Board](https://img.shields.io/badge/organisation-board-ff0066.svg)](https://waffle.io/hoaproject/praspel)

## Praspel and the Realistic Domains

Praspel (PHP Realistic Annotation and Specification Language) is a
**formal specification language** for PHP. It is based on the
Design-by-Contract paradigm and uses preconditions, postconditions,
invariants etc. Specifications are written in the comments of the PHP
code. Praspel is used for manual, automated or **automatic software
validation and verification**.

This language is inspired by [JML](http://www.jmlspecs.org/) but the
difference is in the way to specify the data. PHP is dynamically and
weakly typed. To specify the data, Praspel relies on **realistic
domains**: Structures allowing to validate and generate data, with the
ability to compose them to represent more **complex data**. Realistic
domains are implemented in
[the `Hoa\Realdom` library](https://central.hoa-project.net/Resource/Library/Realdom).

## Data generators

A contract can be used to automatically generate unit tests. A test is
constitued of 2 parts: Test data, and an oracle. Test data are crucial
since it must reflect “realistic” data as much as possible, and being
to generate data at the limits is also very important. Realistic
Domains is an answer to this problem by being able to generate
integers, reals, strings (based on regular expressions or grammars for
instance), arrays (based on a constraint solver) or objects, for the
PHP language types. Thus, it is possible to combine these realistic
domains to generate more sophisticated data (like dates, object models
etc.).

## Contract Coverage Criteria

The contract language can be evaluated to validate and verify data
manipulated by the program on one hand. On the other hand, we have
algorithms able to automatically generate test data from a piece of a
contract. In order to ensure whether the contract is correctly
covered, we have defined several contract coverage criteria.

Thus, we are able to generate unit test suites satisfying these
contract coverage criteria, and thus ensuring that generated unit
test suites reflect all the behavior expressed in the contract.

## Research papers

This language is the result of several research papers, journals and PhD thesis.

  * *Praspel: A Specification Language for Contract-Driven Testing in PHP*,
    presented at [ICTSS 2011](http://ictss2011.lri.fr/) (Paris, France)
    ([article](https://hoa-project.net/En/Literature/Research/Ictss11.pdf),
     [presentation](http://keynote.hoa-project.net/Ictss11/EDGB11.pdf)),
  * *Grammar-Based Testing using Realistic Domains in PHP*,
    presented at [A-MOST 2012](https://sites.google.com/site/amost2012/) (Montréal, Canada)
    ([article](https://hoa-project.net/En/Literature/Research/Amost12.pdf),
     [presentation](http://keynote.hoa-project.net/Amost12/EDGB12.pdf),
     [details](https://hoa-project.net/En/Event/Amost12.html)),
  * *A Constraint Solver for PHP Arrays*,
    presented at [CSTVA 2013](http://cstva2013.univ-fcomte.fr/) (Luxembourg, Luxembourg)
    ([article](https://hoa-project.net/En/Literature/Research/Cstva13.pdf),
     [presentation](http://keynote.hoa-project.net/Cstva13/EGB13.pdf),
     [details](https://hoa-project.net/En/Event/Cstva13.html)).
 
[Learn more](https://central.hoa-project.net/Documentation/Library/Praspel).

## Installation

With [Composer](https://getcomposer.org/), to include this library into
your dependencies, you need to
require [`hoa/praspel`](https://packagist.org/packages/hoa/praspel):

```sh
$ composer require hoa/praspel '~1.0'
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

## Documentation

The
[hack book of `Hoa\Praspel`](https://central.hoa-project.net/Documentation/Library/Praspel)
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

## Related projects

The following projects are using this library:

  * [`atoum/praspel-extension`](http://central.hoa-project.net/Resource/Contributions/Atoum/PraspelExtension),
    Test data generation, and unit test suite generation inside atoum.
