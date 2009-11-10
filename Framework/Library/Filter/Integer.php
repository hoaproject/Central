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
 * Copyright (c) 2007, 2009 Ivan ENDERLIN. All rights reserved.
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
 * @package     Hoa_Filter
 * @subpackage  Hoa_Filter_Integer
 *
 */

/**
 * Hoa_Framework
 */
require_once 'Framework.php';

/**
 * Hoa_Filter_Abstract
 */
import('Filter.Abstract');

/**
 * Class Hoa_Filter_Integer.
 *
 * Apply an integer filter.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2009 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Filter
 * @subpackage  Hoa_Filter_Integer
 */

class Hoa_Filter_Integer extends Hoa_Filter_Abstract {

    /**
     * Keep integer from string.
     *
     * @const int
     */
    const KEEP = 0;

    /**
     * Cast string to integer.
     *
     * @const int
     */
    const CAST = 1;

    /**
     * Needed arguments.
     *
     * @var Hoa_Filter_Abstract array
     */
    protected $arguments = array(
        'operation' => 'specify the type of operation : KEEP (0) or CAST (1).'
    );



    /**
     * Apply an integer filter.
     *
     * @access  public
     * @param   string  $string    The string to filter.
     * @return  string
     */
    public function filter ( $string = null ) {

        if($this->getFilterArgument('operation') == self::CAST)
            return (int) (string) $string;

        elseif($this->getFilterArgument('operation') == self::KEEP)
            return preg_replace('#[^[:digit:]]#', '', (string) $string);

        return null;
    }
}
