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
 * @category    Framework
 * @package     Hoa_Database
 * @subpackage  Hoa_Database_Model_Join
 *
 */

/**
 * Hoa_Framework
 */
require_once 'Framework.php';

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
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2008 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
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
