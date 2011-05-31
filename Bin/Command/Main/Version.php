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
 * Class \Bin\Command\Main\Version.
 *
 * Get informations about versions.
 *
 * @author     Ivan Enderlin <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright © 2007-2011 Ivan Enderlin.
 * @license    New BSD License
 */

class Version extends \Hoa\Console\Dispatcher\Kit {

    /**
     * Options description.
     *
     * @var \Bin\Command\Main\Version array
     */
    protected $options = array(
        array('version',    \Hoa\Console\GetOption::NO_ARGUMENT, 'v'),
        array('revision',   \Hoa\Console\GetOption::NO_ARGUMENT, 'r'),
        array('signature',  \Hoa\Console\GetOption::NO_ARGUMENT, 's'),
        array('no-verbose', \Hoa\Console\GetOption::NO_ARGUMENT, 'V'),
        array('help',       \Hoa\Console\GetOption::NO_ARGUMENT, 'h'),
        array('help',       \Hoa\Console\GetOption::NO_ARGUMENT, '?')
    );



    /**
     * The entry method.
     *
     * @access  public
     * @return  int
     */
    public function main ( ) {

        $version  = HOA_VERSION_MAJOR . '.' . HOA_VERSION_MINOR . '.' .
                    HOA_VERSION_RELEASE . HOA_VERSION_STATUS;
        $revision = HOA_REVISION;
        $verbose  = true;
        $message  = null;
        $info     = null;

        while(false !== $c = $this->getOption($v)) switch($c) {

            case 'r':
                $info    = $revision;
                $message = 'Framework revision number: ' .
                           $this->stylize($info, 'info') . '.';
              break;

            case 'v':
                $info    = $version;
                $message = 'Framework version: ' .
                           $this->stylize($info, 'info') . '.';
              break;

            case 'V':
                $verbose = false;
              break;

            case 'h':
            case '?':
                return $this->usage();
              break;

            case 's':
            default:
                $info = $message = 'Hoa ' . $version . ' (' .
                                   $revision . ').' . "\n" .
                                   \Hoa\Core::©();
              break;
        }

        if(true === $verbose)
            cout($message);
        else
            cout($info);

        return;
    }

    /**
     * The command usage.
     *
     * @access  public
     * @return  int
     */
    public function usage ( ) {

        cout('Usage   : main:version <options>');
        cout('Options :');
        cout($this->makeUsageOptionsList(array(
            'v'    => 'Get the framework version.',
            'r'    => 'Get the framework revision number.',
            's'    => 'Get the complete framework signature.',
            'V'    => 'No-verbose, i.e. be as quiet as possible, just print ' .
                      'essential informations.',
            'help' => 'This help.'
        )));

        return;
    }
}

}
