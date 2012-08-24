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

namespace Hoa\Database\IDal {

/**
 * Interface \Hoa\Database\IDal\WrapperStatement.
 *
 * Interface of a DAL statement wrapper.
 *
 * @author     Ivan Enderlin <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright © 2007-2012 Ivan Enderlin.
 * @license    New BSD License
 */

interface WrapperStatement {

    /**
     * Execute a prepared statement.
     *
     * @access  public
     * @param   array   $bindParameters    Bind parameters values if
     *                                     bindParameter() is not called.
     * @return  bool
     * @throw   \Hoa\Database\Exception
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
     * @throw   \Hoa\Database\Exception
     */
    public function bindParameter ( $parameter, &$value, $type = null,
                                    $length = null);

    /**
     * Return an array containing all of the result set rows.
     *
     * @access  public
     * @return  array
     * @throw   \Hoa\Database\Exception
     */
    public function fetchAll ( );

    /**
     * Close the cursor, enabling the statement to be executed again.
     *
     * @access  public
     * @return  bool
     * @throw   \Hoa\Database\Exception
     */
    public function closeCursor ( );

    /**
     * Fetch the SQLSTATE associated with the last operation on the statement
     * handle.
     *
     * @access  public
     * @return  string
     * @throw   \Hoa\Database\Exception
     */
    public function errorCode ( );

    /**
     * Fetch extends error information associated with the last operation on the
     * statement handle.
     *
     * @access  public
     * @return  array
     * @throw   \Hoa\Database\Exception
     */
    public function errorInfo ( );
}

}
