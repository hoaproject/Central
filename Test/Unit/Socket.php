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

use Hoa\Socket as SUT;
use Hoa\Test;

/**
 * Class \Hoa\Socket\Test\Unit\Socket.
 *
 * Test suite for the socket object.
 *
 * @copyright  Copyright © 2007-2017 Hoa community
 * @license    New BSD License
 */
class Socket extends Test\Unit\Suite
{
    public function case_full_domain_name()
    {
        return $this->_case_check(
            'tcp://hoa-project.net:80',
            [
                'type'      => SUT::ADDRESS_DOMAIN,
                'transport' => 'tcp',
                'address'   => 'hoa-project.net',
                'port'      => 80
            ]
        );
    }

    public function case_domain_name_without_port()
    {
        return $this->_case_check(
            'tcp://hoa-project.net',
            [
                'type'      => SUT::ADDRESS_DOMAIN,
                'transport' => 'tcp',
                'address'   => 'hoa-project.net',
                'port'      => -1
            ]
        );
    }

    public function case_full_ipv4()
    {
        return $this->_case_check(
            'tcp://12.345.67.789:80',
            [
                'type'      => SUT::ADDRESS_IPV4,
                'transport' => 'tcp',
                'address'   => '12.345.67.789',
                'port'      => 80
            ]
        );
    }

    public function case_ipv4_without_port()
    {
        return $this->_case_check(
            'tcp://12.345.67.789',
            [
                'type'      => SUT::ADDRESS_IPV4,
                'transport' => 'tcp',
                'address'   => '12.345.67.789',
                'port'      => -1
            ]
        );
    }

    public function case_ipv4_with_wildcard()
    {
        return $this->_case_check(
            'tcp://*:80',
            [
                'type'      => SUT::ADDRESS_IPV4,
                'transport' => 'tcp',
                'address'   => '0.0.0.0',
                'port'      => 80
            ]
        );
    }

    public function case_ipv4_with_wildcard_without_port()
    {
        return $this->_case_check(
            'tcp://*',
            [
                'type'      => SUT::ADDRESS_IPV4,
                'transport' => 'tcp',
                'address'   => '0.0.0.0',
                'port'      => -1
            ]
        );
    }

    public function case_full_ipv6()
    {
        return $this->_case_check(
            'tcp://[2001:0db8:85a3:0000:0000:8a2e:0370:7334]:80',
            [
                'type'      => SUT::ADDRESS_IPV6,
                'transport' => 'tcp',
                'address'   => '2001:0db8:85a3:0000:0000:8a2e:0370:7334',
                'port'      => 80
            ]
        );
    }

    public function case_ipv6_without_port()
    {
        return $this->_case_check(
            'tcp://2001:0db8:85a3:0000:0000:8a2e:0370:7334',
            [
                'type'      => SUT::ADDRESS_IPV6,
                'transport' => 'tcp',
                'address'   => '2001:0db8:85a3:0000:0000:8a2e:0370:7334',
                'port'      => -1
            ]
        );
    }

    public function case_short_ipv6()
    {
        return $this->_case_check(
            'tcp://[2001:0db8:85a3::]:80',
            [
                'type'      => SUT::ADDRESS_IPV6,
                'transport' => 'tcp',
                'address'   => '2001:0db8:85a3::',
                'port'      => 80
            ]
        );
    }

    public function case_ipv6_disabled_by_STREAM_PF_INET6()
    {
        $this
            ->given(
                $this->function->defined         = false,
                $this->function->function_exists = false
            )
            ->exception(function () {
                new SUT('tcp://[2001:0db8:85a3::]:80');
            })
                ->isInstanceOf('Hoa\Socket\Exception');
    }

    public function case_ipv6_disabled_by_AF_INET6()
    {
        $this
            ->given(
                $this->function->function_exists = true,
                $this->function->defined = function ($constantName) {
                    return 'AF_INET6' !== $constantName;
                }
            )
            ->exception(function () {
                new SUT('tcp://[2001:0db8:85a3::]:80');
            })
                ->isInstanceOf('Hoa\Socket\Exception');
    }

    public function case_full_path()
    {
        return $this->_case_check(
            'file:///Hoa/Socket',
            [
                'type'      => SUT::ADDRESS_PATH,
                'transport' => 'file',
                'address'   => '/Hoa/Socket',
                'port'      => -1
            ]
        );
    }

    public function case_no_a_URI()
    {
        $this
            ->exception(function () {
                new SUT('foobar');
            })
                ->isInstanceOf('Hoa\Socket\Exception');
    }

    public function case_has_port()
    {
        $this
            ->when($result = new SUT('tcp://hoa-project.net:80'))
            ->then
                ->boolean($result->hasPort())
                    ->isTrue();
    }

    public function case_has_no_port()
    {
        $this
            ->when($result = new SUT('tcp://hoa-project.net'))
            ->then
                ->boolean($result->hasPort())
                    ->isFalse();
    }

    public function case_is_not_secured()
    {
        $this
            ->when($result = new SUT('tcp://hoa-project.net:80'))
            ->then
                ->boolean($result->isSecured())
                    ->isFalse();
    }

    protected function _case_check($uri, $expect)
    {
        $this
            ->given(
                $this->constant->STREAM_PF_INET6       = true,
                $this->function->stream_get_transports = ['tcp', 'file']
            )
            ->when($result = new SUT($uri))
            ->then
                ->integer($result->getAddressType())
                    ->isEqualTo($expect['type'])
                ->string($result->getTransport())
                    ->isEqualTo($expect['transport'])
                ->string($result->getAddress())
                    ->isEqualTo($expect['address'])
                ->integer($result->getPort())
                    ->isEqualTo($expect['port']);
    }
}
