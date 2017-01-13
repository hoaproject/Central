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

namespace Hoa\Registry\Test\Unit;

use Hoa\Registry as LUT;
use Hoa\Test;

/**
 * Class \Hoa\Registry\Test\Unit\Registry.
 *
 * Test suite of the registry.
 *
 * @copyright  Copyright © 2007-2017 Hoa community
 * @license    New BSD License
 */
class Registry extends Test\Unit\Suite
{
    public function case_no_instance()
    {
        $this
            ->exception(function () {
                new LUT();
            })
                ->isInstanceOf('Hoa\Registry\Exception');
    }

    public function case_classic()
    {
        $this
            ->given(
                $string = $this->realdom->string('a', 'z', 5),
                $key    = $this->sample($string),
                $value  = $this->sample($string),
                LUT::set($key, $value)
            )
            ->when($result = LUT::get($key))
            ->then
                ->string($result)
                    ->isEqualTo($value);
    }

    public function case_value_is_an_object()
    {
        $this
            ->given(
                $string = $this->realdom->string('a', 'z', 5),
                $object = $this->realdom->class('StdClass'),
                $key    = $this->sample($string),
                $value  = $this->sample($object),
                LUT::set($key, $value)
            )
            ->when($result = LUT::get($key))
            ->then
                ->object($result)
                    ->isInstanceOf('StdClass')
                    ->isIdenticalTo($value);
    }

    public function case_isRegistered()
    {
        $this
            ->given(
                $string = $this->realdom->string('a', 'z', 5),
                $key    = $this->sample($string),
                $value  = $this->sample($string)
            )
            ->when($result = LUT::isRegistered($key))
            ->then
                ->boolean($result)
                    ->isFalse()

            ->when(
                LUT::set($key, $value),
                $result = LUT::isRegistered($key)
            )
            ->then
                ->boolean($result)
                    ->isTrue()

            ->when(
                LUT::remove($key),
                $result = LUT::isRegistered($key)
            )
            ->then
                ->boolean($result)
                    ->isFalse();
    }

    public function case_protocol()
    {
        $this
            ->given(
                $string = $this->realdom->string('a', 'z', 5),
                $key    = $this->sample($string),
                $value  = $this->sample($string),
                LUT::set($key, $value)
            )
            ->when($result = resolve('hoa://Library/Registry#' . $key))
            ->then
                ->string($result)
                    ->isEqualTo($value);
    }

    public function case_value_protocol_and_references()
    {
        $this
            ->given(
                $string = $this->realdom->string('a', 'z', 5),
                $object = $this->realdom->class('StdClass'),
                $key    = $this->sample($string),
                $value  = $this->sample($object),
                LUT::set($key, $value)
            )
            ->when(
                $result1 = resolve('hoa://Library/Registry#' . $key),
                $result2 = LUT::get($key)
            )
            ->then
                ->object($result1)
                    ->isInstanceOf('StdClass')
                    ->isIdenticalTo($value)
                    ->isIdenticalTo($result2)

            ->given($dummy = $this->sample($string))
            ->when(
                $result1->foo = $dummy,
                $result3 = resolve('hoa://Library/Registry#' . $key)
            )
            ->then
                ->object($result1)
                    ->isIdenticalTo($result2)
                    ->isIdenticalTo($result3)

                ->string($result1->foo)
                    ->isEqualTo($dummy);
    }
}
