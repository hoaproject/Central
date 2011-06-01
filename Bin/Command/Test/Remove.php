<?php

/**
 * Hoa
 *
 *
 * @license
 *
 * New BSD License
 *
 * Copyright © 2007-2011, Ivan Enderlin. All rights reserved.
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
 * \Hoa\Test
 */
-> import('Test.~')

/**
 * \Hoa\File\Finder
 */
-> import('File.Finder');

}

namespace Bin\Command\Test {

/**
 * Class \Bin\Command\Test\Remove.
 *
 * Remove a test.
 *
 * @author     Ivan Enderlin <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright © 2007-2011 Ivan Enderlin.
 * @license    New BSD License
 */

class Remove extends \Hoa\Console\Dispatcher\Kit {

    /**
     * Options description.
     *
     * @var \Bin\Command\Test\Remove array
     */
    protected $options = array(
        array('revision',   \Hoa\Console\GetOption::REQUIRED_ARGUMENT, 'r'),
        array('no-verbose', \Hoa\Console\GetOption::NO_ARGUMENT,       'V'),
        array('help',       \Hoa\Console\GetOption::NO_ARGUMENT,       'h'),
        array('help',       \Hoa\Console\GetOption::NO_ARGUMENT,       '?')
    );



    /**
     * The entry method.
     *
     * @access  public
     * @return  int
     */
    public function main ( ) {

        $repository = null;
        $test       = new \Hoa\Test();
        $repos      = $test->getParameters()->getFormattedParameter('repository');
        $finder     = new \Hoa\File\Finder(
            $repos,
            \Hoa\File\Finder::LIST_DIRECTORY,
            \Hoa\File\Finder::SORT_MTIME |
            \Hoa\File\Finder::SORT_REVERSE
        );
        $revision   = basename($finder->getIterator()->current());
        $rev        = false;
        $verbose    = true;

        while(false !== $c = $this->getOption($v)) switch($c) {

            case 'r':
                $handle   = $this->parser->parseSpecialValue(
                    $v,
                    array('HEAD' => $revision)
                );
                $revision = $handle[0];
                $rev      = true;
              break;

            case 'V':
                $verbose = false;
              break;

            case 'h':
            case '?':
                return $this->usage();
              break;
        }

        if(false === $rev && true === $verbose) {

            cout('No revision was given; assuming ' .
                 $this->stylize('HEAD', 'info') . ', i.e ' .
                 $this->stylize($revision, 'info') . '.');
            cout();
        }

        $repository = $repos . $revision;

        if(!is_dir($repository))
            throw new \Hoa\Console\Exception(
                'Repository %s does not exist.', 0, $repository);

        if(false === $finder->getIterator()->current()) {

            if(true === $verbose)
                cout('Repository ' . $this->stylize($repos, 'info') .
                     ' is empty.');

            return;
        }

        cout($this->stylize('Important', 'attention'));
        $sure = cin(
            'Are you sure to delete ' . $this->stylize($repository, 'info') . '?',
            \Hoa\Console\Io::TYPE_YES_NO
        );

        if(false === $sure && true === $verbose) {

            cout('Removing abord.');

            return;
        }

        if(true === $verbose) {

            cout();
            cout('Removing…:');
        }

        foreach($finder as $i => $file)
            if($revision === $file->getFilename()) {

                $status = $file->define()->delete();

                if(true === $verbose)
                    $this->status($file->getFilename(), $status);
            }

        return;
    }

    /**
     * The command usage.
     *
     * @access  public
     * @return  int
     */
    public function usage ( ) {

        cout('Usage   : test:run <options>');
        cout('Options :');
        cout($this->makeUsageOptionsList(array(
            'r'    => 'Revision of the repository tests:' . "\n" .
                      '    [revision name] for a specified revision;' . "\n" .
                      '    HEAD            for the latest revision.',
            'V'    => 'No-verbose, i.e. be as quiet as possible, just print ' .
                      'essential informations.',
            'help' => 'This help.'
        )));

        return;
    }
}

}
