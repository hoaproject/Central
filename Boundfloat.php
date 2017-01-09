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

/**
 * Class \Hoa\Realdom\Boundfloat.
 *
 * Realistic domain: boundfloat.
 *
 * @copyright  Copyright © 2007-2017 Hoa community
 * @license    New BSD License
 */
class Boundfloat extends RealdomFloat implements IRealdom\Interval
{
    /**
     * Realistic domain name.
     *
     * @const string
     */
    const NAME = 'boundfloat';

    /**
     * Realistic domains defined arguments.
     *
     * @var array
     */
    protected $_arguments = [
        'Constfloat lower' => PHP_FLOAT_MIN,
        'Constfloat upper' => PHP_FLOAT_MAX
    ];



    /**
     * Construct a realistic domain.
     *
     * @return  void
     */
    protected function construct()
    {
        $lower = $this['lower']->getConstantValue();
        $upper = $this['upper']->getConstantValue();

        if ($lower > $upper) {
            throw new Exception\InvalidArgument(
                '$lower must be strictly lower than $upper; given %d and %d.',
                0,
                [$lower, $upper]
            );
        }
    }

    /**
     * Predicate whether the sampled value belongs to the realistic domains.
     *
     * @param   mixed   $q    Sampled value.
     * @return  boolean
     */
    protected function _predicate($q)
    {
        return
            parent::_predicate($q) &&
            $q >= $this['lower']->getConstantValue() &&
            $q <= $this['upper']->getConstantValue();
    }

    /**
     * Sample one new value.
     *
     * @param   \Hoa\Math\Sampler  $sampler    Sampler.
     * @return  mixed
     */
    protected function _sample(Math\Sampler $sampler)
    {
        return $sampler->getFloat(
            $this['lower']->sample($sampler),
            $this['upper']->sample($sampler)
        );
    }

    /**
     * Get lower bound of the domain.
     *
     * @return  \Hoa\Realdom
     */
    public function getLowerBound()
    {
        return $this['lower']->getConstantValue();
    }

    /**
     * Get upper bound of the domain.
     *
     * @return  \Hoa\Realdom
     */
    public function getUpperBound()
    {
        return $this['upper']->getConstantValue();
    }

    /**
     * Reduce the lower bound.
     *
     * @param   mixed  $value    Value.
     * @return  bool
     */
    public function reduceRightTo($value)
    {
        $lower = $this['lower']->getConstantValue();
        $upper = min($this['upper']->getConstantValue(), $value);

        if ($lower > $upper) {
            return false;
        }

        $this['upper'] = new Constinteger($value);

        return true;
    }

    /**
     * Reduce the upper bound.
     *
     * @param   int  $value    Value.
     * @return  bool
     */
    public function reduceLeftTo($value)
    {
        $lower = max($this['lower']->getConstantValue(), $value);
        $upper = $this['upper']->getConstantValue();

        if ($lower > $upper) {
            return false;
        }

        $this['lower'] = new Constinteger($value);

        return true;
    }
}
