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
 * \Hoa\Router\Exception
 */
-> import('Router.Exception.~')

/**
 * \Hoa\Router\Exception\NotFound
 */
-> import('Router.Exception.NotFound')

/**
 * \Hoa\Router
 */
-> import('Router.~');

}

namespace Hoa\Router {

/**
 * Class \Hoa\Router\Cli.
 *
 * CLI router.
 *
 * @author     Ivan Enderlin <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright © 2007-2011 Ivan Enderlin.
 * @license    New BSD License
 */

class Cli implements Router, \Hoa\Core\Parameter\Parameterizable {

    /**
     * Parameters.
     *
     * @var \Hoa\Core\Parameter object
     */
    protected $_parameters    = null;

    /**
     * All rules buckets.
     *
     * @var \Hoa\Router\Http array
     */
    protected $_rules          = array();

    /**
     * Selected rule after routing.
     *
     * @var \Hoa\Router\Http array
     */
    protected $_rule           = null;

    /**
     * CLI methods that the router understand.
     *
     * @var \Hoa\Router\Http array
     */
    protected static $_methods = array(
        'get', // classic call
        'post' // pipe/stdin
    );



    /**
     * Constructor.
     *
     * @access  public
     * @return  void
     */
    public function __construct ( Array $parameters = array() ) {

        $this->_parameters = new \Hoa\Core\Parameter(
            $this,
            array(),
            array(
                'base'          => null,
                'rules.public'  => array(),
                'rules.private' => array()
            )
        );
        $this->_parameters->setParameters($parameters);

        foreach($this->_parameters->getParameter('rules.public') as $id => $rule) {

            @list($methods, $pattern, $call, $able, $variables)
                = $rule;

            if(null === $variables)
                $variables = array();

            $this->addRule($methods, $id, $pattern, $call, $able, $variables);
        }

        foreach($this->_parameters->getParameter('rules.private') as $id => $rule) {

            @list($methods, $pattern, $call, $able, $variables)
                = $rule;

            if(null === $variables)
                $variables = array();

            $this->addPrivateRule(
                $methods, $id, $pattern, $call, $able, $variables
            );
        }

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
     * Fallback for add*Rule() methods.
     *
     * @access  public
     * @param   int     $visibility    Visibility (please, see
     *                                 Router::VISIBILITY_* constants).
     * @param   array   $methods       HTTP methods allowed by the rule.
     * @param   string  $id            ID.
     * @param   string  $pattern       Pattern (on-subdomain@on-request).
     * @param   mixed   $call          Call (first part).
     * @param   mixed   $able          Able (second part).
     * @param   array   $variables     Variables (default or additional values).
     * @return  \Hoa\Router\Http
     * @throw   \Hoa\Router\Exception
     */
    protected function _addRule ( $visibility,  Array $methods, $id, $pattern,
                                  $call, $able, Array $variables ) {

        if(true === $this->ruleExists($id))
            throw new Exception(
                'Cannot add rule %s because it already exists.', 0, $id);

        $methods = array_map('strtolower', $methods);
        $diff    = array_diff($methods, self::$_methods);

        if(!empty($diff))
            throw new Exception(
                (1 == count($diff)
                    ? 'Method %s is'
                    : 'Methods %s are') .
                ' invalid for the rule %s (valid methods are: %s).',
                1, array(implode(', ', $diff), $id,
                         implode(', ', self::$_methods)));

        $this->_rules[$id] = array(
            Router::RULE_VISIBILITY => $visibility,
            Router::RULE_METHODS    => $methods,
            Router::RULE_ID         => $id,
            Router::RULE_PATTERN    => $pattern,
            Router::RULE_CALL       => $call,
            Router::RULE_ABLE       => $able,
            Router::RULE_VARIABLES  => $variables
        );

        return $this;
    }

    /**
     * Add a public rule.
     *
     * @access  public
     * @param   array   $methods      HTTP methods allowed by the rule.
     * @param   string  $id           ID.
     * @param   string  $pattern      Pattern (on-subdomain@on-request).
     * @param   mixed   $call         Call (first part).
     * @param   mixed   $able         Able (second part).
     * @param   array   $variables    Variables (default or additional values).
     * @return  \Hoa\Router\Http
     * @throw   \Hoa\Router\Exception
     */
    public function addRule ( Array $methods, $id, $pattern, $call = null,
                              $able = null, Array $variables = array() ) {

        return $this->_addRule(
            Router::VISIBILITY_PUBLIC,
            $methods,
            $id,
            $pattern,
            $call,
            $able,
            $variables
        );
    }

    /**
     * Add a private rule.
     *
     * @access  public
     * @param   array   $methods      HTTP methods allowed by the rule.
     * @param   string  $id           ID.
     * @param   string  $pattern      Pattern (on-subdomain@on-request).
     * @param   mixed   $call         Call (first part).
     * @param   mixed   $able         Able (second part).
     * @param   array   $variables    Variables (default or additional values).
     * @return  \Hoa\Router\Http
     * @throw   \Hoa\Router\Exception
     */
    public function addPrivateRule ( Array $methods, $id, $pattern, $call = null,
                                     $able = null, Array $variables = array() ) {

        return $this->_addRule(
            Router::VISIBILITY_PRIVATE,
            $methods,
            $id,
            $pattern,
            $call,
            $able,
            $variables
        );
    }

    /**
     * Helper for adding rules.
     * Methods are concatenated by _. If prefixed by _, it's a private rule. In
     * addition, the keyword “all” takes place for all methods.
     * Examples:
     *     get(…)        : addRule(array('get'), …);
     *     get_post(…)   : addRule(array('get', 'post'), …);
     *     post_get(…)   : same that previous;
     *     _get(…)       : addPrivateRule(array('get'), …);
     *     all(…)        : addRule(array(<all methods>), …).
     *
     * @access  public
     * @param   string  $name         Please, see API documentation.
     * @param   array   $arguments    Arguments for add*Rule() methods.
     * @return  \Hoa\Router\Http
     * @throw   \Hoa\Router\Exception
     */
    public function __call ( $name, $arguments ) {

        if('_' == $name[0]) {

            $name   = substr($name, 1);
            $method = 'addPrivateRule';
        }
        else
            $method = 'addRule';

        if('all' == $name)
            array_unshift($arguments, self::$_methods);
        else
            array_unshift($arguments, explode('_', $name));

        return call_user_func_array(array($this, $method), $arguments);
    }

    /**
     * Check if a rule exists.
     *
     * @access  public
     * @param   string  $id    ID.
     * @return  bool
     */
    public function ruleExists ( $id ) {

        return isset($this->_rules[$id]);
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
     * Get a specific rule.
     *
     * @access  public
     * @param   string  $id    ID.
     * @return  array
     * @throw   \Hoa\Router\Exception
     */
    public function getRule ( $id ) {

        if(false === $this->ruleExists($id))
            throw new Exception(
                'Rule %s does not exist.', 2, $id);

        return $this->_rules[$id];
    }

    /**
     * Get the selected rule after routing.
     *
     * @access  public
     * @return  array
     */
    public function getTheRule ( ) {

        return $this->_rule;
    }

    /**
     * Find the appropriated rule.
     *
     * @access  public
     * @param   string  $uri     URI or complete URL (without scheme). If null,
     *                           it will be deduce.
     * @param   string  $base    Base. If null, it will be deduce.
     * @return  \Hoa\Router\Cli
     * @throw   \Hoa\Router\Exception\NotFound
     */
    public function route ( $uri = null, $base = null ) {

        if(null === $uri)
            $uri = $this->getURI();

        $method = $this->getMethod();
        $rules  = array_filter(
            $this->getRules(),
            function ( $rule ) use ( &$method ) {

                if(Router::VISIBILITY_PUBLIC != $rule[Router::RULE_VISIBILITY])
                    return false;

                if(false === in_array($method, $rule[Router::RULE_METHODS]))
                    return false;

                return true;
            }
        );

        $gotcha = false;

        foreach($rules as $rule) {

            $pattern = $rule[Router::RULE_PATTERN];

            if(0 !== preg_match('#^' . $pattern . '$#i', $uri, $muri)) {

                $gotcha = true;
                break;
            }
        }

        if(false === $gotcha)
            throw new Exception\NotFound(
                'Cannot found an appropriated rule to route %s.', 5, $uri);

        array_shift($muri);
        $rule[Router::RULE_VARIABLES]['_call'] = $rule[Router::RULE_CALL];
        $rule[Router::RULE_VARIABLES]['_able'] = $rule[Router::RULE_ABLE];

        foreach($muri as $key => $value) {

            if(!is_string($key))
                continue;

            $key = strtolower($key);

            if(isset($rule[Router::RULE_VARIABLES][$key]) && empty($value))
                continue;

            $rule[Router::RULE_VARIABLES][$key] = $value;
        }

        $this->_rule = $rule;

        return $this;
    }

    /**
     * Unroute a rule (i.e. route()^-1).
     *
     * @access  public
     * @param   string  $id           ID.
     * @param   array   $variables    Variables.
     * @return  string
     */
    public function unroute ( $id, Array $variables = array() ) {

        $rule      = $this->getRule($id);
        $pattern   = $rule[Router::RULE_PATTERN];
        $variables = array_merge($rule[Router::RULE_VARIABLES], $variables);
        $out       = preg_replace_callback(
            '#\(\?\<([^>]+)>[^\)]*\)#',
            function ( Array $matches ) use ( &$variables ) {

                $m = strtolower($matches[1]);

                if(!isset($variables[$m]))
                    return '';

                return $variables[$m];
            },
            $pattern
        );

        return str_replace(
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
    }

    /**
     * Get HTTP method.
     *
     * @access  public
     * @return  string
     */
    public function getMethod ( ) {

        return 'get';
    }

    /**
     * Whether the router is called asynchronously or not.
     *
     * @access  public
     * @return  bool
     */
    public function isAsynchronous ( ) {

        return false;
    }

    /**
     * Get URI.
     *
     * @access  public
     * @return  string
     */
    public function getURI ( ) {

        if(!isset($_SERVER['argv']))
            return null;

        array_shift($_SERVER['argv']);
        $out = null;

        foreach($_SERVER['argv'] as $arg) {

            if(false !== strpos($arg, '=')) {

                if(false !== strpos($arg, '"'))
                    $arg = str_replace(
                        '=',
                        '="',
                        str_replace('"', '\\"', $arg)
                    ) . '"';
                elseif(false !== strpos($arg, '\''))
                    $arg = str_replace(
                        '=',
                        '=\'',
                        str_replace('\'', '\\\'', $arg)
                    ) . '\'';
                elseif(false !== strpos($arg, ' '))
                    $arg = str_replace('=', '="', $arg) . '"';
            }
            elseif(false !== strpos($arg, ' '))
                $arg = '"' . str_replace('"', '\\"', $arg) . '"';
            elseif(false !== strpos($arg, '"'))
                $arg = '"' . str_replace('"', '\\"', $arg) . '"';
            elseif(false !== strpos($arg, '\''))
                $arg = '"' . $arg . '"';

            $out .= ' ' . $arg;
        }

        return ltrim($out);
    }
}

}
