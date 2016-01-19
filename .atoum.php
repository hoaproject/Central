<?php

require_once __DIR__ . DIRECTORY_SEPARATOR . '.autoload.atoum.php';

$runner->addExtension(new Atoum\PraspelExtension\Manifest());
$runner->addExtension(new mageekguy\atoum\ruler\extension($script));
$runner->addExtension(new mageekguy\atoum\visibility\extension($script));
