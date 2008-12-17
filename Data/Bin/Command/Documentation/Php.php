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
 * Hoa_Console_System
 */
import('Console.System.~');

/**
 * Class PhpCommand.
 *
 * Search in different PHP reference or API manuals.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2008 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 */

class PhpCommand extends Hoa_Console_Command_Abstract {

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
    protected $programName = 'Php';

    /**
     * Options description.
     *
     * @var VersionCommand array
     */
    protected $options     = array(
        array('function',      parent::NO_ARGUMENT, 'f'),
        array('class',         parent::NO_ARGUMENT, 'c'),
        array('extension',     parent::NO_ARGUMENT, 'e'),
        array('configuration', parent::NO_ARGUMENT, 'i'),
        array('documentation', parent::NO_ARGUMENT, 'd'),
        array('help',          parent::NO_ARGUMENT, 'h'),
        array('help',          parent::NO_ARGUMENT, '?')
    );



    /**
     * The entry method.
     *
     * @access  public
     * @return  int
     */
    public function main ( ) {

        $path = HOA_DATA_CONFIGURATION_CACHE . DS . 'Console.php';

        if(!file_exists($path))
            throw new Hoa_Console_Command_Exception(
                'The cache “Console” is not found in %s. Must generate it.',
                0, HOA_DATA_CONFIGURATION_CACHE);

        parent::listInputs($name);

        if(null === $name)
            return $this->usage();

        $cache   = require HOA_DATA_CONFIGURATION_CACHE . DS . 'Console.php';
        $php     = $cache['command']['php'];
        $browser = $cache['command']['browser'];
        $out     = null;

        while(false !== $c = parent::getOption($v)) {

            switch($c) {

                case 'f':
                    $out = Hoa_Console_System::execute($php . ' --rf ' . $name);
                  break;

                case 'c':
                    $out = Hoa_Console_System::execute($php . ' --rc ' . $name);
                  break;

                case 'e':
                    $out = Hoa_Console_System::execute($php . ' --re ' . $name);
                  break;

                case 'i':
                    $out = Hoa_Console_System::execute($php . ' --ri ' . $name);
                  break;

                case 'h':
                case '?':
                    return $this->usage();
                  break;

                case 'd':
                default:
                    $out = Hoa_Console_System::execute($browser . ' http://php.net/' . $name);
                    empty($out)
                    and
                    $out = 'Open ' .
                           parent::stylize(
                               'http://php.net/' . $name,
                               'info'
                           ) . '.';
            }
        }

        cout($out);

        return HC_SUCCESS;
    }

    /**
     * The command usage.
     *
     * @access  public
     * @return  int
     */
    public function usage ( ) {

        cout('Usage   : documentation:php [-f] [-c] [-e] [-i] [-d] <name>');
        cout('Options :');
        cout(parent::makeUsageOptionsList(array(
            'f'    => 'Show information about function <name>.',
            'c'    => 'Show information about class <name>.',
            'e'    => 'Show information about extension <name>.',
            'i'    => 'Show configuration about extension <name>.',
            'd'    => 'Open the online documentation on the <name> page.',
            'help' => 'This help.'
        )));

        return HC_SUCCESS;
    }
}
