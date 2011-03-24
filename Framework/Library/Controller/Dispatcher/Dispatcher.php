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
 * \Hoa\Controller\Exception
 */
-> import('Controller.Exception')

/**
 * \Hoa\Controller\Application
 */
-> import('Controller.Application');

}

namespace Hoa\Controller\Dispatcher {

/**
 * Class \Hoa\Controller\Dispatcher.
 *
 * .
 *
 * @author     Ivan Enderlin <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright © 2007-2011 Ivan Enderlin.
 * @license    New BSD License
 */

abstract class Dispatcher implements \Hoa\Core\Parameterizable {

    /**
     * The \Hoa\Controller\Dispatcher parameters.
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
     * Build a new dispatcher.
     *
     * @access  public
     * @param   array   $parameters    Parameters.
     * @return  void
     */
    public function __construct ( Array $parameters = array() ) {

        $this->_parameters = new \Hoa\Core\Parameter(
            $this,
            array(
                'controller' => 'main',
                'action'     => 'main',
                'method'     => null
            ),
            array(
                'synchronous.file'        => 'hoa://Application/Controller/(:controller:U:).php',
                'synchronous.controller'  => '(:controller:U:)Controller',
                'synchronous.action'      => '(:action:U:)Action',

                'asynchronous.file'       => '(:%synchronous.file:)',
                'asynchronous.controller' => '(:%synchronous.controller:)',
                'asynchronous.action'     => '(:%synchronous.action:)Async'
            ),
            __CLASS__
        );
        $this->setParameters($parameters);

        return;
    }

    /**
     * Set many parameters to a class.
     *
     * @access  public
     * @param   array   $in    Parameters to set.
     * @return  void
     * @throw   \Hoa\Core\Exception
     */
    public function setParameters ( Array $in ) {

        return $this->_parameters->setParameters($this, $in);
    }

    /**
     * Get many parameters from a class.
     *
     * @access  public
     * @return  array
     * @throw   \Hoa\Core\Exception
     */
    public function getParameters ( ) {

        return $this->_parameters->getParameters($this);
    }

    /**
     * Set a parameter to a class.
     *
     * @access  public
     * @param   string  $key      Key.
     * @param   mixed   $value    Value.
     * @return  mixed
     * @throw   \Hoa\Core\Exception
     */
    public function setParameter ( $key, $value ) {

        return $this->_parameters->setParameter($this, $key, $value);
    }

    /**
     * Get a parameter from a class.
     *
     * @access  public
     * @param   string  $key    Key.
     * @return  mixed
     * @throw   \Hoa\Core\Exception
     */
    public function getParameter ( $key ) {

        return $this->_parameters->getParameter($this, $key);
    }

    /**
     * Get a formatted parameter from a class (i.e. zFormat with keywords and
     * other parameters).
     *
     * @access  public
     * @param   string  $key    Key.
     * @return  mixed
     * @throw   \Hoa\Core\Exception
     */
    public function getFormattedParameter ( $key ) {

        return $this->_parameters->getFormattedParameter($this, $key);
    }

    /**
     * Dispatch a router rule to a controller and an action (could be a class, a
     * stream, a closure, a function etc.).
     *
     * @access  public
     * @param   \Hoa\Controller\Router  $router    Router.
     * @param   \Hoa\View\Viewable      $view      View.
     * @return  mixed
     * @throw   \Hoa\Controller\Exception
     */
    public function dispatch ( \Hoa\Controller\Router $router,
                               \Hoa\View\Viewable     $view = null ) {

        $rule     = $router->getTheRule();

        if(null === $rule)
            $rule = $router->route()->getTheRule();

        if(null === $view)
            $view = $this->_currentView;
        else
            $this->_currentView = $view;

        $rule[\Hoa\Controller\Router::RULE_COMPONENT]['_this']
            = new \Hoa\Controller\Application(
                $router,
                $this,
                $view
            );

        if(!isset($_SERVER['REQUEST_METHOD'])) {

            if(!isset($_SERVER['argv']))
                throw new \Hoa\Controller\Exception(
                    'Cannot identified the request method.', 0);

            $method = null;
        }
        else
            $method = strtoupper($_SERVER['REQUEST_METHOD']);

        $this->_parameters->setKeyword($this, 'method', strtolower($method));

        return $this->resolve(
            $rule[\Hoa\Controller\Router::RULE_COMPONENT],
            $rule[\Hoa\Controller\Router::RULE_PATTERN]
        );
    }

    /**
     * Resolve the dispatch call.
     *
     * @access  protected
     * @param   array      $components    All components from the router.
     * @param   string     $pattern       Pattern (kind of ID).
     * @return  mixed
     * @throw   \Hoa\Controller\Exception
     */
    abstract protected function resolve ( Array $components, $pattern );

    /**
     * Try to know if the dispatcher is called asynchronously.
     *
     * @access  public
     * @return  bool
     */
    public function isCalledAsynchronously ( ) {

        if(!isset($_SERVER['HTTP_X_REQUESTED_WITH']))
            return false;

        return 'xmlhttprequest' == strtolower($_SERVER['HTTP_X_REQUESTED_WITH']);
    }
}

}
