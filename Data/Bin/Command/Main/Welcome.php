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
 * Copyright (c) 2007, 2008 Ivan ENDERLIN. All rights reserved.
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
 * @copyright   Copyright (c) 2007, 2008 Ivan ENDERLIN.
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
    protected $options     = array();



    /**
     * The entry method.
     *
     * @access  public
     * @return  int
     */
    public function main ( ) {

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
                '    ' . parent::stylize('dependency', 'command'),
                'To know dependencies between packages.'
            ),
            array(
                '    ' . parent::stylize('root', 'command'),
                'To know some roots.'
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
                '    ' . parent::stylize('delete', 'command'),
                'Delete a controller.',
            ),
            array(
                '    ' . parent::stylize('whereis', 'command'),
                'Return the path of a controller.'
            ),

            // Action.
            /*
            array('Action'),
            array(
                '    ' . parent::stylize('create', 'command'),
                'Create a new action in a controller.'
            ),
            array(
                '    ' . parent::stylize('delete', 'command'),
                'Delete an action in a controller.'
            ),
            array(
                '    ' . parent::stylize('whereis', 'command'),
                'Return the path of an action.'
            ),
            */

            // View.
            /*
            array('View'),
            array(
                '    ' . parent::stylize('theme', 'command'),
                'Manage theme for the view layer.'
            ),
            array(
                '    ' . parent::stylize('layout', 'command'),
                'Manage layout for the view layer.'
            ),
            */

            // Maintenance.
            array('Maintenance'),
            array(
                '    ' . parent::stylize('cache', 'command'),
                'Manage the framework package configuration caches.'
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

            // Tool.
            /*
            array('Tool'),
            array(
                '    ' . parent::stylize('form', 'command'),
                'An helper to built a form.'
            ),
            array(
                '    ' . parent::stylize('gettext', 'command'),
                'An helper to compile the .po to .mo.'
            )
            */
        )));

        cout();

        return HC_SUCCESS;
    }

    /**
     * The command usage.
     *
     * @access  public
     * @return  int
     */
    public function usage ( ) {

        return HC_SUCCESS;
    }
}
