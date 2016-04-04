![Hoa](http://static.hoa-project.net/Image/Hoa_small.png)

Hoa is a **modular**, **extensible** and **structured** set of PHP libraries.
Moreover, Hoa aims at being a bridge between industrial and research worlds.

# Hoa\Graph ![state](http://central.hoa-project.net/State/Graph)

This library allows to create and manipulate directed graphs, a common data
structure. A directed graph is basically a set of vertices (aka nodes) and
directed edges between vertices.

## Installation

With [Composer](http://getcomposer.org/), to include this library into your
dependencies, you need to require
[`hoa/graph`](https://packagist.org/packages/hoa/graph):

```json
{
    "require": {
        "hoa/graph": "~0.0"
    }
}
```

Please, read the website to [get more informations about how to
install](http://hoa-project.net/Source.html).

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
![result.svg](http://central.hoa-project.net/Resource/Library/Graph/Documentation/Image/Simple.svg?format=raw)

We can see that `n1` is the parent of `n2` and `n3`. `n2` is the parent of `n3`.
`n3` is parent of `n4` and also or iself. And finally, `n4` is the parent of
`n2`.

Our directed graph is created. Depending of the node, we can add more
information on it. The `SimpleNode` class has been used. It extends the
`Hoa\Graph\Node` interface.

## Documentation

Different documentations can be found on the website:
[http://hoa-project.net/](http://hoa-project.net/).

## License

Hoa is under the New BSD License (BSD-3-Clause). Please, see
[`LICENSE`](http://hoa-project.net/LICENSE).
