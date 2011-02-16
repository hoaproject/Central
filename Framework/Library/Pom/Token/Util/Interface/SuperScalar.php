<?php

/**
 * Hoa
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
 *
 *
 * @category    Framework
 * @package     Hoa_Pom
 * @subpackage  Hoa_Pom_Token_Util_Interface_SuperScalar
 *
 */

/**
 * Interface Hoa_Pom_Token_Util_Interface_SuperScalar.
 *
 * Whether a data is (uniform or not) super-scalar.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2011 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Pom
 * @subpackage  Hoa_Pom_Token_Util_Interface_SuperScalar
 */

interface Hoa_Pom_Token_Util_Interface_SuperScalar {

    /**
     * Check if a data is an uniform super-scalar or not.
     *
     * Lemma:
     *   In PHP, keys of arrays should be typed. For example:
     *   array('0' => 1) is different of array(0 => 1).
     *
     * Definition 1: Super-scalar
     *   An array, an object or a resource are —per definition—
     *   super-scalars, because they are structures that should contain many
     *   differents types.
     *
     * Definition 2: Uniform super-scalar
     *   A super-scalar data should contains many types. A uniform super-scalar
     *   data must verify two conditions:
     *     * all data must have the same type;
     *     * all data must be scalar or uniform super-scalar.
     *
     * Examples:
     *   array(
     *       0 => true,        // boolean
     *       1 => 3,           // integer
     *       3 => 'five',      // string
     *       4 => new Seven()  // object
     *   )
     *   is a super-scalar.
     *
     *   array(
     *       0 => 1,           // integer
     *       1 => 3,           // integer
     *       2 => 5,           // integer
     *       3 => 7            // integer
     *   )
     *   is a uniform super-scalar.
     *
     * Advanced examples:
     *   array(
     *       '0' => 1,         // string  => integer
     *       1   => 3          // integer => integer
     *   )
     *   is a super-scalar.
     *
     *   array(
     *       0 => 'one',       // integer => string
     *       1 => 'three',     // integer => string
     *       2 => 'five',      // integer => string
     *       3 => 'seven'      // integer => string
     *   )
     *   is a uniform super-scalar.
     *
     *   array(
     *       0 => 1,           // integer => integer = scalar
     *       1 => 3,           // integer => integer = scalar
     *       2 => array(
     *                0 => 5,  // integer => integer = scalar
     *                1 => 7   // integer => integer = scalar
     *       ),                //                    = uniform super-scalar
     *       3 => 9,           // integer => integer = scalar
     *       4 => 11           // integer => integer = scalar
     *   )
     *   is a uniform super-scalar.
     *
     * @access  public
     * @return  bool
     */
    public function isUniformSuperScalar ( ); 
}
