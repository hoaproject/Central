<?php

/**
 * Hoa
 *
 *
 * @license
 *
 * New BSD License
 *
 * Copyright © 2007-2012, Ivan Enderlin. All rights reserved.
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

namespace Hoa\Core\Bin {

/**
 * Class \Hoa\Core\Bin\Welcome.
 *
 * Welcome screen.
 *
 * @author     Ivan Enderlin <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright © 2007-2012 Ivan Enderlin.
 * @license    New BSD License
 */

class Welcome extends \Hoa\Console\Dispatcher\Kit {

    /**
     * Options description.
     *
     * @var \Hoa\Core\Bin\Welcome array
     */
    protected $options = array(
        array('library', \Hoa\Console\GetOption::REQUIRED_ARGUMENT, 'l'),
        array('help',    \Hoa\Console\GetOption::NO_ARGUMENT,       'h'),
        array('help',    \Hoa\Console\GetOption::NO_ARGUMENT,       '?'),
    );



    /**
     * The entry method.
     *
     * @access  public
     * @return  int
     */
    public function main ( ) {

        $library = null;

        while(false !== $c = $this->getOption($v)) switch($c) {

            case 'l':
                $library = $this->parser->parseSpecialValue($v);
              break;

            case 'h':
            case '?':
                return $this->usage();
              break;

            case '__ambiguous':
                $this->resolveOptionAmbiguity($v);
              break;
        }

        cout(\Hoa\Console\Chrome\Text::align(
            $this->stylize('Hoa', 'h1'),
            \Hoa\Console\Chrome\Text::ALIGN_CENTER
        ));

        cout('Welcome in the command-line interface of Hoa :-).' .  "\n");
        cout($this->stylize('List of available commands', 'h2'));

        cout();

        if(null !== $library)
            $library = array_map('strtolower', $library);

        $locations = array_merge(
            resolve('hoa://Library', true, true),
            resolve('hoa://Data/Library', true, true)
        );
        $iterator  = new \AppendIterator();

        foreach($locations as $location)
            $iterator->append(new \GlobIterator(
                $location . '*' . DS . 'Bin' . DS . '*.php'
            ));

        $binaries = array();

        foreach($iterator as $entry) {

            $pathname = $entry->getPathname();
            $lib      = basename(dirname(dirname($pathname)));
            $bin      = mb_strtolower(
                mb_substr($entry->getBasename(), 0, -4)
            );

            if(   null !== $library
               && false === in_array(strtolower($lib), $library))
                continue;

            if('Core' === $lib && 'hoa' === $bin)
                continue;

            if(!isset($binaries[$lib]))
                $binaries[$lib] = array();

            $lines       = file($pathname);
            $description = '';

            // Berk…
            for($i = count($lines) - 1; $i >= 0; --$i)
                if('__halt_compiler();' . "\n" === $lines[$i]) {

                    $description = trim(implode('', array_slice($lines, $i + 1)));
                    break;
                }

            unset($lines);

            $binaries[$lib][] = array(
                'name'        => $bin,
                'description' => $description
            );
        }

        $out = array();

        foreach($binaries as $group => $commands) {

            $out[] = array($group);

            foreach($commands as $binary)
                $out[] = array(
                    '    ' . $this->stylize($binary['name'], 'command'),
                    $binary['description']
                );
        }

        cout(\Hoa\Console\Chrome\Text::columnize($out));

        return;
    }

    /**
     * The command usage.
     *
     * @access  public
     * @return  int
     */
    public function usage ( ) {

        cout('Usage   : core:welcome <options>');
        cout('Options :');
        cout($this->makeUsageOptionsList(array(
            'l'    => 'Filter libraries to list (comma-separated).',
            'help' => 'This help.'
        )));

        return;
    }
}

}

__halt_compiler();
This page.
