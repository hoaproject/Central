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

namespace Hoa\Dispatcher\Test\Unit;

use Hoa\Dispatcher as LUT;
use Hoa\Test;

/**
 * Class \Hoa\Dispatcher\Test\Unit\Kit.
 *
 * Test suite of the kit.
 *
 * @copyright  Copyright © 2007-2015 Hoa community
 * @license    New BSD License
 */
class Kit extends Test\Unit\Suite
{
    public function case_construct()
    {
        $this
            ->given(
                $router     = new \Mock\Hoa\Router(),
                $dispatcher = new \Mock\Hoa\Dispatcher(),
                $view       = new \Mock\Hoa\View\Viewable()
            )
            ->when($result = new LUT\Kit($router, $dispatcher, $view))
            ->then
                ->object($result->router)
                    ->isIdenticalTo($router)
                ->object($result->dispatcher)
                    ->isIdenticalTo($dispatcher)
                ->object($result->view)
                    ->isIdenticalTo($view)
                ->variable($result->data)
                    ->isNull();
    }

    public function case_construct_no_view()
    {
        $this
            ->given(
                $router     = new \Mock\Hoa\Router(),
                $dispatcher = new \Mock\Hoa\Dispatcher()
            )
            ->when($result = new LUT\Kit($router, $dispatcher))
            ->then
                ->object($result->router)
                    ->isIdenticalTo($router)
                ->object($result->dispatcher)
                    ->isIdenticalTo($dispatcher)
                ->variable($result->view)
                    ->isNull()
                ->variable($result->data)
                    ->isNull();
    }
}
