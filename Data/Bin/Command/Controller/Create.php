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
 * Copyright (c) 2007, 2009 Ivan ENDERLIN. All rights reserved.
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
 * Class CreateCommand.
 *
 * Create a controller according to controller parameters.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2009 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 */

class CreateCommand extends Hoa_Console_Command_Abstract {

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
    protected $programName = 'Create';

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
     * The entry method.
     *
     * @access  public
     * @return  int
     */
    public function main ( ) {

        $primary = null;

        while(false !== $c = parent::getOption($v)) {

            switch($c) {

                case 'p':
                    $primary = $v;
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

        $class = Hoa_Framework_Parameter::zFormat(
            $configurations['parameters']['controller.class'],
            $configurations['keywords'],
            $configurations['parameters']
        );

        if(null === $primary) {

            $directory = Hoa_Framework_Parameter::zFormat(
                $configurations['parameters']['controller.directory'],
                $configurations['keywords'],
                $configurations['parameters']
            );
            $file      = Hoa_Framework_Parameter::zFormat(
                $configurations['parameters']['controller.file'],
                $configurations['keywords'],
                $configurations['parameters']
            );
            $method    = null;
            $extends   = 'Hoa_Controller_Action_Standard';
        }
        else {

            $extends   = $class;
            $directory = Hoa_Framework_Parameter::zFormat(
                $configurations['parameters']['action.directory'],
                $configurations['keywords'],
                $configurations['parameters']
            );
            $file      = Hoa_Framework_Parameter::zFormat(
                $configurations['parameters']['action.file'],
                $configurations['keywords'],
                $configurations['parameters']
            );
            $class     = Hoa_Framework_Parameter::zFormat(
                $configurations['parameters']['action.class'],
                $configurations['keywords'],
                $configurations['parameters']
            );
            $method    = Hoa_Framework_Parameter::zFormat(
                $configurations['parameters']['action.method'],
                $configurations['keywords'],
                $configurations['parameters']
            );
        }

        $model = Hoa_Framework_Parameter::zFormat(
            $configurations['parameters']['model.directory'],
            $configurations['keywords'],
            $configurations['parameters']
        );

        if(file_exists($directory . $file))
            throw new Hoa_Console_Command_Exception(
                'Controller %s already exists at %s.',
                2, array($class, $directory . $file));

        if(!is_dir($directory))
            parent::status(
                'Create ' .
                parent::stylize('controller', 'info') .
                ' directory at ' .
                parent::stylize($directory, 'info') . '.',
                Hoa_File_Directory::create($directory)
            );

        $s = true;
        $f = new Hoa_File_Write($directory . $file);
        $s = $f->writeAll(
           '<?php' . "\n\n" .
           'class ' . $class  . ' extends ' . $extends . ' {' . "\n\n" .
           (null !== $method
               ? '    public function ' . $method . ' ( ) {' . "\n\n" .
                 '    }' . "\n"
               : '') .
           '}'
        );

        parent::status(
            'Create ' .
            parent::stylize('controller', 'info') .
            ' file and class at ' .
            parent::stylize($file, 'info') . '.',
            (bool) $s
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

        cout('Usage   : controller:create <options> controllerName');
        cout('Options :');
        cout(parent::makeUsageOptionsList(array(
            'p'    => 'Specify the primary controller name when creating a ' .
                      'secondary controller.',
            'help' => 'This help.'
        )));

        return HC_SUCCESS;
    }
}
