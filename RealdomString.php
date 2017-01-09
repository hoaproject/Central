<?php

/**
 * Hoa
 *
 *
 * @license
 *
 * New BSD License
 *
 * Copyright © 2007-2017, Hoa community. All rights reserved.
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

namespace Hoa\Realdom;

use Hoa\Math;
use Hoa\Ustring;

/**
 * Class \Hoa\Realdom\RealdomString.
 *
 * Realistic domain: string.
 *
 * @copyright  Copyright © 2007-2017 Hoa community
 * @license    New BSD License
 */
class          RealdomString
    extends    Realdom
    implements IRealdom\Nonconvex,
               IRealdom\Finite
{
    /**
     * Realistic domain name.
     *
     * @const string
     */
    const NAME = 'string';

    /**
     * Realistic domain defined arguments.
     *
     * @var array
     */
    protected $_arguments   = [
        'Constinteger|Conststring codepointMin' => 0x20,
        'Constinteger|Conststring codepointMax' => 0x7e,
        'Integer                  length'
    ];

    /**
     * Discredited values.
     *
     * @var array
     */
    protected $_discredited = [];



    /**
     * Construct a realistic domain.
     *
     * @return  void
     */
    protected function construct()
    {
        if ($this['codepointMin'] instanceof Conststring) {
            $char                 = mb_substr($this['codepointMin']->getConstantValue(), 0, 1);
            $this['codepointMin'] = new Constinteger(Ustring::toCode($char));
        }

        if ($this['codepointMax'] instanceof Conststring) {
            $char                 = mb_substr($this['codepointMax']->getConstantValue(), 0, 1);
            $this['codepointMax'] = new Constinteger(Ustring::toCode($char));
        }

        return;
    }

    /**
     * Predicate whether the sampled value belongs to the realistic domains.
     *
     * @param   mixed  $q    Sampled value.
     * @return  boolean
     */
    protected function _predicate($q)
    {
        if (!is_string($q)) {
            return false;
        }

        $length = mb_strlen($q);

        if (false === $this['length']->predicate($length)) {
            return false;
        }

        if (0 === $length) {
            return true;
        }

        $split  = preg_split('#(?<!^)(?!$)#u', $q);
        $out    = true;
        $handle = 0;
        $min    = $this['codepointMin']->getConstantValue();
        $max    = $this['codepointMax']->getConstantValue();

        foreach ($split as $letter) {
            $handle = Ustring::toCode($letter);
            $out    = $out && ($min <= $handle) && ($handle <= $max);
        }

        return $out;
    }

    /**
     * Sample one new value.
     *
     * @param   \Hoa\Math\Sampler  $sampler    Sampler.
     * @return  mixed
     */
    protected function _sample(Math\Sampler $sampler)
    {
        $string = null;
        $min    = $this['codepointMin']->getConstantValue();
        $max    = $this['codepointMax']->getConstantValue();
        $length = $this['length']->sample($sampler);

        if (0 > $length) {
            return false;
        }

        for ($i = 0; $i < $length; ++$i) {
            $string .= Ustring::fromCode($sampler->getInteger(
                $min,
                $max,
                $this->_discredited
            ));
        }

        return $string;
    }

    /**
     * Discredit a value.
     *
     * @param   mixed  $value    Value to discredit.
     * @return  \Hoa\Realdom
     */
    public function discredit($value)
    {
        $_value = Ustring::toCode($value);

        if (true  === in_array($_value, $this->_discredited) ||
            false === $this->predicate($value)) {
            return $this;
        }

        $this->_discredited[] = $_value;

        return $this;
    }

    /**
     * Get size of the domain.
     *
     * @return  int
     */
    public function getSize()
    {

        // @TODO : this is only for length=1.
        return
            $this['codepointMax']->getConstantValue() -
            $this['codepointMin']->getConstantValue() -
            count($this->_discredited)                + 1;
    }
}
