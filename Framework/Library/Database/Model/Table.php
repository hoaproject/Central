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
 * @category    Framework
 * @package     Hoa_Database
 * @subpackage  Hoa_Database_Model_Table
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
 * Hoa_Database_Model_Field
 */
import('Database.Model.Field');

/**
 * Hoa_Database_Constraint_Table
 */
import('Database.Constraint.Table');

/**
 * Hoa_Database_QueryBuilder_Table
 */
import('Database.QueryBuilder.Table');

/**
 * Hoa_Database_Dal
 */
import('Database.Dal.~');

/**
 * Hoa_Database_Cache_Query
 */
import('Database.Cache.Query');

/**
 * Class Hoa_Database_Model_Table.
 *
 * Class that represents a table, i.e. a collection of fields.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2009 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Database
 * @subpackage  Hoa_Database_Model_Table
 */

class Hoa_Database_Model_Table extends    Hoa_Database_QueryBuilder_Table
                               implements Iterator, Countable {

    /**
     * The base name that contains this table.
     *
     * @var Hoa_Database_Model_Table string
     */
    protected $_base        = null;

    /**
     * The table name.
     *
     * @var Hoa_Database_Model_Table string
     */
    protected $_name        = null;

    /**
     * The previous table name (before rename it with an AS clause).
     *
     * @var Hoa_Database_Model_Table string
     */
    private $_nameWas       = null;

    /**
     * The constraint instance.
     *
     * @var Hoa_Database_Constraint_Field object
     */
    protected $_constraints = array();

    /**
     * The field collection.
     *
     * @var Hoa_Database_Model_Table array
     */
    private $_field         = array();

    /**
     * Last field name was called.
     *
     * @var Hoa_Database_Model_Table string
     */
    private $_lastField     = null;

    /**
     * Collection of linked table (on this master table).
     *
     * @var Hoa_Database_Model_Table array
     */
    private $_linkedTable   = array();

    /**
     * Cache instance. One instance per table if necessary.
     *
     * @var Hoa_Database_Cache_Abstract object
     */
    private $_cache         = null;



    /**
     * Set constraints and the field collection.
     *
     * @access  public
     * @return  void
     * @throw   Hoa_Database_Model_Exception
     */
    public function __construct ( ) {

        if(empty($this->_base))
            throw new Hoa_Database_Model_Exception(
                'A table must have a base name.', 0);

        if(empty($this->_name))
            throw new Hoa_Database_Model_Exception(
                'A table must have a name.', 1);

        $this->setConstraints();
        $this->setFieldCollection();
    }

    /**
     * Set the table constraints.
     *
     * @access  protected
     * @return  Hoa_Database_Constraint_Table
     */
    final protected function setConstraints ( ) {

        $old                = $this->_constraints;
        $this->_constraints = new Hoa_Database_Constraint_Table($this, $this->_constraints);

        return $this->_constraints;
    }

    /**
     * Get the table constraints.
     *
     * @access  public
     * @return  Hoa_Database_Constraint_Table
     */
    final public function getConstraints ( ) {

        return $this->_constraints;
    }

    /**
     * Scan the (child) public properties and add fields.
     *
     * @access  protected
     * @return  void
     * @throw   Hoa_Database_Model_Exception
     */
    final protected function setFieldCollection ( ) {

        $reflection = new ReflectionClass($this);
        $flag       = false;

        foreach($reflection->getProperties() as $key => $property) {

            if(false === $property->isPublic())
                continue;

            $name = $this->addField($property->getName() ,$property->getValue($this));
            $property->setValue($this, $this->getField($name));
            $flag = true;
        }

        if(false === $flag)
            throw new Hoa_Database_Model_Exception(
                'A table must not be empty (%s is empty).',
                2, $this->getName());
    }

    /**
     * Add a field.
     * The field can have many type. Please see the Hoa_Database_Model_Field
     * class.
     *
     * @access  protected
     * @param   string     $name     The field name.
     * @param   mixed      $value    The field definition, should be an array or a
     *                               Hoa_Database_Model_Field instance.
     * @return  string
     * @throw   Hoa_Database_Model_Exception
     */
    final protected function addField ( $name, $value ) {

        if(is_array($value))
            if(!isset($value['name']))
                $value['name'] = $name;

        if(!($value instanceof Hoa_Database_Model_Field))
            $value = new Hoa_Database_Model_Field($value, $this);

        if(true === $this->fieldExists($value->getName()))
            throw new Hoa_Database_Model_Exception(
                'The field %s already exists, cannot overwrite.',
                3, $value->getName());

        $this->_field[$value->getName()] = $value;

        return $value->getName();
    }

    /**
     * Get a field.
     *
     * @access  public
     * @param   string  $name    The field name.
     * @return  Hoa_Database_Model_Field
     * @throw   Hoa_Database_Model_Exception
     */
    final public function getField ( $name ) {

        if(false === $this->fieldExists($name))
            throw new Hoa_Database_Model_Exception(
                'The field %s does not exists.', 4, $name);

        return $this->_field[$name];
    }

    /**
     * Check if a field already exists.
     *
     * @access  public
     * @param   string  $name    The field name.
     * @return  bool
     */
    final public function fieldExists ( $name ) {

        return isset($this->_field[$name]);
    }

    /**
     * Remove a field.
     *
     * @access  public
     * @param   string  $name    The field name.
     * @return  bool
     */
    final public function removeField ( $name ) {

        unset($this->_field[$name]);
        unset($this->{$name});
    }

    /**
     * Set the base name.
     *
     * @access  protected
     * @param   string     $name    The base name.
     * @return  string
     */
    final protected function setBaseName ( $name ) {

        $old         = $this->_base;
        $this->_base = $name;

        return $old;
    }

    /**
     * Get the base name that contains this table.
     *
     * @access  public
     * @return  string
     */
    final public function getBaseName ( ) {

        return $this->_base;
    }

    /**
     *
     */
    final protected function setName ( $name ) {

        $old         = $this->_name;
        $this->_name = $name;

        return $old;
    }

    /**
     * Get the table name.
     *
     * @access  public
     * @return  string
     */
    final public function getName ( ) {

        return $this->_name;
    }

    /**
     * Get the first table name.
     *
     * @access  public
     * @return  string
     */
    final public function getFirstName ( ) {

        if(null === $this->_nameWas)
            return $this->getName();

        return $this->_nameWas;
    }

    /**
     * Get the table name with the AS clause if the table was renamed.
     *
     * @access  public
     * @return  string
     */
    final public function getNameWithAs ( ) {

        if(null !== $this->_nameWas)
            $out = $this->_nameWas . ' AS ' . $this->getName();
        else
            $out = $this->getName();

        return $out;
    }

    /**
     * Rename the table (apply the AS clause).
     *
     * @access  public
     * @param   string  $name    The new table name.
     * @return  Hoa_Database_Model_Table
     */
    final public function rename ( $name ) {

        $new = $name;
        $old = $this->getName();

        if(null !== $this->_nameWas)
            $old =  $this->_nameWas;

        $this->_name    = $new;
        $this->_nameWas = $old;

        return $this;
    }

    /**
     * Get the current field for the iterator.
     *
     * @access  public
     * @return  Hoa_Database_Model_Field
     */
    final public function current ( ) {

        return current($this->_field);
    }

    /**
     * Get the current field name for the iterator.
     *
     * @access  public
     * @return  string
     */
    final public function key ( ) {

        return key($this->_field);
    }

    /**
     * Advance the internal field collection pointer, and return the current
     * field.
     *
     * @access  public
     * @return  Hoa_Database_Model_Field
     */
    final public function next ( ) {

        return next($this->_field);
    }

    /**
     * Rewind the internal field collection pointer, and return the first
     * field.
     *
     * @access  public
     * @return  Hoa_Database_Model_Field
     */
    final public function rewind ( ) {

        return reset($this->_field);
    }

    /**
     * Check if there is a current element after calls to the rewind or the next
     * methods.
     *
     * @access  public
     * @return  bool
     */
    final public function valid ( ) {

        if(empty($this->_field))
            return false;

        $key    = key($this->_field);
        $return = (next($this->_field) ? true : false);
        prev($this->_field);

        if(false === $return) {

            end($this->_field);
            if($key === key($this->_field))
                $return = true;
        }

        return $return;
    }

    /**
     * Count the number of field in this table.
     *
     * @access  public
     * @return  int
     */
    final public function count ( ) {

        return count($this->_field);
    }

    /**
     * Get the last field that was called.
     *
     * @access  protected
     * @return  string
     */
    final protected function getLastField ( ) {

        return $this->_lastField;
    }

    /**
     * Fields are accessible from public attributes, but if the field name is
     * prefixed of an underscore, then the goal is to define a criterion.
     * This method catchs all underscore prefixed public attributes, look if it
     * is a field ; if yes, saves (temporally) its name, and returns the field.
     * Please, see the Hoa_Database_Model_Field::__call() method.
     *
     * @access  public
     * @param   string  $name    The attribute name.
     * @return  Hoa_Database_Model_Field
     * @throw   Hoa_Database_Model_Exception
     */
    final public function __get ( $name ) {

        if($name{0} != '_')
            throw new Hoa_Database_Model_Exception(
                'Undefined property : %s::$%s.',
                5, array(get_class($this), $name));

        $name = substr($name, 1);

        /**
         * Not for now … sorry.
         *
         *  if(substr($name, 0, 5) == 'join_') {
         *
         *      $name = substr($name, 5);
         *
         *      foreach($this->getLinkedTables() as $foo => $table)
         *          if(strcasecmp($name, $table->getName()) == 0)
         *              return $table;
         *
         *      throw new Hoa_Database_Model_Exception(
         *          'The join table %s does not exists.', 6, $name);
         *  }
         *
         */

        if(false === $this->fieldExists($name))
            throw new Hoa_Database_Model_Exception(
                'Cannot assign criterion on the field %s.',
                6, $name);

        $this->_lastField = $name;

        return $this->getField($name);
    }

    /**
     * When a criterion method was called on a field, the returned object is the
     * table that contains the field.
     * This method catchs all undeclared methods and redirects the call on the
     * latest used field.
     * It allows user to write this :
     *     $table->_id  ->equal(3)
     *                  ->_or()
     *                  ->equal(7)
     *           ->_attr->equal('abc');
     * instead of :
     *     $table->id->equal(3);
     *     $table->id->_or();
     *     $table->id->equal(7);
     *     $table->attr->equal('abc');
     * It is faster and simpler.
     *
     * @access  public
     * @param   string  $name     The method name.
     * @param   array   $value    The method arguments.
     */
    final public function __call ( $name, $value ) {

        if(null === $this->getLastField())
            throw new Hoa_Database_Model_Exception(
                'Call to undefined method %s::%s().',
                7, array(get_class($this), $name));

        return $this->getField($this->getLastField())->__call($name, $value);
    }

    /**
     * Try to get a cached query. If cache exists (according to the cache name
     * and the context), the query will automatically be executed.
     *
     * @access  public
     * @param   string  $cacheName    Cache name.
     * @param   mixed   ...           Context.
     * @param   ...     ...           ...
     * @return  mixed
     * @throw   Hoa_Database_Model_Exception
     */
    final public function getCachedQuery ( $cacheName ) {

        $context = func_get_args();
        array_shift($context);

        $get = $this->getCache()->get($cacheName, $context);

        if(false === $get)
            return false;

        $this->queryExecute($get['q'], $get['p'], $get['t']);

        return $this;
    }

    /**
     * Get the latest built query and try to execute it into the DAL layer. If a
     * cache name is given, it will be created.
     *
     * @access  public
     * @param   string  $cacheName    Cache name.
     * @return  void
     * @throw   Hoa_Database_Model_Exception
     */
    final public function query ( $cacheName = null ) {

        $query = parent::getQuery();
        $qType = parent::getType();

        if(empty($query))
            throw new Hoa_Database_Model_Exception(
                'No query to execute.', 8);

        $preparedValue = array();
        foreach($this as $foo => $field) {

            $preparedValue = array_merge(
                                 $preparedValue,
                                 $field->getCriterion()->getPreparedValue()
                             );
            $field->getCriterion()->_reset();
        }

        foreach($this->getLinkedTables() as $foo => $table) {

            foreach($table as $oof => $field) {

                $preparedValue = array_merge(
                                     $preparedValue,
                                     $field->getCriterion()->getPreparedValue()
                                 );
                $field->getCriterion()->_reset();
            }
        }

        $preparedValue = array_merge($preparedValue, $this->getPreparedValue());
        $this->resetPreparedValue();

        $tmp = $this->queryExecute($query, $preparedValue, $qType);

        $this->getCache()->set($cacheName, $query, $preparedValue, $qType);

        return $tmp;
    }

    /**
     * Execute a query according to a query string, prepared values and a query
     * type.
     *
     * @access  private
     * @param   string   $query            The query string.
     * @param   array    $preparedValue    Prepared values.
     * @param   string   $qType            The query type.
     * @return  void
     * @throw   Hoa_Database_Model_Exception
     */
    final private function queryExecute ( $query, Array $preparedValue, $qType ) {

        $statement = Hoa_Database_Dal::getLastInstance()->prepare($query);
        $statement->execute($preparedValue);
        $fetch     = $statement->fetchAll();

        parent::eraseQuery();

        return $fetch;
    }

    /**
     * Get a collection instance.
     *
     * @access  public
     * @return  Hoa_Database_Model_Collection
     * @throw   Hoa_Database_Model_Exception
     */
    final public function getCollection ( ) {

        $name = Hoa_Database::getInstance()
                ->getParameter('collection.classname', $this->getName());

        if(!class_exists($name))
            throw new Hoa_Database_Model_Exception(
                'Cannot find the collection %s from the table %s.',
                9, array($name, $this->getName()));

        return new $name();
    }

    /**
     * Add a linked table on this master table.
     *
     * @access  public
     * @param   Hoa_Database_Model_Table  $table    Table to link.
     * @return  Hoa_Database_Model_Table
     */
    final public function addLinkedTable ( Hoa_Database_Model_Table $table ) {

        $this->_linkedTable[$table->getNameWithAs()] = $table;

        return $this;
    }

    /**
     * Get all linked tables.
     *
     * @access  public
     * @return  array
     */
    final public function getLinkedTables ( ) {

        return $this->_linkedTable;
    }

    /**
     * Get a specific linked table.
     *
     * @access  public
     * @param   string  $name    Name (original or renamed) of table.
     * @return  Hoa_Database_Model_Table
     */
    final public function getLinkedTable ( $name ) {

        foreach($this->getLinkedTables() as $full => $table)
            if(strcasecmp($table->getName(), $name) == 0)
                return $table;

        throw new Hoa_Database_Model_Exception(
            'The %s linked table does not exists, cannot get it.', 10, $name);
    }

    /**
     * Check if a linked table exists.
     *
     * @access  public
     * @param   string  $name    Name (original or renamed) of table.
     * @return  bool
     */
    final public function linkedTableExists ( $name ) {

        foreach($this->getLinkedTables() as $full => $table)
            if(strcasecmp($table->getName(), $name) == 0)
                return true;

        return false;
    }

    /**
     * Get the cache instance.
     *
     * @access  protected
     * @return  Hoa_Database_Cache_Query
     */
    final protected function getCache ( ) {

        if(null !== $this->_cache)
            return $this->_cache;

        $this->_cache = new Hoa_Database_Cache_Query(
                            $this->getBaseName() . '.' . $this->getName()
                        );

        return $this->_cache;
    }

    /**
     * Wrap the Hoa_Database_Cache_Query::clean() method.
     *
     * @access  public
     * @param   string  $cacheName    Cache name.
     * @param   mixed   ...           Context.
     * @param   ...     ...           ...
     * @return  void
     */
    final public function cleanCache ( $cacheName ) {

        $context = func_get_args();
        array_shift($context);

        $this->getCache()->clean($cacheName, $context);

        return;
    }

    /**
     * Wrap the Hoa_Database_Cache_Query::cleanAll() method.
     *
     * @access  public
     * @return  void
     */
    final public function cleanAllCaches ( ) {

        $this->getCache()->cleanAll();

        return;
    }

    /**
     * Return all fields, i.e. fields of this table but of linked tables
     * recursively.
     *
     * @access  public
     * @param   bool    $clean     Clean static variable. Must not be used by
     *                             user.
     * @return  array
     */
    final public function getAllFields ( $clean = true ) {

        static $memory = array();

        if($clean === true)
            $memory = array();

        if(in_array($this->getNameWithAs(), $memory))
            return array();

        $memory[] = $this->getNameWithAs();
        $fields   = array();

        foreach($this as $foo => $field)
            $fields[] = $field;

        foreach($this->getLinkedTables() as $foo => $table)
            $fields = array_merge(
                          $fields,
                          $table->getAllFields(false)
                      );

        return $fields;
    }
}
