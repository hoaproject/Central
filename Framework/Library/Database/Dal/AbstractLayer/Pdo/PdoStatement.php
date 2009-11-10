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
 * @subpackage  Hoa_Database_Dal_AbstractLayer_Pdo_PdoStatement
 *
 */

/**
 * Hoa_Framework
 */
require_once 'Framework.php';

/**
 * Hoa_Database_Dal_Exception
 */
import('Database.Dal.Exception');

/**
 * Hoa_Database_Dal_Interface_WrapperStatement
 */
import('Database.Dal.Interface.WrapperStatement');

/**
 * Class Hoa_Database_Dal_AbstractLayer_Pdo_PdoStatement.
 *
 * Wrap PDOStatement.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2009 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Database
 * @subpackage  Hoa_Database_Dal_AbstractLayer_Pdo_PdoStatement
 */

class Hoa_Database_Dal_AbstractLayer_Pdo_PdoStatement implements Hoa_Database_Dal_Interface_WrapperStatement {

    /**
     * The statement instance.
     *
     * @var PDOStatement object
     */
    protected $statement = null;



    /**
     * Create a statement instance.
     *
     * @access  public
     * @param   PDOStatement  $statement    The PDOStatement instance.
     * @return  void
     */
    public function __construct ( PDOStatement $statement ) {

        $this->setStatement($statement);
    }

    /**
     * Set the statement instance.
     *
     * @access  protected
     * @param   PDOStatement  $statement    The PDOStatement instance.
     * @return  PDOStatement
     */
    protected function setStatement ( PDOStatement $statement ) {

        $old             = $this->statement;
        $this->statement = $statement;

        return $old;
    }

    /**
     * Get the statement instance.
     *
     * @access  protected
     * @return  PDOStatement
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
     * @return  Hoa_Database_Dal_Pdo_PdoStatement
     * @throw   Hoa_Database_Dal_Exception
     */
    public function execute ( Array $bindParameters = array() ) {

        if(empty($bindParameters)) {

            $this->getStatement()->execute();

            return $this;
        }

        if(false === $this->getStatement()->execute($bindParameters))
            throw new Hoa_Database_Dal_Exception(
                '%3$s (%1$s/%2$d)', 0, $this->errorInfo());

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
                                    $length = null ) {

        if(null === $type)
            return $this->getStatement()->bindParam($parameter, $value);
        
        if(null === $length)
            return $this->getStatement()->bindParam($parameter, $value, $type);

        return $this->getStatement()->bindParam($parameter, $value, $type, $length);
    }

    /**
     * Return an array containing all of the result set rows.
     *
     * @access  public
     * @return  array
     * @throw   Hoa_Database_Dal_Exception
     */
    public function fetchAll ( ) {

        return $this->getStatement()->fetchAll(PDO::FETCH_ASSOC);
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
