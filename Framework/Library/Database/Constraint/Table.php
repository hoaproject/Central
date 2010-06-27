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
 * @category    Framework
 * @package     Hoa_Database
 * @subpackage  Hoa_Database_Constraint_Table
 *
 */

/**
 * Hoa_Core
 */
require_once 'Core.php';

/**
 * Hoa_Database_Constraint_Exception
 */
import('Database.Constraint.Exception');

/**
 * Hoa_Database_Constraint_Abstract
 */
import('Database.Constraint.Abstract');

/**
 * Class Hoa_Database_Constraint_Table.
 *
 * Manage table constraints.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2010 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Database
 * @subpackage  Hoa_Database_Constraint_Table
 */

class Hoa_Database_Constraint_Table extends Hoa_Database_Constraint_Abstract {

    /**
     * Constraints.
     *
     * @var Hoa_Database_Constraint_Abstract array
     */
    protected $constraints = array(
        'charSet'     => 'utf8',
        'charCollate' => 'utf8_bin'
    );



    /**
     * Set the table when calling the parent constructor.
     *
     * @access  public
     * @param   Hoa_Database_Model_Table  $table          The table that needs
     *                                                    constraints.
     * @param   array                     $constraints    The table constraints.
     * @return  void
     * @throw   Hoa_Database_Constraint_Exception
     */
    public function __construct ( Hoa_Database_Model_Table $table,
                                  Array                    $constraints = array() ) {

        parent::__construct($table, $constraints);
    }

    /**
     * Get the table char set.
     *
     * @access  public
     * @return  string
     */
    public function getCharSet ( ) {

        return $this->getConstraint('charSet');
    }

    /**
     * Get the table char collate.
     *
     * @access  public
     * @return  string
     */
    public function getCharCollate ( ) {

        return $this->getConstraint('charCollate');
    }

    /**
     * Get primary keys.
     *
     * @accesss  public
     * @return   ArrayObject
     */
    public function getPrimaries ( ) {

        $out = new ArrayObject(
                   array(),
                   ArrayObject::ARRAY_AS_PROPS,
                  'ArrayIterator'
               );

        foreach($this->getObject() as $foo => $field)
            if(true === $field->getConstraint()->isPrimary())
                $out->offsetSet($foo, $field);

        return $out;
    }

    /**
     * Get unique keys.
     *
     * @accesss  public
     * @return   ArrayObject
     */
    public function getUniques ( ) {

        $out = new ArrayObject(
                   array(),
                   ArrayObject::ARRAY_AS_PROPS,
                  'ArrayIterator'
               );

        foreach($this->getObject() as $foo => $field)
            if(true === $field->getConstraint()->isUnique())
                $out->offsetSet($foo, $field);

        return $out;
    }

    /**
     * Get index keys.
     *
     * @accesss  public
     * @return   ArrayObject
     */
    public function getIndexes ( ) {

        $out = new ArrayObject(
                   array(),
                   ArrayObject::ARRAY_AS_PROPS,
                  'ArrayIterator'
               );

        foreach($this->getObject() as $foo => $field)
            if(true === $field->getConstraint()->isIndex())
                $out->offsetSet($foo, $field);

        return $out;
    }

    /**
     * Get foreign keys.
     *
     * @accesss  public
     * @return   ArrayObject
     */
    public function getForeigns ( ) {

        $out = new ArrayObject(
                   array(),
                   ArrayObject::ARRAY_AS_PROPS,
                  'ArrayIterator'
               );

        foreach($this->getObject() as $foo => $field)
            if(true === $field->getConstraint()->isForeign())
                $out->offsetSet($foo, $field);

        return $out;
    }
}
