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

namespace Hoa\Devtools\Bin;

use Hoa\Console;
use Hoa\File;

/**
 * Class \Hoa\Devtools\Bin\Snapshot.
 *
 * Assistant to create a snapshot.
 *
 * @copyright  Copyright © 2007-2017 Hoa community
 * @license    New BSD License
 */
class Snapshot extends Console\Dispatcher\Kit
{
    /**
     * Options description.
     *
     * @var array
     */
    protected $options = [
        ['only-changelog',      Console\GetOption::NO_ARGUMENT,       'c'],
        ['only-tag',            Console\GetOption::NO_ARGUMENT,       't'],
        ['only-github-release', Console\GetOption::NO_ARGUMENT,       'g'],
        ['break-bc',            Console\GetOption::NO_ARGUMENT,       'b'],
        ['minimum-tag',         Console\GetOption::REQUIRED_ARGUMENT, 'm'],
        ['help',                Console\GetOption::NO_ARGUMENT,       'h'],
        ['help',                Console\GetOption::NO_ARGUMENT,       '?']
    ];



    /**
     * The entry method.
     *
     * @return  int
     */
    public function main()
    {
        $breakBC    = false;
        $minimumTag = null;
        $doSteps    = [
            // -1 and 1 mean true,
            // 0 means false.
            'test'      => -1,
            'changelog' => -1,
            'tag'       => -1,
            'github'    => -1
        ];

        $onlyStep = function ($step) use (&$doSteps) {
            $doSteps[$step] = 1;

            foreach ($doSteps as &$doStep) {
                if (-1 === $doStep) {
                    $doStep = 0;
                }
            }

            return;
        };

        while (false !== $c = $this->getOption($v)) {
            switch ($c) {
                case '__ambiguous':
                    $this->resolveOptionAmbiguity($v);

                    break;

                case 'c':
                    $onlyStep('changelog');

                    break;

                case 't':
                    $onlyStep('tag');

                    break;

                case 'g':
                    $onlyStep('github');

                    break;

                case 'b':
                    $breakBC = $v;

                    break;

                case 'm':
                    $minimumTag = $v;

                    break;

                case 'h':
                case '?':
                default:
                    return $this->usage();
            }
        }

        $this->parser->listInputs($repositoryRoot);

        if (empty($repositoryRoot)) {
            return $this->usage();
        }

        if (false === file_exists($repositoryRoot . DS . '.git')) {
            throw new Console\Exception(
                '%s is not a valid Git repository.',
                0, $repositoryRoot);
        }

        date_default_timezone_set('UTC');

        $allTags = $tags = [];

        $_tags = Console\Processus::execute(
            'git --git-dir=' . $repositoryRoot . '/.git ' .
                'tag'
        );

        if (!empty($_tags)) {
            $allTags = $tags = explode("\n", $_tags);
        }

        rsort($tags);

        $currentMCN = 0;

        if (!empty($tags)) {
            list($currentMCN) = explode('.', $tags[0], 2);
        }

        if (true === $breakBC) {
            ++$currentMCN;
        }

        $newTag = $currentMCN . '.' . date('y.m.d');

        if (null === $minimumTag) {
            if (!empty($tags)) {
                $tags = [$tags[0]];
            }
        } else {
            $toInt = function ($tag) {
                list($x, $y, $m, $d) = explode('.', $tag);

                return $x * 1000000 + $y * 10000 + $m * 100 + $d * 1;
            };

            $_tags       = [];
            $_minimumTag = $toInt($minimumTag);

            foreach ($tags as $tag) {
                if ($toInt($tag) >= $_minimumTag) {
                    $_tags[] = $tag;
                }
            }

            $tags = $_tags;
        }

        $changelog = '';

        echo 'We are going to snapshot this library together, by following ',
             'these steps:', "\n",
             '  1. tests must pass,', "\n",
             '  2. updating the CHANGELOG.md file,', "\n",
             '  3. commit the CHANGELOG.md file,', "\n",
             '  4. creating a tag,', "\n",
             '  5. pushing the tag,', "\n",
             '  6. creating a release on Github.', "\n";

        $step = function ($stepGroup, $message, $task) use ($doSteps) {
            echo "\n\n";
            Console\Cursor::colorize('foreground(black) background(yellow)');
            echo 'Step “', $message, '”.';
            Console\Cursor::colorize('normal');
            echo "\n";

            if (0 === $doSteps[$stepGroup]) {
                $answer = 'no';
            } else {
                $answer = $this->readLine('Would you like to do this one: [yes/no] ');
            }

            if ('yes' === $answer) {
                echo "\n";
                $task();
            } else {
                Console\Cursor::colorize('foreground(red)');
                echo 'Aborted!', "\n";
                Console\Cursor::colorize('normal');
            }
        };

        $step(
            'test',
            'tests must pass',
            function () {
                echo
                    'Tests must be green. Execute:', "\n",
                    '    $ hoa test:run -d Test', "\n",
                    'to run the tests.', "\n";

                $this->readLine('Press Enter when it is green (or Ctrl-C to abort).');
            }
        );

        $step(
            'changelog',
            'updating the CHANGELOG.md file',
            function () use ($tags, $newTag, $repositoryRoot, &$changelog) {
                $changelog = null;

                if (empty($tags)) {
                    $changelog .=
                        '# ' . $newTag . "\n\n" .
                        Console\Processus::execute(
                            'git --git-dir=' . $repositoryRoot . '/.git ' .
                                'log ' .
                                    '--first-parent ' .
                                    '--pretty="format:  * %s (%aN, %aI)"',
                            false
                        ) . "\n\n" .
                        '(first snapshot)' . "\n";
                } else {
                    array_unshift($tags, 'HEAD');

                    for ($i = 0, $max = count($tags) - 1; $i < $max; ++$i) {
                        $fromStep = $tags[$i];
                        $toStep   = $tags[$i + 1];
                        $title    = $fromStep;

                        if ('HEAD' === $fromStep) {
                            $title = $newTag;
                        }

                        $changelog .=
                            '# ' . $title . "\n\n" .
                            Console\Processus::execute(
                                'git --git-dir=' . $repositoryRoot . '/.git ' .
                                    'log ' .
                                        '--first-parent ' .
                                        '--pretty="format:  * %s (%aN, %aI)" ' .
                                        $fromStep . '...' . $toStep,
                                false
                            ) . "\n\n";
                    }
                }

                $file = new File\ReadWrite($repositoryRoot . DS . 'CHANGELOG.md');
                $file->rewind();

                $temporary = new File\ReadWrite($repositoryRoot . DS . '._hoa.CHANGELOG.md');
                $temporary->truncate(0);
                $temporary->writeAll($changelog);
                $temporary->close();

                echo 'The CHANGELOG is ready.', "\n";
                $this->readLine('Press Enter to check and edit the file (empty the file to abort).');

                Console\Chrome\Editor::open($temporary->getStreamName());

                $temporary->open();
                $changelog = $temporary->readAll();

                if (empty(trim($changelog))) {
                    $temporary->delete();
                    $temporary->close();

                    exit;
                }

                $previous = $file->readAll();
                $file->truncate(0);
                $file->writeAll($changelog . $previous);

                $temporary->delete();
                $temporary->close();
                $file->close();

                return;
            }
        );

        $step(
            'changelog',
            'commit the CHANGELOG.md file',
            function () use ($newTag, $repositoryRoot) {
                echo Console\Processus::execute(
                    'git --git-dir=' . $repositoryRoot . '/.git ' .
                        'add ' .
                            '--verbose ' .
                            'CHANGELOG.md'
                );
                echo Console\Processus::execute(
                    'git --git-dir=' . $repositoryRoot . '/.git ' .
                        'commit ' .
                            '--verbose ' .
                            '--message "Prepare ' . $newTag . '." ' .
                            'CHANGELOG.md'
                );

                return;
            }
        );

        $step(
            'tag',
            'creating a tag',
            function () use (
                $breakBC,
                $step,
                $currentMCN,
                $repositoryRoot,
                $tags,
                $newTag,
                $allTags
            ) {
                if (true === $breakBC) {
                    echo 'A BC break has been introduced, ',
                         'few more steps are required:', "\n";

                    $step(
                        'tag',
                        'update the composer.json file',
                        function () use ($currentMCN, $repositoryRoot) {
                            echo 'The `extra.branch-alias.dev-master` value ',
                                 'must be set to `',
                                 $currentMCN, '.x-dev`', "\n";

                            $this->readLine('Press Enter to edit the file.');

                            Console\Chrome\Editor::open(
                                $repositoryRoot . DS . 'composer.json'
                            );
                        }
                    );

                    $step(
                        'tag',
                        'open issues to update parent dependencies',
                        function () {
                            echo 'Some libraries may depend on this one. ',
                                 'Issues must be opened to update this ',
                                 'dependency.', "\n";

                            $this->readLine('Press Enter when it is done (or Ctrl-C to abort).');
                        }
                    );

                    $step(
                        'tag',
                        'update the README.md file',
                        function () use ($currentMCN, $repositoryRoot) {
                            echo 'The installation Section must invite the ',
                                 'user to install the version ',
                                 '`~', $currentMCN, '.0`.', "\n";

                            $this->readLine('Press Enter when it is done (or Ctrl-C to abort).');

                            Console\Chrome\Editor::open(
                                $repositoryRoot . DS . 'README.md'
                            );
                        }
                    );

                    $step(
                        'tag',
                        'commit the composer.json and README.md files',
                        function () use ($currentMCN, $repositoryRoot) {
                            echo Console\Processus::execute(
                                'git --git-dir=' . $repositoryRoot . '/.git ' .
                                    'add ' .
                                        '--verbose ' .
                                        'composer.json README.md'
                            );
                            echo Console\Processus::execute(
                                'git --git-dir=' . $repositoryRoot . '/.git ' .
                                    'commit ' .
                                        '--verbose ' .
                                        '--message "Update because of the BC break." ' .
                                        'composer.json README.md'
                            );
                        }
                    );
                }

                $status = Console\Processus::execute(
                    'git --git-dir=' . $repositoryRoot . '/.git ' .
                        'status ' .
                            '--short'
                );

                if (!empty($status)) {
                    Console\Cursor::colorize('foreground(white) background(red)');
                    echo 'At least one file is not commited!';
                    Console\Cursor::colorize('normal');
                    echo
                        "\n", '(tips: use `git stash` if it is not related ',
                        'to this snapshot)', "\n";

                    $this->readLine('Press Enter when everything is clean.');
                }

                echo
                    'Here is the list of tags:', "\n",
                    '  * ', implode(',' . "\n" . '  * ', $allTags), '.', "\n",
                    'We are going to create the following tag: ',
                    $newTag, '.', "\n";

                $answer = $this->readLine('Is it correct? [yes/no] ');

                if ('yes' !== $answer) {
                    return;
                }

                Console\Processus::execute(
                    'git --git-dir=' . $repositoryRoot . '/.git ' .
                        'tag -s ' . $newTag
                );
            }
        );

        $step(
            'tag',
            'push the new snapshot',
            function () use ($repositoryRoot) {
                Console\Cursor::colorize('foreground(white) background(red)');
                echo 'This step ',
                Console\Cursor::colorize('underlined');
                echo 'must not';
                Console\Cursor::colorize('!underlined');
                echo ' be undo!';
                Console\Cursor::colorize('normal');

                echo "\n";

                $i = 5;
                while ($i-- > 0) {
                    Console\Cursor::clear('↔');
                    echo($i + 1);
                    sleep(1);
                }

                $remotes = Console\Processus::execute(
                    'git --git-dir=' . $repositoryRoot . '/.git ' .
                        'remote ' .
                            '--verbose'
                );

                $gotcha = false;

                foreach (explode("\n", $remotes) as $remote) {
                    if (0 !== preg_match('/(git@git.hoa-project.net:[^ ]+)/', $remote, $matches)) {
                        $gotcha = true;

                        break;
                    }
                }

                if (false === $gotcha) {
                    echo 'No remote has been found.';

                    return;
                }

                echo
                    "\n", 'To push tag, execute:', "\n",
                    '    $ git push ', $matches[1], "\n",
                    '    $ git push ', $matches[1], ' --tags', "\n";

                $this->readLine('Press Enter when it is done (or Ctrl-C to abort).');
            }
        );

        $step(
            'github',
            'create a release on Github',
            function () use ($newTag, $changelog, $repositoryRoot) {
                $temporary = new File\ReadWrite($repositoryRoot . DS . '._hoa.GithubRelease.md');
                $temporary->truncate(0);

                if (!empty($changelog)) {
                    $temporary->writeAll($changelog);
                }

                $temporary->close();

                Console\Chrome\Editor::open($temporary->getStreamName());

                $temporary->open();
                $temporary->rewind();
                $body = $temporary->readAll();
                $temporary->delete();

                $composer = json_decode(file_get_contents('composer.json'));
                list(, $libraryName) = explode('/', $composer->name);

                $output = json_encode([
                    'tag_name' => $newTag,
                    'body'     => $body
                ]);

                $authToken = $this->readLine('Authentication token: ');

                $context = stream_context_create([
                    'http' => [
                        'method'  => 'POST',
                        'header'  => 'Host: api.github.com' . CRLF .
                                     'User-Agent: Hoa\Devtools' . CRLF .
                                     'Accept: application/json' . CRLF .
                                     'Content-Type: application/json' . CRLF .
                                     'Content-Length: ' . strlen($output) . CRLF .
                                     'Authorization: token ' . $authToken . CRLF,
                        'content' => $output
                    ]
                ]);

                echo file_get_contents(
                    'https://api.github.com/repos/hoaproject/' . $libraryName . '/releases',
                    false,
                    $context
                );
            }
        );

        echo "\n", '🍺 🍺 🍺', "\n";

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
            'Usage   : devtools:snapshot <options> repository-root', "\n",
            'Options :', "\n",
            $this->makeUsageOptionsList([
                'b'    => 'Whether we have break the backward compatibility ' .
                          'or not.',
                'c'    => 'Only do steps related to the CHANGELOG.md file.',
                't'    => 'Only do steps related to the tag.',
                'g'    => 'Only do steps related to Github release.',
                'm'    => 'Set the minimum tag (default: the latest, often ' .
                          'useful with --only-changelog).',
                'help' => 'This help.'
            ]), "\n";

        return;
    }
}

__halt_compiler();
Assistant to create a snapshot.
