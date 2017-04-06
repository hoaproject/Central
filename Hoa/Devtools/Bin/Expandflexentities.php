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

use Hoa\Consistency\Consistency;
use Hoa\Console;
use Hoa\File;

/**
 * Class \Hoa\Devtools\Bin\ExpandFlexEntities
 *
 * This command resolves the `class_alias` function for IDE.
 *
 * @copyright  Copyright © 2007-2017 Hoa community
 * @license    New BSD License
 */
class Expandflexentities extends Console\Dispatcher\Kit
{
    /**
     * Options description.
     *
     * @var array
     */
    protected $options = [
        ['output',  Console\GetOption::REQUIRED_ARGUMENT, 'o'],
        ['dry-run', Console\GetOption::NO_ARGUMENT,       'd'],
        ['verbose', Console\GetOption::NO_ARGUMENT,       'v'],
        ['help',    Console\GetOption::NO_ARGUMENT,       'h'],
        ['help',    Console\GetOption::NO_ARGUMENT,       '?']
    ];

    /**
     * The entry method.
     *
     * @return  void
     */
    public function main()
    {
        $dryRun  = false;
        $output  = 'php://output';
        $verbose = false;

        while (false !== $c = $this->getOption($v)) {
            switch ($c) {
                case 'd':
                    $dryRun = true;

                    break;

                case 'o':
                    $output = $v;

                    break;

                case 'V':
                    $verbose = true;

                    break;

                case 'h':
                case '?':
                    return $this->usage();

                case '__ambiguous':
                    $this->resolveOptionAmbiguity($v);

                    break;
            }
        }

        $hoaPath = 'hoa://Library/';
        $aliases = [];
        $finder  = new File\Finder();
        $finder
            ->in($hoaPath)
            ->name('#\.php$#')
            ->files();

        foreach ($finder as $file) {
            $pathName = $file->getPathName();
            $raw      = file_get_contents($pathName);

            if (0 === preg_match('#flexEntity\(\'(?P<classname>[^\']+)#', $raw, $class)) {
                preg_match('#\nclass_alias\(\'(?P<classname>[^\']+)#', $raw, $class);
            }

            if (empty($class)) {
                continue;
            }

            $FQCN      = $class['classname']; // Fully-Qualified Class Name
            $alias     = Consistency::getEntityShortestName($FQCN);
            $className = substr($alias, strrpos($alias, '\\') + 1);

            preg_match(
                '#((?:(?:abstract|final)\s)?' .
                'class|interface|trait)\s+' . $className . '\s#m',
                $raw,
                $keyword
            );

            $aliases[] = [
                'FQCN'      => $FQCN,
                'alias'     => $alias,
                'keyword'   => $keyword[1],
                'className' => $className
            ];

            if (true === $verbose) {
                echo
                    $keyword[1] . ': ' .
                    $FQCN . ' > ' .
                    $alias . "\n";
            }
        }

        $out = '<?php ' . "\n";

        foreach ($aliases as $class) {
            $ns = substr($class['alias'], 0, strrpos($class['alias'], '\\'));

            $out .=
                'namespace ' . $ns . ' {' . "\n" .
                $class['keyword'] . ' ' . $class['className'] .
                ' extends \\' . $class['FQCN'] . ' {}' . "\n" .
                '}' . "\n";
        }

        if (true === $dryRun) {
            echo $out;

            return;
        }

        file_put_contents($output, $out);

        return;
    }

    /**
     * The command usage.
     *
     * @return  void
     */
    public function usage()
    {
        echo
            'Usage   : devtools:expandflexentities <options>', "\n",
            'Options :', "\n",
             $this->makeUsageOptionsList([
                 'output'  => 'Where to output the result.',
                 'dry-run' => 'No written operation.',
                 'verbose' => 'Echo all information.',
                 'help'    => 'This help.'
             ]), "\n";

        return;
    }
}

__halt_compiler();
Expand entities to ease auto-completion in IDE.
