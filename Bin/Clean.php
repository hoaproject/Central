<?php

/**
 * Hoa
 *
 *
 * @license
 *
 * New BSD License
 *
 * Copyright © 2007-2014, Ivan Enderlin. All rights reserved.
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
 * \Hoa\File\Finder
 */
-> import('File.Finder')

/**
 * \Hoa\File\Directory
 */
-> import('File.Directory');

}

namespace Hoa\Test\Bin {

/**
 * Class Hoa\Test\Bin\Clean.
 *
 * Clean generated tests.
 *
 * @author     Ivan Enderlin <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright © 2007-2014 Ivan Enderlin.
 * @license    New BSD License
 */

class Clean extends \Hoa\Console\Dispatcher\Kit {

    /**
     * Options description.
     *
     * @var \Hoa\Test\Bin\Clean array
     */
    protected $options = array(
        array('all',       \Hoa\Console\GetOption::NO_ARGUMENT,       'a'),
        array('libraries', \Hoa\Console\GetOption::REQUIRED_ARGUMENT, 'l'),
        array('help',      \Hoa\Console\GetOption::NO_ARGUMENT,       'h'),
        array('help',      \Hoa\Console\GetOption::NO_ARGUMENT,       '?')
    );



    /**
     * The entry method.
     *
     * @access  public
     * @return  int
     */
    public function main ( ) {

        $libraries = array();

        while(false !== $c = $this->getOption($v)) switch($c) {

            case 'a':
                $iterator = new \Hoa\File\Finder();
                $iterator->in(resolve('hoa://Library/', true, true))
                         ->directories()
                         ->maxDepth(1);

                foreach($iterator as $fileinfo) {

                    $libraryName    = $fileinfo->getBasename();
                    $pathname       = resolve('hoa://Library/' . $libraryName);
                    $automaticTests = $pathname . DS . 'Test' . DS .
                                      'Praspel' . DS;

                    if(is_dir($automaticTests))
                        $libraries[] = $automaticTests;
                }

                if(empty($libraries)) {

                    echo 'Already clean.';

                    return;
                }
              break;

            case 'l':
                foreach($this->parser->parseSpecialValue($v) as $library) {

                    $libraryName    = ucfirst(strtolower($library));
                    $pathname       = resolve('hoa://Library/' . $libraryName);
                    $automaticTests = $pathname . DS . 'Test' . DS .
                                      'Praspel' . DS;

                    if(is_dir($automaticTests))
                        $libraries[] = $automaticTests;
                }

                if(empty($libraries)) {

                    echo 'Already clean.';

                    return;
                }
              break;

            case '__ambiguous':
                $this->resolveOptionAmbiguity($v);
              break;

            case 'h':
            case '?':
            default:
                return $this->usage();
              break;
        }

        if(empty($libraries))
            return $this->usage();

        foreach($libraries as $path) {

            $status = 'Clean ' .
                      (40 < strlen($path)
                           ? '…' . substr($path, -39)
                           : $path);
            echo '  ⌛ ' , $status;

            $directory = new \Hoa\File\Directory($path);

            if(false === $directory->delete())
                echo '  ', \Hoa\Console\Chrome\Text::colorize('✖︎', 'foreground(red)'),
                     ' ', $status, "\n";
            else {

                \Hoa\Console\Cursor::clear('↔');
                echo '  ', \Hoa\Console\Chrome\Text::colorize('✔︎', 'foreground(green)'),
                     ' ', $status, "\n";
            }

            $directory->close();
        }

        return;
    }

    /**
     * The command usage.
     *
     * @access  public
     * @return  int
     */
    public function usage ( ) {

        echo 'Usage   : test:clean <options>', "\n",
             'Options :', "\n",
             $this->makeUsageOptionsList(array(
                 'a'    => 'Clean all generated tests of all libraries.',
                 'l'    => 'Clean all generated tests of some libraries.',
                 'help' => 'This help.'
             )), "\n";

        return;
    }
}

}

__halt_compiler();
Clean generated tests.
