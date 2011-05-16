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
 * Class \Hoa\Router\Http.
 *
 * HTTP router.
 *
 * @author     Ivan Enderlin <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright © 2007-2011 Ivan Enderlin.
 * @license    New BSD License
 */

class Http implements Router {

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
     * Base.
     *
     * @var \Hoa\Router\Http string
     */
    protected $_base           = null;

    /**
     * HTTP methods that the router understand.
     *
     * @var \Hoa\Router\Http array
     */
    protected static $_methods = array(
        'get',
        'post',
        'put',
        'delete',
        'head',
        'options'
    );



    /**
     * Constructor.
     *
     * @access  public
     * @return  void
     */
    public function __construct ( ) {

        return;
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
    public function addRule ( Array $methods, $id, $pattern, $call,
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
     *     all(…)        : addRule(array(<all methods>), …);
     *     head_delete(…): addRule(array('head', 'delete'), …).
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
     * @return  array
     * @throw   \Hoa\Router\Exception\NotFound
     */
    public function route ( $uri = null, $base = null ) {

        if(null === $uri)
            $uri = $this->getURI();

        if(null === $base)
            $base = $this->getBase();

        $bootstrap = $this->getBootstrap();

        if(!empty($base)) {

            if(0 === preg_match('#^' . $base . '(.*)?$#', $uri, $matches))
                throw new Exception\NotFound(
                    'Cannot match the base %s in the URI %s.',
                    3, array($base, $uri));

            if(0 === preg_match('#^' . $base . '(.*)?$', $bootstrap, $matchees))
                throw new Exception\NotFound(
                    'Cannot match the base %s in the bootstrap %s.',
                    4, array($base, $bootstrap));

            $uri       = ltrim($matches[1],  '/');
            $bootstrap = ltrim($matchees[1], '/');
        }

        $method    = $this->getMethod();
        $subdomain = $this->getSubdomain();
        $rules     = array_filter(
            $this->getRules(),
            function ( $rule ) use ( &$method, &$subdomain ) {

                if(Router::VISIBILITY_PUBLIC != $rule[Router::RULE_VISIBILITY])
                    return false;

                if(false === in_array($method, $rule[Router::RULE_METHODS]))
                    return false;

                if(false !== $pos = strpos($rule[Router::RULE_PATTERN], '@'))
                    if(empty($subdomain))
                        return false;
                    else
                        return 0 !== preg_match(
                            '#^' . substr($rule[3], 0, $pos) . '$#i',
                            $subdomain
                        );

                return empty($subdomain);
            }
        );

        $gotcha = false;

        foreach($rules as $rule) {

            $pattern = $rule[Router::RULE_PATTERN];

            if(false !== $pos = strpos($pattern, '@'))
                $pattern = substr($pattern, $pos + 1);

            $pattern = ltrim($pattern, '/');

            if(0 !== preg_match('#^' . $pattern . '$#i', $uri, $muri)) {

                $gotcha = true;
                break;
            }
        }

        if(false === $gotcha)
            throw new Exception\NotFound(
                'Cannot found an appropriated rule to route %s.', 5, $uri);

        if(false !== $pos)
            preg_match(
                '#^' . substr($rule[Router::RULE_PATTERN], 0, $pos) . '$#i',
                $subdomain,
                $msubdomain
            );
        else
            $msubdomain = array();

        array_shift($muri);
        $sub = array_shift($msubdomain) ?: null;
        $rule[Router::RULE_VARIABLES]['_domain']    = $this->getDomain();
        $rule[Router::RULE_VARIABLES]['_subdomain'] = $sub;
        $rule[Router::RULE_VARIABLES]['_call']      = $rule[Router::RULE_CALL];
        $rule[Router::RULE_VARIABLES]['_able']      = $rule[Router::RULE_ABLE];
        $rule[Router::RULE_VARIABLES]['_request']   = $_REQUEST;

        foreach(array_merge($muri, $msubdomain) as $key => $value)
            if(is_string($key))
                $rule[Router::RULE_VARIABLES][strtolower($key)]
                    = strtolower($value);

        return $this->_rule = $rule;
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

        if(false !== $pos = strpos($pattern, '@')) {

            $port   = $this->getPort();
            $secure = $this->isSecure();

            return (true === $secure ? 'https://' : 'http://') .
                   $this->_unroute(substr($pattern, 0, $pos), $variables) .
                   '.' . $this->getStrictDomain() .
                   ((80 !== $port && false === $secure) ? ':' . $port : '') .
                   $this->_unroute(substr($pattern, $pos + 1), $variables);
        }

        return $this->_unroute($pattern, $variables);
    }

    /**
     * Real unroute method.
     *
     * @access  protected
     * @param   string  $pattern      Pattern.
     * @param   array   $variables    Variables.
     * @return  string
     */
    protected function _unroute ( $pattern, Array $variables ) {

        $out = preg_replace_callback(
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

        if('cli' === php_sapi_name())
            return 'get';

        return strtolower($_SERVER['REQUEST_METHOD']);
    }

    /**
     * Whether the router is called asynchronously or not.
     *
     * @access  public
     * @return  bool
     */
    public function isAsynchronous ( ) {

        if(!isset($_SERVER['HTTP_X_REQUESTED_WITH']))
            return false;

        return 'xmlhttprequest' == strtolower($_SERVER['HTTP_X_REQUESTED_WITH']);
    }

    /**
     * Get URI.
     *
     * @access  public
     * @return  string
     * @throw   \Hoa\Router\Exception
     */
    public function getURI ( ) {

        if('cli' === php_sapi_name())
            return @$_SERVER['argv'][1] ?: '';

        if(!isset($_SERVER['REQUEST_URI']))
            throw new Exception(
                'Cannot find URI so we cannot route.', 6);

        return ltrim($_SERVER['REQUEST_URI'], '/');
    }

    /**
     * Get domain (with subdomain if exists).
     *
     * @access  public
     * @return  string
     */
    public function getDomain ( ) {

        if('cli' === php_sapi_name())
            return '';

        $domain = $_SERVER['HTTP_HOST'];

        if(false !== $pos = strpos($domain, ':'))
            return substr($domain, 0, $pos);

        return $domain;
    }

    /**
     * Get strict domain (i.e. without subdomain).
     *
     * @access  public
     * @return  string
     */
    public function getStrictDomain ( ) {

        $sub = $this->getSubDomain();

        if(empty($sub))
            return $this->getDomain();

        return substr($this->getDomain(), strlen($sub) + 1);
    }

    /**
     * Get subdomain.
     *
     * @access  public
     * @return  string
     */
    public function getSubDomain ( ) {

        return implode('.', array_slice(explode('.', $this->getDomain()), 0, -2));
    }

    /**
     * Get port.
     *
     * @access  public
     * @return  int
     */
    public function getPort ( ) {

        if('cli' === php_sapi_name())
            return 80;

        return (int) $_SERVER['SERVER_PORT'];
    }

    /**
     * Get bootstrap (script name).
     *
     * @access  public
     * @return  string
     */
    public function getBootstrap ( ) {

        if('cli' === php_sapi_name())
            return '';

        return $_SERVER['SCRIPT_NAME'];
    }

    /**
     * Set base.
     *
     * @access  public
     * @param   string  $base    Base.
     * @return  string
     */
    public function setBase ( $base ) {

        $old         = $this->_base;
        $this->_base = ltrim($base, '/');

        return $old;
    }

    /**
     * Get base.
     *
     * @access  public
     * @return  string
     */
    public function getBase ( ) {

        return $this->_base;
    }

    /**
     * Whether the connection is secure.
     *
     * @access  public
     * @return  bool
     */
    public function isSecure ( ) {

        return 443 === $this->getPort();
    }
}

}
