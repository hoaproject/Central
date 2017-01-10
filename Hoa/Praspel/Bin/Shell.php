<?php

/**
 * Hoa
 *
 *
 * @license
 *
 * New BSD License
 *
 * Copyright © 2007-2017, Hoa community. All rights reserved.
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

namespace Hoa\Praspel\Bin;

use Hoa\Compiler;
use Hoa\Console;
use Hoa\File;
use Hoa\Math;
use Hoa\Praspel;
use Hoa\Realdom;

/**
 * Class \Hoa\Praspel\Bin\Shell.
 *
 * Interactive Praspel shell.
 *
 * @copyright  Copyright © 2007-2017 Hoa community
 * @license    New BSD License
 */
class Shell extends Console\Dispatcher\Kit
{
    /**
     * Options description.
     *
     * @var array
     */
    protected $options = [
        ['help', Console\GetOption::NO_ARGUMENT, 'h'],
        ['help', Console\GetOption::NO_ARGUMENT, '?']
    ];



    /**
     * The entry method.
     *
     * @return  int
     */
    public function main()
    {
        while (false !== $c = $this->getOption($v)) {
            switch ($c) {
                case 'h':
                case '?':
                    return $this->usage();

                case '__ambiguous':
                    $this->resolveOptionAmbiguity($v);

                    break;
            }
        }

        Realdom::setDefaultSampler(new Math\Sampler\Random());

        $compiler = Compiler\Llk::load(
            new File\Read('hoa://Library/Praspel/Grammar.pp')
        );
        $interpreter = new Praspel\Visitor\Interpreter();
        $dump        = new Praspel\Visitor\Compiler();
        $interpreter->visit($compiler->parse('@requires;'));

        $iterator = new \RegexIterator(
            new \DirectoryIterator('hoa://Library/Realdom'),
            '/\.php$/'
        );
        $words = [];

        foreach ($iterator as $file) {
            $classname = 'Hoa\Realdom\\' . substr($file->getFilename(), 0, -4);
            $class     = new \ReflectionClass($classname);

            if ($class->isSubclassOf('\Hoa\Realdom')) {
                $words[] = $classname::NAME;
            }
        }

        $readline = new Console\Readline();
        $readline->setAutocompleter(
            new Console\Readline\Autocompleter\Word($words)
        );

        $expression = '.h';

        do {
            try {
                if ('.' === $expression[0]) {
                    @list($expression, $tail) = explode(' ', $expression);
                }

                switch ($expression) {
                    case '.h':
                    case '.help':
                        echo
                            'Usage:', "\n",
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
                        Console\Cursor::clear('↕');

                        break;

                    case '.v':
                    case '.variables':
                        foreach ($interpreter->getClause() as $variable) {
                            echo
                                $variable->getName(), ': ',
                                $variable->getHeld()->toPraspel(), "\n";
                        }

                        break;

                    case '.s':
                    case '.sample':
                        if (null === $tail) {
                            echo 'You must precise a variable name.', "\n";

                            break;
                        }

                        $_clause = $interpreter->getClause();

                        if (!isset($_clause[$tail])) {
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
                        if (null === $tail) {
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
                        if (null === $expression) {
                            break;
                        }

                        $interpreter->visit(
                            $compiler->parse($expression, 'expression')
                        );

                        break;

                }
            } catch (\Exception $e) {
                echo $e->getMessage(), "\n";
            }

            echo "\n";
        } while (false !== $expression = $readline->readLine('> '));

        return;
    }

    /**
     * The command usage.
     *
     * @return  int
     */
    public function usage()
    {
        echo
            'Usage   : praspel:shell <options> [expression]', "\n",
            'Options :', "\n",
            $this->makeUsageOptionsList([
                'help' => 'This help.'
            ]), "\n";

        return;
    }
}

__halt_compiler();
Interactive Praspel shell.
