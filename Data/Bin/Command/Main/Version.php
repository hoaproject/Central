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
 * Copyright (c) 2007, 2008 Ivan ENDERLIN. All rights reserved.
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
 * Hoa_Version
 */
import('Version.~');

/**
 * Class VersionCommand.
 *
 * This command allow to know version and revision of the framework.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2008 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 */

class VersionCommand extends Hoa_Console_Command_Abstract {

    /**
     * Author name.
     *
     * @var VersionCommand string
     */
    protected $author      = 'Ivan Enderlin';

    /**
     * Program name.
     *
     * @var VersionCommand string
     */
    protected $programName = 'Version';

    /**
     * Options description.
     *
     * @var VersionCommand array
     */
    protected $options     = array(
        array('version',   parent::NO_ARGUMENT, 'v'),
        array('revision',  parent::NO_ARGUMENT, 'r'),
        array('signature', parent::NO_ARGUMENT, 's'),
        array('help',      parent::NO_ARGUMENT, 'h'),
        array('help',      parent::NO_ARGUMENT, '?')
    );



    /**
     * The entry method.
     *
     * @access  public
     * @return  int
     */
    public function main ( ) {

        while(false !== $c = parent::getOption($v)) {

            switch($c) {

                case 'r':
                    cout(
                        'The framework revision number : ' .
                        Hoa_Version::getRevision()
                    );
                  break;

                case 'v':
                    cout(
                        'The framework version : ' . Hoa_Version::getVersion()
                    );
                  break;

                case 's':
                    cout(Hoa_Version::getSignature());
                  break;

                case 'h':
                case '?':
                    return $this->usage();
                  break;
            }
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

        cout('Usage   : main:version [-v] [-r]');
        cout('Options :');
        cout(parent::makeUsageOptionsList(array(
            'v'    => 'Get the framework version.',
            'r'    => 'Get the framework revision number.',
            's'    => 'Get the complete framework signature.',
            'help' => 'This help.'
        )));

        return HC_SUCCESS;
    }
}
