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

namespace Hoa\Controller {

from('Hoa')

/**
 * \Hoa\Controller\Exception
 */
-> import('Controller.Exception');

}

namespace Hoa\Controller {

/**
 * Class \Hoa\Controller\Router.
 *
 * .
 *
 * @author     Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright (c) 2007-2011 Ivan ENDERLIN.
 * @license    New BSD License
 */

class Router implements \Hoa\Core\Parameterizable {

    /**
     * Rule: ID index.
     *
     * @const int
     */
    const RULE_ID         = 0;

    /**
     * Rule: pattern index.
     *
     * @const int
     */
    const RULE_PATTERN    = 1;

    /**
     * Rule: component index.
     *
     * @const int
     */
    const RULE_COMPONENT  = 2;

    /**
     * Rule: on index.
     *
     * @const int
     */
    const RULE_ON         = 3;

    /**
     * Rule: dispatcher index.
     *
     * @const int
     */
    const RULE_DISPATCHER = 4;

    /**
     * The \Hoa\Controller\Router parameters.
     *
     * @var \Hoa\Core\Parameter object
     */
    private $_parameters    = null;

    /**
     * All rules.
     *
     * @var \Hoa\Controller\Router array
     */
    protected $_rules       = array();

    /**
     * Public rules.
     *
     * @var \Hoa\Controller\Router array
     */
    protected $_publicRules = array();

    /**
     * The selected rule after routing.
     *
     * @var \Hoa\Controller\Router array
     */
    protected $_theRule     = null;

    /**
     * Many dispatchers: from id to dispatch.
     *
     * @var \Hoa\Controller\Router array
     */
    protected $_dispatchers = array();



    /**
     * Build a router.
     *
     * @access  public
     * @param   array   $parameters    Parameters.
     * @return  void
     * @throw   \Hoa\Controller\Exception
     */
    public function __construct ( Array $parameters = array() ) {

        $this->_parameters = new \Hoa\Core\Parameter(
            $this,
            array(
            ),
            array(
                'base'     => '/',
                'rewrited' => false,
                'rules'    => array()
            )
        );
        $this->setParameters($parameters);

        foreach($this->getParameter('rules') as $name => $rule) {

            if(3 > count($rule))
                throw new Exception(
                    'Rule %s must be at least a 4-uplet: [id, pattern, ' .
                    'controller, action(, extra(, dispatcher)?)?]. We have ' .
                    'a %d-uplet in the configuration file.',
                    0, array($name, count($rule)));

            @list($id,     $pattern, $controller,
                  $action, $extra,   $dispatcher) = $rule;

            if(null === $extra)
                $extra = array();

            $this->addRule(
                $id,
                $pattern,
                $controller,
                $action,
                $extra,
                $dispatcher
            );
        }

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
     * Add a rule to the router.
     *
     * @access  public
     * @param   string  $id            ID.
     * @param   string  $pattern       A regular expression.
     * @param   string  $controller    A class name, an instance or null.
     * @param   string  $action        A method name, a function name, a
     *                                 closure or null.
     * @param   array   $extra         Extra data.
     * @param   string  $dispatcher    Dispatcher ID.
     * @return  \Hoa\Controller\Router
     */
    public function addRule ( $id, $pattern, $controller = null, $action = null,
                              Array $extra = array(), $dispatcher = '_default' ) {

        $this->addPrivateRule(
            $id,
            $pattern,
            $controller,
            $action,
            $extra,
            $dispatcher
        );
        $this->_publicRules[$id] = &$this->_rules[$id];

        return $this;
    }

    /**
     * Add a private rule to the router. A private rule cannot be routed, but
     * only unrouted.
     *
     * @access  public
     * @param   string  $id            ID.
     * @param   string  $pattern       A regular expression.
     * @param   string  $controller    A class name, an instance or null.
     * @param   string  $action        A method name, a function name, a
     *                                 closure or null.
     * @param   array   $extra         Extra data.
     * @param   string  $dispatcher    Dispatcher ID.
     * @return  \Hoa\Controller\Router
     */
    public function addPrivateRule ( $id, $pattern, $controller = null,
                                     $action = null, Array $extra = array(),
                                     $dispatcher = '_default' ) {

        if($controller instanceof \Closure) {

            $action     = $controller;
            $controller = null;
        }

        if(is_string($controller))
            $controller = strtolower($controller);

        if(is_string($action))
            $action     = strtolower($action);

        $this->_rules[$id] = array(
            self::RULE_ID         => $id,
            self::RULE_PATTERN    => str_replace('#', '\#', $pattern),
            self::RULE_COMPONENT  => array_merge(
                array(
                    'controller'  => $controller,
                    'action'      => $action
                ),
                $extra
            ),
            self::RULE_ON         => null,
            self::RULE_DISPATCHER => $dispatcher
        );

        return $this;
    }

    /**
     * Add dispatchers.
     *
     * @access  public
     * @param   array   $dispatchers    From ID to dispatcher.
     * @return  \Hoa\Controller\Router
     */
    public function addDispatchers ( Array $dispatchers ) {

        foreach($dispatchers as $id => $dispatcher)
            $this->addDispatcher($id, $dispatcher);

        return $this;
    }

    /**
     * Add a dispatcher.
     *
     * @access  public
     * @param   string                     $id            ID.
     * @param   \Hoa\Controller\Dispatcher  $dispatcher    Dispatcher.
     * @return  \Hoa\Controller\Router
     */
    public function addDispatcher ( $id, Dispatcher $dispatcher ) {

        $this->_dispatchers[$id] = $dispatcher;

        return $this;
    }

    /**
     * Get all rules.
     *
     * @access  public
     * @return  array
     */
    public function getRules ( ) {

        return $this->_rules;
    }

    /**
     * Get all public rules.
     *
     * @access  public
     * @return  array
     */
    public function getPublicRules ( ) {

        return $this->_publicRules;
    }

    /**
     * Get a specific rule.
     *
     * @access  public
     * @return  array
     * @throw   \Hoa\Controller\Exception
     */
    public function getRule ( $id ) {

        if(false === $this->ruleExists($id))
            throw new Exception(
                'Rule %s does not exist.', 0, $id);

        return $this->_rules[$id];
    }

    /**
     * Get the selected rule after routing.
     *
     * @access  public
     * @return  array
     */
    public function getTheRule ( ) {

        return $this->_theRule;
    }

    /**
     * Whether a rule exists.
     *
     * @access  public
     * @param   string  $id    ID.
     * @return  bool
     */
    public function ruleExists ( $id ) {

        return isset($this->_rules[$id]);
    }

    /**
     * Find the appropriated rule.
     *
     * @access  public
     * @param   string  $uri          URI to route.
     * @param   string  $bootstrap    Bootstrap that runs the route.
     * @return  \Hoa\Controller\Router
     * @throw   \Hoa\Controller\Exception
     */
    public function route ( $uri = null, $bootstrap = null ) {

        if(null === $uri)
            if(!isset($_SERVER['REQUEST_URI'])) {

                if(!isset($_SERVER['argv'][1]))
                    throw new Exception(
                        'Cannot find the URI.', 0);

                $uri = $_SERVER['argv'][0] . '?' . ltrim($_SERVER['argv'][1], '?');
            }
            else
                $uri = ltrim($_SERVER['REQUEST_URI'], '/');

        if(null === $bootstrap)
            $bootstrap = ltrim($_SERVER['SCRIPT_NAME'], '/');

        $rewrited = (bool) $this->getParameter('rewrited');
        $base     = ltrim($this->getFormattedParameter('base'), '/');

        if(0 === preg_match('#^' . $base . '(.*)?$#', $uri, $matches))
            throw new Exception(
                'Cannot match the base %s in the URI %s.',
                1, array($base, $uri));

        if(0 === preg_match('#^' . $base . '(.*)?$#', $bootstrap, $matchees))
            throw new Exception(
                'Cannot match the base %s in the script name %s.',
                2, array($base, $bootstrap));

        $route     = ltrim($matches[1],  '/');
        $bootstrap = ltrim($matchees[1], '/');

        if(false === $rewrited)
            if(0 === preg_match('#^' . $bootstrap . '\?(.*?)$#', $route, $matches))
                $route = '';
            else
                $route = ltrim($matches[1], '/');

        $gotcha = false;

        foreach($this->getPublicRules() as $rule) {

            $pattern = ltrim($rule[self::RULE_PATTERN], '/');

            if(0 !== preg_match('#^' . $pattern . '$#i', $route, $matches)) {

                $gotcha = true;
                break;
            }
        }

        if(false === $gotcha)
            throw new Exception(
                'Cannot found an appropriated rules to route %s.', 3, $route);

        $rule[self::RULE_ON] = $route;
        array_shift($matches);
        $i = 0;

        foreach($matches as $key => $value)
            if(is_string($key)) {

                $rule[self::RULE_COMPONENT][strtolower($key)] = strtolower($value);
                unset($matches[$key]);
                unset($matches[$i]);
                ++$i;
            }

        $rule[self::RULE_COMPONENT] += $matches;
        $this->_theRule              = $rule;

        return $this;
    }

    /**
     * Unroute a rule (i.e. route()^-1).
     *
     * @access  public
     * @param   string  $id        ID of the rule.
     * @param   array   $values    Values to fill the rule.
     * @return  string
     * @throw   \Hoa\Controller\Exception
     */
    public function unroute ( $id, Array $values = array() ) {

        $rule    = $this->getRule($id);
        $pattern = $rule[self::RULE_PATTERN];
        $values  = array_merge($rule[self::RULE_COMPONENT], $values);

        $out = preg_replace_callback(
            '#\(\?\<([^>]+)>[^\)]*\)#',
            function ( Array $matches ) use ($values) {

                return $values[$matches[1]];
            },
            $pattern
        );

        $out = str_replace(
            array(
                '\.', '\\\\', '\+', '\*', '\?', '\[', '\]', '\^', '\$', '\(',
                '\)', '\{', '\}', '\=', '\!', '\<', '\>', '\|', '\:', '\-'
            ),
            array(
                '.', '\\', '+', '*', '?', '[', ']', '^', '$', '(',
                ')', '{', '}', '=', '!', '<', '>', '|', ':', '-'
            ),
            $out
        );

        return $out;
    }

    /**
     * Auto-dispatch, i.e. find the appropriate dispatcher according to the
     * selected rule from this router.
     *
     * @access  public
     * @param   \Hoa\View\Viewable  $view    View.
     * @return  void
     */
    public function autoDispatch ( \Hoa\View\Viewable $view = null ) {

        $rule = $this->getTheRule();

        if(null === $rule)
            $rule = $this->route()->getTheRule();

        $dispatcher = $rule[self::RULE_DISPATCHER];

        if(is_string($dispatcher)) {

            if(!isset($this->_dispatchers[$dispatcher]))
                throw new Exception(
                    'Cannot find the dispatcher %s associated to the rule %s.',
                    4, array($dispatcher, $rule[self::RULE_PATTERN]));

            $dispatcher = $this->_dispatchers[$dispatcher];
        }

        if(!is_object($dispatcher))
            throw new Exception(
                'Cannot find the dispatcher %s associated to the rule %s.',
                5, array($dispatcher, $rule[self::RULE_PATTERN]));

        return $dispatcher->dispatch($this, $view);
    }
}

}
