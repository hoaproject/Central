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
 * @subpackage  Hoa_Database_Dal_Interface_Wrapper
 *
 */

/**
 * Interface Hoa_Database_Dal_Interface_Wrapper.
 *
 * Interface a DAL wrapper.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007-2011 Ivan ENDERLIN.
 * @license     New BSD License
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
