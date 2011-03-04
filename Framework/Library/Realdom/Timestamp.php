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
 * \Hoa\Realdom
 */
-> import('Realdom.~')

/**
 * \Hoa\Realdom\Conststring
 */
-> import('Realdom.Conststring');

}

namespace Hoa\Realdom {

/**
 * Class \Hoa\Realdom\Integer.
 *
 * Realistic domain: timestamp.
 *
 * @author     Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright (c) 2007, 2011 Ivan ENDERLIN.
 * @license    http://gnu.org/licenses/gpl.txt GNU GPL
 */

class Timestamp extends Constinteger {

    /**
     * Realistic domain name.
     *
     * @var \Hoa\Realdom string
     */
    protected $_name = 'timestamp';

    /**
     * Date description. Please, see http://php.net/datetime.formats.
     *
     * @var \Hoa\Realdom\Timestamp int
     */
    protected $_date = 0;



    /**
     * Construct a realistic domain.
     *
     * @access  public
     * @param   \Hoa\Realdom\Conststring  $date    Date description.
     * @return  void
     */
    public function construct ( \Hoa\Realdom\Conststring $date = null ) {

        if(null === $date)
            $date = new \Hoa\Realdom\Conststring(date('d/m/Y H:i:s'));

        $this->_date = $date;

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

        return $q == strtotime($this->_date->getConstantValue());
    }

    /**
     * Sample one new value.
     *
     * @access  protected
     * @param   \Hoa\Test\Sampler  $sampler    Sampler.
     * @return  mixed
     */
    protected function _sample ( \Hoa\Test\Sampler $sampler ) {

        return strtotime($this->_date->sample($sampler));
    }
}

}
