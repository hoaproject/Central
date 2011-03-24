<?php

/**
 * Hoa
 *
 *
 * @license
 *
 * New BSD License
 *
 * Copyright © 2007-2011, Ivan Enderlin. All rights reserved.
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
 * @package     Hoa_XmlRpc
 * @subpackage  Hoa_XmlRpc_Method_Abstract
 *
 */

/**
 * Class Hoa_XmlRpc_Method_Abstract.
 *
 * Abstract class for RPC methods.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright © 2007-2011 Ivan ENDERLIN.
 * @license     New BSD License
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_XmlRpc
 * @subpackage  Hoa_XmlRpc_Method_Abstract
 */

abstract class Hoa_XmlRpc_Method_Abstract {

    /**
     * Path to RPC methods directory.
     *
     * @var Hoa_XmlRpc_Method_System_ListMethods string
     */
    protected $path = '';

    /**
     * RPC parameters.
     *
     * @var Hoa_XmlRpc_Method_System_ListMethods array
     */
    protected $parameters = array();



    /**
     * __construct
     * Sets variables.
     *
     * @access  public
     * @param   path        string    Path to RPC methods directory.
     * @param   parameters  array     RPC parameters.
     * @return  void
     */
    public function __construct ( $path, $parameters ) {

        $this->path       = $path;
        $this->parameters = $parameters;
    }

    /**
     * Get server method response.
     */
    abstract protected function get ( );

    /**
     * value
     * Alias of Hoa_XmlRpc_Value object.
     *
     * @access  public
     * @param   source  mixed    Message source.
     * @param   type    int      Type of source.
     * @return  object
     * @throw   Hoa_XmlRpc_Exception
     */
    public function value ( $source, $type = '' ) {

        return new Hoa_XmlRpc_Value($source, $type);
    }
}
