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

namespace Hoa\Test\Bin;

use Hoa\Console;
use Hoa\Test;

/**
 * Class Hoa\Test\Bin\Generate.
 *
 * Automatically generate test suites based on examples in API documentations
 * or from contracts written in Praspel.
 *
 * @license    New BSD License
 */
class Generate extends Console\Dispatcher\Kit
{
    /**
     * Options description.
     *
     * @var array
     */
    protected $options = [
        ['praspel',   Console\GetOption::NO_ARGUMENT,       'p'],
        ['directory', Console\GetOption::REQUIRED_ARGUMENT, 'd'],
        ['namespace', Console\GetOption::REQUIRED_ARGUMENT, 'n'],
        ['help',      Console\GetOption::NO_ARGUMENT,       'h'],
        ['help',      Console\GetOption::NO_ARGUMENT,       '?']
    ];



    /**
     * The entry method.
     *
     * @return  int
     */
    public function main(): int
    {
        $directoryToScan = null;
        $namespaceToScan = null;

        while (false !== $c = $this->getOption($v)) {
            switch ($c) {
                case 'p':
                    throw new \RuntimeException(
                        'The `-p` option is not implemented yet.',
                        0
                    );

                    break;

                case 'd':
                    $directoryToScan = $v;

                    break;

                case 'n':
                    $namespaceToScan = str_replace('.', '\\', $v);

                    break;

                case '__ambiguous':
                    $this->resolveOptionAmbiguity($v);

                    break;

                case 'h':
                case '?':
                default:
                    return $this->usage();
            }
        }

        if (empty($directoryToScan) || empty($namespaceToScan)) {
            return $this->usage();
        }

        return 0;
    }

    /**
     * The command usage.
     *
     * @return  int
     */
    public function usage(): int
    {
        echo
            'Usage   : test:generate <options>', "\n",
            'Options :', "\n",
            $this->makeUsageOptionsList([
                'p'    => 'Generate test suites based on contracts written in Praspel.',
                'd'    => 'Directory containing entities to parse (classes, interfaces…).',
                'n'    => 'Restrict generation to a particular namespace (`.` is replaced by namespace separator).',
                'help' => 'This help.'
            ]), "\n";

        return 0;
    }
}

__halt_compiler();
Automatically generate test suites.
