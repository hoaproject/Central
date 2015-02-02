<?php

require_once __DIR__ . DIRECTORY_SEPARATOR . '.autoload.atoum.php';

$runner->addExtension(new Atoum\PraspelExtension\Manifest());
$runner->addExtension(new atoum\ruler\extension($script));
