<?php

/**
 * Hoa
 *
 *
 * @license
 *
 * New BSD License
 *
 * Copyright © 2007-2011, Ivan Enderlin. All rights reserved.
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
 * \Hoa\Dispatcher\Exception
 */
-> import('Dispatcher.Exception')

/**
 * \Hoa\Router
 */
-> import('Router.~');

}

namespace Hoa\Dispatcher {

/**
 * Class \Hoa\Dispatcher.
 *
 * Abstract dispatcher.
 *
 * @author     Ivan Enderlin <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright © 2007-2011 Ivan Enderlin.
 * @license    New BSD License
 */

abstract class Dispatcher implements \Hoa\Core\Parameter\Parameterizable {

    /**
     * Parameters.
     *
     * @var \Hoa\Core\Parameter object
     */
    protected $_parameters  = null;

    /**
     * Current view.
     *
     * @var \Hoa\View\Viewable object
     */
    protected $_currentView = null;

    /**
     * Kit's name.
     *
     * @var \Hoa\Dispatcher string
     */
    protected $_kit         = 'Hoa\Dispatcher\Kit';



    /**
     * Build a new dispatcher.
     *
     * @access  public
     * @param   array   $parameters    Parameters.
     * @return  void
     */
    public function __construct ( Array $parameters = array() ) {

        $this->_parameters = new \Hoa\Core\Parameter(
            __CLASS__,
            array(
                'controller' => 'main',
                'action'     => 'main',
                'method'     => null
            ),
            array(
                'synchronous.controller'  => 'Application\Controller\(:controller:U:)',
                'synchronous.action'      => '(:action:U:)Action',

                'asynchronous.controller' => '(:%synchronous.controller:)',
                'asynchronous.action'     => '(:%synchronous.action:)Async',

                /**
                 * Router variables.
                 *
                 * 'variables.…'          => …
                 */
            )
        );
        $this->_parameters->setParameters($parameters);

        return;
    }

    /**
     * Get parameters.
     *
     * @access  public
     * @return  \Hoa\Core\Parameter
     */
    public function getParameters ( ) {

        return $this->_parameters;
    }

    /**
     * Dispatch a router rule.
     *
     * @access  public
     * @param   \Hoa\Router         $router    Router.
     * @param   \Hoa\View\Viewable  $view      View.
     * @return  mixed
     * @throw   \Hoa\Controller\Exception
     */
    public function dispatch ( \Hoa\Router        $router,
                               \Hoa\View\Viewable $view = null ) {

        $rule     = $router->getTheRule();

        if(null === $rule)
            $rule = $router->route();

        if(null === $view)
            $view = $this->_currentView;
        else
            $this->_currentView = $view;

        $parameters        = $this->_parameters;
        $this->_parameters = clone $this->_parameters;

        foreach($rule[\Hoa\Router::RULE_VARIABLES] as $key => $value)
            $this->_parameters->setParameter('variables.' . $key, $value);

        $kit = dnew($this->getKitName(), array($router, $this, $view));

        if(!($kit instanceof Kit))
            throw new Exception(
                'Your kit %s must extend Hoa\Dispatcher\Kit.',
                0, $this->getKitName());

        $rule[\Hoa\Router::RULE_VARIABLES]['_this'] = $kit;
        $this->_parameters->setKeyword('method', $router->getMethod());

        $out               = $this->resolve($rule);
        unset($this->_parameters);
        $this->_parameters = $parameters;

        return $out;
    }

    /**
     * Resolve the dispatch call.
     *
     * @access  protected
     * @param   array      $rule    Rule.
     * @return  mixed
     * @throw   \Hoa\Dispatcher\Exception
     */
    abstract protected function resolve ( Array $rule );

    /**
     * Set kit's name.
     *
     * @access  public
     * @param   string  $kit    Kit's name.
     * @return  string
     * @throw   \Hoa\Dispatcher\Exception
     */
    public function setKitName ( $kit ) {

        $old        = $this->_kit;
        $this->_kit = $kit;

        return $old;
    }

    /**
     * Get kit's name.
     *
     * @access  public
     * @return  string
     */
    public function getKitName ( ) {

        return $this->_kit;
    }
}

}
