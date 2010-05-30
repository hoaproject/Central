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
 * @subpackage  Hoa_Database_QueryBuilder_Dml_Select
 *
 */

/**
 * Hoa_Framework
 */
require_once 'Framework.php';

/**
 * Hoa_Database_QueryBuilder_Dml_Exception
 */
import('Database.QueryBuilder.Dml.Exception');

/**
 * Hoa_Database_QueryBuilder_Interface
 */
import('Database.QueryBuilder.Interface');

/**
 * Class Hoa_Database_QueryBuilder_Dml_Select.
 *
 * Build SELECT query.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2010 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Database
 * @subpackage  Hoa_Database_QueryBuilder_Dml_Select
 */

class Hoa_Database_QueryBuilder_Dml_Select implements Hoa_Database_QueryBuilder_Interface {

    /**
     * Select all fields. Not used here, but in the
     * Hoa_Database_QueryBuilder_Table.
     *
     * @const int
     */
    const ALL = 1;

    /**
     * The built query.
     *
     * @var Hoa_Database_QueryBuilder_Dml_Select string
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
     * @param   array                     $fields    Fields to select. Array of
     *                                               Hoa_Database_Model_Field
     *                                               instance.
     * @return  void
     */
    public function __construct ( Hoa_Database_Model_Table $table,
                                  Array                    $fields = array() ) {

        $this->setTable($table);
        $this->builtQuery($fields);
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
     * @param   array      $fields    Fields to select. Array of
     *                                Hoa_Database_Model_Field instance.
     * @return  string
     */
    protected function builtQuery ( Array $fields = array() ) {

        $query = array(
            'select'  => $fields,
            'from'    => array(),
            'where'   => array(),
            'groupBy' => array(),
            'having'  => array(),
            'orderBy' => array(),
        );

        // SELECT.
        if(empty($fields))
            foreach($this->getTable() as $foo => $field)
                $query['select'][] = $field->getName();

        // FROM.
        $query['from'][] = $this->getTable();

        foreach($this->getTable()->getLinkedTables() as $foo => $table)
            $query['from'][] = $table;

        // WHERE, GROUP BY, HAVNING and ORDER BY.
        $fields = $this->getTable()->getAllFields();
        $where  = array();
        $having = array();

        foreach($fields as $foo => $field) {

            $tmp = trim($field->getWhereString());
            !empty($tmp) and $where[]            = $field;

            $tmp = trim($field->getHavingString());
            !empty($tmp) and $having[]           = $field;

            $tmp = trim($field->getOrderByString());
            !empty($tmp) and $query['orderBy'][] = $tmp;

            $tmp = trim($field->getGroupByString());
            !empty($tmp) and $query['groupBy'][] = $tmp;
        }

        $i   = 0;
        $max = count($where) - 1;
        foreach($where as $foo => $field) {

            if($i++ != $max)
                $field->getCriterion()->_and();

            $query['where'][] = trim($field->getWhereString());
        }

        $i   = 0;
        $max = count($having) - 1;
        foreach($having as $foo => $field) {

            if($i++ != $max)
                $field->getCriterion()->_and();

            $query['having'][] = trim($field->getHavingString());
        }

        $query['where']   = array_unique($query['where']);
        $query['having']  = array_unique($query['having']);
        $query['orderBy'] = array_unique($query['orderBy']);
        $query['groupBy'] = array_unique($query['groupBy']);


        // Here we go …
        $type = null;
        if(null !== $this->getTable()->getSelectType())
            $type = $this->getTable()->getSelectType() . ' ';

        $out  = 'SELECT ' . $type . implode(', ', $query['select']) . "\n";
        $out .= 'FROM   ' . $this->getFromString($query['from']);

        if(!empty($query['where']))
            $out .=
               "\n" .
               'WHERE  ' . str_replace(
                               "\n",
                               "\n" . str_repeat(' ', 7),
                               trim(implode("\n", $query['where']))
                           );

        if(!empty($query['having']))
            $out .=
               "\n" .
               'HAVING ' . str_replace(
                               "\n",
                               "\n" . str_repeat(' ', 7),
                               trim(implode("\n", $query['having']))
                           );

        if(!empty($query['groupBy']))
            $out .=
               "\n" .
               'GROUP  BY ' . implode(', ', $query['groupBy']);

        if(!empty($query['orderBy']))
            $out .=
               "\n" .
               'ORDER  BY ' . implode(', ', $query['orderBy']);


        $this->query = trim($out);

        var_dump(trim($out));

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

    /**
     * Get the FROM clause string.
     *
     * @access  public
     * @param   array   $from    Array of Hoa_Database_Model_Table instance.
     * @return  string
     */
    public function getFromString ( Array $from ) {

        $out = array();

        foreach($from as $foo => $table) {

            /*
            if($table instanceof Hoa_Database_Model_Join)
                $out[] = $table->getJoinString();
            elseif …
            */

            if($table instanceof Hoa_Database_Model_Table)
                $out[] = $table->getNameWithAs();
        }

        return implode(', ', $out);
    }
}
