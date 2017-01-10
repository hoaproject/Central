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

use Hoa\Visitor;

/**
 * Class \Hoa\Praspel\Model\Clause.
 *
 * Represent a clause.
 *
 * @copyright  Copyright © 2007-2017 Hoa community
 * @license    New BSD License
 */
abstract class Clause implements Visitor\Element
{
    /**
     * Name.
     *
     * @const int
     */
    // const NAME = …;

    /**
     * Parent clause.
     *
     * @var \Hoa\Praspel\Model\Clause
     */
    protected $_parent = null;



    /**
     * Build a clause.
     *
     * @param   \Hoa\Praspel\Model\Clause  $parent    Parent.
     */
    public function __construct(Clause $parent)
    {
        $this->setParent($parent);

        return;
    }

    /**
     * Set parent clause.
     *
     * @param   \Hoa\Praspel\Model\Clause  $parent    Parent.
     * @return  \Hoa\Praspel\Model\Clause
     */
    protected function setParent(Clause $parent)
    {
        $old           = $this->_parent;
        $this->_parent = $parent;

        return $old;
    }

    /**
     * Get parent clause.
     *
     * @return  \Hoa\Praspel\Model\Clause
     */
    public function getParent()
    {
        return $this->_parent;
    }

    /**
     * Get the root clause.
     *
     * @return  \Hoa\Praspel\Model\Clause
     */
    public function getRoot()
    {
        $parent = $this;

        while (null !== $nextParent = $parent->getParent()) {
            $parent = $nextParent;
        }

        return $parent;
    }

    /**
     * Get clause name.
     *
     * @return  string
     */
    public function getName()
    {
        return static::NAME;
    }

    /**
     * Get identifier.
     *
     * @return  string
     */
    public function getId()
    {
        $out    = null;
        $parent = $this->getParent();

        if (null !== $parent &&
            !($parent instanceof Specification)) {
            $out .= $this->getParent()->getId() . '_';
        }

        return $out . $this->_getId();
    }

    /**
     * Get identifier (fallback).
     *
     * @return  string
     */
    protected function _getId()
    {
        return $this->getName();
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
