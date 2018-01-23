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
namespace Hoa\Test\Report\Cli\Fields;

use atoum\report\fields;

class Result extends fields\runner\result\cli
{
    public function __toString()
    {
        $string = "\n" . $this->prompt;

        if (null === $this->testNumber) {
            $string .= $this->locale->_('No test running.');
        } elseif (true === $this->success) {
            $string .= $this->successColorizer->colorize(
                sprintf(
                    $this->locale->_('Success (%s, %s, %s, %s, %s)!'),
                    sprintf($this->locale->__('%s test suite', '%s test suites', $this->testNumber), $this->testNumber),
                    sprintf($this->locale->__('%s/%s test case', '%s/%s test cases', $this->testMethodNumber), $this->testMethodNumber - $this->voidMethodNumber - $this->skippedMethodNumber, $this->testMethodNumber),
                    sprintf($this->locale->__('%s void test case', '%s void test cases', $this->voidMethodNumber), $this->voidMethodNumber),
                    sprintf($this->locale->__('%s skipped test case', '%s skipped test cases', $this->skippedMethodNumber), $this->skippedMethodNumber),
                    sprintf($this->locale->__('%s assertion', '%s assertions', $this->assertionNumber), $this->assertionNumber)
                )
            );
        } else {
            $string .= $this->failureColorizer->colorize(
                sprintf(
                    $this->locale->_('Failure (%s, %s, %s, %s, %s, %s, %s, %s)!'),
                    sprintf($this->locale->__('%s test suite', '%s test suites', $this->testNumber), $this->testNumber),
                    sprintf($this->locale->__('%s/%s test case', '%s/%s test cases', $this->testMethodNumber), $this->testMethodNumber - $this->voidMethodNumber - $this->skippedMethodNumber - $this->uncompletedMethodNumber, $this->testMethodNumber),
                    sprintf($this->locale->__('%s void test case', '%s void test cases', $this->voidMethodNumber), $this->voidMethodNumber),
                    sprintf($this->locale->__('%s skipped test case', '%s skipped test cases', $this->skippedMethodNumber), $this->skippedMethodNumber),
                    sprintf($this->locale->__('%s uncompleted test case', '%s uncompleted test case', $this->uncompletedMethodNumber), $this->uncompletedMethodNumber),
                    sprintf($this->locale->__('%s failure', '%s failures', $this->failNumber), $this->failNumber),
                    sprintf($this->locale->__('%s error', '%s errors', $this->errorNumber), $this->errorNumber),
                    sprintf($this->locale->__('%s exception', '%s exceptions', $this->exceptionNumber), $this->exceptionNumber)
                )
            );
        }

        return $string . "\n";
    }
}
