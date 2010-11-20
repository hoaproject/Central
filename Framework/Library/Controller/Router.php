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
 *
 *
 * @category    Framework
 * @package     Hoa_Controller
 * @subpackage  Hoa_Controller_Router
 *
 */

/**
 * Hoa_Core
 */
require_once 'Core.php';

/**
 * Hoa_Controller_Exception
 */
import('Controller.Exception');

/**
 * Class Hoa_Controller_Router.
 *
 * .
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2010 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Controller
 * @subpackage  Hoa_Controller_Router
 */

class Hoa_Controller_Router implements Hoa_Core_Parameterizable {

    const RULE_PATTERN   = 0;
    const RULE_COMPONENT = 1;
    const RULE_ON        = 2;

    /**
     * The Hoa_Controller_Router parameters.
     *
     * @var Hoa_Core_Parameter object
     */
    private $_parameters = null;

    /**
     * All rules.
     *
     * @var Hoa_Controller_Router array
     */
    protected $_rules    = array();

    /**
     * The selected rule after routing.
     *
     * @var Hoa_Controller_Router array
     */
    protected $_theRule  = null;



    /**
     * Build a router.
     *
     * @access  public
     * @param   array   $parameters    Parameters.
     * @return  void
     * @throw   Hoa_Controller_Exception
     */
    public function __construct ( Array $parameters = array() ) {

        $this->_parameters = new Hoa_Core_Parameter(
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
                throw new Hoa_Controller_Exception(
                    'Rule %s must be at least a 3-uplet: [pattern, controller, ' . 
                    'action(, {extra})?]. We have a %d-uplet in the ' .
                    'configuration file.',
                    0, array($name, count($rule)));

            @list($pattern, $controller, $action, $extra) = $rule;

            if(null === $extra)
                $extra = array();

            $this->addRule($pattern, $controller, $action, $extra);
        }

        return;
    }

    /**
     * Set many parameters to a class.
     *
     * @access  public
     * @param   array   $in    Parameters to set.
     * @return  void
     * @throw   Hoa_Exception
     */
    public function setParameters ( Array $in ) {

        return $this->_parameters->setParameters($this, $in);
    }

    /**
     * Get many parameters from a class.
     *
     * @access  public
     * @return  array
     * @throw   Hoa_Exception
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
     * @throw   Hoa_Exception
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
     * @throw   Hoa_Exception
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
     * @throw   Hoa_Exception
     */
    public function getFormattedParameter ( $key ) {

        return $this->_parameters->getFormattedParameter($this, $key);
    }

    /**
     * Add a rule to the router.
     *
     * @access  public
     * @param   string
     * @param   string
     * @param   string
     * @param   array
     * @return  Hoa_Controller_Router
     */
    public function addRule ( $pattern, $controller, $action,
                              Array $extra = array() ) {

        if(is_string($controller))
            $controller = strtolower($controller);

        if(is_string($action))
            $action     = strtolower($action);

        $this->_rules[] = array(
            self::RULE_PATTERN   => str_replace('#', '\#', $pattern),
            self::RULE_COMPONENT => array_merge(
                array(
                    'controller' => $controller,
                    'action'     => $action
                ),
                $extra
            ),
            self::RULE_ON        => null
        );

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
     * Find the appropriated rule.
     *
     * @access  public
     * @param   string  $uri          URI to route (if null, use the
     *                                $_SERVER['REQUEST_URI'] will be used).
     * @param   string  $bootstrap    Bootstrap that runs the route (if null,
     *                                $_SERVER['SCRIPT_NAME'] will be used).
     * @return  Hoa_Controller_Router
     * @throw   Hoa_Controller_Exception
     */
    public function route ( $uri = null, $bootstrap = null ) {

        if(null === $uri) {

            if(!isset($_SERVER['REQUEST_URI']))
                throw new Hoa_Controller_Exception(
                    'Cannot find the URI.', 0);

            $uri = ltrim($_SERVER['REQUEST_URI'], '/');
        }

        if(null === $bootstrap)
            $bootstrap = ltrim($_SERVER['SCRIPT_NAME'], '/');

        $rewrited = (bool) $this->getParameter('rewrited');
        $base     = ltrim($this->getFormattedParameter('base'), '/');

        if(0 === preg_match('#^' . $base . '(.*)?$#', $uri, $matches))
            throw new Hoa_Controller_Exception(
                'Cannot match the base %s in the URI %s.',
                1, array($base, $uri));

        if(0 === preg_match('#^' . $base . '(.*)?$#', $bootstrap, $matchees))
            throw new Hoa_Controller_Exception(
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
            throw new Hoa_Controller_Exception(
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
     * Get the selected rule after routing.
     *
     * @access  public
     * @return  array
     */
    public function getTheRule ( ) {

        return $this->_theRule;
    }
}
