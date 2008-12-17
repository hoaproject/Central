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
 * Hoa_Controller_Router_Pattern
 */
import('Controller.Router.Pattern');

/**
 * Hoa_File
 */
import('File.~');

/**
 * Class DeleteCommand.
 *
 * Delete a controller, according to controller parameters.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2008 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 */

class DeleteCommand extends Hoa_Console_Command_Abstract {

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
    protected $programName = 'Delete';

    /**
     * Options description.
     *
     * @var VersionCommand array
     */
    protected $options     = array(
        array('primary', parent::REQUIRED_ARGUMENT, 'p'),
        array('help',    parent::NO_ARGUMENT,       'h'),
        array('help',    parent::NO_ARGUMENT,       '?')
    );

    /**
     * The router instance.
     *
     * @var Hoa_Controller_Router_Pattern object
     */
    protected $router      = null;



    /**
     * The entry method.
     *
     * @access  public
     * @return  int
     */
    public function main ( ) {

        $isSecondary = false;
        $primary     = null;

        while(false !== $c = parent::getOption($v)) {

            switch($c) {

                case 'p':
                    $isSecondary = true;
                    $primary     = $v;
                  break;

                case 'h':
                case '?':
                    return $this->usage();
            }
        }

        if(!file_exists(HOA_DATA_CONFIGURATION_CACHE . DS . 'Controller.php'))
            throw new Hoa_Console_Command_Exception(
                'The cache “Controller” is not found in %s. Must generate it.',
                0, HOA_DATA_CONFIGURATION_CACHE);

        $options      = require HOA_DATA_CONFIGURATION_CACHE . DS . 'Controller.php';
        $this->router = new Hoa_Controller_Router_Pattern();

        parent::listInputs($name);

        if(!file_exists(HOA_BASE . DS . $options['route']['directory']))
            throw new Hoa_Console_Command_Exception(
                'Cannot delete the controller, because the application does not ' .
                'exist. Must create it before', 1);

        if(null === $name)
            throw new Hoa_Console_Command_Exception(
                'Must precise the controller name in input (see the usage)', 2);

        $directory       = HOA_BASE . DS . $options['route']['directory'] . DS;
        $primaryName     = true === $isSecondary ? $primary : $name;
        $secondaryName   = true === $isSecondary ? $name    : null;

        $controllerFile  = $this->router->transform(
                               $options['pattern']['controller']['file'],
                               $primaryName
                           );
        $controllerDir   = $this->router->transform(
                               $options['pattern']['controller']['directory'],
                               $primaryName
                           ) . DS;
        $actionFile      = $this->router->transform(
                               $options['pattern']['action']['file'],
                               $secondaryName
                           );
        $md5             = md5(time());


        if(false === $isSecondary) {

            if(file_exists($directory . $controllerFile))
                parent::status(
                    'Delete the primary controller.',
                    Hoa_File::move(
                        $directory . $controllerFile,
                        HOA_DATA_LOSTFOUND . DS . $md5 . DS . $controllerFile,
                        true
                    )
                );

            if(is_dir($directory . $controllerDir))
                parent::status(
                    'Delete secondaries controllers.',
                    Hoa_File::move(
                        $directory . $controllerDir,
                        HOA_DATA_LOSTFOUND . DS . $md5 . DS . $controllerDir,
                        true
                    )
                );
        }
        else

            if(file_exists($directory . $controllerDir . $actionFile))
                parent::status(
                    'Delete the secondary controller.',
                    Hoa_File::move(
                        $directory . $controllerDir . $actionFile,
                        HOA_DATA_LOSTFOUND . DS . $md5 . DS . $controllerDir . $actionFile,
                        true
                    )
                );

		return HC_SUCCESS;
    }

    /**
     * The command usage.
     *
     * @access  public
     * @return  int
     */
    public function usage ( ) {

        cout('Usage   : controller:delete [-p] <controller name>');
        cout('Options :');
        cout(parent::makeUsageOptionsList(array(
            'p'    => 'Specify the primary controller name when searching a ' .
                      'secondary controller.',
            'help' => 'This help.'
        )));

        return HC_SUCCESS;
    }
}
