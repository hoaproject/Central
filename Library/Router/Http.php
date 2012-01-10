<?php

/**
 * Hoa
 *
 *
 * @license
 *
 * New BSD License
 *
 * Copyright © 2007-2012, Ivan Enderlin. All rights reserved.
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
 * @copyright  Copyright © 2007-2012 Ivan Enderlin.
 * @license    New BSD License
 */

class Http implements Router, \Hoa\Core\Parameter\Parameterizable {

    /**
     * Parameters.
     *
     * @var \Hoa\Core\Parameter object
     */
    protected $_parameters      = null;

    /**
     * All rules buckets.
     *
     * @var \Hoa\Router\Http array
     */
    protected $_rules           = array();

    /**
     * Selected rule after routing.
     *
     * @var \Hoa\Router\Http array
     */
    protected $_rule            = null;

    /**
     * Base.
     *
     * @var \Hoa\Router\Http string
     */
    protected $_base            = null;

    /**
     * HTTP methods that the router understand.
     *
     * @var \Hoa\Router\Http array
     */
    protected static $_methods  = array(
        'get',
        'post',
        'put',
        'delete',
        'head',
        'options'
    );

    /**
     * Subdomain stack: static or dynamic.
     * A subdomain is said dynamic if at least one rule pattern considers
     * subdomain. It changes the default rules filter behavior.
     *
     * @var \Hoa\Router\Http int
     */
    protected $_subdomainStack  = _static;

    /**
     * Subdomain suffix.
     * A string to append to subdomain on each rule.
     *
     * @var \Hoa\Router\Http string
     */
    protected $_subdomainSuffix = null;



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
        $this->setBase($this->_parameters->getParameter('base'));

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

        if(   _static == $this->_subdomainStack
           && false   != strpos($pattern, '@')) {

            $this->_subdomainStack = _dynamic;

            if(null !== $suffix = $this->getSubdomainSuffix())
                $pattern = str_replace('@', '\.' . $suffix . '@', $pattern);
        }
        elseif(null !== $suffix = $this->getSubdomainSuffix())
            $pattern = $suffix . '@' . $pattern;

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
     *                           it will be deduce. Can contain subdomain.
     * @param   string  $base    Base. If null, it will be deduce.
     * @return  \Hoa\Router\Http
     * @throw   \Hoa\Router\Exception\NotFound
     */
    public function route ( $uri = null, $base = null ) {

        if(null === $uri) {

            $uri       = $this->getURI();
            $subdomain = $this->getSubdomain();
        }
        else {

            if(false !== $pos = strpos($uri, '@'))
                list($subdomain, $uri) = explode('@', $uri, 2);
            else
                $subdomain             = $this->getSubdomain();

            $uri   = ltrim($uri, '/');
        }

        if(null === $base)
            $base  = $this->getBase();

        if(!empty($base)) {

            if(0 === preg_match('#^' . $base . '(.*)?$#', $uri, $matches))
                throw new Exception\NotFound(
                    'Cannot match the base %s in the URI %s.',
                    3, array($base, $uri));

            $uri = ltrim($matches[1],  '/');
        }

        // Please, see http://php.net/language.variables.external, section “Dots
        // in incoming variable names”.
        unset($_REQUEST[$_uri = str_replace('.', '_', $uri)]);
        unset($_GET[$_uri]);

        $method         = $this->getMethod();
        $subdomainStack = $this->getSubdomainStack();
        $rules          = array_filter(
            $this->getRules(),
            function ( $rule ) use ( &$method, &$subdomain, &$subdomainStack ) {

                if(Router::VISIBILITY_PUBLIC != $rule[Router::RULE_VISIBILITY])
                    return false;

                if(false === in_array($method, $rule[Router::RULE_METHODS]))
                    return false;

                if(false !== $pos = strpos($rule[Router::RULE_PATTERN], '@'))
                    if(empty($subdomain))
                        return false;
                    else
                        return 0 !== preg_match(
                            '#^' .
                            substr($rule[Router::RULE_PATTERN], 0, $pos)
                            . '$#i',
                            $subdomain
                        );

                return _dynamic == $subdomainStack
                           ? empty($subdomain)
                           : true;
            }
        );

        if(empty($rules))
            throw new Exception\NotFound(
                'No rule to apply to route %s.', 4, $uri);

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

        foreach(array_merge($muri, $msubdomain) as $key => $value) {

            if(!is_string($key))
                continue;

            $key = strtolower($key);

            if(isset($rule[Router::RULE_VARIABLES][$key]) && empty($value))
                continue;

            $rule[Router::RULE_VARIABLES][$key] = strtolower($value);
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

        foreach($variables as $KeY => $value)
            if($KeY != $key = strtolower($KeY)) {

                unset($variables[$KeY]);
                $variables[$key] = $value;
            }

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

        if(true === array_key_exists('_subdomain', $variables)) {

            $port   = $this->getPort();
            $secure = $this->isSecure();

            return (true === $secure ? 'https://' : 'http://') .
                   $variables['_subdomain'] .
                   (!empty($variables['_subdomain']) ? '.' : '') .
                   $this->getStrictDomain() .
                   ((80 !== $port && false === $secure) ? ':' . $port : '') .
                   $this->_unroute($pattern, $variables);
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
    public function getSubdomain ( ) {

        $domain = $this->getDomain();

        if($domain == long2ip(ip2long($domain)))
            return null;

        return implode('.', array_slice(explode('.', $domain), 0, -2));
    }

    /**
     * Set subdomain stack: static or dynamic.
     *
     * @access  public
     * @param   int  $stack    Stack: _static or _dynamic constants.
     * @return  int
     */
    public function setSubdomainStack ( $stack ) {

        $old                   = $this->_subdomainStack;
        $this->_subdomainStack = $stack;

        return $old;
    }

    /**
     * Get subdomain stack.
     *
     * @access  public
     * @return  int
     */
    public function getSubdomainStack ( ) {

        return $this->_subdomainStack;
    }

    /**
     * Set subdomain suffix.
     *
     * @access  public
     * @param   string  $suffix    Suffix.
     * @return  string
     */
    public function setSubdomainSuffix ( $suffix ) {

        $old                    = $this->_subdomainSuffix;
        $this->_subdomainSuffix = $suffix;

        return $old;
    }

    /**
     * Get subdomain suffix.
     *
     * @access  public
     * @return  string
     */
    public function getSubdomainSuffix ( ) {

        return $this->_subdomainSuffix;
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
