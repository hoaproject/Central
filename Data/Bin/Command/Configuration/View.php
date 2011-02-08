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
 */

namespace {

/**
 * Class ViewCommand.
 *
 * This command allows to view a package configuration.
 *
 * @author     Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright (c) 2007, 2010 Ivan ENDERLIN.
 * @license    http://gnu.org/licenses/gpl.txt GNU GPL
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

        $package   = str_replace('_', '', $package);
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
