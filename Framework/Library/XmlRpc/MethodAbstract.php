<?php

/**
 * Hoa
 *
 *
 * @license
 *
 * GNU General Public License
 *
 * This file is part of HOA Open Accessibility.
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
 * @copyright   Copyright (c) 2007, 2011 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
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
