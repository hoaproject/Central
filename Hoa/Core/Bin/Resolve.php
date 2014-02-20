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
 * \Hoa\Console
 */
-> import('Console.~');

}

namespace Hoa\Core\Bin {

/**
 * Class \Hoa\Core\Bin\Resolve.
 *
 * This command resolves some hoa:// paths.
 *
 * @author     Ivan Enderlin <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright © 2007-2014 Ivan Enderlin.
 * @license    New BSD License
 */

class Resolve extends \Hoa\Console\Dispatcher\Kit {

    /**
     * Options description.
     *
     * @var \Hoa\Core\Bin\Resolve array
     */
    protected $options = array(
        array('exists',     \Hoa\Console\GetOption::NO_ARGUMENT, 'E'),
        array('unfold',     \Hoa\Console\GetOption::NO_ARGUMENT, 'u'),
        array('tree',       \Hoa\Console\GetOption::NO_ARGUMENT, 't'),
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

        $exists  = true;
        $unfold  = false;
        $tree    = false;
        $verbose = \Hoa\Console::isDirect(STDOUT);

        while(false !== $c = $this->getOption($v)) switch($c) {

            case 'E':
                $exists = false;
              break;

            case 'u':
                $unfold = true;
              break;

            case 't':
                $tree = true;
              break;

            case 'V':
                $verbose = false;
              break;

            case 'h':
            case '?':
                return $this->usage();
              break;

            case '__ambiguous':
                $this->resolveOptionAmbiguity($v);
              break;
        }

        $this->parser->listInputs($path);

        if(null === $path)
            return $this->usage();

        if(true === $tree) {

            $protocol = \Hoa\Core::getProtocol();
            $foo      = substr($path, 0, 6);

            if('hoa://' !== $foo)
                return;

            $path    = substr($path, 6);
            $current = $protocol;

            foreach(explode('/', $path) as $component) {

                if(!isset($current[$component]))
                    break;

                $current = $current[$component];
            }

            echo $current;

            return;
        }

        if(true === $verbose)
            echo \Hoa\Console\Chrome\Text::colorize($path, 'foreground(yellow)'),
                 ' is equivalent to:', "\n";

        $resolved = resolve($path, $exists, $unfold);

        foreach((array) $resolved as $r)
            echo $r, "\n";

        return;
    }

    /**
     * The command usage.
     *
     * @access  public
     * @return  int
     */
    public function usage ( ) {

        echo 'Usage   : core:resolve <options> path', "\n",
             'Options :', "\n",
             $this->makeUsageOptionsList(array(
                 'E'    => 'Do not check if the resolution result exists.',
                 'u'    => 'Unfold all possible results.',
                 't'    => 'Print the tree from the path.',
                 'V'    => 'No-verbose, i.e. be as quiet as possible, just print ' .
                           'essential informations.',
                 'help' => 'This help.'
             )), "\n";

        return;
    }
}

}

__halt_compiler();
Resolve hoa:// paths.
