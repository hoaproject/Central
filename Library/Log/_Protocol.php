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
 * @package     Hoa_Framework
 * @subpackage  Hoa_Framework_Protocol_Framework_Package_Log
 *
 */

/**
 * Hoa_Framework
 */
require_once 'Framework.php';

/**
 * Class Hoa_Framework_Protocol_Framework_Package_Log.
 *
 * The hoa://Framework/Package/Log component.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2008 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Framework
 * @subpackage  Hoa_Framework_Protocol_Framework_Package_Log
 */

class       Hoa_Framework_Protocol_Framework_Package_Log
    extends Hoa_Framework_Protocol {

    /**
     * Component's name.
     *
     * @var Hoa_Framework_Protocol_Framework_Package_Log string
     */
    protected $_name = 'Log';



    /**
     * Queue of the component.
     *
     * @access  public
     * @param   string  $queue    Queue of the component (generally, a filename,
     *                            with probably a query).
     * @return  mixed
     */
    public function reach ( $queue ) {

        $out = HOA_DATA_PRIVATE_LOG;

        switch($queue) {

            case 'NEW':
                $out .= DS . parent::reach($queue) . '.log';
              break;

            default:
                $out .= DS . $queue;
        }

        return $out;
    }
}


/**
 * Plug the component.
 */
Hoa_Framework::getProtocol()
    ->getComponent('Framework')
    ->getComponent('Package')
    ->addComponent(new Hoa_Framework_Protocol_Framework_Package_Log());
