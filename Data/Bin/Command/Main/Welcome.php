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

/**
 * Class WelcomeCommand.
 *
 * This command is runned by default (if no command is specified).
 *
 * @author     Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright © 2007-2011 Ivan ENDERLIN.
 * @license    New BSD License
 */

class WelcomeCommand extends \Hoa\Console\Command\Generic {

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
            parent::stylize('Hoa', 'h1'),
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
                '    ' . parent::stylize('run', 'command'),
                'Run tests.',
            ),
            array(
                '    ' . parent::stylize('remove', 'command'),
                'Remove revisions in the tests repository.',
            ),
            array(
                '    ' . parent::stylize('praspel', 'command'),
                'Start the interpreter for Praspel code.',
            ),

            // XYL.
            array('XYL'),
            array(
                '    ' . parent::stylize('render', 'command'),
                'Make a render of a XYL document.'
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

            // Bhoa.
            array('Bhoa'),
            array(
                '    ' . parent::stylize('start', 'command'),
                'Start a damn stupid HTTP server.'
            ),

            /*
            array('Service'),
            array(
                '    ' . parent::stylize('identica', 'command'),
                'Send a tweet!'
            ),
            array(
                '    ' . parent::stylize('twitter', 'command'),
                'Send a tweet!'
            )
            */
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

}
