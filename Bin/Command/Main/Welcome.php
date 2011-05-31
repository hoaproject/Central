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

namespace Bin\Command\Main {

/**
 * Class \Bin\Command\Main\Welcome.
 *
 * Welcome screen.
 *
 * @author     Ivan Enderlin <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright © 2007-2011 Ivan Enderlin.
 * @license    New BSD License
 */

class Welcome extends \Hoa\Console\Dispatcher\Kit {

    /**
     * Options description.
     *
     * @var \Bin\Command\Main\Welcome array
     */
    protected $options     = array(
        array('help', \Hoa\Console\GetOption::NO_ARGUMENT, 'h'),
        array('help', \Hoa\Console\GetOption::NO_ARGUMENT, '?'),
    );



    /**
     * The entry method.
     *
     * @access  public
     * @return  int
     */
    public function main ( ) {

        while(false !== $c = $this->getOption($v)) switch($c) {

            case 'h':
            case '?':
                return $this->usage();
              break;
        }

        cout(\Hoa\Console\Chrome\Text::align(
            $this->stylize('Hoa', 'h1'),
            \Hoa\Console\Chrome\Text::ALIGN_CENTER
        ));

        cout('Welcome in the command-line interface of Hoa :).' .  "\n");

        cout($this->stylize('List of available commands', 'h2'));
        cout();

        cout(\Hoa\Console\Chrome\Text::columnize(array(
            // Main.
            array('Main'),
            array(
                '    ' . $this->stylize('welcome', 'command'),
                'This homepage.'
            ),
            array(
                '    ' . $this->stylize('version', 'command'),
                'The framework version and revision.'
            ),
            array(
                '    ' . $this->stylize('tree', 'command'),
                'Print contents of a directory.'
            ),
            array(
                '    ' . $this->stylize('uuid', 'command'),
                'Generate an universally unique identifier.'
            ),
            array(
                '    ' . $this->stylize('debugger', 'command'),
                'Start the debugger client.'
            ),
            array(
                '    ' . $this->stylize('bhoa', 'command'),
                'Start a damn stupid HTTP server.'
            )
        )));

        return;
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
        cout($this->makeUsageOptionsList(array(
            'help' => 'This help.'
        )));
        cout(
            'A command line is made up of a ' .
            $this->stylize('group', 'info') .
            ' and a ' .
            $this->stylize('command name', 'info') .
            '. Both are separated by the symbol ' .
            $this->stylize(':', 'info') .
            '. If the group is ' .
            $this->stylize('main', 'info') .
            ' then, it could be ommited.' . "\n" . 'Thus:' . "\n" .
            '    hoa main:version' . "\n" .
            'is equivalent to:' . "\n" .
            '    hoa version' . "\n" .
            'But, no equivalence exists for:' . "\n" .
            '    hoa application:start' . "\n\n" .
            'All commands have a help page accessibles via options ' .
            '-h, -? or --help.'
        );

        return;
    }
}

}
