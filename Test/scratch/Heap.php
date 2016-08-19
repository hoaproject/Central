<?php

require_once dirname(dirname(__DIR__)).'/vendor/autoload.php';

class xcallable
{
    protected $hash;
    protected $value;

    public function __construct($hash, $value)
    {
        $this->hash = $hash;
        $this->value = $value;
    }

    public function getHash()
    {
        return $this->hash;
    }

    public function exec()
    {
        echo $this->getHash()." - $this->value \n\n";
    }
}

$x1 = new xcallable('foo', 'One');
$x2 = new xcallable('bar', 'Two');
$x3 = new xcallable('baz', 'Three');

$heap = new \Hoa\Heap\Max();

$heap->insert($x1, 5, $x1->getHash());
$heap->insert($x2, 100, $x2->getHash());
$heap->insert($x3, 0);

/*
echo "3 = ", $heap->count(), "\n";
echo $x2->getHash(), ' = ', $heap->key(), "\n";
echo $x2->getHash(), ' = ', $heap->current()->getHash(), "\n";

foreach ($heap as $key => $value) {
    $priority = $heap->priority();

    var_dump($key, $value, $priority);
}

var_dump($heap->key());
var_dump($heap->current());

$heap->rewind();

var_dump($heap->key());
var_dump($heap->current());

$heap->end();

var_dump($heap->key());
var_dump($heap->current());
*/

$keyPierre = $heap->insert('Pierre', 200);
var_dump($heap->count());
$element = $heap->detach($keyPierre);
var_dump($heap->count());
var_dump($element);

foreach ($heap as $key => $value) {
    $priority = $heap->priority();

    var_dump($key, $value, $priority);
}

/*$pierre = $heap->top();
var_dump($pierre->key());
var_dump($pierre->current());*/

/*foreach($heap->pop() as $k => $element) {

    var_dump($k, $element);
    echo "\n\n\n";
}

var_dump($heap->count());*/
