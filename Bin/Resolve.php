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

namespace Hoa\Dns\Bin;

use Hoa\Console;
use Hoa\Dns;
use Hoa\Event;
use Hoa\Socket;

/**
 * Class Hoa\Dns\Bin\Resolve.
 *
 * Quick DNS resolver.
 *
 * @copyright  Copyright © 2007-2017 Hoa community
 * @license    New BSD License
 */
class Resolve extends Console\Dispatcher\Kit
{
    /**
     * Options description.
     *
     * @var array
     */
    protected $options = [
        ['listen', Console\GetOption::REQUIRED_ARGUMENT, 'l'],
        ['help',   Console\GetOption::NO_ARGUMENT,       'h'],
        ['help',   Console\GetOption::NO_ARGUMENT,       '?']
    ];



    /**
     * The entry method.
     *
     * @return  int
     */
    public function main()
    {
        $listen = '127.0.0.1:57005';

        while (false !== $c = $this->getOption($v)) {
            switch ($c) {
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
            }
        }

        $redirections = [];
        $inputs       = $this->parser->getInputs();

        if (empty($inputs)) {
            $this->usage();

            return;
        }

        for ($i = 0, $max = count($inputs); $i < $max; $i += 3) {
            $from = str_replace('#', '\#', $inputs[$i]);

            if (false === @preg_match('#^' . $from . '$#', '', $_)) {
                echo 'Expression ', $from, ' does not compile correctly.', "\n";

                return 1;
            }

            if ('to' !== $inputs[$i + 1]) {
                continue;
            }

            $to                  = $inputs[$i + 2];
            $redirections[$from] = $to;
        }

        $dns = new Dns\Resolver(new Socket\Server('udp://' . $listen));
        $dns->on(
            'query',
            function (Event\Bucket $bucket) use (&$redirections) {
                $data = $bucket->getData();
                echo
                    'Resolving domain ', $data['domain'],
                    ' of type ', $data['type'], ' to ';

                foreach ($redirections as $from => $to) {
                    if (0 !== preg_match('#^' . $from . '$#', $data['domain'], $_)) {
                        echo $to, ".\n";

                        return $to;
                    }
                }

                echo '127.0.0.1 (default).', "\n";

                return '127.0.0.1';
            }
        );

        echo 'Server is up, on udp://' . $listen . '!', "\n\n";
        $dns->run();

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
            'Usage   : dns:resolve <options> [<regex> to <ip>]+', "\n",
            'Options :', "\n",
            $this->makeUsageOptionsList([
                'l'    => 'Socket URI to listen (default: 127.0.0.1:57005).',
                'help' => 'This help.'
            ]), "\n",
            'Example: `… dns:resolve \'foo.*\' to 1.2.3.4 \\', "\n",
            '                        \'bar.*\' to 5.6.7.8`.', "\n";

        return;
    }
}

__halt_compiler();
Quick DNS resolver.
