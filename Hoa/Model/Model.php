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

namespace Hoa\Model;

use Hoa\Consistency;
use Hoa\Praspel;
use Hoa\Xyl;

/**
 * Class \Hoa\Model\Exception.
 *
 * Represent a model/document with attributes and relations.
 *
 * @copyright  Copyright © 2007-2017 Hoa community
 * @license    New BSD License
 */
abstract class Model
    implements Xyl\Data\Datable,
               \ArrayAccess,
               \IteratorAggregate,
               \Countable
{
    /**
     * Whether we should check Praspel and validate*().
     *
     * @var bool
     */
    private $__validation            = true;

    /**
     * Bucket of all model attributes.
     *
     * @var array
     */
    private $__attributes            = [];

    /**
     * Current attribute in an array access.
     *
     * @var string
     */
    private $__currentAccess         = null;

    /**
     * Mapping layers.
     *
     * @var array
     */
    protected static $__mappingLayer = ['_default' => null];



    /**
     * Initialize the model.
     *
     */
    final public function __construct()
    {
        $class   = new \ReflectionClass($this);
        $ucfirst = function (array $matches) {
            return ucfirst($matches[1]);
        };
        $default = $class->getDefaultProperties();

        foreach ($class->getProperties() as $property) {
            $_name = $property->getName();

            if ('_' !== $_name[0]
               || '_' === $_name[1]) {
                continue;
            }

            $name      = substr($_name, 1);
            $comment   = $property->getDocComment();
            $comment   = preg_replace('#^(\s*/\*\*\s*)#', '', $comment);
            $comment   = preg_replace('#(\s*\*/)#',       '', $comment);
            $comment   = preg_replace('#^(\s*\*\s*)#m',   '', $comment);
            $relation  = false !== strpos($comment, 'relation(');
            $validator = 'validate' . ucfirst(preg_replace_callback(
                '#_(.)#',
                $ucfirst,
                strtolower($name)
            ));

            $this->__attributes[$name] = [
                'comment'   => $comment ?: false,
                'name'      => $name,
                '_name'     => $_name,
                'validator' => method_exists($this, $validator)
                                   ? $validator
                                   : null,
                'contract'  => null, // be lazy
                'default'   => $default[$_name],
                'value'     => &$this->$_name,
                'relation'  => $relation
            ];

            if (true === $relation && empty($this->$_name)) {
                $this->$_name = [];
            }
        }

        $this->construct();

        return;
    }

    /**
     * User constructor.
     *
     * @return  void
     */
    protected function construct()
    {
        return;
    }

    /**
     * Open many documents.
     *
     * @param   array  $constraints    Contraints.
     * @return  void
     */
    public function openMany(array $constraints = [])
    {
        return;
    }

    /**
     * Open one document.
     *
     * @param   array  $constraints    Contraints.
     * @return  void
     */
    public function open(array $constraints = [])
    {
        return;
    }

    /**
     * Save the document.
     *
     * @return  void
     */
    public function save()
    {
        return;
    }

    /**
     * Map data to attributes.
     *
     * @param   array  $data    Data.
     * @param   array  $map     Map: data name to attribute name.
     * @return  \Hoa\Model
     */
    protected function map(array $data, array $map = null)
    {
        if (null !== $this->__currentAccess) {
            return $this->mapRelation(
                substr($this->__currentAccess, 1),
                $data,
                $map
            );
        }

        if (empty($map)) {
            $map = array_combine($handle = array_keys($data), $handle);
        }

        foreach ($data as $name => $value) {
            if (array_key_exists($name, $map)) {
                $name = $map[$name];
            }

            if ($value == $this->$name) {
                continue;
            }

            $this->$name = $value;
        }

        return $this;
    }

    /**
     * Map data to a relation attribute.
     *
     * @param   array  $name    Relation name.
     * @param   array  $data    Data.
     * @param   array  $map     Map: data name to attribute name.
     * @return  \Hoa\Model
     */
    protected function mapRelation($name, array $data, array $map = null)
    {
        if (!isset($this->$name)) {
            throw new Exception(
                'Cannot map relation %s because it does not exist.', 42, $name);
        }

        $attribute = &$this->getAttribute($name);

        if (true !== $attribute['relation']) {
            throw new Exception(
                'Cannot map relation %s because it is not a relation.',
                43, $name);
        }

        if (null === $attribute['contract']) {
            $attribute['contract'] = Praspel::interpret($attribute['comment']);
        }

        $realdom   = $attribute['contract']
                         ->getClause('invariant')
                         ->getVariable($name)
                         ->getDomains()
                         ->offsetGet(0);
        $classname = $realdom['classname']->getConstantValue();
        $_name     = '_' . $name;

        foreach ($data as $i => $d) {
            $this->__currentAccess = $_name;
            $class                 = new $classname();
            $this->offsetSet($i, $class->map($d, $map));
        }

        $this->__currentAccess = null;

        return $this;
    }

    /**
     * Get constraints, i.e. all attributes values that defer from their default
     * values. We could include the default values if the only argument is set
     * to true.
     *
     * @param   bool  $defaultValues    Whether we include default values.
     * @return  array
     */
    protected function getConstraints($defaultValues = false)
    {
        $out = [];

        foreach ($this->__attributes as $name => $attribute) {
            if (false                 === $attribute['relation']
               && (true                 === $defaultValues
               || $attribute['default'] !== $attribute['value'])) {
                $out[$name] = $attribute['value'];
            }
        }

        return $out;
    }

    /**
     * Check if an attribute exists and is set.
     *
     * @param   string  $name    Attribute name.
     * @return  bool
     */
    public function __isset($name)
    {
        return array_key_exists($name, $this->__attributes);
    }

    /**
     * Set a value to an attribute.
     *
     * @param   string  $name     Name.
     * @param   mixed   $value    Value.
     * @return  void
     * @throws  \Hoa\Model\Exception
     */
    public function __set($name, $value)
    {
        if (!isset($this->$name)) {
            return null;
        }

        $_name     = '_' . $name;
        $attribute = &$this->getAttribute($name);

        if (true === $attribute['relation']) {
            if (!is_array($value)) {
                $value = [$value];
            }

            $this->__currentAccess = $_name;

            foreach ($value as $k => $v) {
                $this->offsetSet($k, $v);
            }

            $this->__currentAccess = null;

            return;
        }

        if (is_numeric($value)) {
            if ($value == $_value = (int) $value) {
                $value = $_value;
            } else {
                $value = (float) $value;
            }
        }

        if (false === $this->isValidationEnabled()) {
            $old          = $this->$_name;
            $this->$_name = $value;

            return $old;
        }

        if (false !== $attribute['comment']) {
            if (null === $attribute['contract']) {
                $attribute['contract'] = Praspel::interpret($attribute['comment']);
            }

            $verdict = $attribute['contract']
                           ->getClause('invariant')
                           ->getVariable($name)
                           ->predicate($value);

            if (false === $verdict) {
                throw new Exception(
                    'Try to set the %s attribute with an invalid data.',
                    0, $name);
            }
        }

        if ((null  !== $validator = $attribute['validator'])
           &&  false === $this->{$validator}($value)) {
            throw new Exception(
                'Try to set the %s attribute with an invalid data.',
                1, $name);
        }

        $old          = $this->$_name;
        $this->$_name = $value;

        return;
    }

    /**
     * Get an attribute value.
     *
     * @param   string  $name    Name.
     * @return  mixed
     */
    public function __get($name)
    {
        if (!isset($this->$name)) {
            return null;
        }

        $attribute = &$this->getAttribute($name);

        if (true === $attribute['relation']) {
            $this->__currentAccess = '_' . $name;

            return $this;
        } elseif (null !== $this->__currentAccess) {
            $this->__currentAccess = null;
        }

        return $this->{'_' . $name};
    }

    /**
     * Check if an offset exists on an attribute.
     *
     * @param   int  $offset    Offset.
     * @return  bool
     */
    private function _offsetExists($offset)
    {
        if (null === $this->__currentAccess) {
            return false;
        }

        return array_key_exists($offset, $this->{$this->__currentAccess});
    }

    /**
     * Check if an offset exists on an attribute.
     *
     * @param   int  $offset    Offset.
     * @return  bool
     */
    public function offsetExists($offset)
    {
        $out                   = $this->_offsetExists($offset);
        $this->__currentAccess = null;

        return $out;
    }

    /**
     * Set a value to a specific offset of the current attribute.
     *
     * @param   int    $offset    Offset.
     * @param   mixed  $value     Value.
     * @return  bool
     * @throws  \Hoa\Model\Exception
     */
    public function offsetSet($offset, $value)
    {
        if (false === $this->isValidationEnabled()) {
            $this->{$this->__currentAccess}[$offset] = $value;

            return null;
        }

        $oldOffset = false !== $this->_offsetExists($offset)
                         ? $this->{$this->__currentAccess}[$offset]
                         : null;

        $this->{$this->__currentAccess}[$offset] = $value;

        $name      = substr($this->__currentAccess, 1);
        $attribute = &$this->getAttribute($name);

        if (false !== $attribute['comment']) {
            if (null === $attribute['contract']) {
                $attribute['contract'] = Praspel::interpret($attribute['comment']);
            }

            $verdict = $attribute['contract']
                           ->getClause('invariant')
                           ->getVariable($name)
                           ->predicate($this->{$this->__currentAccess});
        } else {
            $verdict = true;
        }

        if (false === $verdict) {
            if (null !== $oldOffset) {
                $this->{$this->__currentAccess}[$offset] = $oldOffset;
            } else {
                unset($this->{$this->__currentAccess}[$offset]);
            }

            throw new Exception(
                'Try to set the %s attribute with an invalid data.', 2, $name);
        }

        if ((null  !== $validator = $attribute['validator'])
           &&  false === $this->{$validator}($value)) {
            if (null !== $oldOffset) {
                $this->{$this->__currentAccess}[$offset] = $oldOffset;
            }

            throw new Exception(
                'Try to set the %s attribute with an invalid data.',
                3, $name);
        }

        return $this->__currentAccess = null;
    }

    /**
     * Get a value from a specific offset of the current attribute.
     *
     * @param   int  $offset    Offset.
     * @return  mixed
     */
    public function offsetGet($offset)
    {
        if (false === $this->_offsetExists($offset)) {
            return $this->__currentAccess = null;
        }

        $out                   = &$this->{$this->__currentAccess}[$offset];
        $this->__currentAccess = null;

        return $out;
    }

    /**
     * Unset a specific offset of the current attribute.
     *
     * @param   int  $offset    Offset.
     * @return  void
     */
    public function offsetUnset($offset)
    {
        if (false !== $this->_offsetExists($offset)) {
            unset($this->__currentAccess[$offset]);
        }

        $this->__currentAccess = null;

        return;
    }

    /**
     * Iterate a relation.
     *
     * @return  \ArrayIterator
     */
    public function getIterator()
    {
        if (null === $this->__currentAccess) {
            return null;
        }

        return new \ArrayIterator($this->{$this->__currentAccess});
    }

    /**
     * Count number of attributes.
     *
     * @return  int
     */
    public function count()
    {
        if (null === $this->__currentAccess) {
            return count($this->__attributes);
        }

        $out                   = count($this->{$this->__currentAccess});
        $this->__currentAccess = null;

        return $out;
    }

    /**
     * Get an attribute in the bucket.
     *
     * @param   string  $name    Name.
     * @return  array
     */
    private function &getAttribute($name)
    {
        if (!isset($this->$name)) {
            $out = null;

            return $out;
        }

        return $this->__attributes[$name];
    }

    /**
     * Enable validation or not (i.e. execute Praspel and validate*() methods).
     *
     * @param   bool  $enable    Enable or not.
     * @return  bool
     */
    public function setEnableValidation($enable)
    {
        $old                = $this->__validation;
        $this->__validation = $enable;

        return $old;
    }

    /**
     * Check if validation is enabled or not.
     *
     * @return  bool
     */
    public function isValidationEnabled()
    {
        return $this->__validation;
    }

    /**
     * Set a mapping layer.
     *
     * @param   object  $layer    Layer (e.g. \Hoa\Database\Dal).
     * @param   string  $name     Name.
     * @return  object
     */
    protected static function setMappingLayer($layer, $name = '_default')
    {
        $old = null;

        if (array_key_exists($name, static::$__mappingLayer)) {
            $old = static::$__mappingLayer[$name];
        }

        static::$__mappingLayer[$name] = $layer;

        return $old;
    }

    /**
     * Get a mapping layer.
     *
     * @param   string  $name    Name.
     * @return  object
     */
    public static function getMappingLayer($name = '_default')
    {
        if (!array_key_exists($name, static::$__mappingLayer)) {
            return null;
        }

        return static::$__mappingLayer[$name];
    }

    /**
     * Transform data as an array.
     *
     * @return  array
     */
    public function toArray()
    {
        if (null === $this->__currentAccess) {
            $out = [];

            foreach ($this->__attributes as $attribute) {
                if (false === $attribute['relation']) {
                    $out[$attribute['name']] = $attribute['value'];
                }
            }

            return $out;
        }

        $out = [];

        foreach ($this->{$this->__currentAccess} as $i => $value) {
            $out[$i] = $value->toArray();
        }

        $this->__currentAccess = null;

        return $out;
    }
}

/**
 * Flex entity.
 */
Consistency::flexEntity('Hoa\Model\Model');
