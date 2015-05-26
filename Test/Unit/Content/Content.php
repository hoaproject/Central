<?php

/**
 * Hoa
 *
 *
 * @license
 *
 * New BSD License
 *
 * Copyright © 2007-2015, Hoa community. All rights reserved.
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

namespace Hoa\Mail\Test\Unit\Content;

use Mock\Hoa\Mail\Content as SUT;
use Hoa\Test;

/**
 * Class \Hoa\Mail\Test\Unit\Content\Content.
 *
 * Test suite of the content.
 *
 * @copyright  Copyright © 2007-2015 Hoa community
 * @license    New BSD License
 */
class Content extends Test\Unit\Suite
{
    public function case_content_disposition()
    {
        $this
            ->given($content = new SUT())
            ->when($result = $content['content-disposition'])
            ->then
                ->string($result)
                    ->isEqualTo('inline');
    }

    public function case_content_transfer_encoding()
    {
        $this
            ->given($content = new SUT())
            ->when($result = $content['content-transfer-encoding'])
            ->then
                ->string($result)
                    ->isEqualTo('base64');
    }

    public function case_array_access()
    {
        $this
            ->when($content = new SUT())
            ->then
                ->object($content)
                    ->isInstanceOf('ArrayAccess');
    }

    public function case_headers()
    {
        $this
            ->given(
                $name  = 'FoO',
                $value = 'Bar'
            )
            ->when($result = new SUT())
            ->then
                ->boolean(isset($result[$name]))
                    ->isFalse()

            ->when($result[$name] = $value)
            ->then
                ->boolean(isset($result[$name]))
                    ->isTrue()
                ->string($result[$name])
                    ->isEqualTo($value)

                ->let($_name = strtolower($name))
                ->boolean(isset($result[$_name]))
                    ->isTrue()
                ->string($result[$_name])
                    ->isEqualTo($value)

            ->when(function () use ($result, $name) {
                unset($result[$name]);
            })
            ->then
                ->boolean(isset($result[$name]))
                    ->isFalse();
    }

    public function case_illegal_headers()
    {
        $this
            ->given(
                $value   = $this->realdom->regex('#.*(\n|\r)+.*#'),
                $content = new SUT()
            )
            ->when(function () use ($value, $content) {
                foreach ($this->sampleMany($value, 512) as $badValue) {
                    $this
                        ->exception(function () use ($badValue, $content) {
                            $content['foo' ] = $badValue;
                        })
                            ->isInstanceOf('Hoa\Mail\Exception\Security');
                }
            });
    }

    public function case_get_headers()
    {
        $this
            ->given($content = new SUT())
            ->when($result = $content->getHeaders())
            ->then
                ->array($result)
                    ->isEqualTo([
                        'content-transfer-encoding' => 'base64',
                        'content-disposition'       => 'inline'
                    ]);
    }
}
