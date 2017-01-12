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

namespace Hoa\Worker\Bin;

use Hoa\Console;
use Hoa\Protocol;
use Hoa\Worker;

/**
 * Class \Hoa\Worker\Bin\Stop.
 *
 * Stop worker.
 *
 * @copyright  Copyright © 2007-2017 Hoa community
 * @license    New BSD License
 */
class Stop extends Console\Dispatcher\Kit
{
    /**
     * Options description.
     *
     * @var array
     */
    protected $options = [
        ['run',  Console\GetOption::REQUIRED_ARGUMENT, 'r'],
        ['help', Console\GetOption::NO_ARGUMENT,       'h'],
        ['help', Console\GetOption::NO_ARGUMENT,       '?']
    ];



    /**
     * The entry method.
     *
     * @return  void
     */
    public function main()
    {
        $run = 'hoa://Data/Variable/Run';

        while (false !== $c = $this->getOption($v)) {
            switch ($c) {
                case 'r':
                    $run = $v;

                    break;

                case 'h':
                case '?':
                    return $this->usage();

                case '__ambiguous':
                    $this->resolveOptionAmbiguity($v);

                    break;
            }
        }

        $this->parser->listInputs($workerId);

        if (null === $workerId) {
            return $this->usage();
        }

        $run      = resolve($run);
        $protocol = Protocol::getInstance();
        $protocol['Data']['Variable']['Run']->setReach("\r" . $run . DS);

        $password = $this->readPassword('Password: ');
        $sw       = new Worker\Backend\Shared($workerId, $password);
        $sw->stop();

        return;
    }

    /**
     * The command usage.
     *
     * @return  void
     */
    public function usage()
    {
        echo
            'Usage   : worker:stop <options> <worker_id>', "\n",
            'Options :', "\n",
            $this->makeUsageOptionsList([
                'run'  => 'Define hoa://Data/Variable/Run/ path.',
                'help' => 'This help.'
            ]), "\n";

        return;
    }
}

__halt_compiler();
Stop a worker.
