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
 * Class \Hoa\Realdom\Color
 *
 * Realistic domain: color.
 *
 * @copyright  Copyright © 2007-2017 Hoa community
 * @license    New BSD License
 */
class Color extends RealdomString
{
    /**
     * Realistic domain name.
     *
     * @const string
     */
    const NAME  = 'color';

    /**
     * Regular expression to represent a valid simple color.
     *
     * @const string
     */
    const REGEX = '#^\#[a-f0-9]{3}([a-f0-9]{3})?$#i';
    /**
     * Realistic domains defined arguments.
     *
     * @var array
     */
    protected $_arguments = null;



    /**
     * Construct a realistic domain.
     *
     * @return  void
     */
    protected function construct()
    {
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
        return 0 !== preg_match(static::REGEX, $q, $m);
    }

    /**
     * Sample one new value.
     *
     * @param   \Hoa\Math\Sampler  $sampler    Sampler.
     * @return  string
     */
    protected function _sample(Math\Sampler $sampler)
    {
        if (0 === $sampler->getInteger(0, 1)) {
            return '#' . sprintf(
                '%02' . implode('%02', $this->samplePattern($sampler)),
                $sampler->getInteger(0, 255),
                $sampler->getInteger(0, 255),
                $sampler->getInteger(0, 255)
            );
        }

        return '#' . sprintf(
            '%' . implode('%', $this->samplePattern($sampler)),
            $sampler->getInteger(0, 15),
            $sampler->getInteger(0, 15),
            $sampler->getInteger(0, 15)
        );
    }

    /**
     * Sample patterns.
     *
     * @param   \Hoa\Math\Sampler  $sampler    Sampler.
     * @return  array
     */
    public function samplePattern(Math\Sampler $sampler)
    {
        switch ($sampler->getInteger(0, 2)) {
            case 0:
                return ['x', 'x', 'x'];

            case 1:
                return ['X', 'X', 'X'];

            case 2:
                return [
                    0 === $sampler->getInteger(0, 1) ? 'x' : 'X',
                    0 === $sampler->getInteger(0, 1) ? 'x' : 'X',
                    0 === $sampler->getInteger(0, 1) ? 'x' : 'X',
                ];
        }
    }
}
