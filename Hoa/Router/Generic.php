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

namespace Hoa\Router;

/**
 * Class \Hoa\Router\Generic.
 *
 * Generic router.
 *
 * @copyright  Copyright © 2007-2015 Hoa community
 * @license    New BSD License
 */
abstract class Generic implements Router
{
    /**
     * All rules buckets.
     *
     * @var array
     */
    protected $_rules          = [];

    /**
     * The routed rule.
     *
     * @var array
     */
    protected $_rule           = null;

    /**
     * Methods that the router understand.
     *
     * @var array
     */
    protected static $_methods = [];



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
     * @return  \Hoa\Router\Generic
     * @throws  \Hoa\Router\Exception
     */
    abstract protected function _addRule(
        $visibility,
        $id,
        Array $methods,
        $pattern,
        $call,
        $able,
        Array $variables
    );

    /**
     * Add a public rule.
     *
     * @param   string  $id           ID.
     * @param   array   $methods      HTTP methods allowed by the rule.
     * @param   string  $pattern      Pattern (on-subdomain@on-request).
     * @param   mixed   $call         Call (first part).
     * @param   mixed   $able         Able (second part).
     * @param   array   $variables    Variables (default or additional values).
     * @return  \Hoa\Router\Generic
     * @throws  \Hoa\Router\Exception
     */
    public function addRule(
        $id,
        Array $methods,
        $pattern,
        $call            = null,
        $able            = null,
        Array $variables = []
    ) {
        return $this->_addRule(
            Router::VISIBILITY_PUBLIC,
            $id,
            $methods,
            $pattern,
            $call,
            $able,
            $variables
        );
    }

    /**
     * Add a private rule.
     *
     * @param   string  $id           ID.
     * @param   array   $methods      HTTP methods allowed by the rule.
     * @param   string  $pattern      Pattern (on-subdomain@on-request).
     * @param   mixed   $call         Call (first part).
     * @param   mixed   $able         Able (second part).
     * @param   array   $variables    Variables (default or additional values).
     * @return  \Hoa\Router\Generic
     * @throws  \Hoa\Router\Exception
     */
    public function addPrivateRule(
        $id,
        Array $methods,
        $pattern,
        $call            = null,
        $able            = null,
        Array $variables = []
    ) {
        return $this->_addRule(
            Router::VISIBILITY_PRIVATE,
            $id,
            $methods,
            $pattern,
            $call,
            $able,
            $variables
        );
    }

    /**
     * Helper for adding rules.
     * Methods are concatenated by _. If prefixed by _, it's a private rule. In
     * addition, the keyword “any” takes place for all methods.
     * Examples:
     *     get(…)        : addRule(…, array('get'), …);
     *     get_post(…)   : addRule(…, array('get', 'post'), …);
     *     post_get(…)   : same that above;
     *     _get(…)       : addPrivateRule(…, array('get'), …);
     *     any(…)        : addRule(…, array(<all methods>), …);
     *     head_delete(…): addRule(…, array('head', 'delete'), …).
     *
     * @param   string  $name         Please, see API documentation.
     * @param   array   $arguments    Arguments for add*Rule() methods.
     * @return  \Hoa\Router\Generic
     * @throws  \Hoa\Router\Exception
     */
    public function __call($name, $arguments)
    {
        if ('_' === $name[0]) {
            $name   = substr($name, 1);
            $method = 'addPrivateRule';
        } else {
            $method = 'addRule';
        }

        if ('any' === $name) {
            array_unshift($arguments, static::$_methods);
        } else {
            array_unshift($arguments, explode('_', $name));
        }

        $handle       = $arguments[0];
        $arguments[0] = $arguments[1];
        $arguments[1] = $handle;

        return call_user_func_array([$this, $method], $arguments);
    }

    /**
     * Remove a rule.
     *
     * @param   string  $id    ID.
     * @return  void
     */
    public function removeRule($id)
    {
        unset($this->_rules[$id]);

        return;
    }

    /**
     * Check whether a rule exists.
     *
     * @param   string  $id    ID.
     * @return  bool
     */
    public function ruleExists($id)
    {
        return isset($this->_rules[$id]);
    }

    /**
     * Get all rules.
     *
     * @return  array
     */
    public function getRules()
    {
        return $this->_rules;
    }

    /**
     * Get a specific rule.
     *
     * @param   string  $id    ID.
     * @return  array
     * @throws  \Hoa\Router\Exception
     */
    public function getRule($id)
    {
        if (false === $this->ruleExists($id)) {
            throw new Exception('Rule %s does not exist.', 0, $id);
        }

        return $this->_rules[$id];
    }

    /**
     * Get the selected rule after routing.
     *
     * @return  array
     */
    public function &getTheRule()
    {
        return $this->_rule;
    }
}
