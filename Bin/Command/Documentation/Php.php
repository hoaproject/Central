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
 * \Hoa\Console\System
 */
-> import('Console.System.~');

}

namespace Bin\Command\Documentation {

/**
 * Class \Bin\Command\Documentation\Php.
 *
 * Search in different PHP reference or API manuals.
 *
 * @author     Ivan Enderlin <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright © 2007-2011 Ivan Enderlin.
 * @license    New BSD License
 */

class Php extends \Hoa\Console\Dispatcher\Kit {

    /**
     * Options description.
     *
     * @var \Bin\Command\Documentation\Php array
     */
    protected $options = array(
        array('function',      \Hoa\Console\GetOption::NO_ARGUMENT, 'f'),
        array('class',         \Hoa\Console\GetOption::NO_ARGUMENT, 'c'),
        array('constant',      \Hoa\Console\GetOption::NO_ARGUMENT, 'o'),
        array('extension',     \Hoa\Console\GetOption::NO_ARGUMENT, 'e'),
        array('configuration', \Hoa\Console\GetOption::NO_ARGUMENT, 'i'),
        array('documentation', \Hoa\Console\GetOption::NO_ARGUMENT, 'd'),
        array('help',          \Hoa\Console\GetOption::NO_ARGUMENT, 'h'),
        array('help',          \Hoa\Console\GetOption::NO_ARGUMENT, '?')
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
            throw new \Hoa\Console\Exception(
                'The cache “Console” is not found in %s. Must generate it.',
                0, $path);

        $this->setOptions($this->options);
        $this->parser->listInputs($name);

        if(null === $name)
            return $this->usage();

        $cache   = require $path;
        $php     = $cache['parameters']['command.php'];
        $browser = $cache['parameters']['command.browser'];
        $out     = null;

        while(false !== $c = $this->getOption($v)) switch($c) {

            case 'f':
                $out = \Hoa\Console\System::execute($php . ' --rf ' . $name);
              break;

            case 'c':
                $out = \Hoa\Console\System::execute($php . ' --rc ' . $name);
              break;

            case 'o':
                if(!defined($name))
                    $out = '(undefined)';
                else {

                    ob_start();
                    var_dump(constant($name));
                    $out = $name . ': ' . ob_get_contents();
                    ob_end_clean();
                }
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
                $out = 'Open ' . $this->stylize('http://php.net/' . $name, 'info') . '.';
        }

        cout($out, \Hoa\Console\Io::NO_NEW_LINE);

        return;
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
        cout($this->makeUsageOptionsList(array(
            'f'    => 'Show information about function <name>.',
            'c'    => 'Show information about class <name>.',
            'o'    => 'Show value of constant <name>.',
            'e'    => 'Show information about extension <name>.',
            'i'    => 'Show configuration about configuration <name>.',
            'd'    => 'Open the online documentation on the <name> page.',
            'help' => 'This help.'
        )));

        return;
    }
}

}
