<?php

declare(strict_types=1);

/**
 * Hoa
 *
 *
 * @license
 *
 * New BSD License
 *
 * Copyright © 2007-2017, Hoa community. All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *     * Redistributions of source code must retain the above copyright
 *       notice, this list of conditions and the following disclaimer.
 *     * Redistributions in binary form must reproduce the above copyright
 *       notice, this list of conditions and the following disclaimer in the
 *       documentation and/or other materials provided with the distribution.
 *     * Neither the name of the Hoa nor the names of its contributors may be
 *       used to endorse or promote products derived from this software without
 *       specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDERS AND CONTRIBUTORS BE
 * LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 */
namespace Hoa\Test\Report\Cli;

use atoum;
use atoum\report\fields\runner;
use atoum\report\fields\test;

class Cli extends atoum\reports\realtime
{
    private $runnerTestsCoverageField;

    public function __construct()
    {
        parent::__construct();

        $defaultColorizer       = new Colorizer('foreground(' . Colors::BLUE . ')');
        $defaultPromptColorizer = clone $defaultColorizer;
        $defaultInfoColorizer   = new Colorizer('foreground(' . Colors::YELLOW . ')');

        $firstLevelPrompt  = new atoum\cli\prompt('> ', $defaultPromptColorizer);
        $secondLevelPrompt = new atoum\cli\prompt('~> ', $defaultPromptColorizer);
        $thirdLevelPrompt  = new atoum\cli\prompt('~~> ', $defaultPromptColorizer);

        $atoumPathField = new runner\atoum\path\cli();
        $atoumPathField
            ->setPrompt($firstLevelPrompt)
            ->setTitleColorizer($defaultColorizer);

        $this->addField($atoumPathField);

        $atoumVersionField = new runner\atoum\version\cli();
        $atoumVersionField
            ->setTitlePrompt($firstLevelPrompt)
            ->setTitleColorizer($defaultColorizer);

        $this->addField($atoumVersionField);

        $phpPathField = new runner\php\path\cli();
        $phpPathField
            ->setPrompt($firstLevelPrompt)
            ->setTitleColorizer($defaultColorizer);

        $this->addField($phpPathField);

        $phpVersionField = new runner\php\version\cli();
        $phpVersionField
            ->setTitlePrompt($firstLevelPrompt)
            ->setTitleColorizer($defaultColorizer)
            ->setVersionPrompt($secondLevelPrompt);

        $this->addField($phpVersionField);

        $runnerTestsDurationField = new runner\tests\duration\cli();
        $runnerTestsDurationField
            ->setPrompt(new atoum\cli\prompt("\n" . '> ', $defaultInfoColorizer))
            ->setTitleColorizer($defaultInfoColorizer);

        $this->addField($runnerTestsDurationField);

        $runnerTestsMemoryField = new runner\tests\memory\cli();
        $runnerTestsMemoryField
            ->setPrompt(new atoum\cli\prompt('> ', $defaultInfoColorizer))
            ->setTitleColorizer($defaultInfoColorizer);

        $this->addField($runnerTestsMemoryField);

        $this->runnerTestsCoverageField = new runner\tests\coverage\cli();
        $this->runnerTestsCoverageField
            ->setTitlePrompt($firstLevelPrompt)
            ->setTitleColorizer($defaultColorizer)
            ->setClassPrompt($secondLevelPrompt)
            ->setMethodPrompt($thirdLevelPrompt);

        $this->addField($this->runnerTestsCoverageField);

        $runnerDurationField = new runner\duration\cli();
        $runnerDurationField
            ->setPrompt(new atoum\cli\prompt('> ', $defaultInfoColorizer))
            ->setTitleColorizer($defaultInfoColorizer);

        $this->addField($runnerDurationField);

        $runnerResultField = new Fields\Result();
        $runnerResultField
            ->setSuccessColorizer(new Colorizer('background(' . Colors::GREEN . ') foreground(' . Colors::WHITE . ')'))
            ->setFailureColorizer(new Colorizer('background(' . Colors::MAGENTA . ') foreground(' . Colors::WHITE . ')'));

        $this->addField($runnerResultField);

        $failureColorizer   = new Colorizer('foreground(' . Colors::MAGENTA . ')');
        $failureTitlePrompt = new atoum\cli\prompt("\n");
        $failureTitlePrompt->setColorizer($failureColorizer);
        $failurePrompt = clone $secondLevelPrompt;
        $failurePrompt->setColorizer($failureColorizer);

        $runnerFailuresField = new runner\failures\cli();
        $runnerFailuresField
            ->setTitlePrompt($failureTitlePrompt)
            ->setTitleColorizer($failureColorizer)
            ->setMethodPrompt($failurePrompt);

        $this->addField($runnerFailuresField);

        $runnerOutputsField = new runner\outputs\cli();
        $runnerOutputsField
            ->setTitlePrompt($firstLevelPrompt)
            ->setTitleColorizer($defaultColorizer)
            ->setMethodPrompt($secondLevelPrompt);

        $this->addField($runnerOutputsField);

        $errorColorizer   = new Colorizer('foreground(' . Colors::YELLOW . ')');
        $errorTitlePrompt = new atoum\cli\prompt("\n");
        $errorTitlePrompt->setColorizer($errorColorizer);
        $errorMethodPrompt = clone $secondLevelPrompt;
        $errorMethodPrompt->setColorizer($errorColorizer);
        $errorPrompt = clone $thirdLevelPrompt;
        $errorPrompt->setColorizer($errorColorizer);

        $runnerErrorsField = new runner\errors\cli();
        $runnerErrorsField
            ->setTitlePrompt($errorTitlePrompt)
            ->setTitleColorizer($errorColorizer)
            ->setMethodPrompt($errorMethodPrompt)
            ->setErrorPrompt($errorPrompt);

        $this->addField($runnerErrorsField);

        $exceptionColorizer   = new Colorizer('foreground(' . Colors::YELLOW . ')');
        $exceptionTitlePrompt = new atoum\cli\prompt("\n");
        $exceptionTitlePrompt->setColorizer($exceptionColorizer);
        $exceptionMethodPrompt = clone $secondLevelPrompt;
        $exceptionMethodPrompt->setColorizer($exceptionColorizer);
        $exceptionPrompt = clone $thirdLevelPrompt;
        $exceptionPrompt->setColorizer($exceptionColorizer);

        $runnerExceptionsField = new runner\exceptions\cli();
        $runnerExceptionsField
            ->setTitlePrompt($exceptionTitlePrompt)
            ->setTitleColorizer($exceptionColorizer)
            ->setMethodPrompt($exceptionMethodPrompt)
            ->setExceptionPrompt($exceptionPrompt);

        $this->addField($runnerExceptionsField);

        $uncompletedTestColorizer   = new Colorizer('foreground(' . Colors::GRAY . ')');
        $uncompletedTestTitlePrompt = new atoum\cli\prompt("\n");
        $uncompletedTestTitlePrompt->setColorizer($uncompletedTestColorizer);
        $uncompletedTestMethodPrompt = clone $secondLevelPrompt;
        $uncompletedTestMethodPrompt->setColorizer($uncompletedTestColorizer);
        $uncompletedTestOutputPrompt = clone $thirdLevelPrompt;
        $uncompletedTestOutputPrompt->setColorizer($uncompletedTestColorizer);

        $runnerUncompletedField = new Fields\Uncompleted();
        $runnerUncompletedField
            ->setTitlePrompt($uncompletedTestTitlePrompt)
            ->setTitleColorizer($uncompletedTestColorizer)
            ->setMethodPrompt($uncompletedTestMethodPrompt)
            ->setOutputPrompt($uncompletedTestOutputPrompt);

        $this->addField($runnerUncompletedField);

        $voidTestColorizer   = new Colorizer('foreground(' . Colors::YELLOW . ')');
        $voidTestTitlePrompt = new atoum\cli\prompt("\n");
        $voidTestTitlePrompt->setColorizer($voidTestColorizer);
        $voidTestMethodPrompt = clone $secondLevelPrompt;
        $voidTestMethodPrompt->setColorizer($voidTestColorizer);

        $runnerVoidField = new Fields\Nil();
        $runnerVoidField
            ->setTitlePrompt($voidTestTitlePrompt)
            ->setTitleColorizer($voidTestColorizer)
            ->setMethodPrompt($voidTestMethodPrompt);

        $this->addField($runnerVoidField);

        $skippedTestColorizer    = new Colorizer('foreground(' . Colors::GRAY . ')');
        $skippedTestMethodPrompt = clone $secondLevelPrompt;
        $skippedTestMethodPrompt->setColorizer($skippedTestColorizer);

        $runnerSkippedField = new runner\tests\skipped\cli();
        $runnerSkippedField
            ->setTitlePrompt($firstLevelPrompt)
            ->setTitleColorizer($skippedTestColorizer)
            ->setMethodPrompt($skippedTestMethodPrompt);

        $this->addField($runnerSkippedField);

        $testRunField = new test\run\cli();
        $testRunField
            ->setPrompt(new atoum\cli\prompt("\n" . 'Suite ', $defaultPromptColorizer))
            ->setColorizer($defaultColorizer);

        $this->addField($testRunField);

        $this->addField(new test\event\cli());

        $testDurationField = new Fields\Duration();
        $testDurationField
            ->setPrompt($secondLevelPrompt);

        $this->addField($testDurationField);

        $testMemoryField = new Fields\Memory();
        $testMemoryField
            ->setPrompt($secondLevelPrompt);

        $this->addField($testMemoryField);
    }

    public function hideClassesCoverageDetails()
    {
        $this->runnerTestsCoverageField->hideClassesCoverageDetails();

        return $this;
    }

    public function hideMethodsCoverageDetails()
    {
        $this->runnerTestsCoverageField->hideMethodsCoverageDetails();

        return $this;
    }
}
