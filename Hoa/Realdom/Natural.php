<?php

/**
 * Hoa
 *
 *
 * @license
 *
 * New BSD License
 *
 * Copyright © 2007-2014, Ivan Enderlin. All rights reserved.
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
 * \Hoa\Realdom\Integer
 */
-> import('Realdom.Integer')

/**
 * \Hoa\Realdom\IRealdom\Nonconvex
 */
-> import('Realdom.I~.Nonconvex')

/**
 * \Hoa\Realdom\IRealdom\Finite
 */
-> import('Realdom.I~.Finite');

}

namespace Hoa\Realdom {

/**
 * Class \Hoa\Realdom\Natural.
 *
 * Realistic domain: natural.
 *
 * @author     Ivan Enderlin <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright © 2007-2014 Ivan Enderlin.
 * @license    New BSD License
 */

class          Natural
    extends    Integer
    implements IRealdom\Nonconvex,
               IRealdom\Finite {

    /**
     * Realistic domain name.
     *
     * @const string
     */
    const NAME = 'natural';

    /**
     * Realistic domain defined arguments.
     *
     * @var \Hoa\Realdom array
     */
    protected $_arguments   = array(
        'Constinteger start' => 0,
        'Constinteger step'  => 1
    );

    /**
     * Discredited values.
     *
     * @var \Hoa\Realdom\Natural array
     */
    protected $_discredited = array();



    /**
     * Reset the realistic domain.
     *
     * @access  public
     * @return  void
     */
    public function reset ( ) {

        $this->setValue(null);
        $this->_discredited = array();

        return;
    }

    /**
     * Predicate whether the sampled value belongs to the realistic domains.
     *
     * @access  protected
     * @param   mixed  $q    Sampled value.
     * @return  boolean
     */
    protected function _predicate ( $q ) {

        $q -= $this['start']->getConstantValue();

        return 0 === $q % $this['step']->getConstantValue();
    }

    /**
     * Sample one new value.
     *
     * @access  protected
     * @param   \Hoa\Math\Sampler  $sampler    Sampler.
     * @return  mixed
     */
    protected function _sample ( \Hoa\Math\Sampler $sampler ) {

        $sampled = $this->getValue();

        do {

            if(null === $sampled)
                $sampled = $this['start']->getConstantValue();
            else
                $sampled += $this['step']->getConstantValue();

        } while(true === in_array($sampled, $this->_discredited));

        return $sampled;
    }

    /**
     * Discredit a value.
     *
     * @access  public
     * @param   mixed  $value    Value to discredit.
     * @return  \Hoa\Realdom
     */
    public function discredit ( $value ) {

        if(   true  === in_array($value, $this->_discredited)
           || false === $this->predicate($value))
            return $this;

        $this->_discredited[] = $value;

        return $this;
    }

    /**
     * Get size of the domain.
     *
     * @access  public
     * @return  int
     */
    public function getSize ( ) {

        return PHP_INT_MAX;
    }
}

}
