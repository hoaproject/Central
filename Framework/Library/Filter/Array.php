<?php

/**
 * Hoa
 *
 *
 * @license
 *
 * New BSD License
 *
 * Copyright (c) 2007-2011, Ivan Enderlin. All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *     * Redistributions of source code must retain the above copyright
 *       notice, this list of conditions and the following disclaimer.
 *     * Redistributions in binary form must reproduce the above copyright
 *       notice, this list of conditions and the following disclaimer in the
 *       documentation and/or other materials provided with the distribution.
 *     * Neither the name of the Hoa nor the names of its contributors may be
 *       used to endorse or promote products derived from this software without
 *       specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDERS AND CONTRIBUTORS BE
 * LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 *
 * @category    Framework
 * @package     Hoa_Filter
 * @subpackage  Hoa_Filter_Array
 *
 */

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
 * @copyright   Copyright (c) 2007, 2011 Ivan ENDERLIN.
 * @license     New BSD License
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
