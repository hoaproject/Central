<?php

/**
 * Hoa Framework
 *
 *
 * @license
 *
 * GNU General Public License
 *
 * This file is part of Hoa Open Accessibility.
 * Copyright (c) 2007, 2009 Ivan ENDERLIN. All rights reserved.
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
 *
 *
 * @category    Data
 *
 */

/**
 * Hoa_Test_Praspel_Compiler
 */
import('Test.Praspel.Compiler');

/**
 * Class PraspelCommand.
 *
 * Praspel interpreter for Praspel.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2009 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 */

class PraspelCommand extends Hoa_Console_Command_Abstract {

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

        $file = null;

        while(false !== $c = parent::getOption($v)) {

            switch($c) {

                case 'f':
                    $file = $v;
                  break;

                case 'h':
                case '?':
                    return $this->usage();
                  break;
            }
        }

        if(null !== $file)
            $code = file_get_contents($file);
        else
            parent::listInputs($code);

        if(null === $code)
            $code = 'h';

        $compiler = new Hoa_Test_Praspel_Compiler();
        $praspel  = null;
        $ccode    = null;
        $variable = null;
        $type     = null;

        do {

            switch($code) {

                case 'h':
                case 'help':
                    cout('Usage:');
                    cout('    h[elp]         to print this help;');
                    cout('    <praspel code> to interprete a Praspel code;');
                    cout('    c[ode]         to print current interpreted Praspel code;');
                    cout('    [r[esample]]   to get a new value;');
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

                    $type = $variable->chooseOneType();
                    $type->clear()->randomize();
                    var_dump($type->getValue());
                  break;

                case 'q':
                case 'quit':
                    cout('Bye bye!');
                  break 2;

                default:
                    $ccode = trim($code, ';');
                    $code  = '@requires i: ' . $ccode . ';';

                    try {

                        $compiler = new Hoa_Test_Praspel_Compiler();
                        $compiler->compile($code);

                        $praspel  = $compiler->getRoot();
                    }
                    catch ( Hoa_Exception $e ) {

                        $e->raiseError();
                        continue;
                    }

                    $variable = $praspel->getClause('requires')->getVariable('i');
                    $type     = $variable->chooseOneType();
                    $type->randomize();
                    var_dump($type->getValue());
            }

            cout();
        } while('quit' != $code = cin(
                                      '> ',
                                      Hoa_Console_Core_Io::TYPE_NORMAL,
                                      Hoa_Console_Core_Io::NO_NEW_LINE
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
