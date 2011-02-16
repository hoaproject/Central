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
 */

namespace {

from('Hoa')

/**
 * \Hoa\Realdom\String
 */
-> import('Realdom.String');

}

namespace Hoa\Realdom {

/**
 * Class \Hoa\Realdom\Conststring.
 *
 * Realistic domain: conststring.
 *
 * @author     Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright (c) 2007, 2011 Ivan ENDERLIN.
 * @license    http://gnu.org/licenses/gpl.txt GNU GPL
 */

class Conststring extends String {

    /**
     * Realistic domain name.
     *
     * @var \Hoa\Realdom string
     */
    protected $_name  = 'conststring';

    /**
     * Constant value.
     *
     * @var \Hoa\Realdom string
     */
    protected $_value = '';



    /**
     * Construct a realistic domain.
     *
     * @access  public
     * @param   string  $string    String.
     * @return  void
     * @throw   \Hoa\Realdom\Exception
     */
    public function construct ( $string  = '' ) {

        $this->_value = $string;

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

        return $this->_value === (string) $q;
    }

    /**
     * Sample one new value.
     *
     * @access  protected
     * @return  mixed
     */
    protected function _sample ( \Hoa\Test\Sampler $sampler ) {

        return $this->_value;
    }
}

}
