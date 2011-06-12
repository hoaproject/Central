<?php

/**
 * Hoa
 *
 *
 * @license
 *
 * New BSD License
 *
 * Copyright © 2007-2011, Ivan Enderlin. All rights reserved.
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
 * @author     Ivan Enderlin <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright © 2007-2011 Ivan Enderlin.
 * @license    New BSD License
 */

class Boundinteger extends Integer {

    /**
     * Realistic domain name.
     *
     * @var \Hoa\Realdom string
     */
    protected $_name      = 'boundinteger';

    /**
     * Realistic domains defined arguments.
     *
     * @var \Hoa\Realdom array
     */
    protected $_arguments = array(
        'lower',
        'upper'
    );



    /**
     * Construct a realistic domain.
     *
     * @access  public
     * @return  void
     */
    public function construct ( ) {

        if(!isset($this['lower']))
            $this['lower'] = new Constinteger(~PHP_INT_MAX);

        if(!isset($this['upper']))
            $this['upper'] = new Constinteger( PHP_INT_MAX);

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
               && $q >= $this['lower']->getValue()
               && $q <= $this['upper']->getValue();
    }

    /**
     * Sample one new value.
     *
     * @access  protected
     * @param   \Hoa\Test\Sampler  $sampler    Sampler.
     * @return  mixed
     */
    protected function _sample ( \Hoa\Test\Sampler $sampler ) {

        return $sampler->getInteger(
            $this['lower']->sample($sampler),
            $this['upper']->sample($sampler)
        );
    }
}

}
