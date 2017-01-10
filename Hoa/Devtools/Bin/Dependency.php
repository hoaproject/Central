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

/**
 * Class \Hoa\Devtools\Bin\Dependency.
 *
 * This command manipulates dependencies of a library.
 *
 * @copyright  Copyright © 2007-2017 Hoa community
 * @license    New BSD License
 */
class Dependency extends Console\Dispatcher\Kit
{
    /**
     * Options description.
     *
     * @var array
     */
    protected $options = [
        ['no-verbose',   Console\GetOption::NO_ARGUMENT, 'V'],
        ['only-library', Console\GetOption::NO_ARGUMENT, 'l'],
        ['only-version', Console\GetOption::NO_ARGUMENT, 'v'],
        ['help',         Console\GetOption::NO_ARGUMENT, 'h'],
        ['help',         Console\GetOption::NO_ARGUMENT, '?']
    ];



    /**
     * The entry method.
     *
     * @return  int
     */
    public function main()
    {
        $verbose = Console::isDirect(STDOUT);
        $print   = 'both';

        while (false !== $c = $this->getOption($v)) {
            switch ($c) {
                case 'V':
                    $verbose = false;

                    break;

                case 'l':
                    $print = 'library';

                    break;

                case 'v':
                    $print = 'version';

                    break;

                case 'h':
                case '?':
                    return $this->usage();

                case '__ambiguous':
                    $this->resolveOptionAmbiguity($v);

                    break;
            }
        }

        $this->parser->listInputs($library);

        if (empty($library)) {
            return $this->usage();
        }

        $library = ucfirst(strtolower($library));
        $path    = 'hoa://Library/' . $library . '/composer.json';

        if (true === $verbose) {
            echo 'Dependency for the library ', $library, ':', "\n";
        }

        if (false === file_exists($path)) {
            throw new Console\Exception(
                'Not yet computed or the %s library does not exist.',
                0,
                $library
            );
        }

        $json = json_decode(file_get_contents($path), true);

        if (true === $verbose) {
            $item      = '    • ';
            $separator = ' => ';
        } else {
            $item      = '';
            $separator = ' ';
        }

        foreach ($json['require'] ?: [] as $dependency => $version) {
            switch ($print) {
                case 'both':
                    echo $item, $dependency, $separator, $version, "\n";

                    break;

                case 'library':
                    echo $item, $dependency, "\n";

                    break;

                case 'version':
                    echo $item, $version, "\n";

                    break;
            }
        }

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
            'Usage   : devtools:dependency <options> library', "\n",
            'Options :', "\n",
            $this->makeUsageOptionsList([
                'V'    => 'No-verbose, i.e. be as quiet as possible, just print ' .
                          'essential informations.',
                'l'    => 'Print only the library name.',
                'v'    => 'Print only the version.',
                'help' => 'This help.'
            ]), "\n";

        return;
    }
}

__halt_compiler();
Manipulate dependencies of a library.
