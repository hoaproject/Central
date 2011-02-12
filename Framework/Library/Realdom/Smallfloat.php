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
 */

namespace {

from('Hoa')

/**
 * \Hoa\Realdom\Boundfloat
 */
-> import('Realdom.Boundfloat')

/**
 * \Hoa\Realdom\Constfloat
 */
-> import('Realdom.Constfloat');

}

namespace Hoa\Realdom {

/**
 * Class \Hoa\Realdom\Smallfloat.
 *
 * Realistic domain: smallfloat.
 *
 * @author     Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright (c) 2007, 2011 Ivan ENDERLIN.
 * @license    http://gnu.org/licenses/gpl.txt GNU GPL
 */

class Smallfloat extends Boundfloat {

    /**
     * Realistic domain name.
     *
     * @var \Hoa\Realdom string
     */
    protected $_name = 'smallfloat';



    /**
     * Construct a realistic domain.
     *
     * @access  public
     * @param   \Hoa\Realdom\Constfloat  $lower    Lower bound value.
     * @param   \Hoa\Realdom\Constfloat  $upper    Upper bound value.
     * @return  void
     */
    public function construct ( Constfloat $lower = null,
                                Constfloat $upper = null ) {

        parent::construct(
            new Constfloat(-128.0),
            new Constfloat( 127.0)
        );

        return;
    }
}

}
