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
 * \Hoa\Console\Processus
 */
-> import('Console.Processus')

/**
 * \Hoa\File\Finder
 */
-> import('File.Finder');

}

namespace Hoa\Test\Bin {

/**
 * Class Hoa\Test\Bin\Run.
 *
 * Run tests.
 *
 * @author     Ivan Enderlin <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright © 2007-2014 Ivan Enderlin.
 * @license    New BSD License
 */

class Run extends \Hoa\Console\Dispatcher\Kit {

    /**
     * Options description.
     *
     * @var \Hoa\Test\Bin\Pp array
     */
    protected $options = array(
        array('all',       \Hoa\Console\GetOption::NO_ARGUMENT,       'a'),
        array('library',   \Hoa\Console\GetOption::REQUIRED_ARGUMENT, 'l'),
        array('directory', \Hoa\Console\GetOption::REQUIRED_ARGUMENT, 'd'),
        array('file',      \Hoa\Console\GetOption::REQUIRED_ARGUMENT, 'f'),
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

        $library   = null;
        $directory = null;
        $file      = null;

        while(false !== $c = $this->getOption($v)) switch($c) {

            case 'a':
                $out      = array();
                $iterator = new \Hoa\File\Finder();
                $iterator->in(resolve('hoa://Library/', true, true))
                         ->directories()
                         ->maxDepth(1);

                foreach($iterator as $fileinfo) {

                    $libraryName = $fileinfo->getBasename();
                    $pathname    = resolve('hoa://Library/' . $libraryName);
                    $test        = $pathname . DS . 'Test' . DS . 'Unit';

                    if(is_dir($test))
                        $out[] = $test;
                }

                if(!empty($out))
                    $directory = implode(' ', $out);
              break;

            case 'l':
                $library = ucfirst(strtolower($v));
              break;

            case 'd':
                $directory = $v;
              break;

            case 'f':
                $file = $v;
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

        $atoum = 'atoum';

        if(isset($_SERVER['HOA_ATOUM_BIN']))
            $atoum = $_SERVER['HOA_ATOUM_BIN'];

        $command = $atoum .
                   ' --configurations ' .
                       resolve('hoa://Library/Test/.atoum.php') .
                   ' --bootstrap-file ' .
                       resolve('hoa://Library/Test/.bootstrap.atoum.php') .
                   ' --force-terminal';

        if(null !== $library)
            $command .= ' --directories ' . resolve('hoa://Library/' . $library);
        elseif(null !== $directory)
            $command .= ' --directories ' . $directory;
        elseif(null !== $file)
            $command .= ' --files ' . $file;
        else
            return $this->usage();

        $processus = new \Hoa\Console\Processus($command);
        $processus->on('input', function ( $bucket ) {

            return false;
        });
        $processus->on('output', function ( $bucket ) {

            $data = $bucket->getData();

            echo $data['line'], "\n";

            return;
        });
        $processus->run();

        return;
    }

    /**
     * The command usage.
     *
     * @access  public
     * @return  int
     */
    public function usage ( ) {

        echo 'Usage   : test:run <options>', "\n",
             'Options :', "\n",
             $this->makeUsageOptionsList(array(
                 'a'    => 'Run all tests of all libraries.',
                 'l'    => 'Run all tests of a library.',
                 'd'    => 'Run tests of a specific directory.',
                 'f'    => 'Run test of a specific file.',
                 'help' => 'This help.'
             )), "\n";

        return;
    }
}

}

__halt_compiler();
Run tests.
