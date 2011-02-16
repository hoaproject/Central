<?php

/**
 * Hoa
 *
 *
 * @license
 *
 * GNU General Public License
 *
 * This file is part of Hoa Open Accessibility.
 * Copyright (c) 2007, 2011 Ivan ENDERLIN. All rights reserved.
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
 * @subpackage  Hoa_Database_QueryBuilder_Dml_Insert
 *
 */

/**
 * Hoa_Database_QueryBuilder_Dml_Exception
 */
import('Database.QueryBuilder.Dml.Exception');

/**
 * Hoa_Database_QueryBuilder_Interface
 */
import('Database.QueryBuilder.Interface');

/**
 * Class Hoa_Database_QueryBuilder_Dml_Insert.
 *
 * Build INSERT query.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2011 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Database
 * @subpackage  Hoa_Database_QueryBuilder_Dml_Insert
 */

class Hoa_Database_QueryBuilder_Dml_Insert implements Hoa_Database_QueryBuilder_Interface {

    /**
     * The built query.
     *
     * @var Hoa_Database_QueryBuilder_Dml_Insert string
     */
    protected $query = null;

    /**
     * The table.
     *
     * @var Hoa_Database_Model_Table object
     */
    protected $_table = null;



    /**
     * Set the table and built the query.
     *
     * @access  public
     * @param   Hoa_Database_Model_Table  $table     The table.
     * @param   array                     $values    Array that represents keys
     *                                               and values to insert.
     * @return  void
     */
    public function __construct ( Hoa_Database_Model_Table $table,
                                  Array                    $values ) {

        $this->setTable($table);
        $this->builtQuery($values);
    }

    /**
     * Set the table.
     *
     * @access  protected
     * @param   Hoa_Database_Model_Table  $table    The table.
     * @return  Hoa_Database_Model_Table
     */
    protected function setTable ( Hoa_Database_Model_Table $table ) {

        $old          = $this->_table;
        $this->_table = $table;

        return $old;
    }

    /**
     * Get the table.
     *
     * @access  public
     * @return  Hoa_Database_Model_Table
     */
    public function getTable ( ) {

        return $this->_table;
    }

    /**
     * Built the query.
     *
     * @access  protected
     * @param   array      $values    Array that represents key and values to
     *                                insert.
     * @return  string
     */
    protected function builtQuery ( Array $values ) {

        $pairs = array();

        foreach($values as $field => $value) {

            $pairs[$this->getTable()->getField($field)->getName()]
                = ':' . md5($this->getTable()->getField($field)->getName() . $value);

            $this->getTable()->addPreparedValue(
                md5($this->getTable()->getField($field)->getName() . $value),
                $value
            );
        }

        $out = 'INSERT INTO ' . $this->getTable()->getName() .
               ' (' . implode(', ', array_keys($pairs)) . ') ' . "\n" .
               '       VALUES (' . implode(', ', array_values($pairs)) . ')' . "\n";

        $this->query = trim($out);

        return $this->getQuery();
    }

    /**
     * Get the built query.
     *
     * @access  public
     * @return  string
     */
    public function getQuery ( ) {

        return $this->query;
    }

    /**
     * Call the self::getQuery() method.
     *
     * @access  public
     * @return  string
     */
    public function __toString ( ) {

        return $this->getQuery();
    }
}
