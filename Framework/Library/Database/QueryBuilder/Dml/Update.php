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
 * @subpackage  Hoa_Database_QueryBuilder_Dml_Update
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
 * Class Hoa_Database_QueryBuilder_Dml_Update.
 *
 * Build UPDATE query.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2011 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Database
 * @subpackage  Hoa_Database_QueryBuilder_Dml_Update
 */

class Hoa_Database_QueryBuilder_Dml_Update implements Hoa_Database_QueryBuilder_Interface {

    /**
     * The built query.
     *
     * @var Hoa_Database_QueryBuilder_Dml_Update string
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
     *                                               and values to update.
     * @return  void
     */
    public function __construct ( Hoa_Database_Model_Table $table,
                                  Array                    $values = array() ) {

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
     *                                update.
     * @return  string
     */
    protected function builtQuery ( Array $values = array() ) {

        $query = array(
            'set'   => array(),
            'where' => array(),
        );

        if(empty($values)) {

            $this->query = null;

            return $this->getQuery();
        }

        $lastWhere = null;

        foreach($this->getTable() as $foo => $field) {

            if(true === array_key_exists($field->getName(), $values)) {

                $query['set'][] = $field->getName() .
                                  ' = ' .
                                  ':' . md5($field->getName() . $values[$field->getName()]);

                $this->getTable()->addPreparedValue(
                    md5($field->getName() . $values[$field->getName()]),
                    $values[$field->getName()]
                );
            }

            $tmp = trim($field->getWhereString());

            if(!empty($tmp)) {

                if(null !== $lastWhere) {

                    $lastWhere->getCriterion()->_and();
                    $query['where'][] = trim($lastWhere->getWhereString());
                }

                $lastWhere = $field;
            }
        }

        null !== $lastWhere and $query['where'][] = trim($lastWhere->getWhereString());

        $query['set']   = implode(",\n", $query['set']);
        $query['where'] = implode("\n",  $query['where']);

        $out = 'UPDATE ' . $this->getTable()->getName() . "\n" .
               'SET    ' . "\n" . str_repeat(' ', 7) .
               str_replace(
                   "\n",
                   "\n" . str_repeat(' ', 7),
                   trim($query['set'])
               );

        if(!empty($query['where']))
            $out .=
               "\n" .
               'WHERE  ' . str_replace(
                               "\n",
                               "\n" . str_repeat(' ', 7),
                               trim($query['where'])
                           );

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
