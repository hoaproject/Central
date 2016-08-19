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

///////////////////////////////////////////////////////////////////

$storage = new SplObjectStorage();
//$storage = [];
$heap = new \Hoa\Heap\Max(\Hoa\Heap\Heap::VALUE_TYPE_OBJECT);
$loop = 100 * 1000;

$bench = new \Hoa\Bench\Bench();
$bench->heapInsert->start();
$mem = memory_get_usage();

/** ********************* */

do {
    /*$o = new xcallable(Hoa\Consistency\Consistency::uuid(), 'foo');
    $heap->insert($o, mt_rand(0, 9999), $o->getHash());*/

    $storage[new xcallable(Hoa\Consistency\Consistency::uuid(), 'foo')] = true;
    //$storage[] = new xcallable(Hoa\Consistency\Consistency::uuid(), 'foo');
} while ($loop--);

//sleep(10000);

/** ********************* */

echo number_format(memory_get_usage() - $mem), " Bytes\n";
$bench->heapInsert->stop();

echo $bench;
