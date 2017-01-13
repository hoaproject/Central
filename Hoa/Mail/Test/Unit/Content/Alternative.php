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

namespace Hoa\Mail\Test\Unit\Content;

use Hoa\Mail\Content\Alternative as SUT;
use Hoa\Mail\Content\Html;
use Hoa\Mail\Content\Text;
use Hoa\Test;

/**
 * Class \Hoa\Mail\Test\Unit\Content\Alternative.
 *
 * Test suite of the alternative content.
 *
 * @copyright  Copyright © 2007-2017 Hoa community
 * @license    New BSD License
 */
class Alternative extends Test\Unit\Suite
{
    public function case_content_type()
    {
        $this
            ->when($result = new SUT())
            ->then
                ->string($result['content-type'])
                    ->isEqualTo('multipart/alternative');
    }

    public function case_add_contents_from_constructor()
    {
        $this
            ->given(
                $content1    = new Text('foo'),
                $content2    = new Text('bar'),
                $alternative = new SUT([$content1, $content2])
            )
            ->when($result = count($alternative->getContent()))
            ->then
                ->integer($result)
                    ->isEqualTo(2);
    }

    public function case_basic()
    {
        $this
            ->given(
                $this->function->microtime = function () use (&$microtime) {
                    return $microtime = 42;
                },
                $content1    = new Text('foo'),
                $content2    = new Html('<strong>foo</strong>'),
                $alternative = new SUT([$content1, $content2])
            )
            ->when($result = $alternative->getFormattedContent())
            ->then
                ->string($result)
                    ->isEqualTo(
                        'content-type: multipart/alternative; boundary="__bndry-3d469222fa3ab341c0d491b98a8aa315"' . CRLF .
                        CRLF .

                        // Content 1.
                        '--__bndry-3d469222fa3ab341c0d491b98a8aa315' . CRLF .
                        $content1->getFormattedContent() . CRLF .

                        // Content 2.
                        '--__bndry-3d469222fa3ab341c0d491b98a8aa315' . CRLF .
                        $content2->getFormattedContent() . CRLF .

                        '--__bndry-3d469222fa3ab341c0d491b98a8aa315' .
                        '--' . CRLF
                    );
    }
}
