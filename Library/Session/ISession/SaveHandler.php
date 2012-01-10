<?php

/**
 * Hoa
 *
 *
 * @license
 *
 * New BSD License
 *
 * Copyright © 2007-2012, Ivan Enderlin. All rights reserved.
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
 */

namespace Hoa\Session\ISession {

/**
 * Interface \Hoa\Session\ISession\SaveHandler.
 *
 * Force some methods to be implemented by a class.
 * Theses methods must be implemented for the PHP function
 * session_set_save_handler. Please, see the manuel for more informations.
 *
 * @author     Ivan Enderlin <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright © 2007-2012 Ivan Enderlin.
 * @license    New BSD License
 */

interface SaveHandler {

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

}
