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
 * @subpackage  Hoa_Database_Dal_DalStatement
 *
 */

/**
 * Class Hoa_Database_Dal_DalStatement.
 *
 * The heigher class that represents a DAL statement.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2011 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Database
 * @subpackage  Hoa_Database_Dal_DalStatement
 */

class Hoa_Database_Dal_DalStatement {

    /**
     * The statement instance.
     *
     * @var Hoa_Database_Dal_Interface_WrapperStatement object
     */
    protected $statement = null;



    /**
     * Create a statement instance.
     *
     * @access  public
     * @param   Hoa_Database_Dal_Interface_WrapperStatement  $statement    The
     *                                                                     statement
     *                                                                     instance.
     * @return  void
     */
    public function __construct ( Hoa_Database_Dal_Interface_WrapperStatement $statement ) {

        $this->setStatement($statement);
    }

    /**
     * Set the statement instance.
     *
     * @access  protected
     * @param   Hoa_Database_Dal_Interface_WrapperStatement  $statement    The
     *                                                                     statement
     *                                                                     instance.
     * @return  Hoa_Database_Dal_Interface_WrapperStatement
     */
    protected function setStatement ( Hoa_Database_Dal_Interface_WrapperStatement $statement ) {

        $old             = $this->statement;
        $this->statement = $statement;
    }

    /**
     * Get the statement instance.
     *
     * @access  protected
     * @return  Hoa_Database_Dal_Interface_WrapperStatement
     */
    protected function getStatement ( ) {

        return $this->statement;
    }

    /**
     * Execute a prepared statement.
     *
     * @access  public
     * @param   array   $bindParameters    Bind parameters values if bindParam is
     *                                     not called.
     * @return  Hoa_Database_Dal_DalStatement
     * @throw   Hoa_Database_Dal_Exception
     */
    public function execute ( Array $bindParameters = array() ) {

        if(empty($bindParameters))
            return $this->getStatement()->execute();

        $this->getStatement()->execute($bindParameters);

        return $this;
    }

    /**
     * Bind a parameter to te specified variable name.
     *
     * @access  public
     * @param   mixed   $parameter    Parameter name.
     * @param   mixed   $value        Parameter value.
     * @param   int     $type         Type of value.
     * @param   int     $length       Length of data type.
     * @return  bool
     * @throw   Hoa_Database_Dal_Exception
     */
    public function bindParameter ( $parameter, &$value, $type = null,
                                    $length = null) {

        if(null === $type)
            return $this->getStatement()->bindParameter($parameter, $value);

        if(null === $length)
            return $this->getStatement()->bindParameter(
                $parameter,
                $value,
                $type
            );

        return $this->getStatement()->bindParameter(
            $parameter,
            $value,
            $type,
            $length
        );
    }

    /**
     * Return an array containing all of the result set rows.
     *
     * @access  public
     * @return  array
     * @throw   Hoa_Database_Dal_Exception
     */
    public function fetchAll ( ) {

        return $this->getStatement()->fetchAll();
    }

    /**
     * Close the cursor, enabling the statement to be executed again.
     *
     * @access  public
     * @return  bool
     * @throw   Hoa_Database_Dal_Exception
     */
    public function closeCursor ( ) {

        return $this->getStatement()->closeCursor();
    }

    /**
     * Fetch the SQLSTATE associated with the last operation on the statement
     * handle.
     *
     * @access  public
     * @return  string
     * @throw   Hoa_Database_Dal_Exception
     */
    public function errorCode ( ) {

        return $this->getStatement()->errorCode();
    }

    /**
     * Fetch extends error information associated with the last operation on the
     * statement handle.
     *
     * @access  public
     * @return  array
     * @throw   Hoa_Database_Dal_Exception
     */
    public function errorInfo ( ) {

        return $this->getStatement()->errorInfo();
    }
}
