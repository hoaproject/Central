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
 * Class RootCommand.
 *
 * This command allow to know some roots.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2008 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 */

class RootCommand extends Hoa_Console_Command_Abstract {

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
    protected $programName = 'Root';

    /**
     * Options description.
     *
     * @var VersionCommand array
     */
    protected $options     = array(
        array('framework',   parent::NO_ARGUMENT, 'f'),
        array('data',        parent::NO_ARGUMENT, 'd'),
        array('application', parent::NO_ARGUMENT, 'a'),
        array('check',       parent::NO_ARGUMENT, 'c'),
        array('help',        parent::NO_ARGUMENT, 'h'),
        array('help',        parent::NO_ARGUMENT, '?')
    );



    /**
     * The entry method.
     *
     * @access  public
     * @return  int
     */
    public function main ( ) {

        $framework = Hoa_Framework::getInstance();
        $check     = false;
        $root      = $framework->getFormattedParameter('root.framework');

        while(false !== $c = parent::getOption($v)) {

            switch($c) {

                case 'f':
                    $root = $framework->getFormattedParameter('root.framework');
                  break;

                case 'd':
                    $root = $framework->getFormattedParameter('root.data');
                  break;

                case 'a':
                    $root = $framework->getFormattedParameter('root.application');
                  break;

                case 'c':
                    $check = $v;
                  break;

                case 'h':
                case '?':
                    $this->usage();
                  break;

                case 'm':
                default:
            }
        }

        if(true  === $check)
            if($root ==  getcwd())
                cout('You are at the right place!');
            else
                cout('You are not at the right place :-(.');

        cout($root);

        return HC_SUCCESS;
    }

    /**
     * The command usage.
     *
     * @access  public
     * @return  int
     */
    public function usage ( ) {

        cout('Usage   : main:root [-f] [-d] [-a]');
        cout('Options :');
        cout(parent::makeUsageOptionsList(array(
            'framework'   => 'The framework root.',
            'data'        => 'The data root.',
            'application' => 'The application root.',
            'check'       => 'Check with the current path if it matches.',
            'help'        => 'This help.'
        )));

        return HC_SUCCESS;
    }
}
