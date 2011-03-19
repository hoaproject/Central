<?php

/**
 * Hoa
 *
 *
 * @license
 *
 * GNU General Public License
 *
 * This file is part of Hoa Open Accessibility.
 * Copyright (c) 2007, 2011 Ivan ENDERLIN. All rights reserved.
 *
 * HOA Open Accessibility is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * HOA Open Accessibility is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with HOA Open Accessibility; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

namespace {

from('Hoa')

/**
 * \Hoa\Test\Praspel\Compiler
 */
-> import('Test.Praspel.Compiler')

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

/**
 * Class PraspelCommand.
 *
 * Interactive interpreter for Praspel.
 *
 * @author     Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright (c) 2007, 2011 Ivan ENDERLIN.
 */

class PraspelCommand extends \Hoa\Console\Command\Generic {

    /**
     * Author name.
     *
     * @var PraspelCommand string
     */
    protected $author      = 'Ivan Enderlin';

    /**
     * Program name.
     *
     * @var PraspelCommand string
     */
    protected $programName = 'Praspel';

    /**
     * Options description.
     *
     * @var PraspelCommand array
     */
    protected $options     = array(
        array('file', parent::REQUIRED_ARGUMENT, 'f'),
        array('help', parent::NO_ARGUMENT,       'h'),
        array('help', parent::NO_ARGUMENT,       '?')
    );



    /**
     * The entry method.
     *
     * @access  public
     * @return  int
     */
    public function main ( ) {

        $filename = null;

        while(false !== $c = parent::getOption($v)) {

            switch($c) {

                case 'f':
                    $filename = $v;
                  break;

                case 'h':
                case '?':
                    return $this->usage();
                  break;
            }
        }

        if(null !== $filename) {

            $file = new \Hoa\File\Read($filename);
            $code = $file->readAll();
        }
        else
            parent::listInputs($code);

        if(null === $code)
            $code = 'h';

        $compiler = new \Hoa\Test\Praspel\Compiler();
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

                    } while('quit' != $dcode = cin(
                                                   'debug> ',
                                                   \Hoa\Console\Core\Io::TYPE_NORMAL,
                                                   \Hoa\Console\Core\Io::NO_NEW_LINE
                                               ));
                  break;

                case 'q':
                case 'quit':
                    cout('Bye bye!');
                  break 2;

                default:
                    $ccode = trim($code, ';');
                    $code  = '@requires i: ' . $ccode . ';';

                    try {

                        $compiler = new \Hoa\Test\Praspel\Compiler();
                        $compiler->compile($code);

                        $praspel  = $compiler->getRoot();
                    }
                    catch ( \Hoa\Core\Exception $e ) {

                        cout($e->raise());

                        continue;
                    }

                    $variable  = $praspel->getClause('requires')->getVariable('i');
                    $selection = new \Hoa\Test\Selector\Random(
                        $praspel->getClause('requires')->getVariables()
                    );
                    var_dump($variable->selectDomain($selection->current())->sample());
            }

            cout();

        } while('quit' != $code = cin(
                                      '> ',
                                      \Hoa\Console\Core\Io::TYPE_NORMAL,
                                      \Hoa\Console\Core\Io::NO_NEW_LINE
                                  ));

        return HC_SUCCESS;
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
        cout(parent::makeUsageOptionsList(array(
            'f'    => 'Read a file to initialize the interpreter.',
            'help' => 'This help.'
        )));

        return HC_SUCCESS;
    }
}

}
