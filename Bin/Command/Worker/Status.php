<?php

/**
 * Hoa
 *
 *
 * @license
 *
 * New BSD License
 *
 * Copyright © 2007-2011, Ivan Enderlin. All rights reserved.
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

namespace {

from('Hoa')

/**
 * \Hoa\Worker\Shared
 */
-> import('Worker.Shared');

}

namespace Bin\Command\Worker {

/**
 * Class \Bin\Command\Main\Status.
 *
 * Status all workers.
 *
 * @author     Ivan Enderlin <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright © 2007-2011 Ivan Enderlin.
 * @license    New BSD License
 */

class Status extends \Hoa\Console\Dispatcher\Kit {

    /**
     * Options description.
     *
     * @var VersionCommand array
     */
    protected $options     = array(
        array('help', \Hoa\Console\GetOption::NO_ARGUMENT, 'h'),
        array('help', \Hoa\Console\GetOption::NO_ARGUMENT, '?')
    );



    /**
     * The entry method.
     *
     * @access  public
     * @return  int
     */
    public function main ( ) {

        while(false !== $c = $this->getOption($v)) switch($c) {

            case 'h':
            case '?':
                return $this->usage();
              break;
        }

        $run  = resolve('hoa://Data/Variable/Run/');
        $outi = array(array('ID', 'PID', 'Socket', 'Uptime', 'Messages', 'Last'));
        $outm = array();
        $now  = new \DateTime();
        $t    = 0;

        cout($this->stylize('Shared worker informations', 'info'));
        cout();

        foreach(glob($run . DS . '*.wid') as $wid) {

            $worker = new \Hoa\Worker\Shared(substr(basename($wid), 0, -4));
            $infos  = $worker->getInformations();
            $uptime = new \DateTime();
            $uptime->setTimestamp((int) $infos['start']);
            $last   = new \DateTime();
            $last->setTimestamp((int) $infos['last_message']);

            $outi[]  = array(
                $infos['id'],
                $infos['pid'],
                $infos['socket'],
                $uptime->diff($now)->format('%ad%H:%I:%S'),
                $infos['messages'],
                0 === $infos['last_message']
                    ? '-'
                    : $last->diff($now)->format('%ad%H:%I:%S')
            );

            $outm[] = $infos;

            ++$t;
        }

        cout(\Hoa\Console\Chrome\Text::columnize($outi, 0, 1, '|'));

        $max_id   = 0;
        $max_peak = 0;

        foreach($outm as $m) {

            $max_id < strlen($m['id'])
            and $max_id = strlen($m['id']);

            $max_peak < $m['memory_peak']
            and $max_peak = $m['memory_peak'];
        }

        foreach($outm as $m) {

            $outmm  = str_pad($m['id'], $max_id) . '  ';
            $max    = (int) (($m['memory_peak'] * 39) / $max_peak);
            $peak   = (int) (($m['memory_allocated_peak'] * 40) / $max_peak);
            $memory = (int) (($m['memory_allocated'] * 40) / $max_peak);

            for($i = 0; $i < $memory - 1; ++$i)
                $outmm .= $this->stylize('|', 'success');

            for(; $i < $peak; ++$i)
                $outmm .= $this->stylize('|', 'info');

            for(; $i < $max; ++$i)
                $outmm .= ' ';

            $outmm .= $this->stylize('|', 'nosuccess');

            for(++$i; $i < 40; ++$i)
                $outmm .= ' ';

            $outmm .= '  ' .
                      $this->stylize(
                        number_format($m['memory_allocated'] / 1024) . 'Kb',
                        'success'
                      ) . ' ' .
                      $this->stylize(
                        number_format($m['memory_allocated_peak'] / 1024) . 'Kb',
                        'info'
                      ) . ' ' .
                      $this->stylize(
                        number_format($m['memory_peak'] / 1024) . 'Kb',
                        'nosuccess'
                      );

            echo $outmm . "\n";
        }

        cout();
        cout($t . ' shared worker' . ($t > 1 ? 's are' : ' is') . ' running.');

        return;
    }

    /**
     * The command usage.
     *
     * @access  public
     * @return  int
     */
    public function usage ( ) {

        cout('Usage   : worker:list <options>');
        cout('Options :');
        cout($this->makeUsageOptionsList(array(
            'help' => 'This help.'
        )));
        cout('Legend: ' . $this->stylize('allocated', 'success') . ', ' .
             $this->stylize('allocated peak', 'info') . ', ' .
             $this->stylize('peak', 'nosuccess') . '.');

        return;
    }
}

}
