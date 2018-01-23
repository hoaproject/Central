<?php

declare(strict_types=1);

use Atoum\PraspelExtension;
use Hoa\Test;
use mageekguy\atoum;

/**
 * Register extensions.
 */
$runner->addExtension(new PraspelExtension\Manifest());
$runner->addExtension(new atoum\ruler\extension($script));
$runner->addExtension(new atoum\visibility\extension($script));

/**
 * Our own report.
 */
$report = new Test\Report\Cli\Cli();
$runner->addReport($report->addWriter(new atoum\writers\std\out()));

/**
 * Publish code coverage report on coveralls.io from Travis.
 */
if (false !== getenv('TRAVIS')) {
    $coverallsReport = new atoum\reports\asynchronous\coveralls('.');

    $defaultFinder = $coverallsReport->getBranchFinder();
    $coverallsReport
        ->setBranchFinder(
            function() use ($defaultFinder) {
                if (false === $branch = getenv('TRAVIS_BRANCH')) {
                    $branch = $defaultFinder();
                }

                return $branch;
            }
        )
        ->setServiceName('travis-ci')
        ->setServiceJobId(getenv('TRAVIS_JOB_ID') ?: null)
        ->addDefaultWriter();

    $runner->addReport($coverallsReport);
}
