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
 * Class WhereisCommand.
 *
 * Find a controller and return his path, according to controller parameters.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2010 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 */

class WhereisCommand extends Hoa_Console_Command_Generic {

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
    protected $programName = 'Whereis';

    /**
     * Options description.
     *
     * @var VersionCommand array
     */
    protected $options     = array(
        array('primary',    parent::REQUIRED_ARGUMENT, 'p'),
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

        $verbose = true;
        $primary = null;

        while(false !== $c = parent::getOption($v)) {

            switch($c) {

                case 'p':
                    $primary = $v;
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

        parent::listInputs($controllerName);

        if(null === $controllerName)
            return $this->usage();

        $path = 'hoa://Data/Etc/Configuration/.Cache/HoaControllerFront.php';

        if(!file_exists($path))
            throw new Hoa_Console_Command_Exception(
                'Configuration cache file %s does not exist.', 0, $path);

        $configurations = require $path;

        if(   !is_array($configurations)
           || !isset($configurations['keywords'])
           || !isset($configurations['parameters']))
            throw new Hoa_Console_Command_Exception(
                'Configuration cache files %s appears corrupted.', 1, $path);


        if(null === $primary) {

            $configurations['keywords']['controller'] = $controllerName;
            $configurations['keywords']['action']     = 'index';
        }
        else {

            $configurations['keywords']['controller'] = $primary;
            $configurations['keywords']['action']     = $controllerName;
        }

        $class     = Hoa_Core_Parameter::zFormat(
            $configurations['parameters']['controller.class'],
            $configurations['keywords'],
            $configurations['parameters']
        );

        if(null === $primary) {

            $directory = Hoa_Core_Parameter::zFormat(
                $configurations['parameters']['controller.directory'],
                $configurations['keywords'],
                $configurations['parameters']
            );
            $file      = Hoa_Core_Parameter::zFormat(
                $configurations['parameters']['controller.file'],
                $configurations['keywords'],
                $configurations['parameters']
            );
        }
        else {

            $directory = Hoa_Core_Parameter::zFormat(
                $configurations['parameters']['action.directory'],
                $configurations['keywords'],
                $configurations['parameters']
            );
            $file      = Hoa_Core_Parameter::zFormat(
                $configurations['parameters']['action.file'],
                $configurations['keywords'],
                $configurations['parameters']
            );
        }

        $model = Hoa_Core_Parameter::zFormat(
            $configurations['parameters']['model.directory'],
            $configurations['keywords'],
            $configurations['parameters']
        );

        if(true === $verbose)
            cout(
                'Controller ' .
                parent::stylize($class, 'info') .
                ' has been found at ' .
                parent::stylize($directory . $file, 'info') . '.'
            );
        else
            cout($directory . $file);

		return HC_SUCCESS;
    }

    /**
     * The command usage.
     *
     * @access  public
     * @return  int
     */
    public function usage ( ) {

        cout('Usage   : controller:whereis <options> controllerName');
        cout('Options :');
        cout(parent::makeUsageOptionsList(array(
            'p'    => 'Specify the primary controller name when searching a ' .
                      'secondary controller.',
            'V'    => 'No-verbose, i.e. be as quiet as possible, just print ' .
                      'essential informations.',
            'help' => 'This help.'
        )));

        return HC_SUCCESS;
    }
}
