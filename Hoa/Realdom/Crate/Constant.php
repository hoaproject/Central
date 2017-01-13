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

use Hoa\Praspel;
use Hoa\Realdom;
use Hoa\Visitor;

/**
 * Class \Hoa\Realdom\Crate\Constant.
 *
 * Represent a mocked constant.
 *
 * @copyright  Copyright © 2007-2017 Hoa community
 * @license    New BSD License
 */
class          Constant
    implements Realdom\IRealdom\Crate,
               Realdom\IRealdom\Constant
{
    /**
     * Holder.
     *
     * @var \Hoa\Realdom\IRealdom\Holder
     */
    protected $_holder                = null;

    /**
     * Praspel representation.
     *
     * @var \Closure
     */
    protected $_praspelRepresentation = null;

    /**
     * Original declaration object.
     *
     * @var \Hoa\Praspel\Model\Declaration
     */
    protected $_declaration           = null;



    /**
     * Constructor.
     *
     * @param   \Hoa\Realdom\IRealdom\Holder    $holder         Holder.
     * @param   \Closure                        $praspel        Praspel
     *                                                          representation.
     * @param   \Hoa\Praspel\Model\Declaration  $declaration    Original
     *                                                          declaration
     *                                                          object.
     */
    public function __construct(
        Realdom\IRealdom\Holder $holder,
        \Closure $praspel,
        Praspel\Model\Declaration $declaration = null
    ) {
        $this->setHolder($holder);
        $this->setPraspelRepresentation($praspel);
        $this->setDeclaration($declaration);

        return;
    }

    /**
     * Set holder.
     *
     * @param   \Hoa\Realdom\IRealdom\Holder  $holder    Holder.
     * @return  \Hoa\Realdom\IRealdom\Holder
     */
    protected function setHolder(Realdom\IRealdom\Holder $holder)
    {
        $old           = $this->_holder;
        $this->_holder = $holder;

        return $old;
    }

    /**
     * Get holder.
     *
     * @return  \Hoa\Realdom\IRealdom\Holder
     */
    public function getHolder()
    {
        return $this->_holder;
    }

    /**
     * Get crate types.
     *
     * @return  array
     * @throws  \Hoa\Realdom\Exception
     */
    public function getTypes()
    {
        $held   = $this->getHolder()->getHeld();
        $out    = [];
        $prefix = 'Hoa\Realdom\\';

        foreach ($held as $realdom) {
            if ($realdom instanceof Realdom\RealdomArray) {
                $out[] = $prefix . 'Constarray';
            } elseif ($realdom instanceof Realdom\Boolean) {
                $out[] = $prefix . 'Constboolean';
            } elseif ($realdom instanceof Realdom\RealdomFloat) {
                $out[] = $prefix . 'Constfloat';
            } elseif ($realdom instanceof Realdom\Integer) {
                $out[] = $prefix . 'Constinteger';
            } elseif ($realdom instanceof Realdom\RealdomString) {
                $out[] = $prefix . 'Conststring';
            } else {
                throw new Realdom\Exception(
                    'Cannot determine the type.',
                    0
                );
            }
        }

        return $out;
    }

    /**
     * Get constant value.
     *
     * @return  mixed
     */
    public function getConstantValue()
    {
        return $this->getHolder()->getValue();
    }

    /**
     * Set Praspel representation.
     *
     * @param   \Closure  $praspel    Praspel representation.
     * @return  \Closure
     */
    protected function setPraspelRepresentation(\Closure $praspel)
    {
        $old                          = $this->_praspelRepresentation;
        $this->_praspelRepresentation = $praspel;

        return $old;
    }

    /**
     * Get Praspel representation.
     *
     * @return  \Closure
     */
    public function getPraspelRepresentation()
    {
        return $this->_praspelRepresentation;
    }

    /**
     * Set original declaration object.
     *
     * @param   \Hoa\Praspel\Model\Declaration  $declaration    Declaration.
     * @return  \Hoa\Praspel\Model\Declaration
     */
    public function setDeclaration(Praspel\Model\Declaration $declaration)
    {
        $old                = $this->_declaration;
        $this->_declaration = $declaration;

        return $old;
    }

    /**
     * Get original declaration object.
     *
     * @return  \Hoa\Praspel\Model\Declaration
     */
    public function getDeclaration()
    {
        return $this->_declaration;
    }

    /**
     * Get representation of the realistic domain.
     *
     * @return  string
     */
    public function getConstantRepresentation()
    {
        return '';
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
