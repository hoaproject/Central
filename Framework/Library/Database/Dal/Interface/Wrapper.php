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
 * @subpackage  Hoa_Database_Dal_Interface_Wrapper
 *
 */

/**
 * Interface Hoa_Database_Dal_Interface_Wrapper.
 *
 * Interface a DAL wrapper.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2010 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Database
 * @subpackage  Hoa_Database_Dal_Interface_Wrapper
 */

interface Hoa_Database_Dal_Interface_Wrapper {

    /**
     * Create a DAL instance, representing a connection to a database.
     * The constructor is private to make a singleton.
     *
     * @access  public
     * @param   string  $dns              The DNS of database.
     * @param   string  $username         The username to connect to database.
     * @param   string  $password         The password to connect to database.
     * @param   array   $driverOptions    The driver options.
     * @return  void
     * @throw   Hoa_Database_Dal_Exception
     */
    public function __construct ( $dns, $username, $password,
                                  Array $driverOption = array() );

    /**
     * Initiate a transaction.
     *
     * @access  public
     * @return  bool
     * @throw   Hoa_Database_Dal_Exception
     */
    public function beginTransaction ( );

    /**
     * Commit a transaction.
     *
     * @access  public
     * @return  bool
     * @throw   Hoa_Database_Dal_Exception
     */
    public function commit ( );

    /**
     * Roll back a transaction.
     *
     * @access  public
     * @return  bool
     * @throw   Hoa_Database_Dal_Exception
     */
    public function rollBack ( );

    /**
     * Return the ID of the last inserted row or sequence value.
     *
     * @access  public
     * @param   string  $name    Name of sequence object (needed for some
     *                           driver).
     * @return  string
     * @throw   Hoa_Database_Dal_Exception
     */
    public function lastInsertId ( $name = null );

    /**
     * Prepare a statement for execution and returns a statement object.
     *
     * @access  public
     * @param   string  $statement    This must be a valid SQL statement for the
     *                                target database server.
     * @param   array   $options      Options to set attributes values for the
     *                                AbstractLayer Statement.
     * @return  Hoa_Database_Dal_Interface_WrapperStatement
     * @throw   Hoa_Database_Dal_Exception
     */
    public function prepare ( $statement, Array $options = array() );

    /**
     * Quote a string for use in a query.
     *
     * @access  public
     * @param   string  $string    The string to be quoted.
     * @param   int     $type      Provide a data type hint for drivers that
     *                             have alternate quoting styles.
     * @return  string
     * @throw   Hoa_Database_Dal_Exception
     */
    public function quote ( $string = null, $type = -1 );

    /**
     * Execute an SQL statement, returning a result set as a
     * Hoa_Database_Dal_Interface_WrapperStatement object.
     *
     * @access  public
     * @param   string  $statement    The SQL statement to prepare and execute.
     * @return  Hoa_Database_Dal_Interface_WrapperStatement
     * @throw   Hoa_Database_Dal_Exception
     */
    public function query ( $statement );

    /**
     * Fetch the SQLSTATE associated with the last operation on the database
     * handle.
     *
     * @access  public
     * @return  string
     * @throw   Hoa_Database_Dal_Exception
     */
    public function errorCode ( );

    /**
     * Fetch extends error information associated with the last operation on the
     * database handle.
     *
     * @access  public
     * @return  array
     * @throw   Hoa_Database_Dal_Exception
     */
    public function errorInfo ( );

    /**
     * Return an array of available drivers.
     *
     * @access  public
     * @return  array
     * @throw   Hoa_Datatase_Dal_Exception
     */
    public function getAvailableDrivers ( );

    /**
     * Set attributes.
     *
     * @access  public
     * @param   array   $attributes    Attributes values.
     * @return  array
     * @throw   Hoa_Database_Dal_Exception
     */
    public function setAttributes ( Array $attributes );

    /**
     * Set a specific attribute.
     *
     * @access  public
     * @param   mixed   $attribute    Attribute name.
     * @param   mixed   $value        Attribute value.
     * @return  mixed
     * @throw   Hoa_Database_Dal_Exception
     */
    public function setAttribute ( $attribute, $value );

    /**
     * Retrieve all database connection attributes.
     *
     * @access  public
     * @return  array
     * @throw   Hoa_Database_Dal_Exception
     */
    public function getAttributes ( );

    /**
     * Retrieve a database connection attribute.
     *
     * @access  public
     * @param   string  $attribute    Attribute name.
     * @return  mixed
     * @throw   Hoa_Database_Dal_Exception
     */
    public function getAttribute ( $attribute );
}
