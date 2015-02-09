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
 * Assistant to create a snapshot.
 *
 * @author     Ivan Enderlin <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright © 2007-2015 Ivan Enderlin.
 * @license    New BSD License
 */

class Snapshot extends Console\Dispatcher\Kit {

    /**
     * Options description.
     *
     * @var \Hoa\Devtools\Bin\Snapshot array
     */
    protected $options = [
        ['break-bc', Console\GetOption::NO_ARGUMENT, 'b'],
        ['help',     Console\GetOption::NO_ARGUMENT, 'h'],
        ['help',     Console\GetOption::NO_ARGUMENT, '?']
    ];



    /**
     * The entry method.
     *
     * @access  public
     * @return  int
     */
    public function main ( ) {

        $breakBC = false;

        while(false !== $c = $this->getOption($v)) switch($c) {

            case '__ambiguous':
                $this->resolveOptionAmbiguity($v);
              break;

            case 'b':
                $breakBC = $v;
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

        echo 'We are going to release this library together, by following ',
             'these steps:', "\n",
             '  1. tests must pass,', "\n",
             '  2. updating the CHANGELOG.md file,', "\n",
             '  3. commit the CHANGELOG.md file,', "\n",
             '  4. creating a tag,', "\n",
             '  5. pushing the tag,', "\n",
             '  6. creating a release on Github.', "\n";

        $step = function ( $message, $task ) {

            echo "\n\n";
            Console\Cursor::colorize('foreground(black) background(yellow)');
            echo 'Step “', $message, '”.';
            Console\Cursor::colorize('normal');
            echo "\n";

            $answer = $this->readLine('Would you like to do this one: [yes/no] ');

            if('yes' === $answer) {

                echo "\n";
                $task();
            }
            else {

                Console\Cursor::colorize('foreground(red)');
                echo 'Aborted!', "\n";
                Console\Cursor::colorize('normal');
            }
        };

        $step(
            'tests must pass',
            function ( ) {

                echo 'Tests must be green. Execute:', "\n",
                     '    $ hoa test:run -d Test', "\n",
                     'to run the tests.', "\n";
                $this->readLine('Press Enter when it is green.');
            }
        );

        $step(
            'updating the CHANGELOG.md file',
            function ( ) use ( $tags, $repositoryRoot ) {

                $changelog = null;

                for($i = 0, $max = count($tags) - 1; $i < $max; ++$i) {

                    $fromStep = $tags[$i];
                    $toStep   = $tags[$i + 1];

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
                        $changelog .=  $toStep . "\n\n" .
                                      '(first snapshot)';
                }

                echo $changelog;

                return;
            }
        );

        $step(
            'commit the CHANGELOG.md file',
            function ( ) {

                echo 'Great! Now commit the CHANGELOG.md file!', "\n";
                $this->readLine('Press Enter when it is done.');

                return;
            }
        );

        $step(
            'creating a tag',
            function ( ) use ( $breakBC, $step, $currentMCN, $repositoryRoot,
                               $tags, $newTag ) {

                if(true === $breakBC) {

                    echo 'A BC break has been introduced, ',
                         'few more steps are required:', "\n";

                    $step(
                        'update the composer.json file',
                        function ( ) use ( $currentMCN ) {

                            echo 'The `extra.branch-alias.dev-master` value ',
                                 'must be set to `',
                                 $currentMCN, '.x-dev`', "\n";
                            $this->readLine('Press Enter when it is done.');
                        }
                    );

                    $step(
                        'open issues to update parent dependencies',
                        function ( ) {

                            echo 'Some libraries may depend on this one. ',
                                 'Issues must be opened to update this ',
                                 'dependency.', "\n";
                            $this->readLine('Press Enter when it is done.');
                        }
                    );

                    $step(
                        'update the README.md file',
                        function ( ) use ( $currentMCN ) {

                            echo 'The installation Section must invite the ',
                                 'user to install the version ',
                                 '`~', $currentMCN, '.0`.', "\n";
                            $this->readLine('Press Enter when it is done.');
                        }
                    );

                    $step(
                        'commit the composer.json and README.md files',
                        function ( ) use ( $currentMCN ) {

                            echo 'Great! Now commit the composer.json and ',
                                 'README.md files!', "\n";
                            $this->readLine('Press Enter when it is done.');
                        }
                    );
                }

                $status = Console\Processus::execute(
                    'git --git-dir=' . $repositoryRoot . '/.git ' .
                        'status ' .
                            '--short'
                );

                if(!empty($status)) {

                    Console\Cursor::colorize('foreground(white) background(red)');
                    echo 'At least one file is not commited!';
                    Console\Cursor::colorize('normal');
                    echo "\n", '(tips: use `git stash` if it is not related ',
                         'to this snapshot)', "\n";

                    $this->readLine('Press Enter when everything is clean.');
                }

                echo 'Here is the list of tags:', "\n",
                     '  * ', implode(',' . "\n" . '  * ', $tags), '.', "\n",
                     'We are going to create the following tag: ',
                     $newTag, '.', "\n";

                $answer = $this->readLine('Is it correct? [yes/no] ');

                if('yes' !== $answer)
                    return;

                Console\Processus::execute(
                    'git --git-dir=' . $repositoryRoot . '/.git ' .
                        'tag ' . $newTag
                );
            }
        );

        $step(
            'push the new snapshot',
            function ( ) use ( $repositoryRoot ) {

                Console\Cursor::colorize('foreground(white) background(red)');
                echo 'This step ',
                Console\Cursor::colorize('underlined');
                echo 'must not';
                Console\Cursor::colorize('!underlined');
                echo ' be undo!';
                Console\Cursor::colorize('normal');

                echo "\n";

                $i = 5;
                while($i-- > 0) {

                    Console\Cursor::clear('↔');
                    echo ($i + 1);
                    sleep(1);
                }

                $remotes = Console\Processus::execute(
                    'git --git-dir=' . $repositoryRoot . '/.git ' .
                        'remote ' .
                            '--verbose'
                );

                $gotcha = false;

                foreach(explode("\n", $remotes) as $remote)
                    if(0 !== preg_match('/(git@git.hoa-project.net:[^ ]+)/', $remote, $matches)) {

                        $gotcha = true;

                        break;
                    }

                if(false === $gotcha) {

                    echo 'No remote has been found.';

                    return;
                }

                echo "\n", 'To push tag, execute:', "\n",
                     '    $ git push ', $matches[1], "\n";
                $this->readLine('Press Enter when it is done.');
            }
        );

        $step(
            'create a release on Github',
            function ( ) {

                
            }
        );

        echo "\n", '🍺 🍺 🍺', "\n";

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
                 'help' => 'This help.'
             ]), "\n";

        return;
    }
}

__halt_compiler();
Assistant to create a snapshot.
