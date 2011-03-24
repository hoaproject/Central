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
 *
 *
 * @category    Framework
 * @package     Hoa_Database
 * @subpackage  Hoa_Database_Constraint_Table
 *
 */

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
 * @copyright   Copyright (c) 2007-2011 Ivan ENDERLIN.
 * @license     New BSD License
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
