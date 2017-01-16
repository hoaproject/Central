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

namespace Hoa\Xyl\Interpreter\Html;

use Hoa\Xyl;

/**
 * Class \Hoa\Xyl\Interpreter\Html\Concrete.
 *
 * Parent of all XYL components for this interpreter.
 *
 * @copyright  Copyright © 2007-2017 Hoa community
 * @license    New BSD License
 */
abstract class Concrete extends Xyl\Element\Concrete
{
    /**
     * Attributes mapping between XYL and HTML.
     * If $_attributesMapping is equal to “…”, it means that all XYL attributes
     * are copied as HTML attributes. Else, it is a simple mapping, e.g.:
     *     'href' => 'src'
     *
     * @var array
     */
    protected static $_attributesMapping = …;

    /**
     * HTML attributes.
     *
     * @var array
     */
    protected $_htmlAttributes           = [];

    /**
     * HTML attributes type (from mapping).
     *
     * @var array
     */
    protected $_htmlAttributesType       = [];

    /**
     * Whether content could exist or not.
     * 0 to false, 1 to true, 2 to maybe.
     *
     * @var int
     */
    protected $_contentFlow              = 2;



    /**
     * Construct element.
     *
     * @return  void
     */
    public function construct()
    {
        parent::construct();
        $this->mapAttributes();

        return;
    }

    /**
     * Map attributes (from XYL to HTML).
     *
     * @return  void
     */
    protected function mapAttributes()
    {
        $parent = get_called_class();
        $mapped = [];

        do {
            static::_mapAttributes($this, $parent, $mapped);
        } while (__CLASS__ !== $parent = get_parent_class($parent));

        static::_mapAttributes($this, $parent, $mapped);

        return;
    }

    /**
     * Real attributes mapping.
     *
     * @param   \Hoa\Xyl\Element\Concrete  $self       Element that will receive
     *                                                 attributes.
     * @param   string                     $parent     Parent's name.
     * @param   array                      &$mapped    Mapped attributes.
     * @return  void
     */
    private static function _mapAttributes($self, $parent, array &$mapped)
    {
        if (!isset($parent::$_attributesMapping) ||
            !isset($parent::$_attributes)) {
            return;
        }

        $map = $parent::$_attributesMapping;

        if (… === $map) {
            $map = array_keys($parent::$_attributes);
        }

        $map = array_diff($map, $mapped);

        foreach ($map as $from => $to) {
            if (is_int($from)) {
                $from = $to;
            }

            $type                           = $parent::$_attributes[$from];
            $self->_htmlAttributesType[$to] = $type;
            $mapped[]                       = $from;

            if (self::ATTRIBUTE_TYPE_CUSTOM == $type) {
                $values = $self->abstract->readCustomAttributes($from);

                foreach ($values as $name => $value) {
                    $self->writeAttribute($to . '-' . $name, $value);
                }

                continue;
            } elseif (null === $value = $self->abstract->readAttribute($from)) {
                continue;
            }

            $self->writeAttribute($to, $value);
        }

        return;
    }

    /**
     * Read all attributes.
     *
     * @return  array
     */
    public function readAttributes()
    {
        return $this->_htmlAttributes;
    }

    /**
     * Read a specific attribute.
     *
     * @param   string  $name    Attribute's name.
     * @return  string
     */
    public function readAttribute($name)
    {
        $attributes = $this->readAttributes();

        if (false === array_key_exists($name, $attributes)) {
            return null;
        }

        return $attributes[$name];
    }

    /**
     * Whether an attribute exists.
     *
     * @param   string  $name    Attribute's name.
     * @return  bool
     */
    public function attributeExists($name)
    {
        return true === array_key_exists($name, $this->readAttributes());
    }

    /**
     * Read attributes value as a list.
     *
     * @return  array
     */
    public function readAttributesAsList()
    {
        $attributes = $this->readAttributes();

        foreach ($attributes as $name => &$value) {
            $value = explode(' ', $value);
        }

        return $attributes;
    }

    /**
     * Read a attribute value as a list.
     *
     * @param   string  $name    Attribute's name.
     * @return  array
     */
    public function readAttributeAsList($name)
    {
        return explode(' ', $this->readAttribute($name));
    }

    /**
     * Read custom attributes (as a set).
     * For example:
     *     <component data-abc="def" data-uvw="xyz" />
     * “data” is a custom attribute, so the $set.
     *
     * @param   string  $set    Set name.
     * @return  array
     */
    public function readCustomAttributes($set)
    {
        $out     = [];
        $set    .= '-';
        $strlen  = strlen($set);

        foreach ($this->readAttributes() as $name => $value) {
            if ($set === substr($name, 0, $strlen)) {
                $out[substr($name, $strlen)] = $value;
            }
        }

        return $out;
    }

    /**
     * Read custom attributes values as a list.
     *
     * @param   string  $set    Set name.
     * @return  array
     */
    public function readCustomAttributesAsList($set)
    {
        $out = [];

        foreach ($this->readCustomAttributes($set) as $name => $value) {
            $out[$name] = explode(' ', $value);
        }

        return $out;
    }

    /**
     * Read attributes as a string.
     *
     * @param   bool  $compute    Whether we automatically call the
     *                            Hoa\Xyl\Element\Concrete::computeAttributeValue()
     *                            method.
     * @return  string
     */
    public function readAttributesAsString($compute = true)
    {
        $out = null;

        foreach ($this->readAttributes() as $name => $value) {
            if (null !== $value) {
                $out .=
                    ' ' . $name . '="' .
                    str_replace(
                        '"',
                        '\"',
                        true === $compute
                            ? $this->computeAttributeValue(
                                $value,
                                isset($this->_htmlAttributesType[$name])
                                    ? $this->_htmlAttributesType[$name]
                                    : parent::ATTRIBUTE_TYPE_NORMAL,
                                $name
                            )
                            : $value
                    ) .
                    '"';
            }
        }

        return $out;
    }

    /**
     * Write attributes.
     * If an attribute does not exist, it will be created.
     *
     * @param   array   $attributes    Attributes.
     * @return  void
     */
    public function writeAttributes(array $attributes)
    {
        foreach ($attributes as $name => $value) {
            $this->writeAttribute($name, $value);
        }

        return;
    }

    /**
     * Write an attribute.
     * If the attribute does not exist, it will be created.
     *
     * @param   string  $name     Name.
     * @param   string  $value    Value.
     * @return  void
     */
    public function writeAttribute($name, $value)
    {
        $this->_htmlAttributes[$name] = $value;

        return;
    }

    /**
     * Write custom attributes (as a set).
     * For example:
     *     <component data-abc="def" data-uvw="xyz" />
     * “data” is a custom attribute, so the $set.
     *
     * @param   string  $set      Set name.
     * @param   array   $pairs    Pairs of attribute/value (e.g. abc => def,
     *                            uvw => xyz).
     * @return  void
     */
    public function writeCustomAttributes($set, array $pairs)
    {
        foreach ($pairs as $attribute => $value) {
            $this->writeAttribute($set . '-' . $attribute, $value);
        }

        return;
    }

    /**
     * Remove an attribute.
     *
     * @param   string  $name    Name.
     * @return  void
     */
    public function removeAttribute($name)
    {
        unset($this->_htmlAttributes[$name]);

        return;
    }

    /**
     * Get component name.
     *
     * @reurn   string
     */
    public function getName()
    {
        return $this->abstract->getName();
    }
}
