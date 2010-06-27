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
 * @package     Hoa_Filter
 *
 */

/**
 * Hoa_Core
 */
require_once 'Core.php';

/**
 * Hoa_Filter_Exception
 */
import('Filter.Exception');

/**
 * Hoa_Filter_Abstract
 */
import('Filter.Abstract');

/**
 * Hoa_Factory
 */
import('Factory.~');

/**
 * Class Hoa_Filter.
 *
 * Build a stack of filter.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2010 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Filter
 */

class Hoa_Filter extends Hoa_Filter_Abstract {

    /**
     * Collection of filters.
     *
     * @var Hoa_Filter array
     */
    protected $filters = array();


    /**
     * Add a filter.
     *
     * @access  public
     * @param   mixed   $filters    The filters.
     * @return  void
     * @throw   Hoa_Filter_Exception
     */
    public function addFilter ( $filters ) {

        if(!is_array($filters))
            $filters = array($filters => array());

        foreach($filters as $filter => $arguments) {

            if(is_int($filter)) {

                $filter    = $arguments;
                $arguments = array();
            }

            if(is_array($filter)) {

                $arguments = current($filter);
                $filter    = key($filter);
            }

            if(!is_array($arguments))
                $arguments = array($arguments);

            $arguments = array($arguments);

            $filter = Hoa_Factory::get('Filter', $filter, $arguments);

            if(!($filter instanceof Hoa_Filter_Abstract))
                throw new Hoa_Filter_Exception(
                    'The filter %s does not extend Hoa_Filter_Abstract.',
                    0, get_class($filter));

            if($this->filterExists(get_class($filter)))
                throw new Hoa_Filter_Exception(
                    'The filter %s already exists.',
                    1, get_class($filter));

            $this->filters[get_class($filter)] = $filter;
        }
    }

    /**
     * Check if a filter already exists or not.
     *
     * @access  public
     * @param   string  $filter    The filter.
     * @return  bool
     */
    public function filterExists ( $filter ) {

        return isset($this->filters[$filter]);
    }

    /**
     * Get filters.
     *
     * @access  protected
     * @return  array
     */
    protected function getFilters ( ) {

        return $this->filters;
    }

    /**
     * Check if a data is valid.
     *
     * @access  public
     * @param   string  $string    The string to filter.
     * @return  bool
     */
    public function filter ( $string = null ) {

        foreach($this->getFilters() as $name => $filter)
            $string = $filter->filter($string);

        return $string;
    }
}
