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
use Hoa\Xyl;

/**
 * Class Hoa\Devtools\Bin\Documentation.
 *
 * Generate documentation.
 *
 * @copyright  Copyright © 2007-2017 Hoa community
 * @license    New BSD License
 */
class Documentation extends Console\Dispatcher\Kit
{
    /**
     * Options description.
     *
     * @var array
     */
    protected $options = [
        ['clean',       Console\GetOption::NO_ARGUMENT,       'c'],
        ['directories', Console\GetOption::REQUIRED_ARGUMENT, 'd'],
        ['language',    Console\GetOption::REQUIRED_ARGUMENT, 'l'],
        ['open',        Console\GetOption::NO_ARGUMENT,       'o'],
        ['help',        Console\GetOption::NO_ARGUMENT,       'h'],
        ['help',        Console\GetOption::NO_ARGUMENT,       '?']
    ];



    /**
     * The entry method.
     *
     * @return  int
     */
    public function main()
    {
        $directories = [];
        $clean       = false;
        $lang        = 'En';
        $open        = false;

        while (false !== $c = $this->getOption($v)) {
            switch ($c) {
                case 'd':
                    foreach ($this->parser->parseSpecialValue($v) as $directory) {
                        $directory = realpath($directory);

                        if (false === is_dir($directory)) {
                            throw new Console\Exception(
                                'Directory %s does not exist.',
                                0,
                                $directory
                            );
                        }

                        $directories[] = $directory;
                    }

                    break;

                case 'c':
                    $clean = true;

                    break;

                case 'l':
                    $lang = ucfirst(strtolower($v));

                    break;

                case 'o':
                    $open = $v;

                    break;

                case '__ambiguous':
                    $this->resolveOptionAmbiguity($v);

                    break;

                case 'h':
                case '?':
                default:
                    return $this->usage();
            }
        }

        $workspace =
            resolve('hoa://Library/Devtools/Resource/Documentation') . DS .
            'HackBook.output';

        if (true === $clean) {
            if (true === is_dir($workspace)) {
                $directory = new File\Directory($workspace);
                $directory->delete();
                unset($directory);
            }

            return;
        }

        if (empty($directories)) {
            $directories[] = getcwd();
        }

        clearstatcache(true);

        $workspace .= DS . $lang;

        if (false === is_dir($workspace)) {
            File\Directory::create($workspace);
        }

        Console\Cursor::colorize('foreground(yellow)');
        echo 'Selected language: ', $lang, '.', "\n\n";
        Console\Cursor::colorize('normal');

        require_once 'hoa://Library/Devtools/Resource/Documentation/Router.php';
        // $router is defined.

        $finder = new File\Finder();

        foreach ($directories as $location) {
            $_location = $location . DS . 'Documentation' . DS . $lang;

            if (false === is_dir($_location)) {
                throw new Console\Exception(
                    'There is no documentation for the %s library ' .
                    '(checked directory %s).',
                    1,
                    [basename($location), $_location]
                );
            }

            $finder->in($_location);
        }

        foreach (resolve('hoa://Library', true, true) as $location) {
            $libraryFinder = new File\Finder();
            $libraryFinder
                ->in($location)
                ->directories()
                ->maxDepth(1);

            foreach ($libraryFinder as $_location) {
                $_location =
                    $_location->getPathName() . DS .
                    'Documentation' . DS .
                    $lang;

                if (true === is_dir($_location)) {
                    $finder->in($_location);
                }
            }
        }

        $vendors = [];

        foreach ($finder as $entry) {
            $path    = dirname(dirname($entry->getPath()));
            $vendor  = ucfirst(strtolower(basename(dirname($path))));
            $library = ucfirst(strtolower(basename($path)));

            if (!isset($vendors[$vendor])) {
                $vendors[$vendor] = [];
            }

            $vendors[$vendor][$library] = [
                'library'  => $library,
                'vendor'   => $vendor,
                'fullname' => $vendor . '\\' . $library
            ];
        }

        foreach ($vendors as $vendor => &$libraries) {
            $libraries = array_values($libraries);
        }

        $layout = new File\Read('hoa://Library/Devtools/Resource/Documentation/Layout.xyl');
        $xyl    = new Xyl(
            $layout,
            new File\Write($workspace . '/index.html'),
            new Xyl\Interpreter\Html(),
            $router
        );
        $xyl->setTheme('');
        $data = $xyl->getData();

        foreach ($vendors as $vendor => $libraries) {
            $data->vendors->vendor = [
                'name'    => $vendor,
                'library' => $libraries
            ];
        }

        $xyl->addOverlay('hoa://Library/Devtools/Resource/Documentation/Index.xyl');
        $xyl->render();

        echo 'Generate', "\t";
        Console\Cursor::colorize('foreground(green)');
        echo 'index.html';
        Console\Cursor::colorize('normal');
        echo '.', "\n";

        $xyl = null;

        foreach ($vendors as $vendor => $libraries) {
            File\Directory::create(
                $workspace .
                dirname(
                    $router->unroute(
                        'full',
                        [
                            'vendor'  => $libraries[0]['vendor'],
                            'chapter' => $libraries[0]['library']
                        ]
                    )
                )
            );

            foreach ($libraries as $library) {
                $in =
                    'hoa://Library/' . $library['library'] .
                    '/Documentation/' . $lang . '/Index.xyl';

                $out =
                    $workspace .
                    $router->unroute(
                        'full',
                        [
                            'vendor'  => $library['vendor'],
                            'chapter' => $library['library']
                        ]
                    );

                if (true === file_exists($out) &&
                    filemtime($in) <= filemtime($out)) {
                    echo 'Skip', "\t\t";
                    Console\Cursor::colorize('foreground(green)');
                    echo $library['fullname'];
                    Console\Cursor::colorize('normal');
                    echo '.', "\n";

                    continue;
                }

                $out = new File\Write($out);
                $out->truncate(0);

                if (null === $xyl) {
                    $xyl = new Xyl(
                        $layout,
                        $out,
                        new Xyl\Interpreter\Html(),
                        $router
                    );
                    $xyl->setTheme('');
                    $xyl->addOverlay('hoa://Library/Devtools/Resource/Documentation/Chapter.xyl');
                } else {
                    $xyl->setOutputStream(
                        new File\Write($out)
                    );
                }

                $xyl->addOverlay($in);
                $xyl->getData()->name[0]    = $library['fullname'];
                $xyl->getData()->library[0] = $library['library'];

                try {
                    $xyl->render();
                } catch (\Exception $e) {
                    echo $e->getMessage(), "\n";
                }

                $xyl->removeOverlay($in);

                echo 'Generate', "\t";
                Console\Cursor::colorize('foreground(green)');
                echo $library['fullname'];
                Console\Cursor::colorize('normal');
                echo '.', "\n";
            }
        }

        $pathname = escapeshellarg('file://' . $workspace . '/index.html');

        echo "\n";

        if (true === $open) {
            if (isset($_SERVER['BROWSER'])) {
                echo
                    'Opening…', "\n",
                    Console\Processus::execute($_SERVER['BROWSER'] . ' ' . $pathname, false);

                return;
            }

            $utilities = [
                'open',
                'xdg-open',
                'gnome-open',
                'kde-open'
            ];

            foreach ($utilities as $utility) {
                if (null !== $utilityPath = Console\Processus::locate($utility)) {
                    echo
                        'Opening…', "\n",
                        Console\Processus::execute($utilityPath . ' ' . $pathname, false);

                    return;
                }
            }

            echo 'Did not succeed to open the documentation automatically.', "\n";
        }

        echo "\n", 'Open ', $pathname, '.', "\n";

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
            'Usage   : devtools:documentation <options>', "\n",
            'Options :', "\n",
            $this->makeUsageOptionsList([
                'd'    => 'Scan documentation of some directories ' .
                          '(root of the library).',
                'c'    => 'Clean the generated documentation.',
                'l'    => 'Language (default: en).',
                'o'    => 'Open the documentation in a browser after its computation.',
                'help' => 'This help.'
            ]), "\n";

        return;
    }
}

__halt_compiler();
Generate offline documentation.
