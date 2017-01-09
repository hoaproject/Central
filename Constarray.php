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
 * Class \Hoa\Realdom\Constarray.
 *
 * Realistic domain: constarray.
 *
 * @copyright  Copyright © 2007-2017 Hoa community
 * @license    New BSD License
 */
class Constarray extends RealdomArray implements IRealdom\Constant
{
    /**
     * Realistic domain name.
     *
     * @const string
     */
    const NAME = 'constarray';

    /**
     * Realistic domain defined arguments.
     *
     * @var array
     */
    protected $_arguments = [
        'pairs'
    ];



    /**
     * Construct a realistic domain.
     *
     * @return  void
     */
    protected function construct()
    {
        $this['length'] = new Constinteger(count($this['pairs']));

        return;
    }

    /**
     * Reset realistic domain.
     *
     * @return  void
     */
    public function reset()
    {
        foreach ($this['pairs'] as $pair) {
            $pair[0]->reset();

            if (isset($pair[1])) {
                $pair[1]->reset();
            }
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
        if (!is_array($q)) {
            return false;
        }

        $count = count($q);

        if (false === $this['length']->predicate($count)) {
            return false;
        }

        $pairs = $this['pairs'];

        foreach ($q as $_key => $_value) {
            $out = false;

            foreach ($pairs as $pair) {
                $key   = $pair[0];
                $value = $pair[1];

                if (false === $key->predicate($_key)) {
                    continue;
                }

                if (false === $value->predicate($_value)) {
                    continue;
                }

                $out = true;

                break;
            }

            if (false === $out) {
                return false;
            }
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
        $out = [];

        foreach ($this['pairs'] as $pair) {
            if (!isset($pair[1])) {
                $out[] = $pair[0]->sample($sampler);

                continue;
            }

            $key       = $pair[0]->sample($sampler);
            $value     = $pair[1]->sample($sampler);
            $out[$key] = $value;
        }

        return $out;
    }

    /**
     * Get constant value.
     *
     * @return  float
     */
    public function getConstantValue()
    {
        return $this['pairs'];
    }

    /**
     * Get representation of the realistic domain.
     *
     * @return  string
     */
    public function getConstantRepresentation()
    {
        $handle = [];

        foreach ($this['pairs'] as $pair) {
            $_handle = null;

            foreach ($pair as $_pair) {
                if (null === $_handle) {
                    $_handle  = isset($pair[1]) ? 'from ' :  'to ';
                } else {
                    $_handle .= ' to ';
                }

                if (null !== $holder = $_pair->getHolder()) {
                    $_handle .= $holder->getName();
                } else {
                    $_handle .= $this->getPraspelVisitor()->visit($_pair);
                }
            }

            $handle[] = $_handle;
        }

        return '[' . implode(', ', $handle) . ']';
    }
}
