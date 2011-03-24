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

/**
 * Class ViewCommand.
 *
 * This command allows to view a package configuration.
 *
 * @author     Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright (c) 2007-2011 Ivan ENDERLIN.
 * @license    New BSD License
 */

class ViewCommand extends \Hoa\Console\Command\Generic {

    /**
     * Author name.
     *
     * @var ViewCommand string
     */
    protected $author      = 'Ivan Enderlin';

    /**
     * Program name.
     *
     * @var ViewCommand string
     */
    protected $programName = 'View';

    /**
     * Options description.
     *
     * @var ViewCommand array
     */
    protected $options     = array(
        array('keyword',    parent::REQUIRED_ARGUMENT, 'k'),
        array('parameter',  parent::REQUIRED_ARGUMENT, 'p'),
        array('keywords'  , parent::NO_ARGUMENT,       'K'),
        array('parameters', parent::NO_ARGUMENT,       'P'),
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

        $keyword    = null;
        $parameter  = null;
        $keywords   = false;
        $parameters = false;

        while(false !== $c = parent::getOption($v)) {

            switch($c) {

                case 'c':
                    $cache = true;
                  break;

                case 'k':
                    $keyword = $v;
                  break;

                case 'p':
                    $parameter = $v;
                  break;

                case 'K':
                    $keywords = true;
                  break;

                case 'P':
                    $parameters = true;
                  break;

                case 'h':
                case '?':
                    return $this->usage();
                  break;
            }
        }

        if(false === $keywords && false === $parameters)
            $keywords = $parameters = true;

        parent::listInputs($package);

        if(null === $package)
            return $this->usage();

        $package   = str_replace('\\', '', $package);
        $path      = 'hoa://Data/Etc/Configuration/.Cache/' . $package . '.php';

        if(!file_exists($path))
            throw new \Hoa\Console\Command\Exception(
                'Cannot find the package configuration at %s.', 0, $path);

        $configurations = require $path;

        if(   !is_array($configurations)
           || !isset($configurations['keywords'])
           || !isset($configurations['parameters']))
            throw new \Hoa\Console\Command\Exception(
                'File %s appears corrupted.', 1, $path);

        if(null !== $keyword) {

            $keywords       = true;
            $parameters     = false;

            if(!isset($configurations['keywords'][$keyword]))
                throw new \Hoa\Console\Command\Exception(
                    'Keyword %s is not found in configuration %s.',
                    1, array($keyword, $path));

            $configurations['keywords'] = array(
                $keyword => $configurations['keywords'][$keyword]
            );
        }

        if(null !== $parameter) {

            $keywords       = false;
            $parameters     = true;

            if(!isset($configurations['parameters'][$parameter]))
                throw new \Hoa\Console\Command\Exception(
                    'Parameter %s is not found in configuration %s.',
                    1, array($keyword, $path));

            $configurations['parameters'] = array(
                $parameter => $configurations['parameters'][$parameter]
            );
        }

        if(true === $keywords) {

            cout(parent::stylize('Keywords', 'h2'));
            cout();

            $handle = array();
            foreach($configurations['keywords'] as $key => $value) {

                if(is_array($value))
                    $value = 'array()';

                $handle[] = array('    ' . parent::stylize($key, 'info'), $value);
            }

            cout(parent::columnize(
                $handle,
                \Hoa\Console\Chrome\Text::ALIGN_LEFT,
                .5,
                0,
                '|=> '
            ));
        }

        if(true === $parameters) {

            cout(parent::stylize('Parameters', 'h2'));
            cout();

            $handle = array();
            foreach($configurations['parameters'] as $key => $value) {

                if(is_array($value))
                    $value = 'array()';

                $handle[] = array('    ' . parent::stylize($key, 'info'), $value);
            }

            cout(parent::columnize(
                $handle,
                \Hoa\Console\Chrome\Text::ALIGN_LEFT,
                .5,
                0,
                '|=> '
            ));
        }

        return HC_SUCCESS;
    }

    /**
     * The command usage.
     *
     * @access  public
     * @return  int
     */
    public function usage ( ) {

        cout('Usage   : configuration:view <options> package');
        cout('Options :');
        cout(parent::makeUsageOptionsList(array(
            'p'    => 'Specify a parameter.',
            'k'    => 'Specify a keyword.',
            'P'    => 'Print all parameters ',
            'K'    => 'Print all keywords.',
            'help' => 'This help.'
        )));

        return HC_SUCCESS;
    }
}

}
