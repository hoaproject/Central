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

use Hoa\Router;
use Hoa\Test;

/**
 * Class \Hoa\Dispatcher\Test\Unit\Dispatcher.
 *
 * Test suite of the abstract dispatcher.
 *
 * @copyright  Copyright © 2007-2015 Hoa community
 * @license    New BSD License
 */
class Dispatcher extends Test\Unit\Suite
{
    public function case_getParameters()
    {
        $this
            ->given($dispatcher = new \Mock\Hoa\Dispatcher())
            ->when($result = $dispatcher->getParameters())
            ->then
                ->object($result)
                    ->isInstanceOf('Hoa\Core\Parameter');
    }

    public function case_kitname()
    {
        $this
            ->given($dispatcher = new \Mock\Hoa\Dispatcher())
            ->when($result = $dispatcher->setKitName('foo'))
            ->then
                ->string($result)
                    ->isEqualTo('Hoa\Dispatcher\Kit')

            ->when($result = $dispatcher->getKitName())
                ->string($result)
                    ->isEqualTo('foo');
    }

    public function case_dispatch_already_routed()
    {
        $this
            ->given(
                $dispatcher                          = new \Mock\Hoa\Dispatcher(),
                $parameters                          = $dispatcher->getParameters(),
                $routedRule                          = null,
                $routedRouter                        = null,
                $routedView                          = null,
                $routedParameters                    = null,
                $this->calling($dispatcher)->resolve = function ($rule, $router, $view) use (
                    &$routedRule,
                    &$routedRouter,
                    &$routedView,
                    &$routedParameters
                ) {

                    $routedRule       = $rule;
                    $routedRouter     = $router;
                    $routedView       = $view;
                    $routedParameters = $this->getParameters();

                    return;
                },
                $router = new Router\Cli(),
                $router->get('a', '(?<foo>fooo) (?<bar>baar)'),
                $router->route('fooo baar')
            )
            ->when($dispatcher->dispatch($router))
            ->then
                ->array($routedRule)
                ->object($routedRouter)
                    ->isIdenticalTo($router)
                ->variable($routedView)
                    ->isNull()
                ->object($routedParameters)
                    ->isInstanceOf('Hoa\Core\Parameter')

                ->object($dispatcher->getParameters())
                    ->isIdenticalTo($parameters)
                ->object($parameters)
                    ->isNotIdenticalTo($routedParameters)
                ->string($routedParameters->getParameter('variables.foo'))
                    ->isEqualTo('fooo')
                ->string($routedParameters->getParameter('variables.bar'))
                    ->isEqualTo('baar');
    }

    public function case_dispatch_auto_route()
    {
        $this
            ->given(
                $dispatcher                          = new \Mock\Hoa\Dispatcher(),
                $parameters                          = $dispatcher->getParameters(),
                $routedRule                          = null,
                $routedRouter                        = null,
                $routedView                          = null,
                $routedParameters                    = null,
                $this->calling($dispatcher)->resolve = function ($rule, $router, $view) use (
                    &$routedRule,
                    &$routedRouter,
                    &$routedView,
                    &$routedParameters
                ) {

                    $routedRule       = $rule;
                    $routedRouter     = $router;
                    $routedView       = $view;
                    $routedParameters = $this->getParameters();

                    return;
                },
                $router = new Router\Cli(),
                $router->get('a', '(?<foo>fooo) (?<bar>baar)'),
                $router->route('fooo baar'),
                $theRule = $router->getTheRule(),

                $router = new \Mock\Hoa\Router(),
                $this->calling($router)->getTheRule[0] = $theRule,
                $this->calling($router)->getTheRule[1] = null,
                $this->calling($router)->route         = null
            )
            ->when($dispatcher->dispatch($router))
            ->then
                ->array($routedRule)
                ->object($routedRouter)
                    ->isIdenticalTo($router)
                ->variable($routedView)
                    ->isNull()
                ->object($routedParameters)
                    ->isInstanceOf('Hoa\Core\Parameter')

                ->object($dispatcher->getParameters())
                    ->isIdenticalTo($parameters)
                ->object($parameters)
                    ->isNotIdenticalTo($routedParameters)
                ->string($routedParameters->getParameter('variables.foo'))
                    ->isEqualTo('fooo')
                ->string($routedParameters->getParameter('variables.bar'))
                    ->isEqualTo('baar');
    }

    public function case_dispatch_return()
    {
        $this
            ->given(
                $dispatcher                          = new \Mock\Hoa\Dispatcher(),
                $this->calling($dispatcher)->resolve = 42,

                $router = new Router\Cli(),
                $router->get('a', '(?<foo>foo) (?<bar>bar)'),
                $router->route('foo bar')
            )
            ->when($result = $dispatcher->dispatch($router))
            ->then
                ->integer($result)
                    ->isEqualTo(42);
    }
}
