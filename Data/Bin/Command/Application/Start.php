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
 * Hoa_Controller_Front
 */
import('Controller.Front');

/**
 * Hoa_File_Directory
 */
import('File.Directory');

/**
 * Hoa_File_Write
 */
import('File.Write');

/**
 * Class StartCommand.
 *
 * Start the application, i.e. create the MVC structure and the bootstrap.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2010 Ivan ENDERLIN.
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
        array('bootstrap', parent::REQUIRED_ARGUMENT, 'b'),
        array('view',      parent::REQUIRED_ARGUMENT, 'v'),
        array('help',      parent::NO_ARGUMENT,       'h'),
        array('help',      parent::NO_ARGUMENT,       '?')
    );



    /**
     * The entry method.
     *
     * @access  public
     * @return  int
     */
    public function main ( ) {

        $bootstrap = null;
        $view      = null;

        while(false !== $c = parent::getOption($v)) {

            switch($c) {

                case 'b':
                    $bootstrap = $v;
                  break;

                case 'v':
                    $view = $v;
                  break;

                case 'h':
                case '?':
                    return $this->usage();
            }
        }

        parent::listInputs($bootstrap);

        $path = 'hoa://Data/Etc/Configuration/.Cache/HoaControllerFront.php';

        if(!file_exists($path))
            throw new Hoa_Console_Command_Exception(
                'The Controller cache is not found in %s. Must generate it.',
                0, $path);

        $configurations = require $path;

        if(   !is_array($configurations)
           || !isset($configurations['keywords'])
           || !isset($configurations['parameters']))
            throw new Hoa_Console_Command_Exception(
                'Configuration cache filse %s appears corrupted.', 1, $path);

        if(null !== $view)
            $configurations['keywords']['view'] = $view;

        $cd = Hoa_Framework_Parameter::zFormat(
            $configurations['parameters']['controller.directory'],
            $configurations['keywords'],
            $configurations['parameters']
        );
        $md = Hoa_Framework_Parameter::zFormat(
            $configurations['parameters']['model.share.directory'],
            $configurations['keywords'],
            $configurations['parameters']
        );
        $vd = Hoa_Framework_Parameter::zFormat(
            $configurations['parameters']['view.directory'],
            $configurations['keywords'],
            $configurations['parameters']
        );

        parent::status(
            'Create ' .
            parent::stylize('controller', 'info') .
            ' directory at ' .
            parent::stylize($cd, 'info') . '.',
            Hoa_File_Directory::create($cd)
        );
        parent::status(
            'Create ' .
            parent::stylize('model', 'info') .
            ' directory at ' .
            parent::stylize($md, 'info') . '.',
            Hoa_File_Directory::create($md)
        );
        parent::status(
            'Create ' .
            parent::stylize('view', 'info') .
            ' directory at ' .
            parent::stylize($vd, 'info') . '.',
            Hoa_File_Directory::create($vd)
        );

        if(null === $bootstrap)
            return HC_SUCCESS;

        $p = 'hoa://Application/Public/' . $bootstrap . '.php';

        parent::status(
            'Create ' .
            parent::stylize($bootstrap, 'info') .
            ' bootstrap file at ' .
            parent::stylize($p, 'info') . '.',
            Hoa_File_Directory::create(dirname($p))
            &&
            new Hoa_File_Write($p)
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

        cout('Usage   : application:start <options>');
        cout('Options :');
        cout(parent::makeUsageOptionsList(array(
            'b'    => 'Bootstrap name.',
            'v'    => 'View theme name.',
            'help' => 'This help.'
        )));

        return HC_SUCCESS;
    }
}
