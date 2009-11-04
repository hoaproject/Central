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
 * @package     Hoa_Filter
 * @subpackage  Hoa_Filter_Array
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
 * Hoa_Filter
 */
import('Filter.~');

/**
 * Class Hoa_Filter_Array.
 *
 * Apply filters on an array.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2008 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Filter
 * @subpackage  Hoa_Filter_Array
 */

class Hoa_Filter_Array extends Hoa_Filter_Abstract {

    /**
     * Needed arguments.
     *
     * @var Hoa_Filter_Abstract array
     */
    protected $arguments = array(
        'filters' => 'specify an associative array of key => filter to apply.'
    );



    /**
     * Apply filters.
     * In this case, the string must be an array. If it is not an array, the
     * string will be convert to an array.
     *
     * @access  public
     * @param   string  $string    The string to filter.
     * @return  array
     * @throw   Hoa_Filter_Exception
     */
    public function filter ( $string = null ) {

        if(!is_array($string))
            $string = array($string);

        $filters    = $this->getFilterArgument('filters');
        $lastFilter = current($filters);

        foreach($filters as $key => &$filter)
            if($filter === null)
                $filter     = $lastFilter;
            else
                $lastFilter = $filters[$key];

        foreach($string as $key => &$value) {

            $add    = new Hoa_Filter();

            if(!isset($filters[$key]))
                if(isset($filters['*']))
                    $add->addFilter($filters['*']);
                else
                    continue;
            else
                $add->addFilter($filters[$key]);

            $value  = $add->filter($value);
            $add    = null;
        }

        return $string;
    }
}
