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
 * @subpackage  Hoa_Database_Model_Base
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
 * Class Hoa_Database_Model_Base.
 *
 * Class that represents a base, i.e. a collection of tables.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2009 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Database
 * @subpackage  Hoa_Database_Model_Base
 */

class Hoa_Database_Model_Base implements Iterator, Countable {

    /**
     * The table name.
     *
     * @var Hoa_Database_Model_Base string
     */
    protected $_name = null;

    /**
     * The table collection.
     *
     * @var Hoa_Database_Model_Base array
     */
    private $_table  = null;



    /**
     * Set constraints and the field collection.
     *
     * @access  public
     * @return  void
     * @throw   Hoa_Database_Model_Exception
     */
    public function __construct ( ) {

        if(empty($this->_name))
            throw new Hoa_Database_Model_Exception(
                'A base must have a name.', 0);

        $this->setTableCollection();
    }

    /**
     * Set the table collection with scanning the base directory.
     *
     * @access  protected
     * @return  void
     * @throw   Hoa_Database_Model_Exception
     */
    final protected function setTableCollection ( ) {

        $directory = Hoa_Database::getInstance()
                         ->getParameter(
                             'base.directory',
                             $this->getName()
                           );

        if(!is_dir($directory))
            throw new Hoa_Database_Model_Exception(
                'Cannot found the base directory %s.', 0, $directory);

        $reflection = new ReflectionClass($this);

        foreach($reflection->getProperties() as $key => $property) {

            if(false === $property->isPublic())
                continue;

            $name = $this->addTable($property->getName(), $property->getValue($this));
            $property->setValue($this, $this->getTable($name));
        }
    }

    /**
     * Add a table.
     *
     * @access  protected
     * @param   string     $name     The attribute name.
     * @param   string     $value    If no value is given, the attribute name will
     *                               choosen to be the table name.
     * @return  string
     * @throw   Hoa_Database_Model_Exception
     */
    final protected function addTable ( $name, $value ) {

        if(is_string($value))
            $name   = $value;

        if(true === $this->tableExists($name))
            throw new Hoa_Database_Model_Exception(
                'The table %s already exists in the base %s, ' .
                'cannot overwrite.', 1, array($name, $this->getName()));

        $base       = Hoa_Database::getInstance()
                      ->getParameter('base.directory', $this->getName());
        $file       = Hoa_Database::getInstance()
                      ->getParameter('table.filename', $name);
        $class      = Hoa_Database::getInstance()
                      ->getParameter('table.classname', $name);
        $collection = Hoa_Database::getInstance()
                      ->getParameter('collection.classname', $name);

        if(!file_exists($base . DS . $file))
            throw new Hoa_Database_Model_Exception(
                'Cannot find the table %s : %s.',
                2, array($name, $base . DS . $file));

        require_once $base . DS . $file;

        if(!class_exists($class))
            throw new Hoa_Database_Model_Exception(
                'Cannot find the table class %s in the file %s.',
                3, array($class, $base . DS . $file));

        if(!class_exists($collection))
            throw new Hoa_Database_Model_Exception(
                'Cannot find the collection class %S in the file %s.',
                4, array($collection, $base . DS . $file));

        $this->_table[$name] = new $class();

        return $name;
    }

    /**
     * Get a table.
     *
     * @access  public
     * @param   string  $name    The table name.
     * @return  Hoa_Database_Model_Table
     * @throw   Hoa_Database_Model_Exception
     */
    public function getTable ( $name ) {

        if(false === $this->tableExists($name))
            throw new Hoa_Database_Model_Exception(
                'The table %s does not exists.', 2, $name);

        return $this->_table[$name];
    }

    /**
     * Check if a table already exists.
     *
     * @access  public
     * @param   string  $name    The table name.
     * @return  bool
     */
    public function tableExists ( $name ) {

        return isset($this->_table[$name]);
    }

    /**
     * Remove a table.
     *
     * @access  public
     * @param   string  $name    The table name.
     * @return  bool
     */
    public function removeTable ( $name ) {

        unset($this->_table[$name]);
        unset($this->{$name});
    }

    /**
     * Get the base name.
     *
     * @access  public
     * @return  string
     */
    public function getName ( ) {

        return $this->_name;
    }

    /**
     * Get the current table for the iterator.
     *
     * @access  public
     * @return  Hoa_Database_Model_Table
     */
    public function current ( ) {

        return current($this->_table);
    }

    /**
     * Get the current table name for the iterator.
     *
     * @access  public
     * @return  string
     */
    public function key ( ) {

        return key($this->_table);
    }

    /**
     * Advance the internal table collection pointer, and return the current
     * table.
     *
     * @access  public
     * @return  Hoa_Database_Model_Table
     */
    public function next ( ) {

        return next($this->_table);
    }

    /**
     * Rewind the internal table collection pointer, and return the first
     * table.
     *
     * @access  public
     * @return  Hoa_Database_Model_Table
     */
    public function rewind ( ) {

        return reset($this->_table);
    }

    /**
     * Check if there is a current element after calls to the rewind or the next
     * methods.
     *
     * @access  public
     * @return  bool
     */
    public function valid ( ) {

        if(empty($this->_table))
            return false;

        $key    = key($this->_table);
        $return = (next($this->_table) ? true : false);
        prev($this->_table);

        if(false === $return) {

            end($this->_table);
            if($key === key($this->_table))
                $return = true;
        }

        return $return;
    }

    /**
     * Count the number of table in this table.
     *
     * @access  public
     * @return  int
     */
    public function count ( ) {

        return count($this->_table);
    }
}
