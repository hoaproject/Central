<?php

/**
 * Hoa
 *
 *
 * @license
 *
 * New BSD License
 *
 * Copyright (c) 2007-2011, Ivan Enderlin. All rights reserved.
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

namespace {

from('Hoa')

/**
 * \Hoa\Controller\Exception
 */
-> import('Controller.Exception')

/**
 * \Hoa\Controller\Dispatcher
 */
-> import('Controller.Dispatcher.~');

}

namespace Hoa\Controller\Dispatcher {

/**
 * Class \Hoa\Controller\Dispatcher\Basic.
 *
 * .
 *
 * @author     Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright (c) 2007, 2011 Ivan ENDERLIN.
 * @license    New BSD License
 */

class Basic extends Dispatcher {

    /**
     * Resolve the dispatch call.
     *
     * @access  protected
     * @param   array      $components    All components from the router.
     * @param   string     $pattern       Pattern (kind of ID).
     * @return  mixed
     * @throw   \Hoa\Controller\Exception
     */
    protected function resolve ( Array $components, $pattern ) {

        $called     = null;
        $controller = $components['controller'];
        $action     = $components['action'];
        $arguments  = array();
        $reflection = null;
        $method     = strtoupper($this->_parameters->getKeyword($this, 'method'));

        if($action instanceof \Closure) {

            $called     = $action;
            $reflection = new \ReflectionMethod($action, '__invoke');

            foreach($reflection->getParameters() as $i => $parameter) {

                $name = $parameter->getName();

                if(isset($components[$name])) {

                    $arguments[$name] = $components[$name];
                    continue;
                }

                if(false === $parameter->isOptional())
                    throw new \Hoa\Controller\Exception(
                        'The closured action for the rule with pattern %s needs ' .
                        'a value for the parameter $%s and this value does not ' .
                        'exist.',
                        0, array($pattern, $parameter->getName()));
            }
        }
        elseif(null === $controller && is_string($action)) {

            $reflection = new \ReflectionFunction($action);

            foreach($reflection->getParameters() as $i => $parameter) {

                $name = $parameter->getName();

                if(isset($components[$name])) {

                    $arguments[$name] = $components[$name];
                    continue;
                }

                if(false === $parameter->isOptional())
                    throw new \Hoa\Controller\Exception(
                        'The functional action for the rule with pattern %s needs ' .
                        'a value for the parameter $%s and this value does not ' .
                        'exist.',
                        1, array($pattern, $parameter->getName()));
            }
        }
        else {

            $async = $this->isCalledAsynchronously();

            if(!is_object($controller)) {

                if(false === $async) {

                    $_file       = 'synchronous.file';
                    $_controller = 'synchronous.controller';
                    $_action     = 'synchronous.action';
                }
                else {

                    $_file       = 'asynchronous.file';
                    $_controller = 'asynchronous.controller';
                    $_action     = 'asynchronous.action';
                }

                $this->_parameters->setKeyword($this, 'controller', $controller);
                $this->_parameters->setKeyword($this, 'action',     $action);

                $file       = $this->getFormattedParameter($_file);
                $controller = $this->getFormattedParameter($_controller);
                $action     = $this->getFormattedParameter($_action);

                if(!file_exists($file))
                    throw new \Hoa\Controller\Exception(
                        'File %s is not found (method: %s, asynchronous: %s).',
                        2, array($file, $method,
                                 true === $async ? 'true': 'false'));

                require_once $file;

                if(!class_exists($controller))
                    throw new \Hoa\Controller\Exception(
                        'Controller %s is not found in the file %s ' .
                        '(method: %s, asynchronous: %s).',
                        3, array($controller, $file, $method,
                                 true === $async ? 'true': 'false'));

                if(is_subclass_of($controller, '\Hoa\Controller\Application')) {

                    $application = $components['_this'];
                    $controller  = new $controller(
                        $application->router,
                        $application->dispatcher,
                        $application->view
                    );
                    $controller->construct();
                }
                else
                    $controller = new $controller();
            }

            if(!method_exists($controller, $action))
                throw new \Hoa\Controller\Exception(
                    'Action %s does not exist on the controller %s ' .
                    '(method: %s, asynchronous: %s).',
                    5, array($action, get_class($controller), $method,
                             true === $async ? 'true': 'false'));

            $called     = $controller;
            $reflection = new \ReflectionMethod($controller, $action);

            foreach($reflection->getParameters() as $i => $parameter) {

                $name = $parameter->getName();

                if(isset($components[$name])) {

                    $arguments[$name] = $components[$name];

                    continue;
                }

                if(false === $parameter->isOptional())
                    throw new \Hoa\Controller\Exception(
                        'The action %s on the controller %s needs a value for ' .
                        'the parameter $%s and this value does not exist.',
                        6, array($action, get_class($controller),
                                 $parameter->getName()));
            }
        }

        if($reflection instanceof \ReflectionFunction)
            $return = $reflection->invokeArgs($arguments);
        elseif($reflection instanceof \ReflectionMethod)
            $return = $reflection->invokeArgs($called, $arguments);

        return $return;
    }
}

}
