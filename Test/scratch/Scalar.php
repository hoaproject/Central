<?php

require_once dirname(dirname(__DIR__)) . '/vendor/autoload.php';

$heap = new \Hoa\Heap\Max();
$loop = 100 * 1000;

$bench = new \Hoa\Bench\Bench();
$bench->heapInsert->start();
$mem = memory_get_usage();

/** ********************* */

do {
    $heap->insert('foo', mt_rand(0, 10000));
} while ($loop--);

/** ********************* */

echo number_format(memory_get_usage() - $mem), " Bytes\n";
$bench->heapInsert->stop();

echo $bench;
