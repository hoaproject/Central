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
 * @subpackage  Hoa_Controller_Router_Pattern
 *
 */

/**
 * Hoa_Framework
 */
require_once 'Framework.php';

/**
 * Class Hoa_Controller_Router_Pattern.
 *
 * Router pattern engine. Apply pattern.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2008 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Controller
 * @subpackage  Hoa_Controller_Router_Pattern
 */

class Hoa_Controller_Router_Pattern {

    /**
     * Verify if first letter is in upper case.
     *
     * @access  public
     * @param   string  $string    String.
     * @return  bool
     */
    public function isUcFirst ( $string = '' ) {

        return $string == ucfirst($string);
    }

    /**
     * Match a string.
     *
     * @access  public
     * @param   string  $pattern        Pattern.
     * @param   string  $replacement    Replacement string.
     * @return  string
     */
    public function transform ( $pattern = '', $replacement = '' ) {

        if(empty($pattern))
            return false;

        preg_match('#^([^\(]+)?(?:\(:([\w]+)\))?(.*)?$#', $pattern, $matches);

        list(, $pre, $var, $post) = $matches;

        if(!empty($var)) {

            if($this->isUcFirst($var))
                $replacement = ucfirst($replacement);

            $return = $pre . $replacement . $post;
        }
        else
            $return = $pattern;

        return $return;
    }
}
