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
 * Hoa_File
 */
import('File.~');

/**
 * Hoa_File_Dir
 */
import('File.Dir');

/**
 * Hoa_Controller_Router_Pattern
 */
import('Controller.Router.Pattern');

/**
 * Class StartCommand.
 *
 * Start the application, i.e. create the MVC structure and the bootstrap.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2008 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 */

class StartCommand extends Hoa_Console_Command_Abstract {

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
    protected $programName = 'Start';

    /**
     * Options description.
     *
     * @var VersionCommand array
     */
    protected $options     = array(
        array('with-bootstrap', parent::NO_ARGUMENT, 'b'),
        array('with-layout',    parent::NO_ARGUMENT, 'l'),
        array('help',           parent::NO_ARGUMENT, 'h'),
        array('help',           parent::NO_ARGUMENT, '?')
    );



    /**
     * The entry method.
     *
     * @access  public
     * @return  int
     */
    public function main ( ) {

        $withBootstrap = false;
        $withLayout    = false;

        while(false !== $c = parent::getOption($v)) {

            switch($c) {

                case 'b':
                    $withBootstrap = true;
                  break;

                case 'l':
                    $withLayout    = true;
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

        $options    = require HOA_DATA_CONFIGURATION_CACHE . DS . 'Controller.php';
        $router     = new Hoa_Controller_Router_Pattern();

        $controller = HOA_BASE . DS .
                      $options['route']['directory'];

        $viewTheme  = HOA_BASE . DS .
                      $router->transform($options['view']['directory'], $options['view']['theme']);

        $viewHelper = HOA_BASE . DS .
                      $options['view']['helper']['directory'];

        $viewLayout = $viewTheme . DS .
                      $router->transform($options['pattern']['view']['layout'], $options['view']['layout']);

        $model      = HOA_BASE . DS .
                      $router->transform($options['model']['directory'], null);

        $bootstrap  = HOA_BASE . DS . 'index.php';

        if(file_exists($controller))
            throw new Hoa_Console_Command_Exception(
                'Cannot create the application, because it is already exist.', 1);

        cout(parent::stylize('Directories', 'info'));

        parent::status(
            'Create the controller directory.',
            Hoa_File_Dir::create($controller)
        );

        parent::status(
            'Create the model directory.',
            Hoa_File_Dir::create($model)
        );

        parent::status(
            'Create the view theme directory.',
            Hoa_File_Dir::create($viewTheme)
        );

        parent::status(
            'Create the view helper directory.',
            Hoa_File_Dir::create($viewHelper)
        );

        cout(parent::stylize('Files', 'info'));

        parent::status(
            'Create the default view layout file.',
            false !== Hoa_File::write(
                $viewLayout,
                true === $withLayout
                    ? Hoa_File::readAll(HOA_DATA_TEMPLATE . DS . 'ViewLayout.tpl')
                    : Hoa_File::readAll(HOA_DATA_TEMPLATE . DS . 'ViewLayoutSimple.tpl')
            )
        );

        parent::status(
            'Create the bootstrap file.',
            false !== Hoa_File::write(
                $bootstrap,
                true === $withBootstrap
                    ? Hoa_File::readAll(HOA_DATA_TEMPLATE . DS . 'Bootstrap.tpl')
                    : Hoa_File::readAll(HOA_DATA_TEMPLATE . DS . 'BootstrapSimple.tpl')
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

        cout('Usage   : application:start [-b] [-l]');
        cout('Options :');
        cout(parent::makeUsageOptionsList(array(
            'b'    => 'Write the template when creating the bootstrap file.',
            'l'    => 'Write the template when creating the default view layout file.',
            'help' => 'This help.'
        )));

        return HC_SUCCESS;
    }
}
