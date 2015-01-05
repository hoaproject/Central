<?php

/**
 * Hoa
 *
 *
 * @license
 *
 * New BSD License
 *
 * Copyright © 2007-2015, Ivan Enderlin. All rights reserved.
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
 */

namespace {

from('Hoa')

/**
 * \Hoa\Praspel\Model\Clause
 */
-> import('Praspel.Model.Clause');

}

namespace Hoa\Praspel\Model {

/**
 * Class \Hoa\Praspel\Model\Is.
 *
 * Represent the @is clause.
 *
 * @author     Ivan Enderlin <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright © 2007-2015 Ivan Enderlin.
 * @license    New BSD License
 */

class Is extends Clause {

    /**
     * Name.
     *
     * @const string
     */
    const NAME = 'is';

    /**
     * Property: pure.
     *
     * @const int
     */
    const PURE = 1;

    /**
     * Properties.
     *
     * @var \Hoa\Praspel\Model\Is int
     */
    protected $_property = 0;



    /**
     * Check if a property is declared.
     *
     * @access  public
     * @param   int  $property    Property.
     * @return  bool
     */
    public function is ( $property ) {

        return $property === ($this->_property & $property);
    }

    /**
     * Set the property value.
     *
     * @access  public
     * @param   int  $property    Property.
     * @return  int
     */
    public function setProperty ( $property ) {

        $old             = $this->_property;
        $this->_property = $property;

        return $old;
    }

    /**
     * Add a property.
     *
     * @access  public
     * @param   int  $property    Property.
     * @return  int
     */
    public function addProperty ( $property ) {

        $old              = $this->_property;
        $this->_property |= $property;

        return $old;
    }

    /**
     * Remove a property.
     *
     * @access  public
     * @param   int  $property    Property.
     * @return  int
     */
    public function removeProperty ( $property ) {

        $old              = $this->_property;
        $this->_property ^= $property;

        return $old;
    }

    /**
     * Get the property value.
     *
     * @access  public
     * @return  int
     */
    public function getProperty ( ) {

        return $this->_property;
    }

    /**
     * Get property name.
     *
     * @access  public
     * @return  string
     */
    public function getPropertyName ( ) {

        $out = array();

        if(true === $this->is(static::PURE))
            $out[] = 'pure';

        return implode(', ', $out);
    }

    /**
     * Get property value from a string.
     *
     * @access  public
     * @param   string  $property    Property name.
     * @return  int
     */
    public static function getPropertyValue ( $property ) {

        switch($property) {

            case 'pure':
                return static::PURE;
        }

        return 0;
    }
}

}
