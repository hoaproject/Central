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
 * Copyright (c) 2007, 2011 Ivan ENDERLIN. All rights reserved.
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
 */

namespace {

/**
 * Class VersionCommand.
 *
 * This command allows to know version and revision of the framework.
 *
 * @author     Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright (c) 2007, 2011 Ivan ENDERLIN.
 * @license    http://gnu.org/licenses/gpl.txt GNU GPL
 */

class VersionCommand extends \Hoa\Console\Command\Generic {

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
        array('version',    parent::NO_ARGUMENT, 'v'),
        array('revision',   parent::NO_ARGUMENT, 'r'),
        array('signature',  parent::NO_ARGUMENT, 's'),
        array('no-verbose', parent::NO_ARGUMENT, 'V'),
        array('help',       parent::NO_ARGUMENT, 'h'),
        array('help',       parent::NO_ARGUMENT, '?')
    );



    /**
     * The entry method.
     *
     * @access  public
     * @return  int
     */
    public function main ( ) {

        $version  = HOA_VERSION_MAJOR . '.' . HOA_VERSION_MINOR . '.' .
                    HOA_VERSION_RELEASE . HOA_VERSION_STATUS;
        $revision = HOA_REVISION;
        $verbose  = true;
        $message  = null;
        $info     = null;

        while(false !== $c = parent::getOption($v)) {

            switch($c) {

                case 'r':
                    $info    = $revision;
                    $message = 'Framework revision number: ' .
                               parent::stylize($info, 'info') . '.';
                  break;

                case 'v':
                    $info    = $version;
                    $message = 'Framework version: ' .
                               parent::stylize($info, 'info') . '.';
                  break;

                case 'V':
                    $verbose = false;
                  break;

                case 'h':
                case '?':
                    return $this->usage();
                  break;

                case 's':
                default:
                    $info = $message = 'Hoa Framework ' . $version . ' (' .
                                       $revision . ').' . "\n" .
                                       \Hoa\Core::©();
                  break;
            }
        }

        if(true === $verbose)
            cout($message);
        else
            cout($info);

        return HC_SUCCESS;
    }

    /**
     * The command usage.
     *
     * @access  public
     * @return  int
     */
    public function usage ( ) {

        cout('Usage   : main:version <options>');
        cout('Options :');
        cout(parent::makeUsageOptionsList(array(
            'v'    => 'Get the framework version.',
            'r'    => 'Get the framework revision number.',
            's'    => 'Get the complete framework signature.',
            'V'    => 'No-verbose, i.e. be as quiet as possible, just print ' .
                      'essential informations.',
            'help' => 'This help.'
        )));

        return HC_SUCCESS;
    }
}

}
