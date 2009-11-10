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
 * @subpackage  Hoa_Database_QueryBuilder_Dml_Interface
 *
 */

/**
 * Hoa_Framework
 */
require_once 'Framework.php';

/**
 * Interface Hoa_Database_QueryBuilder_Dml_Interface.
 *
 * Interface for the Data Modeling Language, i.e. delete, insert, select and
 * update methods.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2009 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Database
 * @subpackage  Hoa_Database_QueryBuilder_Dml_Interface
 */

interface Hoa_Database_QueryBuilder_Dml_Interface {

    /**
     * Delete data.
     *
     * @access  public
     * @return  void
     */
    public function delete ( );

    /**
     * Insert data.
     *
     * @access  public
     * @param   array   $values    Keys and values that need to be insered.
     * @return  void
     */
    public function insert ( Array $values );

    /**
     * Select data.
     *
     * @access  public
     * @param   mixed   $field    Field to select (null will select all fields).
     * @param   ...     ...       ...
     * @return  void
     */
    public function select ( $field = null );

    /**
     * Update data.
     *
     * @access  public
     * @param   array   $values    Keys and values that need to be updated.
     * @return  void
     */
    public function update ( Array $values );
}
