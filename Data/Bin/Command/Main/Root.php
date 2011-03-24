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
 * Class RootCommand.
 *
 * This command allows to know some roots.
 *
 * @author     Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright © 2007-2011 Ivan ENDERLIN.
 * @license    New BSD License
 */

class RootCommand extends \Hoa\Console\Command\Generic {

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
    protected $programName = 'Root';

    /**
     * Options description.
     *
     * @var VersionCommand array
     */
    protected $options     = array(
        array('framework',   parent::NO_ARGUMENT, 'f'),
        array('data',        parent::NO_ARGUMENT, 'd'),
        array('application', parent::NO_ARGUMENT, 'a'),
        array('check',       parent::NO_ARGUMENT, 'c'),
        array('no-verbose',  parent::NO_ARGUMENT, 'V'),
        array('help',        parent::NO_ARGUMENT, 'h'),
        array('help',        parent::NO_ARGUMENT, '?')
    );



    /**
     * The entry method.
     *
     * @access  public
     * @return  int
     */
    public function main ( ) {

        $framework = \Hoa\Core::getInstance();
        $verbose   = true;
        $info      = $framework->getFormattedParameter('root.framework');
        $message   = 'Framework\'s root: ' .
                     parent::stylize($info, 'info') . '.';
        $check     = false;

        while(false !== $c = parent::getOption($v)) {

            switch($c) {

                case 'd':
                    $info    = $framework->getFormattedParameter('root.data');
                    $message = 'Data\'s root: ' .
                               parent::stylize($info, 'info') . '.';
                  break;

                case 'a':
                    $info    = $framework->getFormattedParameter('root.application');
                    $message = 'Application\'s root: ' .
                               parent::stylize($info, 'info') . '.';
                  break;

                case 'c':
                    $check = $v;
                  break;

                case 'V':
                    $verbose = false;
                  break;

                case 'h':
                case '?':
                    return $this->usage();
                  break;

                case 'f':
                default:
                    $info    = $framework->getFormattedParameter('root.framework');
                    $message = 'Framework\'s root: ' .
                               parent::stylize($info, 'info') . '.';
                  break;
            }
        }

        if(true === $check)
            if($info == getcwd())
                if(true === $verbose)
                    cout('You are at the right place!');
                else
                    cout('1');
            else
                if(true === $verbose)
                    cout('You are not at the right place :-(.');
                else
                    cout('0');

        if(true === $verbose)
            cout($message);
        else
            cout($info);

        return HC_SUCCESS;
    }

    /**
     * The command usage.
     *
     * @access  public
     * @return  int
     */
    public function usage ( ) {

        cout('Usage   : main:root <options>');
        cout('Options :');
        cout(parent::makeUsageOptionsList(array(
            'f'    => 'The framework root.',
            'd'    => 'The data root.',
            'a'    => 'The application root.',
            'c'    => 'Check with the current path if it matches.',
            'V'    => 'No-verbose, i.e. be as quiet as possible, just print ' .
                      'essential informations.',
            'help' => 'This help.'
        )));

        return HC_SUCCESS;
    }
}

}
