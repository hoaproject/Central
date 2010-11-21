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
 * @subpackage  Hoa_Database_Criterion_Abstract
 *
 */

/**
 * Hoa_Database_Criterion_Exception
 */
import('Database.Criterion.Exception');

/**
 * Class Hoa_Database_Criterion_Abstract.
 *
 * Abstract class to manage criterion.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2010 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Database
 * @subpackage  Hoa_Database_Criterion_Abstract
 */

abstract class Hoa_Database_Criterion_Abstract {

    /**
     * The where and the having clauses understand search conditions. We got one
     * array for these two clauses, the index is given by these following
     * constants.
     *
     * @const string
     */
    const MODE_WHERE     = 'where';
    const MODE_HAVING    = 'having';

    /**
     * Criteria are separated by boolean-keyword, here : “and” and “or”.
     *
     * @const string
     */
    const BOOLEAN_AND    = 'AND';
    const BOOLEAN_OR     = 'OR';

    /**
     * Criteria can have subgroups for boolean expressions.
     *
     * @const string
     */
    const SUBGROUP_OPEN  = '(';
    const SUBGROUP_CLOSE = ')';

    /**
     * The field instance.
     *
     * @var Hoa_Database_Model_Field object
     */
    protected $_field           = null;

    /**
     * The search conditions array.
     *
     * @var Hoa_Database_Criterion_Abstract array
     */
    protected $searchCondition  =  array(
        self::MODE_WHERE        => array(),
        self::MODE_HAVING       => array()
    );

    /**
     * The order by clauses array.
     *
     * @var Hoa_Database_Criterion_Abstract string
     */
    protected $orderBy          = null;

    /**
     * The group by clauses array.
     *
     * @var Hoa_Database_Criterion_Abstract string
     */
    protected $groupBy          = null;

    /**
     * The current mode for search conditions array (please, see the MODE_*
     * constants).
     *
     * @var Hoa_Database_Criterion_Abstract string
     */
    protected $mode             = self::MODE_WHERE;

    /**
     * Whether the boolean-keyword criteria seperator is set.
     *
     * @var Hoa_Database_Criterion_Abstract bool
     */
    protected $booleanTermIsSet = true;

    /**
     * Number of sub-groups in criteria, i.e. number of sub-conditions.
     *
     * @var Hoa_Database_Criterion_Abstract int
     */
    protected $subGroupNumber   = 0;

    /**
     * Prepared query values.
     *
     * @var Hoa_Database_Criterion_Abstract array
     */
    protected $preparedValue   = array();



    /**
     * Set the field instance.
     *
     * @access  public
     * @param   Hoa_Database_Model_Field  $field    The field instance.
     * @return  void
     */
    public function __construct ( Hoa_Database_Model_Field $field ) {

        $this->setField($field);
    }

    /**
     * Set the field instance.
     *
     * @access  protected
     * @param   Hoa_Database_Model_Field  $field    The field instance.
     * @return  Hoa_Database_Model_Field
     */
    protected function setField ( Hoa_Database_Model_Field $field ) {

        $old          = $this->_field;
        $this->_field = $field;

        return $old;
    }

    /**
     * Get the field instance.
     *
     * @access  protected
     * @return  Hoa_Database_Model_Field
     */
    protected function getField ( ) {

        return $this->_field;
    }

    /**
     * Add a linked table to this referenced table (master table).
     *
     * @access  protected
     * @param   Hoa_Database_Model_Table  $table    Table to add.
     * @return  void
     */
    protected function addLinkedTable ( Hoa_Database_Model_Table $table ) {

        $this->getField()->getTable()->addLinkedTable($table);
    }

    /**
     * Check if the boolean term is set.
     *
     * @access  public
     * @return  bool
     */
    public function isBoolTermSet ( ) {

        return $this->booleanTermIsSet;
    }

    /**
     * Set the flag self::$booleanTermIsSet to false.
     *
     * @access  protected
     * @return  void
     */
    protected function unsetBooleanTerm ( ) {

        $this->booleanTermIsSet = false;
    }

    /**
     * Set the flag self::$booleanTermIsSet to true.
     *
     * @access  protected
     * @return  void
     */
    protected function setBooleanTerm ( ) {

        $this->booleanTermIsSet = true;
    }

    /**
     * Add a search condition.
     *
     * @access  protected
     * @param   string     $condition    The search condition to add.
     * @param   mixed      $value        If the query should be prepared, this
     *                                   is its value, else the value is set in
     *                                   the $condition (normally) ; e.g. in a
     *                                   sub-query condition.
     * @return  void
     */
    protected function addSearchCondition ( $condition, $value = null ) {

        if(false === $this->isBoolTermSet())
            $this->searchCondition[$this->getMode()][] = self::BOOLEAN_AND;

        $this->unsetBooleanTerm();

        if(null === $condition)
            return;

        if(null === $value) {

            $this->searchCondition[$this->getMode()][] = $this->myVprintf($condition);
            return;
        }

        if(is_string($value))
            $value = array($value);

        $format = array();

        foreach($value as $k => $v) {

            $key = md5($this->getField()->getIdentifier() . $condition . $v);
            $this->preparedValue[$key] = $v;
            $format[]                  = $key;
        }

        $this->searchCondition[$this->getMode()][] = $this->myVprintf($condition, $format);

        return;
    }

    /**
     * Add an order to the field (ASC or DESC clauses).
     *
     * @access  protected
     * @param   string     $order    The order.
     * @return  void
     */
    protected function addOrder ( $order ) {

        $this->orderBy = $this->myVprintf($order);

        return;
    }

    /**
     * Add a group to the GROUP BY fields list.
     *
     * @access  protected
     * @return  void
     */
    protected function addGroup ( $group ) {

        $this->groupBy = $this->myVprintf($group);

        return;
    }

    /**
     * Get the current mode. Please, see the MODE_* constants.
     *
     * @access  public
     * @return  string
     */
    public function getMode ( ) {

        return $this->mode;
    }

    /**
     * Get the number of sub-groups, i.e. the number of sub-conditions.
     *
     * @access  public
     * @return  int
     */
    public function getSubGroupNumber ( ) {

        return $this->subGroupNumber;
    }

    /**
     * Get the WHERE search conditions.
     *
     * @access  public
     * @return  array
     * @throw   Hoa_Database_Criterion_Exception
     */
    public function getWhere ( ) {

        if($this->getSubGroupNumber() > 0)
            throw new Hoa_Database_Criterion_Exception(
                '%d subgroup(s) is/are not closed.',
                0, $this->getSubGroupNumber());

        return $this->searchCondition[self::MODE_WHERE];
    }

    /**
     * Get the HAVING search conditions.
     *
     * @access  public
     * @return  array
     * @throw   Hoa_Database_Criterion_Exception
     */
    public function getHaving ( ) {

        if($this->getSubGroupNumber() > 0)
            throw new Hoa_Database_Criterion_Exception(
                '%d subgroup(s) is/are not closed.',
                0, $this->getSubGroupNumber());

        return $this->searchCondition[self::MODE_HAVING];
    }

    /**
     * Get the order of this field.
     *
     * @access  public
     * @return  string
     */
    public function getOrderBy ( ) {

        return $this->orderBy;
    }

    /**
     * Get the group of this field.
     *
     * @access  public
     * @return  string
     */
    public function getGroupBy ( ) {

        return $this->groupBy;
    }

    /**
     * Get the prepared values.
     *
     * @access  public
     * @return  string
     */
    public function getPreparedValue ( ) {

        return $this->preparedValue;
    }

    /**
     * Transform data with printf format to a valid string.
     *
     * @access  protected
     * @param   string     $data        Data to transform.
     * @param   array      $prepared    Name of a prepared query (:name).
     * @return  string
     */
    protected function myVprintf ( $data, Array $prepared = array() ) {

        if(empty($prepared))
            return sprintf($data, $this->getField()->__toString());

        $prepared = array_map(
            create_function(
                '$name',
                'return \':\' . $name;'
            ),
            $prepared
        );

        array_unshift($prepared, $this->getField()->__toString());

        return vsprintf($data, $prepared);
    }

    /**
     * Reset the object.
     *
     * @access  public
     * @return  void
     */
    public function _reset ( ) {

        $this->searchCondition  = array(
            self::MODE_WHERE    => array(),
            self::MODE_HAVING   => array()
        );
        $this->orderBy          = null;
        $this->groupBy          = null;
        $this->mode             = self::MODE_WHERE;
        $this->booleanTermIsSet = true;
        $this->subGroupNumber   = 0;
        $this->preparedValue    = array();
    }
}
