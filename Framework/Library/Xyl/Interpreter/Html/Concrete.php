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
 * \Hoa\Xyl\Element\Concrete
 */
-> import('Xyl.Element.Concrete');

}

namespace Hoa\Xyl\Interpreter\Html {

/**
 * Class \Hoa\Xyl\Interpreter\Html\Concrete.
 *
 * Sub-concrete element.
 *
 * @author     Ivan Enderlin <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright © 2007-2011 Ivan Enderlin.
 * @license    New BSD License
 */

abstract class Concrete extends \Hoa\Xyl\Element\Concrete {

    /**
     * Attributes description.
     *
     * @var \Hoa\Xyl\Interpreter\Html\Concrete array
     */
    protected $_attributes        = array(
        'id'    => null,
        'class' => null
    );

    /**
     * Attributes description of the interpreted element.
     *
     * @var \Hoa\Xyl\Interpreter\Html\Concrete array
     */
    protected $_iAttributes       = array(
        'id'    => null,
        'class' => null
    );

    /**
     * Attributes mapping from XYL to the interpreter.
     *
     * @var \Hoa\Xyl\Interpreter\Html\Concrete array
     */
    protected $_attributesMapping = array(
        'id'    => 'id',
        'class' => 'class'
    );

    /**
     * Extra attributes defined by child.
     *
     * @var \Hoa\Xyl\Interpreter\Html\Concrete array
     */
    protected $iAttributes        = null;

    /**
     * Extra attributes mapping defined by child.
     *
     * @var \Hoa\Xyl\Interpreter\Html\Concrete array
     */
    protected $attributesMapping  = null;



    public function construct ( ) {

        if(null !== $this->attributesMapping)
            $this->_attributesMapping = array_merge(
                $this->_attributesMapping,
                $this->attributesMapping
            );

        if(null !== $this->iAttributes)
            $this->_iAttributes = array_merge(
                $this->_iAttributes,
                $this->iAttributes
            );

        $e = $this->getAbstractElement();

        foreach($this->_attributesMapping as $from => $to)
            $this->writeAttribute(
                $to,
                $e->readAttribute($from)
            );

        return;
    }

    /**
     * Read all attributes.
     *
     * @access  public
     * @return  array
     */
    public function readAttributes ( ) {

        return $this->_iAttributes;
    }

    /**
     * Read a specific attribute.
     *
     * @access  public
     * @param   string  $name    Attribute's name.
     * @return  string
     */
    public function readAttribute ( $name ) {

        $attributes = $this->readAttributes();

        if(false === array_key_exists($name, $attributes))
            return null;

        return $attributes[$name];
    }

    /**
     * Whether an attribute exists.
     *
     * @access  public
     * @param   string  $name    Attribute's name.
     * @return  bool
     */
    public function attributeExists ( $name ) {

        return true === array_key_exists($name, $this->readAttributes());
    }

    /**
     * Read attributes value as a list.
     *
     * @access  public
     * @return  array
     */
    public function readAttributesAsList ( ) {

        $attributes = $this->readAttributes();

        foreach($attributes as $name => &$value)
            $value = explode(' ', $value);

        return $attributes;
    }

    /**
     * Read a attribute value as a list.
     *
     * @access  public
     * @param   string  $name    Attribute's name.
     * @return  array
     */
    public function readAttributeAsList ( $name ) {

        return explode(' ', $this->readAttribute($name));
    }

    /**
     * Read custom attributes (as a set).
     * For example:
     *     <component data-abc="def" data-uvw="xyz" />
     * “data” is a custom attribute, so the $set.
     *
     * @access  public
     * @param   string  $set    Set name.
     * @return  array
     */
    public function readCustomAttributes ( $set ) {

        $out     = array();
        $set    .= '-';
        $strlen  = strlen($set);

        foreach($this->readAttributes() as $name => $value)
            if($set === substr($name, 0, $strlen))
                $out[substr($name, $strlen)] = $value;

        return $out;
    }

    /**
     * Read custom attributes values as a list.
     *
     * @access  public
     * @param   string  $set    Set name.
     * @return  array
     */
    public function readCustomAttributesAsList ( $set ) {

        $out = array();

        foreach($this->readCustomAttributes($set) as $name => $value)
            $out[$name] = explode(' ', $value);

        return $out;
    }

    /**
     * Read attributes as a string.
     *
     * @access  public
     * @return  string
     */
    public function readAttributesAsString ( ) {

        $out = null;

        foreach($this->readAttributes() as $name => $value)
            if(null !== $value)
                $out .= ' ' . $name . '="' . str_replace('"', '\"', $value) . '"';

        return $out;
    }

    /**
     * Write attributes.
     * If an attribute does not exist, it will be created.
     *
     * @access  public
     * @param   array   $attributes    Attributes.
     * @return  void
     */
    public function writeAttributes ( Array $attributes ) {

        foreach($attributes as $name => $value)
            $this->writeAttribute($name, $value);

        return;
    }

    /**
     * Write an attribute.
     * If the attribute does not exist, it will be created.
     *
     * @access  public
     * @param   string  $name     Name.
     * @param   string  $value    Value.
     * @return  void
     */
    public function writeAttribute ( $name, $value ) {

        $this->_iAttributes[$name] = $value;

        return;
    }

    /**
     * Write custom attributes (as a set).
     * For example:
     *     <component data-abc="def" data-uvw="xyz" />
     * “data” is a custom attribute, so the $set.
     *
     * @access  public
     * @param   string  $set      Set name.
     * @param   array   $pairs    Pairs of attribute/value (e.g. abc => def,
     *                            uvw => xyz).
     * @return  void
     */
    public function writeCustomAttributes ( $set, Array $pairs ) {

        foreach($pairs as $attribute => $value)
            $this->writeAttribute($set . '-' . $attribute, $value);

        return;
    }

    /**
     * Remove an attribute.
     *
     * @access  public
     * @param   string  $name    Name.
     * @return  void
     */
    public function removeAttribute ( $name ) {

        unset($this->_iAttributes[$name]);

        return;
    }
}

}
