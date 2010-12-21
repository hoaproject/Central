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
 * @package     Hoa_Realdom
 * @subpackage  Hoa_Realdom_Smallinteger
 *
 */

/**
 * Hoa_Realdom_Boundinteger
 */
import('Realdom.Boundinteger') and load();

/**
 * Hoa_Realdom_Constinteger
 */
import('Realdom.Constinteger') and load();

/**
 * Class Hoa_Realdom_Smallinteger.
 *
 * Realistic domain: smallinteger.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2010 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Realdom
 * @subpackage  Hoa_Realdom_Smallinteger
 */

class Hoa_Realdom_Smallinteger extends Hoa_Realdom_Boundinteger {

    /**
     * Realistic domain name.
     *
     * @var Hoa_Realdom string
     */
    protected $_name = 'smallinteger';



    /**
     * Construct a realistic domain.
     *
     * @access  public
     * @param   Hoa_Realdom_Constinteger  $lower    Lower bound value.
     * @param   Hoa_Realdom_Constinteger  $upper    Upper bound value.
     * @return  void
     */
    public function construct ( Hoa_Realdom_Constinteger $lower = null,
                                Hoa_Realdom_Constinteger $upper = null ) {

        parent::construct(
            new Hoa_Realdom_Constinteger(-128),
            new Hoa_Realdom_Constinteger( 127)
        );

        return;
    }
}
