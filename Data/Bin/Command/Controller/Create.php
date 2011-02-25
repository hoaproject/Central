<?php

/**
 * Hoa
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

from('Hoa')

/**
 * \Hoa\File\Write
 */
-> import('File.Write')

/**
 * \Hoa\File\Directory
 */
-> import('File.Directory');

/**
 * Class CreateCommand.
 *
 *
 *
 * @author     Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright (c) 2007, 2011 Ivan ENDERLIN.
 * @license    http://gnu.org/licenses/gpl.txt GNU GPL
 */

class CreateCommand extends \Hoa\Console\Command\Generic {

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
        array('asynchronous', parent::NO_ARGUMENT, 'a'),
        array('help',         parent::NO_ARGUMENT, 'h'),
        array('help',         parent::NO_ARGUMENT, '?')
    );



    /**
     * The entry method.
     *
     * @access  public
     * @return  int
     */
    public function main ( ) {

        $as = 'synchronous';

        while(false !== $c = parent::getOption($v)) {

            switch($c) {

                case 'a':
                    $as = 'asynchronous';
                  break;

                case 'h':
                case '?':
                    return $this->usage();
            }
        }

        parent::listInputs($controller);

        if(null === $controller)
            return $this->usage();

        cout('Creating ' . parent::stylize($controller, 'info') . ' controller.');

        $configuration = require 'hoa://Data/Etc/Configuration/.Cache/HoaControllerDispatcher.php';
        $keywords      = $configuration['keywords'];
        $parameters    = $configuration['parameters'];

        $keywords['controller'] = $controller;

        $file       = \Hoa\Core\Parameter::zFormat(
            $parameters[$as . '.file'],
            $keywords,
            $parameters
        );
        $controller = \Hoa\Core\Parameter::zFormat(
            $parameters[$as . '.controller'],
            $keywords,
            $parameters
        );

        parent::status(
            'Create ' . parent::stylize(dirname($file), 'info') . '.',
            \Hoa\File\Directory::create(dirname($file))
        );

        $controllerStatus = true;

        if(false === file_exists($file)) {

            $handle = new \Hoa\File\Write($file);
            $controllerStatus = false !== $handle->writeAll(
                '<?php' . "\n\n" .
                'class ' . $controller . ' {' . "\n\n" .
                '}'
            );
        }

        parent::status(
            'Create ' . parent::stylize($file, 'info') . '.',
            $controllerStatus
        );

        parent::status(
            'Create ' . parent::stylize($controller, 'info') . '.',
            $controllerStatus
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

        cout('Usage   : controller:create <options> controller-name');
        cout('Options :');
        cout(parent::makeUsageOptionsList(array(
            'a'    => 'Whether the controller is asynchronous.',
            'help' => 'This help.'
        )));

        return HC_SUCCESS;
    }
}

}
