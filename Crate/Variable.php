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

namespace Hoa\Realdom\Crate;

use Hoa\Realdom;
use Hoa\Visitor;

/**
 * Class \Hoa\Realdom\Crate\Variable.
 *
 * Represent a variable.
 *
 * @copyright  Copyright © 2007-2017 Hoa community
 * @license    New BSD License
 */
class Variable implements Realdom\IRealdom\Crate
{
    /**
     * Variable.
     *
     * @var \Hoa\Realdom\IRealdom\Holder
     */
    protected $_variable = null;



    /**
     * Constructor.
     *
     * @param   \Hoa\Realdom\IRealdom\Holder  $variable    Variable.
     */
    public function __construct(Realdom\IRealdom\Holder $variable)
    {
        $this->setVariable($variable);

        return;
    }

    /**
     * Set variable.
     *
     * @param   \Hoa\Realdom\IRealdom\Holder  $variable    Variable.
     * @return  \Hoa\Realdom\IRealdom\Holder
     */
    public function setVariable(Realdom\IRealdom\Holder $variable)
    {
        $old             = $this->_variable;
        $this->_variable = $variable;
        $this->_domains  = &$variable->getDomains();

        return $old;
    }

    /**
     * Get variable.
     *
     * @return  \Hoa\Realdom\IRealdom\Holder
     */
    public function getVariable()
    {
        return $this->_variable;
    }

    /**
     * Get domains of the variable.
     *
     * @return  \Hoa\Realdom\Disjunction
     */
    public function &getDomains()
    {
        return $this->_domains;
    }

    /**
     * Get crate types.
     *
     * @return  array
     */
    public function getTypes()
    {
        $out = [];

        foreach ($this->getDomains() as $realdom) {
            $out[] = get_class($realdom);
        }

        return $out;
    }

    /**
     * Accept a visitor.
     *
     * @param   \Hoa\Visitor\Visit  $visitor    Visitor.
     * @param   mixed               &$handle    Handle (reference).
     * @param   mixed               $eldnah     Handle (no reference).
     * @return  mixed
     */
    public function accept(
        Visitor\Visit $visitor,
        &$handle = null,
        $eldnah  = null
    ) {
        return $visitor->visit($this, $handle, $eldnah);
    }
}
