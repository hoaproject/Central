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

namespace Hoa\Mime\Test\Unit;

use Hoa\File;
use Hoa\Mime as LUT;
use Hoa\Test;

/**
 * Class \Hoa\Mime\Test\Unit\Documentation.
 *
 * Test suite of the examples in the documentation.
 *
 * @copyright  Copyright © 2007-2015 Hoa community
 * @license    New BSD License
 */
class Documentation extends Test\Unit\Suite
{
    public function case_getExtensionsFromMime_text_html()
    {
        $this
            ->given($mime = 'text/html')
            ->when($extensions = LUT::getExtensionsFromMime($mime))
            ->then
                ->array($extensions)
                    ->isEqualTo([
                        0 => 'html',
                        1 => 'htm'
                    ]);
    }

    public function case_getMimeFromExtension_webm()
    {
        $this
            ->given($extension = 'webm')
            ->when($mime = LUT::getMimeFromExtension($extension))
            ->then
                ->string($mime)
                    ->isEqualTo('video/webm');
    }

    public function case_stream()
    {
        $this
            ->given($file = 'hoa://Test/Vfs/index.html')
            ->when($type = new LUT(new File\Read($file)))
            ->then
                ->string($type->getExtension())
                    ->isEqualTo('html')
                ->array($type->getOtherExtensions())
                    ->isEqualTo([
                        0 => 'htm'
                    ])
                ->string($type->getMime())
                    ->isEqualTo('text/html')
                ->boolean($type->isExperimental())
                    ->isFalse();
    }
}
