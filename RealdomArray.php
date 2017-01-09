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
 * Class \Hoa\Realdom\RealdomArray.
 *
 * Realistic domain: array.
 * Supported constraints: sorted, rsorted, ksorted, krsorted, unique.
 *
 * @copyright  Copyright © 2007-2017 Hoa community
 * @license    New BSD License
 */
class RealdomArray extends Realdom
{
    /**
     * Realistic domain name.
     *
     * @const string
     */
    const NAME = 'array';

    /**
     * Realistic domain defined arguments.
     *
     * @var array
     */
    protected $_arguments = [
        'Constarray pairs',
        'Integer    length'
    ];



    /**
     * Constructor of the realistic domain.
     *
     * @return  void
     */
    protected function construct()
    {
        parent::construct();

        $this->adjustLength();

        return;
    }

    /**
     * Reset the realistic domain.
     *
     * @return  void
     */
    public function reset()
    {
        $this->resetArguments();

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

        $pairs       = $this['pairs']['pairs'];
        $out         = 0 === $count;
        $constraints = &$this->getConstraints();

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

            if (isset($constraints['key'])) {
                $out = true;

                foreach ($constraints['key'] as $kPair) {
                    $key   = $kPair[0];
                    $value = $kPair[1];

                    if (false === $key->predicate($_key)) {
                        continue;
                    }

                    $out = $value->predicate($_value) && $out;
                }
            }

            if (false === $out) {
                return $out;
            }
        }

        if (true === $this->is('unique') &&
            $count !== count(array_unique($q, SORT_REGULAR))) {
            return false;
        }

        if (true === $this->is('sorted')) {
            $previous = array_shift($q);

            foreach ($q as $value) {
                if ($previous > $value) {
                    return false;
                }

                $previous = $value;
            }
        }

        if (true === $this->is('rsorted')) {
            $previous = array_shift($q);

            foreach ($q as $value) {
                if ($previous < $value) {
                    return false;
                }

                $previous = $value;
            }
        }

        if (true === $this->is('ksorted')) {
            reset($q);
            $previous = key($q);

            foreach ($q as $key => $_) {
                if ($previous > $key) {
                    return false;
                }

                $previous = $key;
            }
        }

        if (true === $this->is('krsorted')) {
            reset($q);
            $previous = key($q);

            foreach ($q as $key => $_) {
                if ($previous < $key) {
                    return false;
                }

                $previous = $key;
            }
        }

        return $out;
    }

    /**
     * Sample one new value.
     *
     * @param   \Hoa\Math\Sampler  $sampler    Sampler.
     * @return  mixed
     * @throws  \Hoa\Realdom\Exception\Inconsistent
     */
    protected function _sample(Math\Sampler $sampler)
    {
        $length = $this['length']->sample($sampler);

        if (0 > $length) {
            return false;
        }

        $constraints = &$this->getConstraints();
        $out         =  [];
        $pairs       =  [];
        $unique      =  true === $this->is('unique');

        foreach ($this['pairs']['pairs'] as $pair) {
            $key   = clone $pair[0];
            $value = clone $pair[1];
            $i     = 0;

            foreach ($key as $realdom) {
                if ($realdom instanceof IRealdom\Finite &&
                    $length > $realdom->getSize()) {
                    unset($key[$i--]);
                }

                ++$i;
            }

            if (0 >= count($key)) {
                continue;
            }

            if (true === $unique) {
                $i = 0;

                foreach ($value as $realdom) {
                    if ($realdom instanceof IRealdom\Finite &&
                        $length > $realdom->getSize()) {
                        unset($value[$i--]);
                    }

                    ++$i;
                }

                if (0 >= count($value)) {
                    continue;
                }
            }

            $pairs[] = [$key, $value];
        }

        if (isset($constraints['key'])) {
            foreach ($constraints['key'] as $kPair) {
                $_key   = $kPair[0]->sample($sampler);
                $_value = $kPair[1]->sample($sampler);

                foreach ($pairs as $pair) {
                    $keyRealdoms   = [];
                    $valueRealdoms = [];

                    foreach ($pair[0] as $realdom) {
                        if (true === $realdom->predicate($_key)) {
                            $keyRealdoms[] = $realdom;
                        }
                    }

                    foreach ($pair[1] as $realdom) {
                        if (true === $realdom->predicate($_value)) {
                            $valueRealdoms[] = $realdom;
                        }
                    }

                    if (empty($keyRealdoms) || empty($valueRealdoms)) {
                        continue;
                    }

                    foreach ($keyRealdoms as $realdom) {
                        if ($realdom instanceof IRealdom\Nonconvex) {
                            $realdom->discredit($_key);
                        }
                    }

                    if (false === $unique) {
                        continue;
                    }

                    foreach ($valueRealdoms as $realdom) {
                        if ($realdom instanceof IRealdom\Nonconvex) {
                            $realdom->discredit($_value);
                        }
                    }
                }

                $out[$_key] = $_value;
            }
        }

        $count = count($pairs) - 1;

        for ($i = 0, $length -= count($out); $i < $length; ++$i) {
            if (0 > $count) {
                throw new Exception\Inconsistent(
                    'There is no enought data to sample.',
                    0
                );
            }

            $pair  = $pairs[$sampler->getInteger(0, $count)];
            $key   = $pair[0]->sample($sampler);
            $value = $pair[1]->sample($sampler);

            foreach ($pairs as $p => $_pair) {
                $j = 0;

                foreach ($_pair[0] as $realdom) {
                    if (!($realdom instanceof IRealdom\Nonconvex) ||
                        false === $realdom->predicate($key)) {
                        continue;
                    }

                    $realdom->discredit($key);

                    if ($realdom instanceof IRealdom\Finite) {
                        if (0 === $realdom->getSize()) {
                            unset($_pair[0][$j--]);
                        }
                    }

                    ++$j;
                }

                if (0 === count($_pair[0])) {
                    unset($pairs[$p]);
                    --$count;
                }

                if (false === $unique) {
                    continue;
                }

                foreach ($_pair[1] as $realdom) {
                    if ($realdom instanceof IRealdom\Nonconvex &&
                        true === $realdom->predicate($value)) {
                        $realdom->discredit($value);
                    }
                }
            }

            $out[$key] = $value;
        }

        ksort($out);

        /*
        if(true === $this->is('sorted'))
            asort($out);

        if(true === $this->is('rsorted'))
            arsort($out);

        if(true === $this->is('ksorted'))
            ksort($out);

        if(true === $this->is('krsorted'))
            krsort($out);
        */

        return $out;
    }

    /**
     * Propagate constraints.
     *
     * @param   string  $type           Type.
     * @param   int     $index          Index.
     * @param   array   $constraints    Constraints.
     * @return  void
     * @throws  \Hoa\Realdom\Exception\Inconsistent
     */
    protected function _propagateConstraints(
        $type,
        $index,
        array &$constraints
    ) {
        if ('key' !== $type) {
            return;
        }

        if (!isset($constraints['key'][$index])) {
            return;
        }

        $pairs  = $this['pairs']['pairs'];
        $_pair  = &$constraints['key'][$index];
        $key    = &$_pair[0][0];
        $values = $_pair[1];

        if (!($key instanceof IRealdom\Constant)) {
            return;
        }

        $_key = $key->getConstantValue();

        foreach ($pairs as $p => $pair) {
            $i = 0;

            foreach ($pair[0] as $realdom) {
                if (false === $realdom->predicate($_key)) {
                    unset($pair[0][$i--]);
                }

                ++$i;
            }

            if (0 === count($pair[0])) {
                unset($pairs[$p]);
            }
        }

        if (0 === count($pairs)) {
            throw new Exception\Inconsistent(
                'The constraint %s[%s] = %s is not consistent because the ' .
                'key %2$s does not satisfy the array description.',
                1,
                [
                    $this->getHolder()->getName(),
                    $_key,
                    $this->getPraspelVisitor()->visit($values)
                ]
            );
        }

        $this->adjustLength();

        $minSize = count($constraints['key']);
        $length  = $this['length'];

        if ($length instanceof IRealdom\Constant) {
            if ($minSize > $length->getConstantValue()) {
                throw new Exception\Inconsistent(
                    'There is too many declared keys compared to the array ' .
                    'size (%d > %d).',
                    2,
                    [$minSize, $length->getConstantValue()]
                );
            }
        } elseif (
            $length instanceof IRealdom\Interval &&
            $minSize > $length->getUpperBound()
        ) {
            throw new Exception\Inconsistent(
                'There is too many declared keys compared to the array size ' .
                '(%d ∉ %s).',
                3,
                [$minSize, $this->getPraspelVisitor()->visit($length)]
            );
        }


        return;
    }

    /**
     * Adjust size according to pairs.
     *
     * @return  void
     * @throws  \Hoa\Realdom\Exception\Inconsistent
     */
    protected function adjustLength()
    {
        $pairs   = $this['pairs']['pairs'];
        $length  = $this['length'];
        $maxSize = -1;

        foreach ($pairs as $pair) {
            foreach ($pair[0] as $realdom) {
                if ($realdom instanceof IRealdom\Finite) {
                    $size = $realdom->getSize();

                    if ($maxSize < $size) {
                        $maxSize = $size;
                    }
                }
            }
        }

        if (-1 === $maxSize) {
            return;
        }

        if ($length instanceof IRealdom\Constant) {
            if ($maxSize < $length->getConstantValue()) {
                throw new Exception\Inconsistent(
                    'There is no enough key to sample (%d < %d).',
                    4,
                    [$maxSize, $length->getConstantValue()]
                );
            }
        }

        if ($length instanceof IRealdom\Interval) {
            if ($maxSize < $length->getLowerBound()) {
                throw new Exception\Inconsistent(
                    'There is no enough key to sample (%d ∉ %s).',
                    5,
                    [$maxSize, $this->getPraspelVisitor()->visit($length)]
                );
            }

            if ($maxSize < $length->getUpperBound()) {
                $length->reduceRightTo($maxSize);
            }
        }

        return;
    }
}
