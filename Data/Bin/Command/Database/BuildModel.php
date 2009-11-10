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
 * Hoa_Database
 */
import('Database.~');

/**
 * Hoa_Database_Schema
 */
import('Database.Schema.~');

/**
 * Class BuildModelCommand.
 *
 * Transform a XML schema to a PHP model for database.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2009 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 */

class BuildModelCommand extends Hoa_Console_Command_Abstract {

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
    protected $programName = 'BuildModel';

    /**
     * Options description.
     *
     * @var VersionCommand array
     */
    protected $options     = array(
        array('help', parent::NO_ARGUMENT, 'h'),
        array('help', parent::NO_ARGUMENT, '?')
    );



    /**
     * The entry method.
     *
     * @access  public
     * @return  int
     */
    public function main ( ) {

        while(false !== $c = parent::getOption($v)) {

            switch($c) {

                case 'h':
                case '?':
                    return $this->usage();
                  break;
            }
        }

        parent::listInputs($schema);

        if(null === $schema)
            return $this->usage();

        if(!file_exists(HOA_DATA_CONFIGURATION_CACHE . DS . 'Database.php'))
            throw new Hoa_Console_Command_Exception(
                'The cache “Database” is not found in %s. Must generate it',
                0, HOA_DATA_CONFIGURATION_CACHE . DS . 'Database.php');

        $options = require HOA_DATA_CONFIGURATION_CACHE . DS . 'Database.php';
        Hoa_Database::getInstance(array(
            'base.directory'   => HOA_BASE . DS . $options['base']['directory'],
            'schema.directory' => HOA_BASE . DS . $options['schema']['directory']
        ));

        try {

            $s = new Hoa_Database_Schema($schema);
            $s->process();
        }
        catch ( Hoa_Database_Schema_Exception $e ) {

            throw new Hoa_Console_Command_Exception(
                $e->getFormattedMessage(), 1);
        }

        cout('Database ' .
             parent::stylize('model', 'info') .
             ' created.');

        return HC_SUCCESS;
    }

    /**
     * The command usage.
     *
     * @access  public
     * @return  int
     */
    public function usage ( ) {

        cout('Usage : database:buildModel <schema filename>');
        cout();

        return HC_SUCCESS;
    }
}
