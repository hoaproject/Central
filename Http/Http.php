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

namespace Hoa\Router\Http;

use Hoa\Core;
use Hoa\Router;

/**
 * Class \Hoa\Router\Http.
 *
 * HTTP router.
 *
 * @copyright  Copyright © 2007-2015 Hoa community
 * @license    New BSD License
 */
class Http extends Router\Generic implements Core\Parameter\Parameterizable
{
    /**
     * Secure connection.
     *
     * @const bool
     */
    const SECURE   = true;

    /**
     * Unsecure connection.
     *
     * @const bool
     */
    const UNSECURE = false;

    /**
     * Parameters.
     *
     * @var \Hoa\Core\Parameter
     */
    protected $_parameters      = null;

    /**
     * Path prefix.
     *
     * @var string
     */
    protected $_pathPrefix      = null;

    /**
     * HTTP port.
     *
     * @var int
     */
    protected $_httpPort        = 80;

    /**
     * HTTPS port.
     *
     * @var int
     */
    protected $_httpsPort       = 443;

    /**
     * HTTP methods that the router understand.
     * Must map http://www.iana.org/assignments/http-methods/http-methods.xhtml.
     *
     * @var array
     */
    protected static $_methods  = [
        'connect',
        'delete',
        'get',
        'head',
        'link',
        'options',
        'patch',
        'post',
        'put',
        'trace',
        'unlink'
    ];

    /**
     * Subdomain stack: static or dynamic.
     * A subdomain is said dynamic if at least one rule pattern considers
     * subdomain. It changes the default rules filter behavior.
     *
     * @var int
     */
    protected $_subdomainStack  = _static;

    /**
     * Subdomain suffix.
     * A string to append to subdomain on each rule.
     *
     * @var string
     */
    protected $_subdomainSuffix = null;



    /**
     * Constructor.
     *
     * @return  void
     */
    public function __construct(Array $parameters = [])
    {
        $this->_parameters = new Core\Parameter(
            $this,
            [],
            [
                'prefix'        => null,
                'rules.public'  => [],
                'rules.private' => []
            ]
        );
        $this->_parameters->setParameters($parameters);

        if (null === $prefix = $this->_parameters->getParameter('prefix')) {
            $this->setPrefix(
                ('\\' === $_ = dirname($this->getBootstrap())) ? '/' : $_
            );
        } else {
            $this->setPrefix($prefix);
        }

        foreach ($this->_parameters->getParameter('rules.public') as $id => $rule) {
            @list($methods, $pattern, $call, $able, $variables)
                = $rule;

            if (null === $variables) {
                $variables = [];
            }

            $this->addRule($id, $methods, $pattern, $call, $able, $variables);
        }

        foreach ($this->_parameters->getParameter('rules.private') as $id => $rule) {
            @list($methods, $pattern, $call, $able, $variables)
                = $rule;

            if (null === $variables) {
                $variables = [];
            }

            $this->addPrivateRule(
                $id, $methods, $pattern, $call, $able, $variables
            );
        }

        $this->setDefaultPort(static::getPort(), static::isSecure());

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
     * Fallback for add*Rule() methods.
     *
     * @param   int     $visibility    Visibility (please, see
     *                                 Router::VISIBILITY_* constants).
     * @param   string  $id            ID.
     * @param   array   $methods       HTTP methods allowed by the rule.
     * @param   string  $pattern       Pattern (on-subdomain@on-request).
     * @param   mixed   $call          Call (first part).
     * @param   mixed   $able          Able (second part).
     * @param   array   $variables     Variables (default or additional values).
     * @return  \Hoa\Router\Http
     * @throws  \Hoa\Router\Exception
     */
    protected function _addRule(
        $visibility,
        $id,
        Array $methods,
        $pattern,
        $call,
        $able,
        Array $variables
    ) {
        if (true === $this->ruleExists($id)) {
            throw new Router\Exception(
                'Cannot add rule %s because it already exists.',
                0,
                $id
            );
        }

        array_walk($methods, function (&$method) {
            $method = strtolower($method);
        });
        $diff = array_diff($methods, static::$_methods);

        if (!empty($diff)) {
            throw new Router\Exception(
                (1 == count($diff)
                    ? 'Method %s is'
                    : 'Methods %s are') .
                ' invalid for the rule %s (valid methods are: %s).',
                1,
                [
                    implode(', ', $diff),
                    $id,
                    implode(', ', static::$_methods)
                ]
            );
        }

        if (_static === $this->_subdomainStack &&
            false   !== strpos($pattern, '@')) {
            $this->_subdomainStack = _dynamic;
        }

        $this->_rules[$id] = [
            Router::RULE_VISIBILITY => $visibility,
            Router::RULE_ID         => $id,
            Router::RULE_METHODS    => $methods,
            Router::RULE_PATTERN    => $pattern,
            Router::RULE_CALL       => $call,
            Router::RULE_ABLE       => $able,
            Router::RULE_VARIABLES  => $variables
        ];

        return $this;
    }

    /**
     * Find the appropriated rule.
     * Special variables: _domain, _subdomain, _call, _able and _request.
     *
     * @param   string  $uri       URI. If null, it will be deduced. Can contain
     *                             subdomain.
     * @param   string  $prefix    Path prefix. If null, it will be deduced.
     * @return  \Hoa\Router\Http
     * @throws  \Hoa\Router\Exception\NotFound
     */
    public function route($uri = null, $prefix = null)
    {
        if (null === $uri) {
            $uri       = static::getURI();
            $subdomain = $this->getSubdomain();
        } else {
            if (false !== $pos = strpos($uri, '@')) {
                list($subdomain, $uri) = explode('@', $uri, 2);
            } else {
                $subdomain = $this->getSubdomain();
            }

            $uri = ltrim(urldecode($uri), '/');
        }

        if (null === $prefix) {
            $prefix = $this->getPrefix();
        }

        if (!empty($prefix)) {
            $prefix = ltrim($prefix, '/');

            if (0 === preg_match('#^' . $prefix . '(.*)?$#', $uri, $matches)) {
                throw new Router\Exception\NotFound(
                    'Cannot match the path prefix %s in the URI %s.',
                    2,
                    [$prefix, $uri]
                );
            }

            $uri = ltrim($matches[1],  '/');
        }

        // Please, see http://php.net/language.variables.external, section “Dots
        // in incoming variable names”.
        unset($_REQUEST[$_uri = str_replace('.', '_', $uri)]);
        unset($_GET[$_uri]);

        $method          = $this->getMethod();
        $subdomainStack  = $this->getSubdomainStack();
        $subdomainSuffix = $this->getSubdomainSuffix();

        if (null !== $subdomainSuffix) {
            $subdomainSuffix = '\.' . $subdomainSuffix;
        }

        $rules = array_filter(
            $this->getRules(),
            function ($rule) use (
                &$method,
                &$subdomain,
                &$subdomainStack,
                &$subdomainSuffix
            ) {
                if (Router::VISIBILITY_PUBLIC != $rule[Router::RULE_VISIBILITY]) {
                    return false;
                }

                if (false === in_array($method, $rule[Router::RULE_METHODS])) {
                    return false;
                }

                if (false !== $pos = strpos($rule[Router::RULE_PATTERN], '@')) {
                    if (empty($subdomain)) {
                        return false;
                    } else {
                        return 0 !== preg_match(
                            '#^' .
                            substr($rule[Router::RULE_PATTERN], 0, $pos) .
                            $subdomainSuffix .
                            '$#i',
                            $subdomain
                        );
                    }
                }

                return
                    _dynamic == $subdomainStack
                        ? empty($subdomain)
                        : true;
            }
        );

        if (empty($rules)) {
            throw new Router\Exception\NotFound(
                'No rule to apply to route %s.',
                3,
                $uri
            );
        }

        $gotcha = false;

        foreach ($rules as $rule) {
            $pattern = $rule[Router::RULE_PATTERN];

            if (false !== $pos = strpos($pattern, '@')) {
                $pattern = substr($pattern, $pos + 1);
            }

            $pattern = ltrim($pattern, '/');

            if (0 !== preg_match('#^' . $pattern . '$#i', $uri, $muri)) {
                $gotcha = true;

                break;
            }
        }

        if (false === $gotcha) {
            throw new Router\Exception\NotFound(
                'Cannot found an appropriated rule to route %s.',
                4,
                $uri
            );
        }

        if (false !== $pos) {
            preg_match(
                '#^' .
                substr($rule[Router::RULE_PATTERN], 0, $pos) .
                $subdomainSuffix .
                '$#i',
                $subdomain,
                $msubdomain
            );
        } else {
            $msubdomain = [];
        }

        array_shift($muri);
        $sub                                        = array_shift($msubdomain) ?: null;
        $rule[Router::RULE_VARIABLES]['_uri']       =  $uri;
        $rule[Router::RULE_VARIABLES]['_method']    =  $method;
        $rule[Router::RULE_VARIABLES]['_domain']    =  static::getDomain();
        $rule[Router::RULE_VARIABLES]['_subdomain'] =  $sub;
        $rule[Router::RULE_VARIABLES]['_call']      = &$rule[Router::RULE_CALL];
        $rule[Router::RULE_VARIABLES]['_able']      = &$rule[Router::RULE_ABLE];
        $rule[Router::RULE_VARIABLES]['_request']   =  $_REQUEST;

        $caseless = 0 === preg_match(
            '#\(\?\-[imsxUXJ]+\)#',
            $rule[Router::RULE_PATTERN]
        );

        foreach (array_merge($muri, $msubdomain) as $key => $value) {
            if (!is_string($key)) {
                continue;
            }

            if (true === $caseless) {
                $key = mb_strtolower($key);
            }

            if (isset($rule[Router::RULE_VARIABLES][$key]) && empty($value)) {
                continue;
            }

            if (true === $caseless) {
                $value = mb_strtolower($value);
            }

            $rule[Router::RULE_VARIABLES][$key] = $value;
        }

        $this->_rule = $rule;

        return $this;
    }

    /**
     * Unroute a rule (i.e. route()^-1).
     * Special variables: _subdomain and _fragment.
     * _subdomain accepts 3 keywords:
     *     * __root__ to go back to the root (with the smallest subdomain);
     *     * __self__ to copy the current subdomain (useful if you want a
     *       complete URL with protocol etc., not only the query part);
     *     * __shift__ to shift a subdomain part, i.e. going to the upper
     *       domain; if you want to shift x times, just type __shift__ * x.
     *
     * @param   string  $id           ID.
     * @param   array   $variables    Variables.
     * @param   bool    $secure       Whether the connection is secured. If
     *                                null, will use the self::isSecure() value.
     * @param   string  $prefix       Path prefix. If null, it will be deduced.
     * @return  string
     * @throws  \Hoa\Router\Exception
     */
    public function unroute(
        $id,
        Array $variables = [],
        $secured = null,
        $prefix = null
    ) {
        if (null === $prefix) {
            $prefix = $this->getPrefix();
        }

        $suffix  = $this->getSubdomainSuffix();
        $rule    = $this->getRule($id);
        $pattern = $rule[Router::RULE_PATTERN];

        foreach ($variables as $KeY => $value) {
            if ($KeY != $key = strtolower($KeY)) {
                unset($variables[$KeY]);
                $variables[$key] = $value;
            }
        }

        $variables = array_merge($rule[Router::RULE_VARIABLES], $variables);
        $anchor    = !empty($variables['_fragment'])
                         ? '#' . $variables['_fragment']
                         : null;
        unset($variables['_fragment']);

        $self          = $this;
        $prependPrefix = function ($unroute) use (&$prefix) {
            if (0 !== preg_match('#^https?://#', $unroute)) {
                return $unroute;
            }

            return $prefix . $unroute;
        };
        $getPort = function ($secure) use ($self) {
            $defaultPort = $self->getDefaultPort($secure);

            if (static::UNSECURE === $secure) {
                return 80 !== $defaultPort ? ':' . $defaultPort : '';
            }

            return 443 !== $defaultPort ? ':' . $defaultPort  : '';
        };

        if (true === array_key_exists('_subdomain', $variables) &&
            null !== $variables['_subdomain']) {
            if (empty($variables['_subdomain'])) {
                throw new Router\Exception(
                    'Subdomain is empty, cannot unroute the rule %s properly.',
                    5,
                    $id
                );
            }

            $secure = null === $secured ? static::isSecure() : $secured;

            if (false !== $pos = strpos($pattern, '@')) {
                $pattern = substr($pattern, $pos + 1);
            }

            $subdomain = $variables['_subdomain'];
            $handle    = strtolower($subdomain);

            switch ($handle) {
                case '__self__':
                    $subdomain = $this->getSubdomain();

                    break;

                case '__root__':
                    $subdomain = '';

                    break;

                default:
                    if (0 !== preg_match('#__shift__(?:\s*\*\s*(\d+))?#', $handle, $m)) {
                        $repetition = isset($m[1]) ? (int) $m[1] : 1;
                        $subdomain  = $this->getSubdomain();

                        for (; $repetition >= 1; --$repetition) {
                            if (false === $pos = strpos($subdomain, '.')) {
                                $subdomain = '';

                                break;
                            }

                            $subdomain = substr($subdomain, $pos + 1);
                        }

                        break;
                    }

                    if (null !== $suffix) {
                        $subdomain .= '.' . $suffix;
                    }

                    break;
            }

            if (!empty($subdomain)) {
                $subdomain .= '.';
            }

            return
                (true === $secure ? 'https://' : 'http://') .
                $subdomain .
                $this->getStrictDomain() .
                $getPort($secure) .
                $prependPrefix($this->_unroute($id, $pattern, $variables)) .
                $anchor;
        }

        if (false !== $pos = strpos($pattern, '@')) {
            $subPattern = substr($pattern, 0, $pos);
            $pattern    = substr($pattern, $pos + 1);

            if (null !== $suffix) {
                $subPattern .= '.' . $suffix;
            }

            if ($suffix === $subPattern) {
                return
                    $prependPrefix($this->_unroute($id, $pattern, $variables)) .
                    $anchor;
            }

            $secure = null === $secured ? static::isSecure() : $secured;

            return
                (true === $secure ? 'https://' : 'http://') .
                $this->_unroute($id, $subPattern, $variables, false) .
                '.' . $this->getStrictDomain() .
                $getPort($secure) .
                $prependPrefix($this->_unroute($id, $pattern, $variables)) .
                $anchor;
        }

        return
            $prependPrefix($this->_unroute($id, $pattern, $variables)) .
            $anchor;
    }

    /**
     * Real unroute method.
     *
     * @param   string  $id           ID.
     * @param   string  $pattern      Pattern.
     * @param   array   $variables    Variables.
     * @param   bool    $allowEmpty   Whether allow empty variables.
     * @return  string
     * @throws  \Hoa\Router\Exception
     */
    protected function _unroute(
        $id,
        $pattern,
        Array $variables,
        $allowEmpty = true
    ) {
        $unusedVariables = [];

        foreach ($variables as $key => $variable) {
            if ('_' !== $key[0]) {
                $unusedVariables[$key] = $variable;
            }
        }

        // (?<named>…)
        $out = preg_replace_callback(
            '#\(\?\<([^>]+)>[^\)]*\)[\?\*\+]{0,2}#',
            function (Array $matches) use (&$id, &$variables, &$allowEmpty, &$unusedVariables) {
                $m = strtolower($matches[1]);

                if (!isset($variables[$m]) || '' === $variables[$m]) {
                    if (true === $allowEmpty) {
                        return '';
                    } else {
                        throw new Router\Exception(
                            'Variable %s is empty and it is not allowed when ' .
                            'unrouting rule %s.',
                            6,
                            [$m, $id]
                        );
                    }
                }

                unset($unusedVariables[$m]);

                return $variables[$m];
            },
            // (-…)
            preg_replace('#\(\?\-?[imsxUXJ]+\)#', '', $pattern)
        );

        // (?:
        $out = preg_replace('#(?<!\\\)\(\?:#', '(', $out);

        // (…)?, (…)*
        $out = preg_replace('#(?<!\\\)\((.*)(?<!\\\)\)[\?\*]#', '\1', $out);

        // (…)+
        $out = preg_replace('#(?<!\\\)\((.+)(?<!\\\)\)\+#', '\1', $out);

        // …?, …*, …+
        $out = preg_replace('#(.)(?<![\)\\\])[\?\*\+]#', '\1', $out);

        $out = str_replace(
            [
                '\.', '\\\\', '\+', '\*', '\?', '\[', '\]', '\^', '\$', '\(',
                '\)', '\{', '\}', '\=', '\!', '\<', '\>', '\|', '\:', '\-'
            ],
            [
                '.', '\\', '+', '*', '?', '[', ']', '^', '$', '(',
                ')', '{', '}', '=', '!', '<', '>', '|', ':', '-'
            ],
            $out
        );

        return
            $out .
            (!empty($unusedVariables)
                ? '?' . http_build_query($unusedVariables)
                : '');
    }

    /**
     * Get HTTP method.
     *
     * @return  string
     */
    public function getMethod()
    {
        if ('cli' === php_sapi_name()) {
            return 'get';
        }

        return strtolower($_SERVER['REQUEST_METHOD']);
    }

    /**
     * Whether the router is called asynchronously or not.
     *
     * @return  bool
     */
    public function isAsynchronous()
    {
        if (!isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
            return false;
        }

        return 'xmlhttprequest' == strtolower($_SERVER['HTTP_X_REQUESTED_WITH']);
    }

    /**
     * Get URI.
     *
     * @return  string
     * @throws  \Hoa\Router\Exception
     */
    public static function getURI()
    {
        if ('cli' === php_sapi_name()) {
            return ltrim(@$_SERVER['argv'][1] ?: '', '/');
        }

        if (!isset($_SERVER['REQUEST_URI'])) {
            throw new Router\Exception('Cannot find URI so we cannot route.', 7);
        }

        $uri = ltrim(urldecode($_SERVER['REQUEST_URI']), '/');

        if (false !== $pos = strpos($uri, '?')) {
            $uri = substr($uri, 0, $pos);
        }

        return $uri;
    }

    /**
     * Get query.
     *
     * @return  array
     */
    public static function getQuery()
    {
        if ('cli' === php_sapi_name()) {
            return [];
        }

        if (!isset($_SERVER['REQUEST_URI'])) {
            throw new Router\Exception('Cannot find URI so we cannot get query.', 8);
        }

        $uri = $_SERVER['REQUEST_URI'];

        if (false === $pos = strpos($uri, '?')) {
            return [];
        }

        parse_str(substr($uri, $pos + 1), $out);

        return $out;
    }

    /**
     * Get domain (with subdomain if exists).
     *
     * @return  string
     */
    public static function getDomain()
    {
        static $domain = null;

        if (null === $domain) {
            if ('cli' === php_sapi_name()) {
                return $domain = '';
            }

            $domain = $_SERVER['HTTP_HOST'];

            if (empty($domain)) {
                $domain = $_SERVER['SERVER_ADDR'];
            }

            if (0 !== preg_match('#^(.+):' . static::getPort() . '$#', $domain, $m)) {
                $domain = $m[1];
            }
        }

        return $domain;
    }

    /**
     * Get strict domain (i.e. without subdomain).
     *
     * @return  string
     */
    public function getStrictDomain()
    {
        $sub = $this->getSubdomain();

        if (empty($sub)) {
            return static::getDomain();
        }

        return substr(static::getDomain(), strlen($sub) + 1);
    }

    /**
     * Get subdomain.
     *
     * @param   bool  $withSuffix    With or without suffix.
     * @return  string
     */
    public function getSubdomain($withSuffix = true)
    {
        static $subdomain = null;

        if (null === $subdomain) {
            $domain = static::getDomain();

            if (empty($domain)) {
                return null;
            }

            if ($domain == long2ip(ip2long($domain))) {
                return null;
            }

            if (2 > substr_count($domain, '.', 1)) {
                return null;
            }

            $subdomain = substr(
                $domain,
                0,
                strrpos(
                    $domain,
                    '.',
                    -(strlen($domain) - strrpos($domain, '.') + 1)
                )
            );
        }

        if (true === $withSuffix) {
            return $subdomain;
        }

        $suffix = $this->getSubdomainSuffix();

        if (null === $suffix) {
            return $subdomain;
        }

        return substr($subdomain, 0, -strlen($suffix) - 1);
    }

    /**
     * Set subdomain stack: static or dynamic.
     *
     * @param   int  $stack    Stack: _static or _dynamic constants.
     * @return  int
     */
    public function setSubdomainStack($stack)
    {
        $old                   = $this->_subdomainStack;
        $this->_subdomainStack = $stack;

        return $old;
    }

    /**
     * Get subdomain stack.
     *
     * @return  int
     */
    public function getSubdomainStack()
    {
        return $this->_subdomainStack;
    }

    /**
     * Set subdomain suffix.
     *
     * @param   string  $suffix    Suffix.
     * @return  string
     */
    public function setSubdomainSuffix($suffix)
    {
        $old                    = $this->_subdomainSuffix;
        $this->_subdomainSuffix = preg_quote($suffix);

        return $old;
    }

    /**
     * Get subdomain suffix.
     *
     * @return  string
     */
    public function getSubdomainSuffix()
    {
        return $this->_subdomainSuffix;
    }

    /**
     * Get port.
     *
     * @return  int
     */
    public static function getPort()
    {
        if ('cli' === php_sapi_name()) {
            return 80;
        }

        return (int) $_SERVER['SERVER_PORT'];
    }

    /**
     * Get bootstrap (script name).
     *
     * @return  string
     */
    public static function getBootstrap()
    {
        $sapi = php_sapi_name();

        if ('cli' === $sapi || 'cli-server' === $sapi) {
            return '';
        }

        return $_SERVER['SCRIPT_NAME'];
    }

    /**
     * Set path prefix.
     *
     * @param   string  $prefix    Path prefix.
     * @return  string
     */
    public function setPrefix($prefix)
    {
        $old               = $this->_pathPrefix;
        $this->_pathPrefix = preg_quote(rtrim($prefix, '/'));

        return $old;
    }

    /**
     * Get path prefix (aka “base”).
     *
     * @return  string
     */
    public function getPrefix()
    {
        return $this->_pathPrefix;
    }

    /**
     * Set port.
     *
     * @param   int   $port      Port.
     * @param   bool  $secure    Whether the connection is secured.
     * @return  int
     */
    public function setDefaultPort($port, $secure = self::UNSECURE)
    {
        if (static::UNSECURE === $secure) {
            $old             = $this->_httpPort;
            $this->_httpPort = $port;
        } else {
            $old              = $this->_httpsPort;
            $this->_httpsPort = $port;
        }

        return $old;
    }

    /**
     * Get HTTP port.
     *
     * @param   bool  $secure    Whether the connection is secured.
     * @return  int
     */
    public function getDefaultPort($secure = self::UNSECURE)
    {
        if (static::UNSECURE === $secure) {
            return $this->_httpPort;
        }

        return $this->_httpsPort;
    }

    /**
     * Whether the connection is secure.
     *
     * @return  bool
     */
    public static function isSecure()
    {
        if (!isset($_SERVER['HTTPS'])) {
            return static::UNSECURE;
        }

        return
            (!empty($_SERVER['HTTPS']) && 'off' !== $_SERVER['HTTPS'])
                ? static::SECURE
                : static::UNSECURE;
    }
}

/**
 * Flex entity.
 */
Core\Consistency::flexEntity('Hoa\Router\Http\Http');
