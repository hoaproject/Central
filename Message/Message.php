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

namespace Hoa\XmlRpc\Message;

use Hoa\Consistency;

/**
 * Class \Hoa\XmlRpc\Message.
 *
 * Write XML-RPC values intuitively on a message.
 * XML-RPC specification can be found at: http://xmlrpc.com/spec.
 *
 * @copyright  Copyright © 2007-2017 Hoa community
 * @license    New BSD License
 */
class Message
{
    /**
     * Values bucket: value index.
     *
     * @const int
     */
    const VALUE          =  0;

    /**
     * Values bucket: type index.
     *
     * @const int
     */
    const TYPE           =  1;

    /**
     * Value type: <array>.
     *
     * @const int
     */
    const TYPE_ARRAY     =  2;

    /**
     * Value type: <base64>.
     *
     * @const int
     */
    const TYPE_BASE64    =  3;

    /**
     * Value type: <boolean>.
     *
     * @const int
     */
    const TYPE_BOOLEAN   =  4;

    /**
     * Value type: <datetime.iso8601>.
     *
     * @const int
     */
    const TYPE_DATETIME  =  5;

    /**
     * Value type: <double>.
     *
     * @const int
     */
    const TYPE_FLOAT     =  6;

    /**
     * Value type: <i4> or <integer>.
     *
     * @const int
     */
    const TYPE_INTEGER   =  7;

    /**
     * Value type: <string>.
     *
     * @const int
     */
    const TYPE_STRING    =  8;

    /**
     * Value type: <structure>.
     *
     * @const int
     */
    const TYPE_STRUCTURE =  9;

    /**
     * Value type: <nil>.
     *
     * @const int
     */
    const TYPE_NULL      = 10;

    /**
     * Whether we manipulate a scalar.
     *
     * @const int
     */
    const IS_SCALAR      = 11;

    /**
     * Whether we manipulate an array.
     *
     * @const int
     */
    const IS_ARRAY       = 12;

    /**
     * Whether we manipulate a structure.
     *
     * @const int
     */
    const IS_STRUCTURE   = 13;

    /**
     * Values bucket (value, type).
     *
     * @var array
     */
    protected $_values = [];

    /**
     * Parent (for nested values).
     *
     * @var \Hoa\XmlRpc\Message
     */
    protected $_parent = null;

    /**
     * Current name for structure.
     *
     * @var string
     */
    protected $_name   = 'undefined';

    /**
     * Type of the current value.
     *
     * @var int
     */
    protected $_is     = self::IS_SCALAR;



    /**
     * Build a new value object.
     *
     * @param   int                  $is        Type of object. Please,
     *                                          see the self::IS_*
     *                                          constants.
     * @param   \Hoa\XmlRpc\Message  $parent    Parent.
     */
    public function __construct($is = self::IS_SCALAR, $parent = null)
    {
        $this->_is = $is;
        $this->setParent($parent);

        return;
    }

    /**
     * Generic method to “with” (:-p).
     *
     * @param   mixed  $value    Value.
     * @param   int    $type     Type. Please, see the self::TYPE_* constants.
     * @return  \Hoa\XmlRpc\Message
     */
    protected function _with($value, $type)
    {
        if (self::IS_STRUCTURE == $this->_is) {
            $this->_values[$this->_name] = [$value, $type];
        } else {
            $this->_values[] = [$value, $type];
        }

        return $this;
    }

    /**
     * Start an array.
     *
     * @return  \Hoa\XmlRpc\Message
     */
    public function withArray()
    {
        $self = __CLASS__;

        return new $self(self::IS_ARRAY, $this);
    }

    /**
     * Stop an array.
     *
     * @return  \Hoa\XmlRpc\Message
     */
    public function endArray()
    {
        $parent = $this->getParent();
        $parent->_with($this->getValues(), self::TYPE_ARRAY);

        return $parent;
    }

    /**
     * Add a base64 value.
     *
     * @param   mixed   $data    Data.
     * @return  \Hoa\XmlRpc\Message
     */
    public function withBase64($data)
    {
        return $this->_with($data, self::TYPE_BASE64);
    }

    /**
     * Add a boolean value.
     *
     * @param   mixed   $data    Data.
     * @return  \Hoa\XmlRpc\Message
     */
    public function withBoolean($data)
    {
        return $this->_with((boolean) $data, self::TYPE_BOOLEAN);
    }

    /**
     * Add a date/time value.
     *
     * @param   mixed   $data    Data.
     * @return  \Hoa\XmlRpc\Message
     */
    public function withDateTime($timestamp)
    {
        return $this->_with($timestamp, self::TYPE_DATETIME);
    }

    /**
     * Add a float value.
     *
     * @param   mixed   $data    Data.
     * @return  \Hoa\XmlRpc\Message
     */
    public function withFloat($float)
    {
        return $this->_with((float) $float, self::TYPE_FLOAT);
    }

    /**
     * Add an integer value.
     *
     * @param   mixed   $data    Data.
     * @return  \Hoa\XmlRpc\Message
     */
    public function withInteger($integer)
    {
        return $this->_with((int) $integer, self::TYPE_INTEGER);
    }

    /**
     * Add a string value.
     *
     * @param   mixed   $data    Data.
     * @return  \Hoa\XmlRpc\Message
     */
    public function withString($string)
    {
        return $this->_with((string) $string, self::TYPE_STRING);
    }

    /**
     * Start a structure.
     *
     * @param   mixed   $data    Data.
     * @return  \Hoa\XmlRpc\Message
     */
    public function withStructure()
    {
        $self = __CLASS__;

        return new $self(self::IS_STRUCTURE, $this);
    }

    /**
     * Add a named value.
     *
     * @param   string  $name    Name.
     * @return  \Hoa\XmlRpc\Message
     */
    public function withName($name)
    {
        if (self::IS_STRUCTURE !== $this->_is) {
            return $this;
        }

        $this->_name = $name;

        return $this;
    }

    /**
     * Stop a structure.
     *
     * @return  \Hoa\XmlRpc\Message
     */
    public function endStructure()
    {
        $parent = $this->getParent();
        $parent->_with($this->getValues(), self::TYPE_STRUCTURE);

        return $parent;
    }

    /**
     * Add a null value.
     *
     * @param   mixed   $data    Data.
     * @return  \Hoa\XmlRpc\Message
     */
    public function withNull()
    {
        return $this->_with(null, self::TYPE_NULL);
    }

    /**
     * Set current parent.
     *
     * @param   \Hoa\XmlRpc\Message  $parent    Parent.
     * @return  \Hoa\XmlRpc\Message
     */
    protected function setParent($parent)
    {
        $old           = $this->_parent;
        $this->_parent = $parent;

        return $old;
    }

    /**
     * Get current parent.
     *
     * @return  \Hoa\XmlRpc\Message
     */
    public function getParent()
    {
        return $this->_parent;
    }

    /**
     * Get values bucket.
     *
     * @return  array
     */
    public function getValues()
    {
        return $this->_values;
    }

    /**
     * Get a value as a XML string.
     *
     * @param   mixed  $value    Value.
     * @param   int    $type     Type. Please, see the self::TYPE_* constants.
     * @return  string
     */
    public function getValueAsString($value, $type)
    {
        switch ($type) {
            case self::TYPE_ARRAY:
                $out = '<array>' . "\n" . '  <data>' . "\n";

                foreach ($value as $v) {
                    $out .=
                        '    <value>' . $this->getValueAsString(
                           $v[self::VALUE],
                           $v[self::TYPE]
                        ) . '</value>' . "\n";
                }

                return $out . '  </data>' . "\n" . '</array>';

            case self::TYPE_BASE64:
                return '<base64>' . base64_encode($value) . '</base64>';

            case self::TYPE_BOOLEAN:
                return '<boolean>' . (true == $value ? '1' : '0') . '</boolean>';

            case self::TYPE_DATETIME:
                return '<dateTime.iso8601>' . date('c', $value) . '</dateTime.iso8601>';

            case self::TYPE_FLOAT:
                return '<double>' . $value . '</double>';

            case self::TYPE_INTEGER:
                return '<i4>' . $value . '</i4>';

            case self::TYPE_STRING:
                return '<string>' . htmlspecialchars($value) . '</string>';

            case self::TYPE_STRUCTURE:
                $out = '<struct>' . "\n";

                foreach ($value as $name => $v) {
                    $out .=
                        '  <member>' . "\n" .
                        '    <name>' . $name . '</name>' . "\n" .
                        '    <value>' . $this->getValueAsString(
                            $v[self::VALUE],
                            $v[self::TYPE]
                        ) . '</value>' . "\n" .
                        '  </member>' . "\n";
                }

                return $out . '</struct>';

            case self::TYPE_NULL:
                return '<nil />';
        }
    }

    /**
     * Get values formatted (comprehensive array).
     *
     * @return  array
     */
    public function getFormattedValues()
    {
        return $this->_getFormattedValues($this->getValues());
    }

    /**
     * Built formatted values.
     *
     * @return  array
     */
    protected function _getFormattedValues($values)
    {
        if (!is_array($values)) {
            return $values;
        }

        $formatted = [];

        foreach ($values as $key => $value) {
            $formatted[$key] = $this->_getFormattedValues($value[self::VALUE]);
        }

        return $formatted;
    }

    /**
     * Compute XML values into values bucket.
     *
     * @param   array                $values    Values XML collection.
     * @param   \Hoa\XmlRpc\Message  $self      Current valued object.
     * @return  void
     */
    protected function computeValues($values, $self = null)
    {
        if (null === $self) {
            $self = $this;
        }

        if (!is_array($values)) {
            $values = [$values];
        }

        foreach ($values as $value) {
            switch (strtolower($value->getName())) {
                case 'array':
                    $self = $self->withArray();

                    foreach ($value->data as $data) {
                        $this->computeValues($data->xpath('./value/*'), $self);
                    }

                    $self = $self->endArray();

                    break;

                case 'base64':
                    $self->withBase64(base64_decode($value->readAll()));

                    break;

                case 'boolean':
                    $self->withBoolean((boolean) (int) $value->readAll());

                    break;

                case 'datetime.iso8601':
                    $self->withDateTime(strtotime($value->readAll()));

                    break;

                case 'double':
                    $self->withFloat((float) $value->readAll());

                    break;

                case 'i4':
                case 'int':
                    $self->withInteger((int) $value->readAll());

                    break;

                case 'string':
                    $self->withString(htmlspecialchars_decode($value->readAll()));

                    break;

                case 'struct':
                    $self = $self->withStructure();

                    foreach ($value->member as $member) {
                        $self->withName($member->name->readAll());
                        $this->computeValues($member->xpath('./value/*'), $self);
                    }

                    $self = $self->endStructure();

                    break;

                case 'nil':
                    $self->withNull();

                    break;
            }
        }

        return;
    }
}

/**
 * Flex entity.
 */
Consistency::flexEntity('Hoa\XmlRpc\Message\Message');
