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
-> import('Socket.Internet.DomainName')
-> import('Socket.Connection.Server')
-> import('File.Read');

/**
 * Class DebuggerCommand.
 *
 * A simple debugger.
 *
 * @author     Ivan Enderlin <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright © 2007-2011 Ivan Enderlin.
 * @license    New BSD License
 */

class DebuggerCommand extends \Hoa\Console\Command\Generic {

    /**
     * Author name.
     *
     * @var VersionCommand string
     */
    protected $author      = 'Ivan Enderlin';

    /**
     * Program name.
     *
     * @var VersionCommand string
     */
    protected $programName = 'Debugger';

    /**
     * Options description.
     *
     * @var VersionCommand array
     */
    protected $options     = array(
        array('help', parent::NO_ARGUMENT, 'h'),
        array('help', parent::NO_ARGUMENT, '?')
    );



    /**
     * The entry method.
     *
     * @access  public
     * @return  int
     */
    public function main ( ) {

        while(false !== $c = parent::getOption($v)) {

            switch($c) {

                case 'h':
                case '?':
                    return $this->usage();
                  break;
            }
        }

        $ip         = new \Hoa\Socket\Internet\DomainName(
            'localhost',
            57005,
            'tcp'
        );
        $server     = new \Hoa\Socket\Connection\Server($ip);
        $exceptions = array();
        $exi        = 0;

        $server->connectAndWait();

        cout('Debugger is up!');

        while(true) foreach($server->select() as $node) {

            try {

                $buffer = $server->readLine();

                if(empty($buffer)) {

                    $server->disconnect();

                    if(0 == $exi) {

                        cout(' without any error!' . "\n");

                        continue;
                    }

                    cout("\n");
                    $this->select($exceptions, $exi);

                    continue;
                }

                if('open' == $buffer) {

                    $exceptions = array();
                    $exi        = 0;
                    cout("\n" . '[' . date('H:i:s') . '] ' .
                         'A new execution is running…',
                         \Hoa\Console\Core\Io::NO_NEW_LINE);

                    continue;
                }

                $exception          = unserialize($buffer);
                $exceptions[$exi++] = $exception;

                cout(
                    "\n" .
                    $exi . '. ' .
                    $exception->getFrom() . ': ' .
                    $exception->getFormattedMessage(),
                    \Hoa\Console\Core\Io::NO_NEW_LINE
                );
            }
            catch ( Exception $e ) {

                cout(
                    "\n\n" . '** Exception **' . "\n" .
                    $e->raise() . "\n"
                );
            }
        }

        return HC_SUCCESS;
    }

    /**
     * The command usage.
     *
     * @access  public
     * @return  int
     */
    public function usage ( ) {

        cout('Usage   : main:debugger <options>');
        cout('Options :');
        cout(parent::makeUsageOptionsList(array(
            'help' => 'This help.'
        )));

        return HC_SUCCESS;
    }

    public function select ( Array $exceptions, $exi ) {

        cout(
            'What exception would you like to select? ' .
            '(in [1-' .  $exi . '], q[uit] to quit, h[elp] to get help)'
        );

        $in        = $this->cin('> ');
        $exception = null;

        while('q' !== $in && 'quit'  !== $in) {

            switch($in) {

                case 'l':
                case 'list':
                    foreach($exceptions as $i => $e)
                        cout(
                            $i + 1 . '. ' . $e->getFrom() . ': ' .
                            $e->getFormattedMessage()
                        );
                  break;

                case 'c':
                case 'code':
                    if(null === $exception)
                        break;

                    $_foo = strlen((string) ($line + 8));

                    for($_i = max(1, $line - 8),
                        $_m = min($lines, $line + 8);
                        $_i <= $_m;
                        ++$_i) {

                        $_handle = sprintf('%' . $_foo . 'd', $_i) . '. ';

                        if($_i == $line)
                            $_handle .= '➜  ';
                        else
                            $_handle .= '   ';

                        cout($_handle . $content[$_i - 1]);
                    }
                  break;

                case 'i':
                case 'info':
                    if(null === $exception)
                        break;

                    cout('From: ' . $this->from($trace));
                    cout('Line: ' . $trace['line']);
                    cout('File: ' . $trace['file']);
                  break;

                case 'm':
                case 'message':
                    cout($exception->raise());
                  break;

                case 't':
                case 'trace':
                    if(null === $exception)
                        break;

                    $_j   = count($traces);
                    $_foo = strlen((string) $_j);

                    foreach($traces as $i => $t) {

                        if($ti == $i)
                            $_handle = '➜  ';
                        else
                            $_handle = '   ';

                        $_handle .= sprintf('%' . $_foo . 'd', $_j--) . '. ';
                        $_from    = $this->from($t);
                        $_file    = $t['file'];

                        if(32 <= strlen($_from))
                            $_from = substr($_from, 0, 31) . '…';

                        $_handle .= sprintf('%-32s', $_from) . '  ';

                        if(38 <= strlen($_file))
                            $_file = '…' . substr($_file, -37);

                        $_handle .= $_file . ' @ ' . $t['line'];

                        cout($_handle);
                    }
                  break;

                case 'U':
                case 'UP':
                    $ti      = 0;
                    $in      = 'u';
                  continue 2;

                case 'u':
                case 'up':
                    $ti      = max(0, $ti - 1);
                    $trace   = $traces[$ti];
                    $file    = new \Hoa\File\Read($trace['file']);
                    $content = explode("\n", $file->readAll());
                    $line    = $trace['line'];
                    $lines   = count($content) - 1;
                    $in      = 't';
                  continue 2;

                case 'D':
                case 'DOWN':
                    $ti      = count($traces);
                    $in      = 'd';
                  continue 2;

                case 'd':
                case 'down':
                    $ti      = min(count($traces) - 1, $ti + 1);
                    $trace   = $traces[$ti];
                    $file    = new \Hoa\File\Read($trace['file']);
                    $content = explode("\n", $file->readAll());
                    $line    = $trace['line'];
                    $lines   = count($content) - 1;
                    $in      = 't';
                  continue 2;

                case 'h':
                case 'help':
                    cout(
                        'number    to select an exception;' . "\n" .
                        'l[ist]    to list all exceptions;' . "\n" .
                        'c[ode]    to print the code;' . "\n" .
                        'i[nfo]    to get informations;' . "\n" .
                        'm[essage] to get message error;' . "\n" .
                        't[race]   to print the trace;' . "\n" .
                        'u[p]      to go up in the trace;' . "\n" .
                        'U[P]      to go to the top of the trace;' . "\n" .
                        'd[own]    to go down in the trace;' . "\n" .
                        'D[OWN]    to go to the bottom of the trace;' . "\n" .
                        'h[help]   to print this help.'
                    );
                  break;

                default:
                    $iin       = max(1, min($in, count($exceptions))) - 1;
                    $exception = $exceptions[$iin];
                    $file      = new \Hoa\File\Read($exception->getFile());
                    $content   = explode("\n", $file->readAll());
                    $line      = $exception->getLine();
                    $lines     = count($content) - 1;
                    $traces    = $exception->getBacktrace();
                    array_unshift(
                        $traces,
                        array(
                            'line'     => $exception->getLine(),
                            'file'     => $exception->getFile(),
                            'function' => '*error*'
                        )
                    );

                    for($__i = 0, $__m = count($traces) - 2;
                        $__i <= $__m;
                        ++$__i) {

                        $traces[$__i]['class']    = @$traces[$__i + 1]['class'];
                        $traces[$__i]['function'] = @$traces[$__i + 1]['function'];
                    }

                    $traces[$__i]['class']    = null;
                    $traces[$__i]['function'] = null;

                    $ti        = 0;
                    $trace     = $traces[$ti];
            }

            $in = $this->cin((isset($iin) ? '(#' . ($iin + 1) . ') ' : '') .  '> ');
        }

        return;
    }

    public function from ( $trace ) {

        $from = @$trace['function'] ?: '{main}';

        if(isset($trace['class']))
            $from = $trace['class'] . '::' . $trace['function'];
        elseif(isset($trace['function']))
            $from = $trace['function'];
        else
            $from = '{main}';

        return $from;
    }

    public function cin ( $str ) {

        return cin(
            $str,
            \Hoa\Console\Core\Io::TYPE_NORMAL,
            \Hoa\Console\Core\Io::NO_NEW_LINE
        );
    }

    public static function autoload ( $classname ) {

        from(substr($classname, 0, $pos = strpos($classname, '\\')))
            ->import(str_replace(
                '\\',
                '.',
                substr($classname, $pos + 1)
            ), true);

        return;
    }
}

spl_autoload_register('\DebuggerCommand::autoload');

}
