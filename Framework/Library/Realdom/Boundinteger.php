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
 */

namespace {

from('Hoa')

/**
 * \Hoa\Realdom\Constinteger
 */
-> import('Realdom.Constinteger')

/**
 * \Hoa\Realdom\Integer
 */
-> import('Realdom.Integer');

}

namespace Hoa\Realdom {

/**
 * Class \Hoa\Realdom\Boundinteger.
 *
 * Realistic domain: boundinteger.
 *
 * @author     Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright (c) 2007, 2010 Ivan ENDERLIN.
 * @license    http://gnu.org/licenses/gpl.txt GNU GPL
 */

class Boundinteger extends Integer {

    /**
     * Realistic domain name.
     *
     * @var \Hoa\Realdom string
     */
    protected $_name = 'boundinteger';

    /**
     * Lower bound value.
     *
     * @var \Hoa\Realdom\Constinteger object
     */
    protected $_lower = 0;

    /**
     * Upper bound value.
     *
     * @var \Hoa\Realdom\Constinteger object
     */
    protected $_upper = 0;



    /**
     * Construct a realistic domain.
     *
     * @access  public
     * @param   \Hoa\Realdom\Constinteger  $lower    Lower bound value.
     * @param   \Hoa\Realdom\Constinteger  $upper    Upper bound value.
     * @return  void
     */
    public function construct ( Constinteger $lower = null,
                                Constinteger $upper = null ) {

        if(null === $lower)
            $lower = new Constinteger(~PHP_INT_MAX);

        if(null === $upper)
            $upper = new Constinteger( PHP_INT_MAX);

        $this->_lower = $lower;
        $this->_upper = $upper;

        return;
    }

    /**
     * Predicate whether the sampled value belongs to the realistic domains.
     *
     * @access  public
     * @param   mixed   $q    Sampled value.
     * @return  boolean
     */
    public function predicate ( $q ) {

        return    parent::predicate($q)
               && $q >= $this->getLower()->getValue()
               && $q <= $this->getUpper()->getValue();
    }

    /**
     * Sample one new value.
     *
     * @access  protected
     * @return  mixed
     */
    protected function _sample ( \Hoa\Test\Sampler $sampler ) {

        return $sampler->getInteger(
            $this->getLower()->sample($sampler),
            $this->getUpper()->sample($sampler)
        );
    }

    /**
     * Get the lower bound value.
     *
     * @access  public
     * @return  \Hoa\Realdom\Constinteger
     */
    public function getLower ( ) {

        return $this->_lower;
    }

    /**
     * Get the upper bound value.
     *
     * @access  public
     * @return  \Hoa\Realdom\Constinteger
     */
    public function getUpper ( ) {

        return $this->_upper;
    }
}

}
