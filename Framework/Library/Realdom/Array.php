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
 * @subpackage  Hoa_Realdom_Array
 *
 */

/**
 * Hoa_Realdom
 */
import('Realdom.~') and load();

/**
 * Class Hoa_Realdom_Array.
 *
 * Realistic domain: array.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2010 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Realdom
 * @subpackage  Hoa_Realdom_Array
 */

class Hoa_Realdom_Array extends Hoa_Realdom {

    /**
     * Realistic domain name.
     *
     * @var Hoa_Realdom string
     */
    protected $_name    = 'array';

    /**
     * Domains (pair => 0 (key), 1 (value) => domain disjunction).
     *
     * @var Hoa_Realdom_Array array
     */
    protected $_domains = null;

    /**
     * Length.
     *
     * @var mixed mixed
     */
    protected $_length  = 7;



    /**
     * Construct a realistic domain.
     *
     * @access  public
     * @param   array   $domains    Domains.
     * @param   mixed   $length     Length.
     * @return  void
     * @throw   Hoa_Realdom_Exception
     */
    public function construct ( Array $domains = array(), $length = 7 ) {

        $this->_domains = $domains;
        $this->_length  = $length;

        if(!is_int($length) && !($length instanceof Hoa_Realdom_Integer))
            throw new Hoa_Realdom_Exception(
                'Array needs an integer in second parameter.', 0);

        return;
    }

    /**
     * Predicate whether the sampled value belongs to the realistic domains.
     *
     * @access  public
     * @param   mixed  $q    Sampled value.
     * @return  boolean
     */
    public function predicate ( $q ) {

        if(!is_array($q))
            return false;

        foreach($this->getDomains() as $e => $pairs) {

            $dom = false;
            $ran = false;

            foreach($q as $key => $value) {

                if(isset($pairs[0]))
                    foreach($pairs[0] as $i => $domain)
                        $dom = $dom || $domain->predicate($key);

                foreach($pairs[1] as $i => $domain)
                    $ran = $ran || $domain->predicate($value);
            }

            if(true === $dom && true === $ran)
                return true;
        }

        return false;
    }

    /**
     * Sample one new value.
     *
     * @access  protected
     * @return  mixed
     * @throw   Hoa_Realdom_Exception
     */
    protected function _sample ( Hoa_Test_Sampler $sampler ) {

        $domains    = $this->getDomains();
        $pair       = $domains[$sampler->getInteger(0, count($domains) - 1)];
        $length     = $this->getLength();

        if($length instanceof Hoa_Realdom)
            $length = $length->sample($sampler);

        if(0 > $length)
            throw new Hoa_Realdom_Exception(
                'Cannot sample an array with a negative length; got %d.',
                1, $length);

        $domL       = count($pair[0]) - 1;
        $ranL       = count($pair[1]) - 1;
        $out        = array();

        if(!isset($pair[0]) || empty($pair[0]))
            for($i = 0; $i < $length; ++$i)
                $out[] = $pair[1][$sampler->getInteger(0, $ranL)]
                              ->sample($sampler);
        else
            for($i = 0; $i < $length; ++$i)
                $out[$pair[0][$sampler->getInteger(0, $domL)]
                    ->sample($sampler)] =
                     $pair[1][$sampler->getInteger(0, $ranL)]
                          ->sample($sampler);

        return $out;
    }

    /**
     * Get domains.
     *
     * @access  public
     * @return  array
     */
    public function getDomains ( ) {

        return $this->_domains;
    }

    /**
     * Get length.
     *
     * @access  public
     * @return  mixed
     */
    public function getLength ( ) {

        return $this->_length;
    }
}
