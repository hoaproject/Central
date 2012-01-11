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

namespace Hoa\Router {

/**
 * Interface \Hoa\Router.
 *
 * Router interface (very abstract).
 *
 * @author     Ivan Enderlin <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright © 2007-2012 Ivan Enderlin.
 * @license    New BSD License
 */

interface Router {

    /**
     * Rule visibility: public.
     *
     * @const int
     */
    const VISIBILITY_PUBLIC  = 0;

    /**
     * Rule visibility: private.
     *
     * @const int
     */
    const VISIBILITY_PRIVATE = 1;

    /**
     * Rule bucket: visibility (please, see the self::VISIBILITY_* constants).
     *
     * @const int
     */
    const RULE_VISIBILITY    = 0;

    /**
     * Rule bucket: methods.
     *
     * @const int
     */
    const RULE_METHODS       = 1;

    /**
     * Rule bucket: ID.
     *
     * @const int
     */
    const RULE_ID            = 2;

    /**
     * Rule bucket: pattern.
     *
     * @const int
     */
    const RULE_PATTERN       = 3;

    /**
     * Rule bucket: call.
     *
     * @const int
     */
    const RULE_CALL          = 4;

    /**
     * Rule bucket: able.
     *
     * @const int
     */
    const RULE_ABLE          = 5;

    /**
     * Rule bucket: variables (extracted from patterns).
     *
     * @const int
     */
    const RULE_VARIABLES     = 6;

    /**
     * Get the selected rule after routing.
     *
     * @access  public
     * @return  mixed
     */
    public function getTheRule ( );

    /**
     * Find the appropriated rule.
     *
     * @access  public
     * @return  \Hoa\Router
     * @throw   \Hoa\Router\Exception\NotFound
     */
    public function route ( );

    /**
     * Unroute a rule (i.e. route()^-1).
     *
     * @access  public
     * @param   string  $id           ID.
     * @param   array   $variables    Variables.
     * @return  string
     */
    public function unroute ( $id, Array $variables = array() );

    /**
     * Get method or mode where the router is called.
     *
     * @access  public
     * @return  string
     */
    public function getMethod ( );

    /**
     * Whether the router is called asynchronously or not.
     *
     * @access  public
     * @return  bool
     */
    public function isAsynchronous ( );

    /**
     * Check whether a rule exists.
     *
     * @access  public
     * @param   string  $id    ID.
     * @return  bool
     */
    public function ruleExists ( $id );
}

}
