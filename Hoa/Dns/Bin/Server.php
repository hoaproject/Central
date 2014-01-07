<?php

/**
 * Hoa
 *
 *
 * @license
 *
 * New BSD License
 *
 * Copyright © 2007-2014, Ivan Enderlin. All rights reserved.
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
 * \Hoa\Socket\Server
 */
-> import('Socket.Server')

/**
 * \Hoa\Dns
 */
-> import('Dns.~');

}

namespace Hoa\Dns\Bin {

/**
 * Class Hoa\Dns\Bin\Server.
 *
 * Quick DNS server.
 *
 * @author     Ivan Enderlin <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright © 2007-2014 Ivan Enderlin.
 * @license    New BSD License
 */

class Server extends \Hoa\Console\Dispatcher\Kit {

    /**
     * Options description.
     *
     * @var \Hoa\Dns\Bin\Server array
     */
    protected $options = array(
        array('listen', \Hoa\Console\GetOption::REQUIRED_ARGUMENT, 'l'),
        array('help',   \Hoa\Console\GetOption::NO_ARGUMENT,       'h'),
        array('help',   \Hoa\Console\GetOption::NO_ARGUMENT,       '?')
    );



    /**
     * The entry method.
     *
     * @access  public
     * @return  int
     */
    public function main ( ) {

        $listen = '127.0.0.1:57005';

        while(false !== $c = $this->getOption($v)) switch($c) {

            case 'l':
                $listen = $v;
              break;

            case '__ambiguous':
                $this->resolveOptionAmbiguity($v);
              break;

            case 'h':
            case '?':
            default:
                return $this->usage();
              break;
        }

        $redirections = array();
        $inputs       = $this->parser->getInputs();

        if(empty($inputs)) {

            $this->usage();

            return;
        }

        for($i = 0, $max = count($inputs); $i < $max; $i += 3) {

            $from = str_replace('#', '\#', $inputs[$i]);

            if(false === @preg_match('#^' . $from . '$#', '', $_)) {

                echo 'Expression ', $from, ' does not compile correctly.', "\n";

                return 1;
            }

            if('to' !== $inputs[$i + 1])
                continue;

            $to                  = $inputs[$i + 2];
            $redirections[$from] = $to;
        }

        $dns = new \Hoa\Dns(new \Hoa\Socket\Server('udp://' . $listen));
        $dns->on('query', function ( \Hoa\Core\Event\Bucket $bucket )
                          use ( &$redirections ) {

            $data = $bucket->getData();
            echo 'Resolving domain ', $data['domain'],
                 ' of type ', $data['type'], ' to ';

            foreach($redirections as $from => $to)
                if(0 !== preg_match('#^' . $from . '$#', $data['domain'], $_)) {

                    echo $to, ".\n";

                    return $to;
                }

            echo '127.0.0.1 (default).', "\n";

            return '127.0.0.1';
        });

        echo 'Server is up, on udp://' . $listen . '!', "\n\n";
        $dns->run();

        return;
    }

    /**
     * The command usage.
     *
     * @access  public
     * @return  int
     */
    public function usage ( ) {

        echo 'Usage   : dns:server <options> [<regex> to <ip>]+', "\n",
             'Options :', "\n",
             $this->makeUsageOptionsList(array(
                 'l'    => 'Socket URI to listen (default: 127.0.0.1:57005).',
                 'help' => 'This help.'
             )), "\n",
             'Example: `… dns:server \'foo.*\' to 1.2.3.4 \\', "\n",
             '                       \'bar.*\' to 5.6.7.8`.', "\n";

        return;
    }
}

}

__halt_compiler();
Quick DNS server.
