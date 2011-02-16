<?php

/**
 * Hoa
 *
 *
 * @license
 *
 * GNU General Public License
 *
 * This file is part of Hoa Open Accessibility.
 * Copyright (c) 2007, 2011 Ivan ENDERLIN. All rights reserved.
 *
 * HOA Open Accessibility is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * HOA Open Accessibility is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with HOA Open Accessibility; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
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
 * @license    http://gnu.org/licenses/gpl.txt GNU GPL
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
