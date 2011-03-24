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
 * @package     Hoa_XmlRpc
 * @subpackage  Hoa_XmlRpc_Method_System_ListMethods
 *
 */

/**
 * Hoa_XmlRpc_Method_Abstract
 */
import('XmlRpc.MethodAbstract');

/**
 * Hoa_File_Finder
 */
import('File.Finder');

/**
 * Hoa_File_Undefined
 */
import('File.Undefined');

/**
 * Class Hoa_XmlRpc_Method_System_ListMethods.
 *
 * Get list of RPC methods.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2011 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_XmlRpc
 * @subpackage  Hoa_XmlRpc_Method_System_ListMethods
 */

class Hoa_XmlRpc_Method_System_ListMethods extends Hoa_XmlRpc_Method_Abstract {

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

        parent::__construct($path, $parameters);
    }

    /**
     * get
     * Get response.
     *
     * @access  public
     * @return  string
     */
    public function get ( ) {

        $file   = array();
        $dir    = new Hoa_File_Finder(
            $this->path,
            Hoa_File_Finder::LIST_FILE |
            Hoa_File_Finder::LIST_VISIBLE,
            Hoa_File_Finder::SORT_INAME
        );

        foreach($dir as $i => $scan)
            $file[] = $this->value($scan->getFilename(), 'string');

        return $this->value($file, 'array')->get();
    }
}
