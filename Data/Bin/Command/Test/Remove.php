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

class RemoveCommand extends Hoa_Console_Command_Abstract {

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
        array('revision', parent::REQUIRED_ARGUMENT, 'r'),
        array('help',     parent::NO_ARGUMENT,       'h'),
        array('help',     parent::NO_ARGUMENT,       '?')
    );



    /**
     * The entry method.
     *
     * @access  public
     * @return  int
     */
    public function main ( ) {

        $repository = null;
        $path       = 'hoa://Data/Etc/Configuration/.Cache/HoaTest.php';

        if(!file_exists($path))
            throw new Hoa_Console_Command_Exception(
                'Configuration cache file %s does not exists.', 0, $path);

        $configurations = require $path;
        $repos          = Hoa_Core_Parameter::zFormat(
            $configurations['parameters']['repository'],
            $configurations['keywords'],
            $configurations['parameters']
        );
        $finder         = new Hoa_File_Finder(
            $repos,
            Hoa_File_Finder::LIST_DIRECTORY,
            Hoa_File_Finder::SORT_MTIME |
            Hoa_File_Finder::SORT_REVERSE
        );
        $revision       = basename($finder->getIterator()->current());

        while(false !== $c = parent::getOption($v)) {

            switch($c) {

                case 'r':
                    $handle   = parent::parseSpecialValue(
                        $v,
                        array('HEAD' => $revision)
                    );
                    $revision = $handle[0];
                  break;

                case 'h':
                case '?':
                    return $this->usage();
                  break;
            }
        }

        $repository = $repos . $revision;

        if(!is_dir($repository)) {

            cout('Revision ' . $repository . ' does not exist.');

            return HC_SUCCESS;
        }

        if(false === $finder->getIterator()->current()) {

            cout('Repository ' . parent::stylize($repos, 'info') . ' is empty.');

            return HC_SUCCESS;
        }

        cout(parent::stylize('Important', 'attention'));
        $sure = cin(
            'Are you sure to delete ' . $repository . '?',
            Hoa_Console_Core_Io::TYPE_YES_NO
        );

        if(false === $sure) {

            cout('Removing abord.');

            return HC_SUCCESS;
        }

        foreach($finder as $i => $file)
            if($revision === $file->getFilename())
                cout(parent::status(
                    'Removing ' . $file->getFilename(),
                    $file->define()->delete()
                ));

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
            'help' => 'This help.'
        )));

        return HC_SUCCESS;
    }
}
