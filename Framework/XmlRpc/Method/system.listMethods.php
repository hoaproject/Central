<?php

/**
 * Hoa Framework
 *
 *
 * @license
 *
 * GNU General Public License
 *
 * This file is part of HOA Open Accessibility.
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
 * @package     Hoa_XmlRpc
 * @subpackage  Hoa_XmlRpc_Method_System_ListMethods
 *
 */

/**
 * Hoa_Framework
 */
require_once 'Framework.php';

/**
 * Hoa_XmlRpc_Method_Abstract
 */
import('XmlRpc.MethodAbstract');

/**
 * Hoa_File
 */
import('File.~');

/**
 * Hoa_File_Dir
 */
import('File.Dir');

/**
 * Class Hoa_XmlRpc_Method_System_ListMethods.
 *
 * Get list of RPC methods.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2008 Ivan ENDERLIN.
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
        $dir    = Hoa_File_Dir::scan($this->path, Hoa_File_Dir::LIST_FILE);

        foreach($dir as $i => $fileinfo)
            $file[] = $this->value(Hoa_File_Util::skipExt($fileinfo['name']), 'string');

        return $this->value($file, 'array')->get();
    }
}
