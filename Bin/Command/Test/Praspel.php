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
 * \Hoa\Test\Praspel\Compiler
 */
-> import('Test.Praspel.Compiler', true)

/**
 * \Hoa\Test\Sampler\Random
 */
-> import('Test.Sampler.Random')

/**
 * \Hoa\Test\Selector\Random
 */
-> import('Test.Selector.Random')

/**
 * \Hoa\Test\Praspel\Visitor\Praspel
 */
-> import('Test.Praspel.Visitor.Praspel')

/**
 * \Hoa\Test\Praspel\Visitor\Php
 */
-> import('Test.Praspel.Visitor.Php')

/**
 * \Hoa\Realdom
 */
-> import('Realdom.~')

/**
 * \Hoa\File\Read
 */
-> import('File.Read');

}

namespace Bin\Command\Test {

/**
 * Class \Bin\Command\Test\Praspel.
 *
 * Interactive interpreter for Praspel.
 *
 * @author     Ivan Enderlin <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright © 2007-2011 Ivan Enderlin.
 * @license    New BSD License
 */

class Praspel extends \Hoa\Console\Dispatcher\Kit {

    /**
     * Options description.
     *
     * @var \Bin\Command\Test\Praspel array
     */
    protected $options = array(
        array('file', \Hoa\Console\GetOption::REQUIRED_ARGUMENT, 'f'),
        array('help', \Hoa\Console\GetOption::NO_ARGUMENT,       'h'),
        array('help', \Hoa\Console\GetOption::NO_ARGUMENT,       '?')
    );



    /**
     * The entry method.
     *
     * @access  public
     * @return  int
     */
    public function main ( ) {

        $filename = null;

        while(false !== $c = $this->getOption($v)) switch($c) {

            case 'f':
                $filename = $v;
              break;

            case 'h':
            case '?':
                return $this->usage();
              break;
        }

        if(null !== $filename) {

            $file = new \Hoa\File\Read($filename);
            $code = $file->readAll();
        }
        else
            $this->parser->listInputs($code);

        if(null === $code)
            $code = 'h';

        $praspel  = null;
        $ccode    = null;
        $variable = null;
        $domain   = null;
        $vPraspel = new \Hoa\Test\Praspel\Visitor\Praspel();
        $vPHP     = new \Hoa\Test\Praspel\Visitor\Php();

        \Hoa\Realdom::setSampler(new \Hoa\Test\Sampler\Random());

        do {

            switch($code) {

                case 'h':
                case 'help':
                    cout('Usage:');
                    cout('    h[elp]         to print this help;');
                    cout('    <praspel code> to interprete a Praspel code;');
                    cout('    c[ode]         to print current interpreted Praspel code;');
                    cout('    [r[esample]]   to get a new value;');
                    cout('    d[ebug]        to print informations about debug;');
                    cout('    q[uit]         to quit.');
                    cout();
                  break;

                case 'c':
                case 'code':
                    cout($ccode);
                  break;

                case '':
                case 'r':
                case 'resample':
                    if(null === $variable) {

                        cout('Hum hum… maybe you forget to write a Praspel code?');
                        break;
                    }

                    $selection = new \Hoa\Test\Selector\Random(
                        $praspel->getClause('requires')->getVariables()
                    );
                    var_dump($variable->selectDomain($selection->current())->sample());
                  break;

                case 'd':
                case 'debug':
                    $dcode = 'h';

                    do {

                        switch($dcode) {

                            case 'ph':
                            case 'php':
                                cout($vPHP->visit($praspel));
                              break;

                            case 'pr':
                            case 'praspel':
                                cout($vPraspel->visit($praspel));
                              break;

                            case 'q':
                            case 'quit':
                              break 2;

                            case 'h':
                            case 'help':
                            default:
                                cout('Usage:');
                                cout('    h[elp]    to print this help;');
                                cout('    ph[p]     to print PHP code;');
                                cout('    pr[aspel] to print Praspel code;');
                                cout('    q[uit]    to quit.');
                                cout();
                              break;
                        }

                        cout();

                    } while('quit' != $dcode = $this->readLine('debug> '));
                  break;

                case 'q':
                case 'quit':
                    cout('Bye bye!');
                  break 2;

                default:
                    $ccode = trim($code, ';');
                    $code  = '@requires i: ' . $ccode . ';';

                    try {

                        $praspel  = praspel($code);
                    }
                    catch ( \Hoa\Core\Exception $e ) {

                        cout($e->raise(true));

                        continue;
                    }

                    $variable  = $praspel->getClause('requires')->getVariable('i');
                    $selection = new \Hoa\Test\Selector\Random(
                        $praspel->getClause('requires')->getVariables()
                    );
                    var_dump($variable->selectDomain($selection->current())->sample());
            }

            cout();

        } while('quit' != $code = $this->readline('> '));

        return;
    }

    /**
     * The command usage.
     *
     * @access  public
     * @return  int
     */
    public function usage ( ) {

        cout('Usage   : test:praspel <options> [code]');
        cout('Options :');
        cout($this->makeUsageOptionsList(array(
            'f'    => 'Read a file to initialize the interpreter.',
            'help' => 'This help.'
        )));

        return;
    }
}

}
