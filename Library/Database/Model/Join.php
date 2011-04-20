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
 * @category    Framework
 * @package     Hoa_Database
 * @subpackage  Hoa_Database_Model_Join
 *
 */

/**
 * Hoa_Database
 */
import('Database.~');

/**
 * Hoa_Database_Model_Exception
 */
import('Database.Model.Exception');

/**
 * Hoa_Database_Model_Table
 */
import('Database.Model.Table');

/**
 * Class Hoa_Database_Model_Join.
 *
 * (Alpha class, not used now).
 *
 * @author      Ivan Enderlin <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright © 2007-2011 Ivan Enderlin.
 * @license     New BSD License
 * @since       PHP 5
 * @version     0.1a
 * @package     Hoa_Database
 * @subpackage  Hoa_Database_Model_Join
 */

class Hoa_Database_Model_Join extends Hoa_Database_Model_Table {

    const NATURAL =  0;
    const INNER   =  1;
    const RIGHT   =  2;
    const LEFT    =  4;
    const FULL    =  8;
    const CROSS   = 16;
    const UNION   = 32;

    protected $_type  = null;
    protected $_left  = null;
    protected $_right = null;

    public function __construct ( Hoa_Database_Model_Field $left,
                                  Hoa_Database_Model_Field $right,
                                  $type ) {

        $this->_left  = $left;
        $this->_right = $right;

        $this->setBaseName($left->getTable()->getBaseName());
        $this->setName($left->getTable()->getName() . $right->getTable()->getName());
        $this->setConstraints();

        $fields = array();

        foreach($left->getTable() as $foo => $field)
            $fields[$field->getName()] = clone $field;

        foreach($right->getTable() as $foo => $field)
            $fields[$field->getName()] = clone $field;

        foreach($fields as $name => $field) {

            $field->setTable($this);
            $this->{$field->getName()} = $field;
            $this->addField($name, $field);
        }

        $this->setType($type);
    }

    protected function setType ( $type ) {

        $old         = $this->_type;
        $this->_type = $type;

        return $old;
    }

    public function getType ( ) {

        return $this->_type;
    }

    public function getJoinString ( ) {

        $ln = $this->_left->getTable()->getNameWithAs();
        $rn = $this->_right->getTable()->getNameWithAs();

        switch($this->getType()) {

            case self::INNER:
                return sprintf(
                           '(%s INNER JOIN %s ON %s = %s) AS %s',
                           $ln,
                           $rn,
                           $this->_left->getIdentifier(),
                           $this->_right->getIdentifier(),
                           $this->getName()
                       );
        }
    }
}
