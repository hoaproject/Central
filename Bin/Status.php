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
 * Class \Hoa\Worker\Bin\Status.
 *
 * Status all workers.
 *
 * @copyright  Copyright © 2007-2017 Hoa community
 * @license    New BSD License
 */
class Status extends Console\Dispatcher\Kit
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

                    break;

                case '__ambiguous':
                    $this->resolveOptionAmbiguity($v);

                    break;
            }
        }

        $run      = resolve($run);
        $protocol = Protocol::getInstance();
        $protocol['Data']['Variable']['Run']->setReach("\r" . $run . DS);

        $outi = [['ID', 'PID', 'Socket', 'Uptime', 'Messages', 'Last']];
        $outm = [];
        $now  = new \DateTime();
        $t    = 0;

        echo Console\Chrome\Text::colorize(
            'Shared worker information',
            'foreground(yellow)'
        ), "\n\n";

        foreach (glob($run . DS . '*.wid') as $wid) {
            $worker = new Worker\Shared(substr(basename($wid), 0, -4));
            $infos  = $worker->getInformation();
            $uptime = new \DateTime();
            $uptime->setTimestamp((int) $infos['start']);
            $last   = new \DateTime();
            $last->setTimestamp((int) $infos['last_message']);

            $outi[]  = [
                $infos['id'],
                $infos['pid'],
                $infos['socket'],
                $uptime->diff($now)->format('%ad%H:%I:%S'),
                $infos['messages'],
                0 === $infos['last_message']
                    ? '-'
                    : $last->diff($now)->format('%ad%H:%I:%S')
            ];

            $outm[] = $infos;

            ++$t;
        }

        echo Console\Chrome\Text::columnize($outi, 0, 1, '|'), "\n";

        $max_id   = 0;
        $max_peak = 0;

        foreach ($outm as $m) {
            $max_id < strlen($m['id'])
            and $max_id = strlen($m['id']);

            $max_peak < $m['memory_peak']
            and $max_peak = $m['memory_peak'];
        }

        foreach ($outm as $m) {
            $outmm  = str_pad($m['id'], $max_id) . '  ';
            $max    = (int) (($m['memory_peak'] * 39) / $max_peak);
            $peak   = (int) (($m['memory_allocated_peak'] * 40) / $max_peak);
            $memory = (int) (($m['memory_allocated'] * 40) / $max_peak);

            for ($i = 0; $i < $memory - 1; ++$i) {
                $outmm .= Console\Chrome\Text::colorize(
                    '|',
                    'foreground(green)'
                );
            }

            for (; $i < $peak; ++$i) {
                $outmm .= Console\Chrome\Text::colorize(
                    '|',
                    'foreground(yellow)'
                );
            }

            for (; $i < $max; ++$i) {
                $outmm .= ' ';
            }

            $outmm .= Console\Chrome\Text::colorize(
                '|',
                'foreground(red)'
            );

            for (++$i; $i < 40; ++$i) {
                $outmm .= ' ';
            }

            $outmm .=
                '  ' .
                Console\Chrome\Text::colorize(
                    number_format($m['memory_allocated'] / 1024) . 'Kb',
                    'foreground(green)'
                ) . ' ' .
                Console\Chrome\Text::colorize(
                     number_format($m['memory_allocated_peak'] / 1024) . 'Kb',
                    'foreground(yellow)'
                ) . ' ' .
                Console\Chrome\Text::colorize(
                     number_format($m['memory_peak'] / 1024) . 'Kb',
                    'foreground(red)'
                );

            echo $outmm . "\n";
        }

        echo
            "\n", $t,
            ' shared worker', ($t > 1 ? 's are' : ' is'), ' running.', "\n";

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
            'Usage   : worker:status <options>', "\n",
            'Options :', "\n",
            $this->makeUsageOptionsList([
                'run'  => 'Define hoa://Data/Variable/Run/ path.',
                'help' => 'This help.'
            ]), "\n",
            'Legend: ',
            Console\Chrome\Text::colorize(
               'allocated',
               'foreground(green)'
            ), ', ',
            Console\Chrome\Text::colorize(
               'allocated peak',
               'foreground(yellow)'
            ), ', ',
            Console\Chrome\Text::colorize(
                'peak',
                'foreground(red)'
            ), '.', "\n";

        return;
    }
}

__halt_compiler();
Get status of all workers.
