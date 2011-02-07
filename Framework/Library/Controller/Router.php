<?php

/**
 * Hoa Framework
 *
 *
 * @license
 *
 * GNU General Public License
 *
 * This file is part of Hoa Open Accessibility.
 * Copyright (c) 2007, 2010 Ivan ENDERLIN. All rights reserved.
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
 * @copyright  Copyright (c) 2007, 2010 Ivan ENDERLIN.
 * @license    http://gnu.org/licenses/gpl.txt GNU GPL
 */

class Router implements \Hoa\Core\Parameterizable {

    /**
     * Rule: pattern index.
     *
     * @const int
     */
    const RULE_PATTERN    = 0;

    /**
     * Rule: component index.
     *
     * @const int
     */
    const RULE_COMPONENT  = 1;

    /**
     * Rule: on index.
     *
     * @const int
     */
    const RULE_ON         = 2;

    /**
     * Rule: dispatcher index.
     *
     * @const int
     */
    const RULE_DISPATCHER = 3;

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

            if(2 > count($rule))
                throw new Exception(
                    'Rule %s must be at least a 3-uplet: [pattern, controller, ' .
                    'action(, {extra})?]. We have a %d-uplet in the ' .
                    'configuration file.',
                    0, array($name, count($rule)));

            @list($pattern, $controller, $action, $extra, $dispatcher) = $rule;

            if(null === $extra)
                $extra = array();

            $this->addRule($pattern, $controller, $action, $extra, $dispatcher);
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
     * @param   string  $pattern       A regular expression.
     * @param   string  $controller    A class name, an instance  or null.
     * @param   string  $action        A method name, a function name, a
     *                                 closure or null.
     * @param   array   $extra         Extra data.
     * @param   string  $dispatcher    Dispatcher ID.
     * @return  \Hoa\Controller\Router
     */
    public function addRule ( $pattern, $controller = null, $action = null,
                              Array $extra = array(), $dispatcher = '_default' ) {

        if(is_string($controller))
            $controller = strtolower($controller);

        if(is_string($action))
            $action     = strtolower($action);

        $this->_rules[] = array(
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
     * Get the selected rule after routing.
     *
     * @access  public
     * @return  array
     */
    public function getTheRule ( ) {

        return $this->_theRule;
    }

    /**
     * Find the appropriated rule.
     *
     * @access  public
     * @param   string  $uri          URI to route (if null, use the
     *                                $_SERVER['REQUEST_URI'] will be used).
     * @param   string  $bootstrap    Bootstrap that runs the route (if null,
     *                                $_SERVER['SCRIPT_NAME'] will be used).
     * @return  \Hoa\Controller\Router
     * @throw   \Hoa\Controller\Exception
     */
    public function route ( $uri = null, $bootstrap = null ) {

        if(null === $uri)
            if(!isset($_SERVER['REQUEST_URI'])) {

                if(!isset($_SERVER['argv'][1]))
                    throw new \Hoa\Controller\Exception(
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

        foreach($this->getRules() as $rule) {

            $pattern = $rule[self::RULE_PATTERN];

            if(0 !== preg_match('#' . $pattern . '#i', $route, $matches)) {

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

        foreach($matches as $key => $value) {

            if(is_string($key)) {

                $rule[self::RULE_COMPONENT][strtolower($key)] = strtolower($value);
                unset($matches[$key]);
                unset($matches[$i]);
            }

            ++$i;
        }

        $rule[self::RULE_COMPONENT] += $matches;
        $this->_theRule              = $rule;

        return $this;
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
