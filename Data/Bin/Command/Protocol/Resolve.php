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
 * Class ResolveCommand.
 *
 * This command resolves some hoa:// paths.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2009 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 */

class ResolveCommand extends Hoa_Console_Command_Abstract {

    /**
     * Author name.
     *
     * @var ResolveCommand string
     */
    protected $author      = 'Ivan Enderlin';

    /**
     * Program name.
     *
     * @var ResolveCommand string
     */
    protected $programName = 'Resolve';

    /**
     * Options description.
     *
     * @var ResolveCommand array
     */
    protected $options     = array(
        array('no-verbose', parent::NO_ARGUMENT, 'V'),
        array('help',       parent::NO_ARGUMENT, 'h'),
        array('help',       parent::NO_ARGUMENT, '?')
    );



    /**
     * The entry method.
     *
     * @access  public
     * @return  int
     */
    public function main ( ) {

        $verbose = true;

        while(false !== $c = parent::getOption($v)) {

            switch($c) {

                case 'V':
                    $verbose = false;
                  break;

                case 'h':
                case '?':
                    return $this->usage();
                  break;
            }

            parent::listInputs($path);

            if(null === $path)
                return $this->usage();

            if(true === $verbose)
                cout(parent::stylize($path, 'info') . ' is equivalent to: ');

            cout(Hoa_Framework::getProtocol()->resolve($path));
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

        cout('Usage   : protocol:resolve <options> path');
        cout('Options :');
        cout(parent::makeUsageOptionsList(array(
            'V'    => 'No-verbose, i.e. be as quiet as possible, just print ' .
                      'essential informations.',
            'help' => 'This help.'
        )));

        return HC_SUCCESS;
    }
}
