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
 * Copyright (c) 2007, 2008 Ivan ENDERLIN. All rights reserved.
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
 * @package     Hoa_Session
 * @subpackage  Hoa_Session_SaveHandler_Interface
 *
 */

/**
 * Hoa_Framework
 */
require_once 'Framework.php';

/**
 * Interface Hoa_Session_SaveHandler_Interface.
 *
 * Force some methods to be implemented by a class.
 * Theses methods must be implemented for the PHP function
 * session_set_save_handler. Please, see the manuel for more informations.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2008 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Session
 * @subpackage  Hoa_Session_SaveHandler_Interface
 */

interface Hoa_Session_SaveHandler_Interface {

    /**
     * Open a session.
     *
     * @access  public
     * @param   string  $savePath    Path where the session is stocked.
     * @param   string  $name        Session name.
     * @return  bool
     */
    public function open ( $savePath, $name );

    /**
     * Close a session.
     *
     * @access  public
     * @return  bool
     */
    public function close ( );

    /**
     * Read the session data.
     *
     * @access  public
     * @param   string  $id    Session ID.
     * @return  string
     */
    public function read ( $id );

    /**
     * Write the session data.
     *
     * @access  public
     * @param   string  $id      Session ID.
     * @param   string  $data    Session data.
     * @return  mixed
     */
    public function write ( $id, $data );

    /**
     * Destroy a session.
     *
     * @access  public
     * @param   string  $id    Session ID.
     * @return  bool
     */
    public function destroy ( $id );

    /**
     * The garbage collection remove all old session data older than the value of
     * $maxlifetime variable (in seconds).
     *
     * @access  public
     * @param   int     $maxlifetime    Max lifetime of a session.
     * @return  bool
     */
    public function gc ( $maxlifetime );
}
