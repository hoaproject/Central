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
use Hoa\Socket\Node as SUT;
use Hoa\Test;

/**
 * Class \Hoa\Socket\Test\Unit\Node.
 *
 * Test suite for the node object.
 *
 * @copyright  Copyright © 2007-2017 Hoa community
 * @license    New BSD License
 */
class Node extends Test\Unit\Suite
{
    public function case_constructor()
    {
        $this
            ->given(
                $id     = 'foobar',
                $socket = fopen(__FILE__, 'r'),
                $this->mockGenerator->orphanize('__construct'),
                $connection = new \Mock\Hoa\Socket\Connection()
            )
            ->when($result = new SUT($id, $socket, $connection))
            ->then
                ->object($result)
                ->string($result->getId())
                    ->isEqualTo($id)
                ->resource($result->getSocket())
                    ->isIdenticalTo($socket)
                ->object($result->getConnection())
                    ->isIdenticalTo($connection);
    }

    public function case_set_encryption_type()
    {
        $this
            ->given(
                $this->mockGenerator->orphanize('__construct'),
                $node = new \Mock\Hoa\Socket\Node()
            )
            ->when($result = $node->setEncryptionType(LUT\Server::ENCRYPTION_SSLv23))
            ->then
                ->variable($result)
                    ->isNull()

            ->when($result = $node->setEncryptionType(LUT\Server::ENCRYPTION_TLS))
            ->then
                ->integer($result)
                    ->isEqualTo(LUT\Server::ENCRYPTION_SSLv23);
    }

    public function case_get_encryption_type()
    {
        $this
            ->given(
                $encryption = LUT\Server::ENCRYPTION_TLS,
                $this->mockGenerator->orphanize('__construct'),
                $node = new \Mock\Hoa\Socket\Node(),
                $node->setEncryptionType($encryption)
            )
            ->when($result = $node->getEncryptionType())
            ->then
                ->integer($result)
                    ->isEqualTo($encryption);
    }
}
