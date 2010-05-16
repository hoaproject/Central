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
 * Hoa_Test
 */
import('Test.~');

/**
 * Class InitializeCommand.
 *
 * .
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2009 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 */

class InitializeCommand extends Hoa_Console_Command_Abstract {

    /**
     * Author name.
     *
     * @var InitializeCommand string
     */
    protected $author      = 'Ivan Enderlin';

    /**
     * Program name.
     *
     * @var InitializeCommand string
     */
    protected $programName = 'Initialize';

    /**
     * Options description.
     *
     * @var InitializeCommand array
     */
    protected $options     = array(
        array('no-recursive', parent::NO_ARGUMENT,       'R'),
        array('test-root',    parent::REQUIRED_ARGUMENT, 't'),
        array('no-verbose',   parent::NO_ARGUMENT,       'V'),
        array('help',         parent::NO_ARGUMENT,       'h'),
        array('help',         parent::NO_ARGUMENT,       '?')
    );



    /**
     * The entry method.
     *
     * @access  public
     * @return  int
     */
    public function main ( ) {

        $verbose   = true;
        $recursive = true;
        $root      = null;

        while(false !== $c = parent::getOption($v)) {

            switch($c) {

                case 'R':
                    $recursive = false;
                  break;

                case 't':
                    $root = $v;
                  break;

                case 'V':
                    $verbose = false;
                  break;

                case 'h':
                case '?':
                    return $this->usage();
                  break;
            }
        }

        parent::listInputs($directory);

        if(null === $directory)
            return $this->usage();

        $parameters = array(
            'convict.directory' => $directory,
            'convict.recursive' => true
        );

        if(null !== $root)
            $parameters['root'] = $root;

        try {

            $test = Hoa_Test::getInstance($parameters);
            $test->run();
            $revision = $test->getFormattedParameter('repository') .
                        $test->getFormattedParameter('revision');
        }
        catch ( Hoa_Test_Exception $e ) {

            throw new Hoa_Console_Exception(
                $e->getFormattedMessage(),
                $e->getCode()
            );
        }

        if(true === $verbose) {

            parent::status('Initialize incubator from ' . $directory . '.', true);
            parent::status('Initialize ordeal/oracle.', true);
            parent::status('Initialize ordeal/battleground.', true);
            cout('Root is ' . parent::stylize($revision, 'info'));
        }
        else
            cout($root);

        return HC_SUCCESS;
    }

    /**
     * The command usage.
     *
     * @access  public
     * @return  int
     */
    public function usage ( ) {

        cout('Usage   : test:initialize <options> directory');
        cout('Options :');
        cout(parent::makeUsageOptionsList(array(
            'R'    => 'Just initialize the first level of the root.',
            't'    => 'Precise a special test-root, i.e. where the tests will be placed.',
            'V'    => 'No-verbose, i.e. be as quiet as possible, just print ' .  
                      'essential informations.',
            'help' => 'This help.'
        )));

        return HC_SUCCESS;
    }
}
