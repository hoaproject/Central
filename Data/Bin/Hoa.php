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
 * @category    Data
 *
 */

/**
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2008 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 */

/**
 * Set inlucde path.
 */
ini_set('include_path', dirname(dirname(dirname(__FILE__))) .
                        '/Framework/Core/' .
                        PATH_SEPARATOR .
                        get_include_path());

/**
 * Set the default timezone.
 */
ini_set('date.timezone', 'Europe/Paris');

require_once 'Framework.php';

define('HOA_DATA', Hoa_Framework::getInstance()->getFormattedParameter('root.data'));

class Hoa_Framework_Protocol_Data_Etc extends Hoa_Framework_Protocol {

    protected $_name = 'Etc';

    public function reach ( $queue ) {

        return HOA_DATA . '/Etc/' . $queue;
    }
}

class Hoa_Framework_Protocol_Data_Bin extends Hoa_Framework_Protocol {

    protected $_name = 'Bin';

    public function reach ( $queue ) {

        return HOA_DATA . '/Bin/' . $queue;
    }
}

Hoa_Framework::getProtocol()
    ->getComponent('Data')
    ->addComponent(new Hoa_Framework_Protocol_Data_Bin())
    ->addComponent(new Hoa_Framework_Protocol_Data_Etc());

/**
 * Hoa_Console
 */
import('Console.~');

/**
 * Here we go …
 */
Hoa_Console::getInstance()
    ->importStyle('sheet')
    ->dispatch();
