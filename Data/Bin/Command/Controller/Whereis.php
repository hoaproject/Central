<?php

/**
 * Hoa
 *
 *
 * @license
 *
 * New BSD License
 *
 * Copyright © 2007-2011, Ivan Enderlin. All rights reserved.
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
 * @copyright   Copyright © 2007-2011 Ivan ENDERLIN.
 * @license     New BSD License
 * @since       PHP 5
 * @version     0.1
 */

class WhereisCommand extends \Hoa\Console\Command\Generic {

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
            throw new \Hoa\Console\Command\Exception(
                'Configuration cache file %s does not exist.', 0, $path);

        $configurations = require $path;

        if(   !is_array($configurations)
           || !isset($configurations['keywords'])
           || !isset($configurations['parameters']))
            throw new \Hoa\Console\Command\Exception(
                'Configuration cache files %s appears corrupted.', 1, $path);


        if(null === $primary) {

            $configurations['keywords']['controller'] = $controllerName;
            $configurations['keywords']['action']     = 'index';
        }
        else {

            $configurations['keywords']['controller'] = $primary;
            $configurations['keywords']['action']     = $controllerName;
        }

        $class     = \Hoa\Core\Parameter::zFormat(
            $configurations['parameters']['controller.class'],
            $configurations['keywords'],
            $configurations['parameters']
        );

        if(null === $primary) {

            $directory = \Hoa\Core\Parameter::zFormat(
                $configurations['parameters']['controller.directory'],
                $configurations['keywords'],
                $configurations['parameters']
            );
            $file      = \Hoa\Core\Parameter::zFormat(
                $configurations['parameters']['controller.file'],
                $configurations['keywords'],
                $configurations['parameters']
            );
        }
        else {

            $directory = \Hoa\Core\Parameter::zFormat(
                $configurations['parameters']['action.directory'],
                $configurations['keywords'],
                $configurations['parameters']
            );
            $file      = \Hoa\Core\Parameter::zFormat(
                $configurations['parameters']['action.file'],
                $configurations['keywords'],
                $configurations['parameters']
            );
        }

        $model = \Hoa\Core\Parameter::zFormat(
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
