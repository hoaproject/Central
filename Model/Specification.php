<?php

/**
 * Hoa
 *
 *
 * @license
 *
 * New BSD License
 *
 * Copyright © 2007-2017, Hoa community. All rights reserved.
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

namespace Hoa\Praspel\Model;

use Hoa\Consistency;
use Hoa\Praspel;

/**
 * Class \Hoa\Praspel\Model\Specification.
 *
 * Represent a specification (contains all clauses).
 *
 * @copyright  Copyright © 2007-2017 Hoa community
 * @license    New BSD License
 */
class Specification extends Behavior
{
    /**
     * Name.
     *
     * @const string
     */
    const NAME = '';

    /**
     * Allowed clauses.
     *
     * @var array
     */
    protected static $_allowedClauses = [
        'is',
        'invariant',
        'requires',
        'behavior',
        'default',
        'ensures',
        'throwable',
        'description'
    ];

    /**
     * Implicit variables.
     *
     * @var array
     */
    protected $_implicitVariables     = [];

    /**
     * Binded class.
     *
     * @var string
     */
    protected $_bindedClass           = null;



    /**
     * Cancel the constructor from the parent.
     *
     */
    public function __construct()
    {
        return;
    }

    /**
     * Get an implicit variable.
     *
     * @param   string  $identifier    Identifier.
     * @return  \Hoa\Praspel\Model\Variable\Implicit
     */
    public function getImplicitVariable($identifier)
    {
        if (isset($this->_implicitVariables[$identifier])) {
            return $this->_implicitVariables[$identifier];
        }

        return
            $this->_implicitVariables[$identifier] =
                new Variable\Implicit($identifier, false, $this);
    }

    /**
     * Bind this specification to a specific class.
     * Obligatory for dynamic or static resolutions.
     *
     * @return  string
     */
    public function bindToClass($classname)
    {
        $old                = $this->_bindedClass;
        $this->_bindedClass = ltrim($classname, '\\');

        return $old;
    }

    /**
     * Get binded class.
     *
     * @return  string
     */
    public function getBindedClass()
    {
        return $this->_bindedClass;
    }

    /**
     * Get identifier (fallback).
     *
     * @return  string
     */
    protected function _getId()
    {
        return 'praspel';
    }
}

if (false === Consistency::entityExists('Hoa\Realdom\Disjunction', true)) {
    throw new Praspel\Exception('Hoa\Realdom seems to not be loaded.');
}
