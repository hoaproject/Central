<?php

/**
 * Hoa
 *
 *
 * @license
 *
 * New BSD License
 *
 * Copyright © 2007-2015, Ivan Enderlin. All rights reserved.
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
 * \Hoa\File\Read
 */
-> import('File.Read')

/**
 * \Hoa\Compiler\Llk
 */
-> import('Compiler.Llk.~')

/**
 * \Hoa\Praspel\Visitor\Interpreter
 */
-> import('Praspel.Visitor.Interpreter')

/**
 * \Hoa\Praspel\Visitor\Compiler
 */
-> import('Praspel.Visitor.Compiler')

/**
 * \Hoa\Realdom
 */
-> import('Realdom.~')

/**
 * \Hoa\Math\Sampler\Random
 */
-> import('Math.Sampler.Random')

/**
 * \Hoa\Console\Cursor
 */
-> import('Console.Cursor')

/**
 * \Hoa\Console\Readline
 */
-> import('Console.Readline.~')

/**
 * \Hoa\Console\Readline\Autocompleter\Word
 */
-> import('Console.Readline.Autocompleter.Word');

}

namespace Hoa\Praspel\Bin {

/**
 * Class \Hoa\Praspel\Bin\Shell.
 *
 * Interactive Praspel shell.
 *
 * @author     Ivan Enderlin <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright © 2007-2015 Ivan Enderlin.
 * @license    New BSD License
 */

class Shell extends \Hoa\Console\Dispatcher\Kit {

    /**
     * Options description.
     *
     * @var \Hoa\Praspel\Bin\Shell array
     */
    protected $options = array(
        array('help', \Hoa\Console\GetOption::NO_ARGUMENT, 'h'),
        array('help', \Hoa\Console\GetOption::NO_ARGUMENT, '?')
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

            case '__ambiguous':
                $this->resolveOptionAmbiguity($v);
              break;
        }

        \Hoa\Realdom::setDefaultSampler(new \Hoa\Math\Sampler\Random());

        $compiler    = \Hoa\Compiler\Llk::load(
            new \Hoa\File\Read('hoa://Library/Praspel/Grammar.pp')
        );
        $interpreter = new \Hoa\Praspel\Visitor\Interpreter();
        $dump        = new \Hoa\Praspel\Visitor\Compiler();
        $interpreter->visit($compiler->parse('@requires;'));
        $words       = array();

        from('Hoathis or Hoa')
        -> foreachImport('Realdom.*', function ( $classname ) use ( &$words ) {

            $class = new \ReflectionClass($classname);

            if($class->isSubclassOf('\Hoa\Realdom'))
                $words[] = $classname::NAME;

            return;
        });

        $readline = new \Hoa\Console\Readline();
        $readline->setAutocompleter(
            new \Hoa\Console\Readline\Autocompleter\Word($words)
        );

        $expression = '.h';

        do { try {

        if('.' === $expression[0])
            @list($expression, $tail) = explode(' ', $expression);

        switch($expression) {

            case '.h':
            case '.help':
                echo 'Usage:', "\n",
                     '    .h[elp]      to print this help;', "\n",
                     '    .c[lear]     to clear the screen;', "\n",
                     '    .v[ariables] to print all variables;', "\n",
                     '    .s[ample]    to sample a value of a variable;', "\n",
                     '    .u[nset]     to unset a variable;', "\n",
                     '    .d[ump]      to dump the tree of the expression;', "\n",
                     '    .q[uit]      to quit.', "\n";
              break;

            case '.c':
            case '.clear':
                \Hoa\Console\Cursor::clear('↕');
              break;

            case '.v':
            case '.variables':
                foreach($interpreter->getClause() as $variable)
                    echo $variable->getName(), ': ',
                         $variable->getHeld()->toPraspel(), "\n";
              break;

            case '.s':
            case '.sample':
                if(null === $tail) {

                    echo 'You must precise a variable name.', "\n";
                    break;
                }

                $_clause = $interpreter->getClause();

                if(!isset($_clause[$tail])) {

                    echo 'Variable ', $tail, ' does not exist.', "\n";
                    break;
                }

                $_variable = $_clause[$tail];
                var_export($_variable->sample());
                echo "\n";
                $_variable->reset();
              break;

            case '.u':
            case '.unset':
                if(null === $tail) {

                    echo 'You must precise a variable name.', "\n";
                    break;
                }

                $_clause = $interpreter->getClause();
                unset($_clause[$tail]);
              break;

            case '.d':
            case '.dump':
                echo $dump->visit($interpreter->getRoot());
              break;

            case '.q':
            case '.quit':
                break 2;

            default:
                if(null === $expression)
                    break;

                $interpreter->visit(
                    $compiler->parse($expression, 'expression')
                );
              break;

        } }

        catch ( \Exception $e ) {

            echo $e->getMessage(), "\n";
        }

        echo "\n";

        } while(false !== $expression = $readline->readLine('> '));

        return;
    }

    /**
     * The command usage.
     *
     * @access  public
     * @return  int
     */
    public function usage ( ) {

        echo 'Usage   : praspel:shell <options> [expression]', "\n",
             'Options :', "\n",
             $this->makeUsageOptionsList(array(
                 'help' => 'This help.'
             )), "\n";

        return;
    }
}

}

__halt_compiler();
Interactive Praspel shell.
