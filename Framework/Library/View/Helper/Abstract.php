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
 * Copyright (c) 2007, 2010 Ivan ENDERLIN. All rights reserved.
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
 * @package     Hoa_View
 * @subpackage  Hoa_View_Helper_Abstract
 *
 */

/**
 * Hoa_Core
 */
require_once 'Core.php';

/**
 * Hoa_View_Helper_Exception
 */
import('View.Helper.Exception');

/**
 * Class Hoa_View_Helper_Abstract.
 *
 * Abstract class for view helpers.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2010 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_View
 * @subpackage  Hoa_View_Helper_Abstract
 */

abstract class Hoa_View_Helper_Abstract {

    /**
     * __construct
     * Construct helper.
     *
     * @access  public
     * @param   *abstract*  mixed    Abstract.
     * @return  mixed
     * @throw   Hoa_View_Helper_Exception
     */
    abstract public function __construct ( );

    /**
     * __toString
     * Convert object result to string.
     *
     * @access  public
     * @return  string
     * @throw   Hoa_View_Helper_Exception
     */
    abstract public function __toString ( );
}
