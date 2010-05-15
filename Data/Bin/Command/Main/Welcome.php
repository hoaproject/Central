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
 * Class WelcomeCommand.
 *
 * This command is runned by default (if no command is specified).
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2009 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 */

class WelcomeCommand extends Hoa_Console_Command_Abstract {

    /**
     * Author name.
     *
     * @var HelpCommand string
     */
    protected $author      = 'Ivan Enderlin';

    /**
     * Program name.
     *
     * @var HelpCommand string
     */
    protected $programName = 'Welcome';

    /**
     * Options description.
     *
     * @var HelpCommand array
     */
    protected $options     = array(
        array('help', parent::NO_ARGUMENT, 'h'),
        array('help', parent::NO_ARGUMENT, '?'),
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

        cout(parent::align(
            parent::stylize('Hoa Framework', 'h1'),
            parent::ALIGN_CENTER
        ));

        cout('Welcome in the command-line interface of Hoa :).' .  "\n");

        cout(parent::stylize('List of available commands', 'h2'));
        cout();

        cout(parent::columnize(array(
            // Main.
            array('Main'),
            array(
                '    ' . parent::stylize('welcome', 'command'),
                'This homepage.'
            ),
            array(
                '    ' . parent::stylize('version', 'command'),
                'The framework version and revision.'
            ),
            array(
                '    ' . parent::stylize('changelog', 'command'),
                'To manipulate the changelog.'
            ),
            array(
                '    ' . parent::stylize('root', 'command'),
                'To know some roots.'
            ),
            array(
                '    ' . parent::stylize('tree', 'command'),
                'Print contents of a directory.'
            ),
            array(
                '    ' . parent::stylize('uuid', 'command'),
                'Generate an universally unique identifier.'
            ),

            // Protocol.
            array('Protocol'),
            array(
                '    ' . parent::stylize('resolve', 'command'),
                'Resolve some hoa:// paths.'
            ),
            array(
                '    ' . parent::stylize('print', 'command'),
                'Print the protocol tree.'
            ),

            // Application.
            array('Application'),
            array(
                '    ' . parent::stylize('start', 'command'),
                'Create the application base.'
            ),

            // Controller.
            array('Controller'),
            array(
                '    ' . parent::stylize('create', 'command'),
                'Create a new controller.'
            ),
            array(
                '    ' . parent::stylize('whereis', 'command'),
                'Return the path of a controller.'
            ),

            // Configuration.
            array('Configuration'),
            array(
                '    ' . parent::stylize('view', 'command'),
                'View a package configuration.'
            ),
            array(
                '    ' . parent::stylize('cache', 'command'),
                'Cache the package configurations.'
            ),

            // Test.
            array('Test'),
            array(
                '    ' . parent::stylize('initialize', 'command'),
                'Initialize tests.'
            ),
            array(
                '    ' . parent::stylize('launch', 'command'),
                'Launch initialized tests.',
            ),
            array(
                '    ' . parent::stylize('praspel', 'command'),
                'Start the interactive interpreter for Praspel code.',
            ),

            // Documentation.
            array('Documentation'),
            array(
                '    ' . parent::stylize('php', 'command'),
                'Get the PHP documentation.'
            ),
            array(
                '    ' . parent::stylize('mysql', 'command'),
                'Get the MySQL documentation.'
            ),

            // Database.
            array('Database'),
            array(
                '    ' . parent::stylize('buildModel', 'command'),
                'Build a model from a schema.'
            ),

            array('Service'),
            array(
                '    ' . parent::stylize('identica', 'command'),
                'Send a tweet!'
            ),
            array(
                '    ' . parent::stylize('twitter', 'command'),
                'Send a tweet!'
            )
        )));

        return HC_SUCCESS;
    }

    /**
     * The command usage.
     *
     * @access  public
     * @return  int
     */
    public function usage ( ) {

        cout('Usage   : main:welcome <options>');
        cout('Options :');
        cout(parent::makeUsageOptionsList(array(
            'help' => 'This help.'
        )));
        cout(
            'A command line is made up of a ' .
            parent::stylize('group', 'info') .
            ' and a ' .
            parent::stylize('command name', 'info') .
            '. Both are separated by the symbol ' .
            parent::stylize(':', 'info') .
            '. If the group is ' .
            parent::stylize('main', 'info') .
            ' then, it could be ommited.' . "\n" . 'Thus:' . "\n" .
            '    hoa main:version' . "\n" .
            'is equivalent to:' . "\n" .
            '    hoa version' . "\n" .
            'But, no equivalence exists for:' . "\n" .
            '    hoa application:start' . "\n\n" .
            'All commands have a help page accessibles via options ' .
            '-h, -? or --help.'
        );

        return HC_SUCCESS;
    }
}
