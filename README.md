<p align="center">
  <img src="https://static.hoa-project.net/Image/Hoa.svg" alt="Hoa" width="250px" />
</p>

---

<p align="center">
  <a href="https://travis-ci.org/hoaproject/Graph"><img src="https://img.shields.io/travis/hoaproject/Graph/master.svg" alt="Build status" /></a>
  <a href="https://coveralls.io/github/hoaproject/Graph?branch=master"><img src="https://img.shields.io/coveralls/hoaproject/Graph/master.svg" alt="Code coverage" /></a>
  <a href="https://packagist.org/packages/hoa/graph"><img src="https://img.shields.io/packagist/dt/hoa/graph.svg" alt="Packagist" /></a>
  <a href="https://hoa-project.net/LICENSE"><img src="https://img.shields.io/packagist/l/hoa/graph.svg" alt="License" /></a>
</p>
<p align="center">
  Hoa is a <strong>modular</strong>, <strong>extensible</strong> and
  <strong>structured</strong> set of PHP libraries.<br />
  Moreover, Hoa aims at being a bridge between industrial and research worlds.
</p>

# Hoa\Graph

[![Help on IRC](https://img.shields.io/badge/help-%23hoaproject-ff0066.svg)](https://webchat.freenode.net/?channels=#hoaproject)
[![Help on Gitter](https://img.shields.io/badge/help-gitter-ff0066.svg)](https://gitter.im/hoaproject/central)
[![Documentation](https://img.shields.io/badge/documentation-hack_book-ff0066.svg)](https://central.hoa-project.net/Documentation/Library/Graph)
[![Board](https://img.shields.io/badge/organisation-board-ff0066.svg)](https://waffle.io/hoaproject/graph)

This library allows to create and manipulate directed graphs, a common data
structure. A directed graph is basically a set of vertices (aka nodes) and
directed edges between vertices.

[Learn more](https://central.hoa-project.net/Documentation/Library/Graph).

## Installation

With [Composer](https://getcomposer.org/), to include this library into
your dependencies, you need to
require [`hoa/graph`](https://packagist.org/packages/hoa/graph):

```sh
$ composer require hoa/graph '~1.0'
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

As a quick overview, we propose to see how to create a simple directed graph in
memory and dump the result as [a DOT
script](http://graphviz.org/content/dot-language) in order to visualize it in
SVG. The graph implementation will use the adjacency list structure. Thus:

```php
// Create the graph instance.
// By default, loops are not allowed and we would like loops for this example,
// so we enable them.
$graph = new Hoa\Graph\AdjacencyList(Hoa\Graph::ALLOW_LOOP);

// Create 4 vertices (aka nodes).
$n1 = new Hoa\Graph\SimpleNode('n1');
$n2 = new Hoa\Graph\SimpleNode('n2');
$n3 = new Hoa\Graph\SimpleNode('n3');
$n4 = new Hoa\Graph\SimpleNode('n4');

// Create edges (aka links) between them.
$graph->addNode($n1);
$graph->addNode($n2, [$n1]); // n2 has parent n1.
$graph->addNode($n3, [$n1, $n2, $n3]); // n3 has parents n1, n2 and n3.
$graph->addNode($n4, [$n3]); // n4 has parent n3.
$graph->addNode($n2, [$n4]); // Add parent n4 to n2.
```

The directed graph is created in memory. Now, let's dump into the DOT language:

```php
echo $graph;

/**
 * Will output:
 *     digraph {
 *         n1;
 *         n2;
 *         n3;
 *         n4;
 *         n1 -> n2;
 *         n1 -> n3;
 *         n2 -> n3;
 *         n3 -> n3;
 *         n3 -> n4;
 *         n4 -> n2;
 *     }
 */
```

Then, to compile this DOT script into an SVG document, we will use
[`dot(1)`](http://graphviz.org/pdf/dot.1.pdf):

```sh
$ dot -Tsvg -oresult.svg <( echo 'digraph { … }'; )
```

And the result should look like the following image:

![result.svg](https://central.hoa-project.net/Resource/Library/Graph/Documentation/Image/Simple.svg?format=raw)

We can see that `n1` is the parent of `n2` and `n3`. `n2` is the parent of `n3`.
`n3` is parent of `n4` and also or iself. And finally, `n4` is the parent of
`n2`.

Our directed graph is created. Depending of the node, we can add more
information on it. The `SimpleNode` class has been used. It extends the
`Hoa\Graph\Node` interface.

## Documentation

The
[hack book of `Hoa\Graph`](https://central.hoa-project.net/Documentation/Library/Graph)
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
