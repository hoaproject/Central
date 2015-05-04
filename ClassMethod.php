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

namespace Hoa\Dispatcher;

use Hoa\Router;
use Hoa\View;

/**
 * Class \Hoa\Dispatcher\ClassMethod.
 *
 * This class dispatches on a class/object and a method, nothing more. There is
 * no concept of controller or action, it is just _call and _able.
 *
 * @copyright  Copyright © 2007-2015 Hoa community
 * @license    New BSD License
 */
class ClassMethod extends Dispatcher
{
    /**
     * Resolve the dispatch call.
     *
     * @param   array                $rule      Rule.
     * @param   \Hoa\Router          $router    Router.
     * @param   \Hoa\View\Viewable   $view      View.
     * @return  mixed
     * @throws  \Hoa\Dispatcher\Exception
     */
    protected function resolve(
        Array $rule,
        Router $router,
        View\Viewable $view = null
    ) {
        $called     = null;
        $variables  = &$rule[Router::RULE_VARIABLES];
        $call       = isset($variables['_call'])
                          ? $variables['_call']
                          : $rule[Router::RULE_CALL];
        $able       = isset($variables['_able'])
                          ? $variables['_able']
                          : $rule[Router::RULE_ABLE];
        $rtv        = [$router, $this, $view];
        $arguments  = [];
        $reflection = null;

        $async      = $router->isAsynchronous();
        $class      = $call;
        $method     = $able;

        if (false === $async) {
            $_class  = 'synchronous.call';
            $_method = 'synchronous.able';
        } else {
            $_class  = 'asynchronous.call';
            $_method = 'asynchronous.able';
        }

        $this->_parameters->setKeyword('call', $class);
        $this->_parameters->setKeyword('able', $method);

        $class  = $this->_parameters->getFormattedParameter($_class);
        $method = $this->_parameters->getFormattedParameter($_method);

        try {
            $class = dnew($class, $rtv);
        } catch (\Exception $e) {
            throw new Exception(
                'Class %s is not found ' .
                '(method: %s, asynchronous: %s).',
                0,
                [
                    $class,
                    strtoupper($variables['_method']),
                    true === $async
                        ? 'true'
                        : 'false'
                ],
                $e
            );
        }

        $kitname = $this->getKitName();

        if (!empty($kitname) &&
            !isset($variables['_this']) ||
            !(isset($variables['_this']) &&
            ($variables['_this'] instanceof $kitname))) {
            $variables['_this'] = dnew($kitname, $rtv);
            $variables['_this']->construct();
        }

        if (!method_exists($class, $method)) {
            throw new Exception(
                'Method %s does not exist on the class %s ' .
                '(method: %s, asynchronous: %s).',
                1,
                [
                    $method,
                    get_class($class),
                    strtoupper($variables['_method']),
                    true === $async
                        ? 'true'
                        : 'false'
                ]
            );
        }

        $called     = $class;
        $reflection = new \ReflectionMethod($class, $method);

        foreach ($reflection->getParameters() as $parameter) {
            $name = strtolower($parameter->getName());

            if (true === array_key_exists($name, $variables)) {
                $arguments[$name] = $variables[$name];

                continue;
            }

            if (false === $parameter->isOptional()) {
                throw new Exception(
                    'The method %s on the class %s needs a value for ' .
                    'the parameter $%s and this value does not exist.',
                    2,
                    [$method, get_class($class), $name]
                );
            }
        }

        return $reflection->invokeArgs($called, $arguments);
    }
}
