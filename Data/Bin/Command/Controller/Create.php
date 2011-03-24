<?php

/**
 * Hoa
 *
 *
 * @license
 *
 * New BSD License
 *
 * Copyright (c) 2007-2011, Ivan Enderlin. All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *     * Redistributions of source code must retain the above copyright
 *       notice, this list of conditions and the following disclaimer.
 *     * Redistributions in binary form must reproduce the above copyright
 *       notice, this list of conditions and the following disclaimer in the
 *       documentation and/or other materials provided with the distribution.
 *     * Neither the name of the Hoa nor the names of its contributors may be
 *       used to endorse or promote products derived from this software without
 *       specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDERS AND CONTRIBUTORS BE
 * LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
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
 * @copyright  Copyright (c) 2007-2011 Ivan ENDERLIN.
 * @license    New BSD License
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
