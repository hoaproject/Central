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

use Hoa\Core;
use Hoa\Router;
use Hoa\View;

/**
 * Class \Hoa\Dispatcher.
 *
 * Abstract dispatcher.
 *
 * @copyright  Copyright © 2007-2015 Hoa community
 * @license    New BSD License
 */
abstract class Dispatcher implements Core\Parameter\Parameterizable
{
    /**
     * Parameters.
     *
     * @var \Hoa\Core\Parameter
     */
    protected $_parameters  = null;

    /**
     * Current view.
     *
     * @var \Hoa\View\Viewable
     */
    protected $_currentView = null;

    /**
     * Kit's name.
     *
     * @var string
     */
    protected $_kit         = 'Hoa\Dispatcher\Kit';



    /**
     * Build a new dispatcher.
     *
     * @param   array   $parameters    Parameters.
     * @return  void
     */
    public function __construct(Array $parameters = [])
    {
        $this->_parameters = new Core\Parameter(
            __CLASS__,
            [
                'call' => 'main',
                'able' => 'main'
            ],
            [
                'synchronous.call' => '(:call:U:)',
                'synchronous.able' => '(:able:U:)',

                'asynchronous.call' => '(:%synchronous.call:)',
                'asynchronous.able' => '(:%synchronous.able:)Async',

                /**
                 * Router variables.
                 *
                 * 'variables.…'          => …
                 */
            ]
        );
        $this->_parameters->setParameters($parameters);

        return;
    }

    /**
     * Get parameters.
     *
     * @return  \Hoa\Core\Parameter
     */
    public function getParameters()
    {
        return $this->_parameters;
    }

    /**
     * Dispatch a router rule.
     *
     * @param   \Hoa\Router         $router    Router.
     * @param   \Hoa\View\Viewable  $view      View.
     * @return  mixed
     * @throws  \Hoa\Controller\Exception
     */
    public function dispatch(Router $router, View\Viewable $view = null)
    {
        $rule = $router->getTheRule();

        if (null === $rule) {
            $router->route();
            $rule = $router->getTheRule();
        }

        if (null === $view) {
            $view = $this->_currentView;
        } else {
            $this->_currentView = $view;
        }

        $parameters        = $this->_parameters;
        $this->_parameters = clone $this->_parameters;

        foreach ($rule[Router::RULE_VARIABLES] as $key => $value) {
            $this->_parameters->setParameter('variables.' . $key, $value);
        }

        $out = $this->resolve($rule, $router, $view);
        unset($this->_parameters);
        $this->_parameters = $parameters;

        return $out;
    }

    /**
     * Resolve the dispatch call.
     *
     * @param   array               $rule      Rule.
     * @param   \Hoa\Router         $router    Router.
     * @param   \Hoa\View\Viewable  $view      View.
     * @return  mixed
     * @throws  \Hoa\Dispatcher\Exception
     */
    abstract protected function resolve(
        Array         $rule,
        Router        $router,
        View\Viewable $view = null
    );

    /**
     * Set kit's name.
     *
     * @param   string  $kit    Kit's name.
     * @return  string
     * @throws  \Hoa\Dispatcher\Exception
     */
    public function setKitName($kit)
    {
        $old        = $this->_kit;
        $this->_kit = $kit;

        return $old;
    }

    /**
     * Get kit's name.
     *
     * @return  string
     */
    public function getKitName()
    {
        return $this->_kit;
    }
}

/**
 * Flex entity.
 */
Core\Consistency::flexEntity('Hoa\Dispatcher\Dispatcher');
