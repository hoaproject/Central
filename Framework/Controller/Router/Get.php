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
 * @package     Hoa_Controller
 * @subpackage  Hoa_Controller_Router_Get
 *
 */

/**
 * Hoa_Framework
 */
require_once 'Framework.php';

/**
 * Hoa_Controller_Router_Interface
 */
import('Controller.Router.Interface');

/**
 * Class Hoa_Controller_Router_Get.
 *
 * The GET router takes its values from the $_GET array.
 * It just add the default value if they did not exist.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2008 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Controller
 * @subpackage  Hoa_Controller_Router_Get
 */

class Hoa_Controller_Router_Get implements Hoa_Controller_Router_Interface {

    /**
     * Start the routing.
     *
     * @access  public
     * @param   array   $parameters    Parameters of the routeur.
     * @return  array
     */
    public function route ( Array $parameters = array() ) {

        if(!isset($parameters['default']))
            return $_GET;

        foreach($parameters['default'] as $key => $value) {

            if(!isset($_GET[$key]))
                $_GET[$key] = $value;
        }

        return $_GET;
    }
}
