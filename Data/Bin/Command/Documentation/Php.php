<?php

/**
 * Hoa
 *
 *
 * @license
 *
 * New BSD License
 *
 * Copyright (c) 2007-2011, Ivan Enderlin. All rights reserved.
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
 * \Hoa\Console\System
 */
-> import('Console.System.~');

/**
 * Class PhpCommand.
 *
 * Search in different PHP reference or API manuals.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007-2011 Ivan ENDERLIN.
 * @license     New BSD License
 * @since       PHP 5
 * @version     0.1
 */

class PhpCommand extends \Hoa\Console\Command\Generic {

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
    protected $programName = 'Php';

    /**
     * Options description.
     *
     * @var VersionCommand array
     */
    protected $options     = array(
        array('function',      parent::NO_ARGUMENT, 'f'),
        array('class',         parent::NO_ARGUMENT, 'c'),
        array('extension',     parent::NO_ARGUMENT, 'e'),
        array('configuration', parent::NO_ARGUMENT, 'i'),
        array('documentation', parent::NO_ARGUMENT, 'd'),
        array('help',          parent::NO_ARGUMENT, 'h'),
        array('help',          parent::NO_ARGUMENT, '?')
    );



    /**
     * The entry method.
     *
     * @access  public
     * @return  int
     */
    public function main ( ) {

        $path = 'hoa://Data/Etc/Configuration/.Cache/HoaConsole.php';

        if(!file_exists($path))
            throw new \Hoa\Console\Command\Exception(
                'The cache “Console” is not found in %s. Must generate it.',
                0, $path);

        parent::listInputs($name);

        if(null === $name)
            return $this->usage();

        $cache   = require $path;
        $php     = $cache['parameters']['command.php'];
        $browser = $cache['parameters']['command.browser'];
        $out     = null;

        while(false !== $c = parent::getOption($v)) {

            switch($c) {

                case 'f':
                    $out = \Hoa\Console\System::execute($php . ' --rf ' . $name);
                  break;

                case 'c':
                    $out = \Hoa\Console\System::execute($php . ' --rc ' . $name);
                  break;

                case 'e':
                    $out = \Hoa\Console\System::execute($php . ' --re ' . $name);
                  break;

                case 'i':
                    $out = \Hoa\Console\System::execute($php . ' --ri ' . $name);
                  break;

                case 'h':
                case '?':
                    return $this->usage();
                  break;

                case 'd':
                default:
                    $out = \Hoa\Console\System::execute($browser . ' http://php.net/' . $name);
                    empty($out)
                    and
                    $out = 'Open ' .
                           parent::stylize(
                               'http://php.net/' . $name,
                               'info'
                           ) . '.';
            }
        }

        cout($out);

        return HC_SUCCESS;
    }

    /**
     * The command usage.
     *
     * @access  public
     * @return  int
     */
    public function usage ( ) {

        cout('Usage   : documentation:php <options> name');
        cout('Options :');
        cout(parent::makeUsageOptionsList(array(
            'f'    => 'Show information about function <name>.',
            'c'    => 'Show information about class <name>.',
            'e'    => 'Show information about extension <name>.',
            'i'    => 'Show configuration about configuration <name>.',
            'd'    => 'Open the online documentation on the <name> page.',
            'help' => 'This help.'
        )));

        return HC_SUCCESS;
    }
}

}
