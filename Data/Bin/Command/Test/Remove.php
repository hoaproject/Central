<?php

/**
 * Hoa Framework
 *
 *
 * @license
 *
 * GNU General Public License
 *
 * This file is part of Hoa Open Accessibility.
 * Copyright (c) 2007, 2010 Ivan ENDERLIN. All rights reserved.
 *
 * HOA Open Accessibility is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * HOA Open Accessibility is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with HOA Open Accessibility; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 *
 *
 * @category    Data
 *
 */

/**
 * Hoa_Test
 */
import('Test.~');

/**
 * Hoa_File_Finder
 */
import('File.Finder');

/**
 * Class RemoveCommand.
 *
 * .
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2010 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 */

class RemoveCommand extends Hoa_Console_Command_Generic {

    /**
     * Author name.
     *
     * @var RemoveCommand string
     */
    protected $author      = 'Ivan Enderlin';

    /**
     * Program name.
     *
     * @var RemoveCommand string
     */
    protected $programName = 'Remove';

    /**
     * Options description.
     *
     * @var RemoveCommand array
     */
    protected $options     = array(
        array('revision',   parent::REQUIRED_ARGUMENT, 'r'),
        array('no-verbose', parent::NO_ARGUMENT,       'V'),
        array('help',       parent::NO_ARGUMENT,       'h'),
        array('help',       parent::NO_ARGUMENT,       '?')
    );



    /**
     * The entry method.
     *
     * @access  public
     * @return  int
     */
    public function main ( ) {

        $repository = null;
        $test       = new Hoa_Test();
        $repos      = $test->getFormattedParameter('repository');
        $finder     = new Hoa_File_Finder(
            $repos,
            Hoa_File_Finder::LIST_DIRECTORY,
            Hoa_File_Finder::SORT_MTIME |
            Hoa_File_Finder::SORT_REVERSE
        );
        $revision   = basename($finder->getIterator()->current());
        $rev        = false;
        $verbose    = true;

        while(false !== $c = parent::getOption($v)) {

            switch($c) {

                case 'r':
                    $handle   = parent::parseSpecialValue(
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
        }

        if(false === $rev && true === $verbose) {

            cout('No revision was given; assuming ' .
                 parent::stylize('HEAD', 'info') . ', i.e ' .
                 parent::stylize($revision, 'info') . '.');
            cout();
        }

        $repository = $repos . $revision;

        if(!is_dir($repository))
            throw new Hoa_Console_Command_Exception(
                'Repository %s does not exist.', 0, $repository);

        if(false === $finder->getIterator()->current()) {

            if(true === $verbose)
                cout('Repository ' . parent::stylize($repos, 'info') .
                     ' is empty.');

            return HC_SUCCESS;
        }

        cout(parent::stylize('Important', 'attention'));
        $sure = cin(
            'Are you sure to delete ' . parent::stylize($repository, 'info') . '?',
            Hoa_Console_Core_Io::TYPE_YES_NO
        );

        if(false === $sure && true === $verbose) {

            cout('Removing abord.');

            return HC_SUCCESS;
        }

        if(true === $verbose) {

            cout();
            cout('Removing…:');
        }

        foreach($finder as $i => $file)
            if($revision === $file->getFilename()) {

                $status = $file->define()->delete();

                if(true === $verbose)
                    parent::status($file->getFilename(), $status);
            }

        return HC_SUCCESS;
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
        cout(parent::makeUsageOptionsList(array(
            'r'    => 'Revision of the repository tests:' . "\n" .
                      '    [revision name] for a specified revision;' . "\n" .
                      '    HEAD            for the latest revision.',
            'V'    => 'No-verbose, i.e. be as quiet as possible, just print ' .
                      'essential informations.',
            'help' => 'This help.'
        )));

        return HC_SUCCESS;
    }
}
