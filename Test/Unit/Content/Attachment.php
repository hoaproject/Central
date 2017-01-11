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

use Hoa\File;
use Hoa\Mail\Content\Attachment as SUT;
use Hoa\Test;

/**
 * Class \Hoa\Mail\Test\Unit\Content\Attachment.
 *
 * Test suite of the attachment content.
 *
 * @copyright  Copyright © 2007-2017 Hoa community
 * @license    New BSD License
 */
class Attachment extends Test\Unit\Suite
{
    public function case_basic()
    {
        $this
            ->given(
                $stream = new File\ReadWrite(resolve('hoa://Test/Vfs/Foo.text?type=file')),
                $stream->writeAll('foobar'),
                $name   = 'Hello.txt'
            )
            ->when($result = new SUT($stream, $name))
            ->then
                ->string($result->getFormattedContent())
                    ->isEqualTo(
                        'content-transfer-encoding: base64' . CRLF .
                        'content-disposition: attachment; filename="Hello.txt"; size=6' . CRLF .
                        'content-type: text/plain' . CRLF .
                        CRLF .
                        'Zm9vYmFy'
                    );
    }

    public function case_only_stream()
    {
        $this
            ->given(
                $filename = 'Foo.txt',
                $stream   = new File\ReadWrite(resolve('hoa://Test/Vfs/' . $filename . '?type=file')),
                $stream->writeAll('<strong>foobar</strong>')
            )
            ->when($result = new SUT($stream))
            ->then
                ->string($result['content-type'])
                    ->isEqualTo('text/plain')
                ->string($result['content-disposition'])
                    ->isEqualTo('attachment; filename="' . $filename . '"; size=23');
    }

    public function case_get_stream()
    {
        $this
            ->given(
                $filename = 'Foo.txt',
                $stream   = new File\ReadWrite(resolve('hoa://Test/Vfs/' . $filename . '?type=file')),
                $stream->writeAll('<strong>foobar</strong>')
            )
            ->when($result = new SUT($stream))
            ->then
                ->object($result->getStream())
                    ->isIdenticalTo($stream);
    }

    public function case_force_name()
    {
        $this
            ->given(
                $filename = 'Foo.txt',
                $stream   = new File\ReadWrite(resolve('hoa://Test/Vfs/' . $filename . '?type=file')),
                $stream->writeAll('<strong>foobar</strong>'),
                $name     = 'Bar.txt'
            )
            ->when($result = new SUT($stream, $name))
            ->then
                ->string($result['content-disposition'])
                    ->isEqualTo('attachment; filename="' . $name . '"; size=23');
    }

    public function case_force_name_with_quotes_inside()
    {
        $this
            ->given(
                $filename = 'Foo.txt',
                $stream   = new File\ReadWrite(resolve('hoa://Test/Vfs/' . $filename . '?type=file'))
            )
            ->when($result = new SUT($stream, 'B"a"r.txt'))
            ->then
                ->string($result['content-disposition'])
                    ->isEqualTo('attachment; filename="B-a-r.txt"; size=0');
    }

    public function case_force_mime_type()
    {
        $this
            ->given(
                $filename = 'Foo.txt',
                $stream   = new File\ReadWrite(resolve('hoa://Test/Vfs/' . $filename . '?type=file')),
                $stream->writeAll('<strong>foobar</strong>'),
                $name     = 'Bar.txt',
                $mimeType = 'text/x-test-plain'
            )
            ->when($result = new SUT($stream, $name, $mimeType))
            ->then
                ->string($result['content-type'])
                    ->isEqualTo($mimeType)
                ->string($result['content-disposition'])
                    ->isEqualTo('attachment; filename="' . $name . '"; size=23');
    }

    public function case_unknown_mime_type()
    {
        $this
            ->given(
                $stream = new File\ReadWrite(resolve('hoa://Test/Vfs/Foo.hoa-test?type=file')),
                $stream->writeAll('foobar')
            )
            ->when($result = new SUT($stream))
            ->then
                ->string($result['content-type'])
                    ->isEqualTo('application/octet-stream');
    }

    public function case_content_is_base64_encoded()
    {
        $this
            ->given(
                $text   = 'foobar',
                $stream = new File\ReadWrite(resolve('hoa://Test/Vfs/Foo.text?type=file')),
                $stream->writeAll($text)
            )
            ->when($result = new SUT($stream))
            ->then
                ->string($result['content-transfer-encoding'])
                    ->isEqualTo('base64')
                ->string($result->getFormattedContent(false))
                    ->isEqualTo(base64_encode($text));
    }
}
