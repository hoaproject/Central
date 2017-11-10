<?php

declare(strict_types=1);

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

namespace Hoa\Test\Bin;

use Hoa\Consistency;
use Hoa\Console;
use Hoa\File;
use Hoa\Protocol;
use Hoa\Test;
use Kitab;

/**
 * Class Hoa\Test\Bin\Run.
 *
 * Run tests.
 *
 * @license    New BSD License
 */
class Run extends Console\Dispatcher\Kit
{
    /**
     * Options description.
     *
     * @var array
     */
    protected $options = [
        ['all',                  Console\GetOption::NO_ARGUMENT,       'a'],
        ['libraries',            Console\GetOption::REQUIRED_ARGUMENT, 'l'],
        ['namespaces',           Console\GetOption::REQUIRED_ARGUMENT, 'n'],
        ['directories',          Console\GetOption::REQUIRED_ARGUMENT, 'd'],
        ['files',                Console\GetOption::REQUIRED_ARGUMENT, 'f'],
        ['filter',               Console\GetOption::REQUIRED_ARGUMENT, 'F'],
        ['debug',                Console\GetOption::NO_ARGUMENT,       'D'],
        ['php-binary',           Console\GetOption::REQUIRED_ARGUMENT, 'p'],
        ['concurrent-processes', Console\GetOption::REQUIRED_ARGUMENT, 'P'],
        ['no-code-coverage',     Console\GetOption::NO_ARGUMENT,       'N'],
        ['help',                 Console\GetOption::NO_ARGUMENT,       'h'],
        ['help',                 Console\GetOption::NO_ARGUMENT,       '?']
    ];



    /**
     * The entry method.
     *
     * @return  int
     */
    public function main(): int
    {
        $directories         = [];
        $files               = [];
        $namespaces          = [];
        $filter              = [];
        $debug               = false;
        $php                 = null;
        $concurrentProcesses = 2;
        $preludeFiles        = [];
        $codeCoverage        = true;

        $extractPreludeFiles = function ($composerSchema) {
            if (!file_exists($composerSchema)) {
                return [];
            }

            $schema = json_decode(file_get_contents($composerSchema), true);

            if (!isset($schema['autoload']) ||
                !isset($schema['autoload']['files'])) {
                return [];
            }

            return $schema['autoload']['files'];
        };

        while (false !== $c = $this->getOption($v)) {
            switch ($c) {
                case 'a':
                    $root     = dirname(dirname(__DIR__));
                    $iterator = new File\Finder();
                    $iterator
                        ->in($root)
                        ->directories()
                        ->maxDepth(1);

                    foreach ($iterator as $fileinfo) {
                        $libraryName = $fileinfo->getBasename();

                        if (true === Consistency::isKeyword($libraryName)) {
                            continue;
                        }

                        $pathname       = $root . DS . $libraryName;
                        $tests          = $pathname . DS . 'Test';
                        $composerSchema = $pathname . DS . 'composer.json';

                        if (is_dir($tests)) {
                            $directories[] = $tests;

                            if (!in_array($libraryName, ['Consistency', 'Protocol'])) {
                                foreach ($extractPreludeFiles($composerSchema) as $preludeFile) {
                                    $preludeFiles[] = $pathname . DS . $preludeFile;
                                }
                            }
                        }
                    }

                    break;

                case 'l':
                    foreach ($this->parser->parseSpecialValue($v) as $library) {
                        $libraryName    = ucfirst(strtolower($library));
                        $pathname       = dirname(dirname(__DIR__)) . DS . $libraryName;
                        $tests          = $pathname . DS . 'Test';
                        $composerSchema = $pathname . DS . 'composer.json';

                        if (!is_dir($tests)) {
                            throw new Console\Exception(
                                'Library %s does not exist or has no test.',
                                0,
                                $libraryName
                            );
                        }

                        $directories[] = $tests;

                        if (!in_array($libraryName, ['Consistency', 'Protocol'])) {
                            foreach ($extractPreludeFiles($composerSchema) as $preludeFile) {
                                $preludeFiles[] = $pathname . DS . $preludeFile;
                            }
                        }
                    }

                    break;

                case 'n':
                    foreach ($this->parser->parseSpecialValue($v) as $namespace) {
                        $namespace = str_replace('.', '\\', $namespace);
                        $parts     = explode('\\', $namespace);

                        if (2 > count($parts)) {
                            throw new Console\Exception(
                                'Namespace %s is too short.',
                                1,
                                $namespace
                            );
                        }

                        $head               = Protocol::resolve('hoa://Library/' . $parts[1]);
                        $tail               = implode(DS, array_slice($parts, 2));
                        $namespaceDirectory = $head . DS . $tail;

                        if (!is_dir($namespaceDirectory)) {
                            throw new Console\Exception(
                                'Namespace %s does not exist.',
                                2,
                                $namespace
                            );
                        }

                        $tests = $head . DS . 'Test' . DS;

                        if (is_dir($tests)) {
                            $directories[] = $tests;
                        }

                        $namespaces[] = $namespace;
                    }

                    break;

                case 'd':
                    foreach ($this->parser->parseSpecialValue($v) as $directory) {
                        if (!is_dir($directory)) {
                            throw new Console\Exception(
                                'Directory %s does not exist.',
                                3,
                                $directory
                            );
                        }

                        $directories[] = $directory;
                    }

                    break;

                case 'f':
                    foreach ($this->parser->parseSpecialValue($v) as $file) {
                        if (!file_exists($file)) {
                            throw new Console\Exception(
                                'File %s does not exist.',
                                4,
                                $file
                            );
                        }

                        $files[] = $file;
                    }

                    break;

                case 'F':
                    $filter = $v;

                    break;

                case 'D':
                    $debug = $v;

                    break;

                case 'p':
                    $php = $v;

                    break;

                case 'P':
                    $concurrentProcesses = intval($v);

                    break;

                case 'N':
                    $codeCoverage = false;

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
        }

        $kitabOutputDirectory = File\Temporary\Temporary::getTemporaryDirectory() . DS . 'Hoa.kitab.test.output' . DS;
        Protocol\Protocol::getInstance()['Kitab']['Output']->setReach("\r" . $kitabOutputDirectory . DS);

        $kitabFinder = new Kitab\Finder();
        $kitabFinder->notIn('/^vendor$/');

        if (empty($directories) && empty($files) && empty($namespaces)) {
            $kitabFinder->in('.');

            if (is_dir('Test')) {
                $directories[] = 'Test';
            }

            $directories[] = $kitabOutputDirectory;
        } else {
            foreach ($directories as $directory) {
                $kitabFinder->in($directory);
            }
        }

        if (is_dir($kitabOutputDirectory)) {
            $since = time() - filemtime($kitabOutputDirectory);
            $kitabFinder->modified('since ' . $since . ' seconds');
        } else {
            File\Directory::create($kitabOutputDirectory);
        }

        $kitabTarget   = new Kitab\Compiler\Target\DocTest\DocTest();
        $kitabCompiler = new Kitab\Compiler\Compiler();
        $kitabCompiler->compile($kitabFinder, $kitabTarget);


        // In the `PATH`.
        $atoum = 'atoum';

        if (WITH_COMPOSER) {
            // From the `vendor/hoa/test/Bin/` directory.
            $atoum =
                dirname(dirname(dirname(__DIR__))) . DS .
                'bin' . DS .
                'atoum';

            if (false === file_exists($atoum)) {
                // From `Bin/` directory.
                $atoum =
                    dirname(__DIR__) . DS .
                    'vendor' . DS .
                    'bin' . DS .
                    'atoum';
            }
        } elseif (isset($_SERVER['HOA_ATOUM_BIN'])) {
            $atoum = $_SERVER['HOA_ATOUM_BIN'];
        }

        $command =
            $atoum .
            ' --autoloader-file ' .
                Protocol\Protocol::getInstance()->resolve('hoa://Library/Test/.autoloader.atoum.php') .
            ' --configurations ' .
                Protocol\Protocol::getInstance()->resolve('hoa://Library/Test/.atoum.php') .
            ' --force-terminal' .
            ' --max-children-number ' . $concurrentProcesses;

        if (true === $debug) {
            $command .= ' --debug';
        }

        if (false === $codeCoverage) {
            $command .= ' --no-code-coverage';
        }

        if (null !== $php) {
            $command .= ' --php ' . $php;
        }

        if (!empty($directories)) {
            $command .= ' --directories ' . implode(' ', $directories);
        } elseif (!empty($files)) {
            $command .= ' --files ' . implode(' ', $files);
        } else {
            return $this->usage();
        }

        if (!empty($namespaces)) {
            $command .= ' --namespaces ' . implode(' ', $namespaces);
        }

        if (!empty($filter)) {
            $command .= ' --filter \'' . str_replace('\'', '\'"\'"\'', $filter) . '\'';
        }

        $_server                      = $_SERVER;
        $_server['HOA_PREVIOUS_CWD']  = getcwd();

        if (!empty($preludeFiles)) {
            $_server['HOA_PRELUDE_FILES'] = implode("\n", $preludeFiles);
        }

        $processus = new Processus(
            $command,
            null,
            null,
            Protocol\Protocol::getInstance()->resolve('hoa://Library/Test/'),
            $_server
        );
        $processus->on('input', function ($bucket) {
            return false;
        });
        $processus->on('output', function ($bucket): void {
            $data = $bucket->getData();

            echo $data['line'], "\n";

            return;
        });
        $processus->on('stop', function ($bucket): void {
            // Wait atoum to finish its sub-children.
            sleep(1);
            exit($bucket->getSource()->getExitCode());
        });
        $processus->run();

        return 0;
    }

    /**
     * The command usage.
     *
     * @return  int
     */
    public function usage(): int
    {
        echo
            'Usage   : test:run <options>', "\n",
            'Options :', "\n",
            $this->makeUsageOptionsList([
                'a'    => 'Run tests of all libraries.',
                'l'    => 'Run tests of some libraries.',
                'n'    => 'Run tests of some namespaces.',
                'd'    => 'Run tests of some directories.',
                'f'    => 'Run tests of some files.',
                'F'    => 'Filter tests with a ruler expression (see ' .
                          'Hoa\Ruler).',
                'D'    => 'Activate the debugging mode.',
                'p'    => 'Path to a specific PHP binary.',
                'P'    => 'Maximum concurrent processes that can run.',
                'N'    => 'Disable the code coverage score (can accelerate ' .
                          'test execution).',
                'help' => 'This help.'
            ]), "\n\n",
            'Available variables for filter expressions:', "\n",
            '    * method,', "\n",
            '    * class,', "\n",
            '    * namespace,', "\n",
            '    * tags.', "\n";

        return 0;
    }
}

class Processus extends Console\Processus
{
    /**
     * Avoid to escape the command.
     */
    protected function setCommand(string $command): ?string
    {
        $old            = $this->getCommand();
        $this->_command = $command;

        return $old;
    }
}

__halt_compiler();
Run tests.
