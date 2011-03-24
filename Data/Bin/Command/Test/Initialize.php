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
 * \Hoa\Test
 */
-> import('Test.~');

/**
 * Class InitializeCommand.
 *
 * .
 *
 * @author     Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright © 2007-2011 Ivan ENDERLIN.
 * @license    New BSD License
 */

class InitializeCommand extends \Hoa\Console\Command\Generic {

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
        array('no-verbose', parent::NO_ARGUMENT, 'V'),
        array('help',       parent::NO_ARGUMENT, 'h'),
        array('help',       parent::NO_ARGUMENT, '?')
    );



    /**
     * The entry method.
     *
     * @access  public
     * @return  int
     */
    public function main ( ) {

        $verbose = true;

        while(false !== $c = parent::getOption($v)) {

            switch($c) {

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

        try {

            $test = new \Hoa\Test();
            $test->initialize($directory);
            $revision = $test->getFormattedParameter('repository') .
                        $test->getFormattedParameter('revision');
        }
        catch ( \Hoa\Test\Exception $e ) {

            throw new \Hoa\Console\Exception(
                $e->getFormattedMessage(),
                $e->getCode()
            );
        }

        if(true === $verbose) {

            cout('Initializing a new test revision in the repository:');
            parent::status('incubator from ' . $directory . '.', true);
            parent::status('instrumented code.', true);
            cout();
            cout('Repository root: ' . parent::stylize($revision, 'info'));
        }
        else
            cout($revision);

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
            'V'    => 'No-verbose, i.e. be as quiet as possible, just print ' .  
                      'essential informations.',
            'help' => 'This help.'
        )));

        return HC_SUCCESS;
    }
}

}
