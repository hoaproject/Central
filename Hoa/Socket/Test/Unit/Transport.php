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

namespace Hoa\Socket\Test\Unit;

use Hoa\Socket as LUT;
use Hoa\Socket\Transport as SUT;
use Hoa\Test;

/**
 * Class \Hoa\Socket\Test\Unit\Transport.
 *
 * Test suite for the transport.
 *
 * @copyright  Copyright © 2007-2017 Hoa community
 * @license    New BSD License
 */
class Transport extends Test\Unit\Suite
{
    public function case_get_standards()
    {
        $this
            ->given($standardTransports = stream_get_transports())
            ->when($result = SUT::get())
            ->then
                ->array($result)
                    ->strictlyContainsValues($standardTransports);
    }

    public function case_get_standards_and_vendors()
    {
        $this
            ->given(
                $standardTransports = stream_get_transports(),
                $vendorTransports   = ['foo', 'bar'],
                SUT::register('foo', function () { }),
                SUT::register('bar', function () { })
            )
            ->when($result = SUT::get())
            ->then
                ->array($result)
                    ->strictlyContainsValues(
                        array_merge(
                            $standardTransports,
                            $vendorTransports
                        )
                    );
    }

    public function case_exists_standards()
    {
        $this
            ->given($this->function->stream_get_transports = ['tcp'])
            ->when($result = SUT::exists('tcp'))
            ->then
                ->boolean($result)
                    ->isTrue();
    }

    public function case_exists_standards_and_vendors()
    {
        $this
            ->given(
                $this->function->stream_get_transports = ['tcp'],
                SUT::register('foo', function () { })
            )
            ->when($result = SUT::exists('tcp'))
            ->then
                ->boolean($result)
                    ->isTrue()

            ->when($result = SUT::exists('foo'))
            ->then
                ->boolean($result)
                    ->isTrue();
    }

    public function case_not_exists_standards_and_vendors()
    {
        $this
            ->given(
                $this->function->stream_get_transports = ['tcp'],
                SUT::register('foo', function () { })
            )
            ->when($result = SUT::exists('bar'))
            ->then
                ->boolean($result)
                    ->isFalse();
    }

    public function case_exists_not_in_lower_case()
    {
        $this
            ->given($this->function->stream_get_transports = ['tcp'])
            ->when($result = SUT::exists('TcP'))
            ->then
                ->boolean($result)
                    ->isTrue();
    }

    public function case_register()
    {
        $this
            ->given(
                $oldGet    = SUT::get(),
                $transport = 'foo' . uniqid(),
                $oldExists = SUT::exists($transport)
            )
            ->when($result = SUT::register($transport, function () { }))
            ->then
                ->variable($result)
                    ->isNull()
                ->boolean($oldExists)
                    ->isFalse()
                ->boolean(SUT::exists($transport))
                    ->isTrue()
                ->integer(count(SUT::get()))
                    ->isEqualTo(count($oldGet) + 1);
    }

    public function case_get_unknown_factory()
    {
        $this
            ->given($transport = 'foo' . uniqid())
            ->when(function () use (&$result, $transport) {
                $result = SUT::getFactory($transport);
            })
            ->then
                ->object($result)
                    ->isInstanceOf('Closure')
                ->exception(function () use ($result, $transport) {
                    $result($transport . '://127.0.0.1:80');
                })
                    ->isInstanceOf('Hoa\Socket\Exception');
    }

    public function case_get_standard_factory()
    {
        $this
            ->when(function () use (&$result) {
                $result = SUT::getFactory('tcp');
            })
            ->then
                ->object($result)
                    ->isInstanceOf('Closure')
                ->object($result('tcp://127.0.0.1:80'))
                    ->isEqualTo(new LUT('tcp://127.0.0.1:80'));
    }

    public function case_get_vendor_factory()
    {
        $self = $this;

        $this
            ->given(
                $transport = 'foo',
                $factory   = function ($socketUri) use (&$called, $self, $transport) {
                    $called = true;

                    $self
                        ->string($socketUri)
                            ->isEqualTo($transport . '://bar/baz');

                    return new LUT(
                        str_replace($transport, 'tcp', $socketUri)
                    );
                },
                SUT::register($transport, $factory)
            )
            ->when(function () use (&$result, $transport) {
                $result = SUT::getFactory($transport);
            })
            ->then
                ->object($result)
                    ->isInstanceOf('Closure')
                ->object($result('foo://bar/baz'))
                    ->isEqualTo(new LUT('tcp://bar/baz'))
                ->boolean($called)
                    ->isTrue();
    }
}
