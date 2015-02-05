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

namespace Hoa\Devtools\Bin;

use Hoa\Console;

/**
 * Class \Hoa\Devtools\Bin\Snapshot.
 *
 * Snapshot and generate changelog for the current repository root.
 *
 * @author     Ivan Enderlin <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright © 2007-2015 Ivan Enderlin.
 * @license    New BSD License
 */

class Snapshot extends Console\Dispatcher\Kit {

    /**
     * Options description.
     *
     * @var \Hoa\Devtools\Bin\Requiresnapshot array
     */
    protected $options = [
        ['break-bc',       Console\GetOption::NO_ARGUMENT, 'b'],
        ['only-changelog', Console\GetOption::NO_ARGUMENT, 'c'],
        ['help',           Console\GetOption::NO_ARGUMENT, 'h'],
        ['help',           Console\GetOption::NO_ARGUMENT, '?']
    ];



    /**
     * The entry method.
     *
     * @access  public
     * @return  int
     */
    public function main ( ) {

        $breakBC       = false;
        $onlyChangelog = false;

        while(false !== $c = $this->getOption($v)) switch($c) {

            case '__ambiguous':
                $this->resolveOptionAmbiguity($v);
              break;

            case 'b':
                $breakBC = $v;
              break;

            case 'c':
                $onlyChangelog = $v;
              break;

            case 'h':
            case '?':
            default:
                return $this->usage();
              break;
        }

        $this->parser->listInputs($repositoryRoot);

        if(empty($repositoryRoot))
            return $this->usage();

        if(false === file_exists($repositoryRoot . DS . '.git'))
            throw new Console\Exception(
                '%s is not a valid Git repository.',
                0, $repositoryRoot);

        $tags = explode(
            "\n",
            Console\Processus::execute(
                'git --git-dir=' . $repositoryRoot . '/.git ' .
                    'tag'
            )
        );
        rsort($tags);

        list($currentMCN) = explode('.', $tags[0], 2);

        if(true === $breakBC)
            ++$currentMCN;

        $newTag = $currentMCN . '.' . date('y.m.d');
        $steps  = $tags;

        if(false === $onlyChangelog) {

            $answer = $this->readLine(
                'Create tag ' . $newTag . ' for repository ' .
                realpath($repositoryRoot) . '? [yes/no] '
            );

            if('yes' === $answer) {

                Console\Processus::execute(
                    'git --git-dir=' . $repositoryRoot . '/.git ' .
                        'tag ' . $newTag
                );
                echo 'Done!', "\n";

                array_unshift($steps, $newTag);
            }
            else
                echo 'Aborted!', "\n";
        }
        else
            array_unshift($steps, 'HEAD');

        $changelog = null;

        for($i = 0, $max = count($steps) - 1; $i < $max; ++$i) {

            $fromStep = $steps[$i];
            $toStep   = $steps[$i + 1];

            $changelog .= '# ' . '`' . $fromStep . '`' . "\n\n" .
                          Console\Processus::execute(
                              'git --git-dir=' . $repositoryRoot . '/.git ' .
                                  'log ' .
                                      '--first-parent ' .
                                      '--pretty="format:  * %h %s (%aN, %aI)" ' .
                                      $fromStep . '...' . $toStep,
                              false
                          ) . "\n\n";

            if($max < $i + 2)
                $changelog .= '`' . $toStep . '`' . "\n\n" .
                              '(first snapshot)';
        }

        echo "\n", $changelog;

        return;
    }

    /**
     * The command usage.
     *
     * @access  public
     * @return  int
     */
    public function usage ( ) {

        echo 'Usage   : devtools:snapshot <options> repository-root', "\n",
             'Options :', "\n",
             $this->makeUsageOptionsList([
                 'b'    => 'Whether we have break the backward compatibility ' .
                           'or not.',
                 'c'    => 'Only print the changelog.',
                 'help' => 'This help.'
             ]), "\n";

        return;
    }
}

__halt_compiler();
Snapshot and generate changelog for the current repository root.
