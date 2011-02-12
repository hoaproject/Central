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
 * @subpackage  Hoa_Database_Dal_Interface_WrapperStatement
 *
 */

/**
 * Interface Hoa_Database_Dal_Interface_WrapperStatement.
 *
 * Interface of a DAL statement wrapper.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2011 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Database
 * @subpackage  Hoa_Database_Dal_Interface_WrapperStatement
 */

interface Hoa_Database_Dal_Interface_WrapperStatement {

    /**
     * Execute a prepared statement.
     *
     * @access  public
     * @param   array   $bindParameters    Bind parameters values if
     *                                     bindParameter() is not called.
     * @return  bool
     * @throw   Hoa_Database_Dal_Exception
     */
    public function execute ( Array $bindParameters = array() );

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
                                    $length = null);

    /**
     * Return an array containing all of the result set rows.
     *
     * @access  public
     * @return  array
     * @throw   Hoa_Database_Dal_Exception
     */
    public function fetchAll ( );

    /**
     * Close the cursor, enabling the statement to be executed again.
     *
     * @access  public
     * @return  bool
     * @throw   Hoa_Database_Dal_Exception
     */
    public function closeCursor ( );

    /**
     * Fetch the SQLSTATE associated with the last operation on the statement
     * handle.
     *
     * @access  public
     * @return  string
     * @throw   Hoa_Database_Dal_Exception
     */
    public function errorCode ( );

    /**
     * Fetch extends error information associated with the last operation on the
     * statement handle.
     *
     * @access  public
     * @return  array
     * @throw   Hoa_Database_Dal_Exception
     */
    public function errorInfo ( );
}
