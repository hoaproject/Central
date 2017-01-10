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

namespace Hoa\Acl\Test\Unit;

use Hoa\Acl\Service as SUT;
use Hoa\Test;

/**
 * Class \Hoa\Acl\Test\Unit\Service.
 *
 * Test suite of the service class.
 *
 * @copyright  Copyright © 2007-2017 Hoa community
 * @license    New BSD License
 */
class Service extends Test\Unit\Suite
{
    public function case_constructor()
    {
        $this
            ->given(
                $id    = 'foo',
                $label = 'bar'
            )
            ->when($result = new SUT($id, $label))
            ->then
                ->string($result->getId())
                    ->isEqualTo($id)
                ->string($result->getLabel())
                    ->isEqualTo($label);
    }

    public function case_constructor_with_default_label()
    {
        $this
            ->given($id = 'foo')
            ->when($result = new SUT($id))
            ->then
                ->string($result->getId())
                    ->isEqualTo($id)
                ->variable($result->getLabel())
                    ->isNull();
    }

    public function case_set_id()
    {
        $this
            ->given(
                $oldId   = 'foo',
                $service = new SUT($oldId),
                $id      = 'bar'
            )
            ->when($result = $this->invoke($service)->setId($id))
            ->then
                ->string($result)
                    ->isEqualTo($oldId)
                ->string($service->getId())
                    ->isEqualTo($id);
    }

    public function case_set_label()
    {
        $this
            ->given(
                $id       = 'foo',
                $oldLabel = 'bar',
                $service  = new SUT($id, $oldLabel),
                $label    = 'baz'
            )
            ->when($result = $service->setLabel($label))
            ->then
                ->string($result)
                    ->isEqualTo($oldLabel)
                ->string($service->getLabel())
                    ->isEqualTo($label);
    }
}
