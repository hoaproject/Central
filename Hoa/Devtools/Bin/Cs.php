<?php

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

namespace Hoa\Devtools\Bin;

use Hoa\Console;
use Hoa\Event;

/**
 * Class \Hoa\Devtools\Bin\Cs.
 *
 * Wrapper around `php-cs-fixer`.
 *
 * @copyright  Copyright © 2007-2017 Hoa community
 * @license    New BSD License
 */
class Cs extends Console\Dispatcher\Kit
{
    /**
     * Options description.
     *
     * @var array
     */
    protected $options = [
        ['dry-run', Console\GetOption::NO_ARGUMENT, 'd'],
        ['diff',    Console\GetOption::NO_ARGUMENT, 'D'],
        ['verbose', Console\GetOption::NO_ARGUMENT, 'v'],
        ['help',    Console\GetOption::NO_ARGUMENT, 'h'],
        ['help',    Console\GetOption::NO_ARGUMENT, '?']
    ];



    /**
     * The entry method.
     *
     * @return  int
     */
    public function main()
    {
        $dryRun  = false;
        $diff    = false;
        $verbose = false;

        while (false !== $c = $this->getOption($v)) {
            switch ($c) {
                case '__ambiguous':
                    $this->resolveOptionAmbiguity($v);

                    break;

                case 'd':
                    $dryRun = true;

                    break;

                case 'v':
                    $verbose = true;

                    break;

                case 'D':
                    $diff = true;

                    break;

                case 'h':
                case '?':
                default:
                    return $this->usage();
            }
        }

        $this->parser->listInputs($path);

        if (empty($path)) {
            return $this->usage();
        }

        $phpCsFixer        = Console\Processus::locate('php-cs-fixer');
        $configurationFile = resolve(
            'hoa://Library/Devtools/Resource/PHPCSFixer/ConfigurationFile.php'
        );

        if (empty($phpCsFixer)) {
            throw new Console\Exception('php-cs-fixer binary is not found.', 0);
        }

        $arguments = ['fix', '--config' => $configurationFile];

        if (true === $dryRun) {
            $arguments[] = '--dry-run';
        }

        if (true === $verbose) {
            $arguments[] = '-vvv';
        }

        if (true === $diff) {
            $arguments[] = '--diff';
        }

        $arguments['--allow-risky'] = 'yes';

        $arguments[] = $path;

        $processus = new Console\Processus($phpCsFixer, $arguments);
        $processus->on('input', function () {
            return false;
        });
        $processus->on('output', function (Event\Bucket $bucket) {
            echo $bucket->getData()['line'], "\n";

            return;
        });
        $processus->run();

        return;
    }

    /**
     * The command usage.
     *
     * @return  int
     */
    public function usage()
    {
        echo
            'Usage   : devtools:cs <options> path', "\n",
            'Options :', "\n",
            $this->makeUsageOptionsList([
                'd'    => 'Only shows which files would have been modified.',
                'D'    => 'Produce diff for each file.',
                'v'    => 'Be verbose.',
                'help' => 'This help.'
            ]), "\n";

        return;
    }
}

__halt_compiler();
Wrapper around `php-cs-fixer`.
