<?php

$runner->addExtension(new Atoum\PraspelExtension\Manifest());
$runner->addExtension(new mageekguy\atoum\ruler\extension($script));
$runner->addExtension(new mageekguy\atoum\visibility\extension($script));
$report = new Hoa\Test\Report\Cli\Cli();
$runner->addReport($report->addWriter(new atoum\writers\std\out()));
