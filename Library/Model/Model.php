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
 * \Hoa\Model\Exception
 */
-> import('Model.Exception')

/**
 * \Hoa\Test\Praspel\Compiler
 */
-> import('Test.Praspel.Compiler', true);

}

namespace Hoa\Model {

/**
 * Class \Hoa\Model\Exception.
 *
 * Represent a model/document with atributes and relations.
 *
 * @author     Ivan Enderlin <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright © 2007-2011 Ivan Enderlin.
 * @license    New BSD License
 */

abstract class Model implements \ArrayAccess, \IteratorAggregate, \Countable {

    /**
     * Whether we should check Praspel and validate*().
     *
     * @var \Hoa\Model bool
     */
    private $__validation            = true;

    /**
     * Bucket of all model attributes.
     *
     * @var \Hoa\Model array
     */
    private $__attributes            = array();

    /**
     * Current attribute in an array access.
     *
     * @var \Hoa\Model string
     */
    private $__arrayAccess           = null;

    /**
     * Mapping layers.
     *
     * @var \Hoa\Model array
     */
    protected static $__mappingLayer = array('_default' => null);



    /**
     * Initialize the model.
     *
     * @access  public
     * @return  void
     */
    public function __construct ( ) {

        $class   = new \ReflectionClass($this);
        $ucfirst = function ( Array $matches ) {

            return ucfirst($matches[1]);
        };
        $default = $class->getDefaultProperties();

        foreach($class->getProperties() as $property) {

            $_name = $property->getName();

            if(   '_' !== $_name[0]
               || '_' === $_name[1])
                continue;

            $name      = substr($_name, 1);
            $comment   = $property->getDocComment();
            $comment   = preg_replace('#^(\s*/\*\*\s*)#', '', $comment);
            $comment   = preg_replace('#(\s*\*/)#',       '', $comment);
            $comment   = preg_replace('#^(\s*\*\s*)#m',   '', $comment);
            $validator = 'validate' . ucfirst(preg_replace_callback(
                '#_(.)#',
                $ucfirst,
                strtolower($name)
            ));

            $this->__attributes[$name] = array(
                'comment'   => $comment,
                'name'      => $name,
                '_name'     => $_name,
                'validator' => method_exists($this, $validator)
                                   ? $validator
                                   : null,
                'contract'  => null, // be lazy
                'default'   => $default[$_name],
                'value'     => &$this->$_name
            );
        }

        $this->construct();

        return;
    }

    /**
     * User constructor.
     *
     * @access  public
     * @return  void
     */
    public function construct ( ) {

        return;
    }

    /**
     * Open many documents.
     *
     * @access  public
     * @param   array  $constraints    Contraints.
     * @return  void
     */
    public function openMany ( Array $constraints = array() ) {

        return;
    }

    /**
     * Open one document.
     *
     * @access  public
     * @param   array  $constraints    Contraints.
     * @return  void
     */
    public function open ( Array $constraints = array() ) {

        return;
    }

    /**
     * Save the document.
     *
     * @access  public
     * @return  void
     */
    public function save ( ) {

        return;
    }

    /**
     * Map data to attributes.
     *
     * @access  public
     * @param   array  $data    Data.
     * @param   array  $map     Map: data name to attribute name.
     * @return  void
     */
    protected function map ( Array $data, Array $map = null ) {

        if(empty($map))
            $map = array_combine($handle = array_keys($data), $handle);

        foreach($data as $name => $value) {

            if(array_key_exists($name, $map))
                $name = $map[$name];

            if($value == $this->$name)
                continue;

            $this->$name = $value;
        }

        return;
    }

    /**
     * Get constraints, i.e. all attributes values that defer from their default
     * values. We could include the default values if the only argument is set
     * to true.
     *
     * @access  public
     * @param   bool  $defaultValues    Whether we include default values.
     * @return  array
     */
    protected function getConstraints ( $defaultValues = false ) {

        $out = array();

        foreach($this->__attributes as $name => $attribute)
            if(   true === $defaultValues
               || $attribute['default'] != $attribute['value'])
                $out[$name] = $attribute['value'];

        return $out;
    }

    /**
     * Check if an attribute exists and is set.
     *
     * @access  public
     * @param   string  $name    Attribute name.
     * @return  bool
     */
    public function __isset ( $name ) {

        return isset($this->__attributes[$name]);
    }

    /**
     * Set a value to an attribute.
     *
     * @access  public
     * @param   string  $name     Name.
     * @param   mixed   $value    Value.
     * @return  void
     * @throw   \Hoa\Model\Exception
     */
    public function __set ( $name, $value) {

        if(!isset($this->$name))
            return null;

        $_name = '_' . $name;

        if(is_numeric($value)) {

            if($value == $_value = (int) $value)
                $value = $_value;
            else
                $value = (float) $value;
        }

        if(false === $this->isValidationEnabled()) {

            $old          = $this->$_name;
            $this->$_name = $value;

            return $old;
        }

        $attribute = $this->getAttribute($name);
        $verdict   = praspel($attribute['comment'])
                         ->getClause('invariant')
                         ->getVariable($name)
                         ->predicate($value);

        if(false === $verdict)
            throw new Exception(
                'Try to set the %s attribute with an invalid data.', 0, $name);

        if(   (null  !== $validator = $attribute['validator'])
           &&  false === $this->{$validator}($value))
            throw new Exception(
                'Try to set the %s attribute with an invalid data.',
                1, $name);

        $old          = $this->$_name;
        $this->$_name = $value;

        return;
    }

    /**
     * Get an attribute value.
     *
     * @access  public
     * @param   string  $name    Name.
     * @return  mixed
     */
    public function __get ( $name ) {

        if(!isset($this->$name))
            return null;

        $handle = &$this->{'_' . $name};

        if(is_array($handle)) {

            $this->__arrayAccess = '_' . $name;

            return $this;
        }
        elseif(null !== $this->__arrayAccess)
            $this->__arrayAccess = null;

        return $handle;
    }

    /**
     * Check if an offset exists on an attribute.
     *
     * @access  public
     * @param   int  $offset    Offset.
     * @return  bool
     */
    private function _offsetExists ( $offset ) {

        if(null === $this->__arrayAccess)
            return false;

        return array_key_exists($offset, $this->{$this->__arrayAccess});
    }

    /**
     * Check if an offset exists on an attribute.
     *
     * @access  public
     * @param   int  $offset    Offset.
     * @return  bool
     */
    public function offsetExists ( $offset ) {

        $out                 = $this->_offsetExists($offset);
        $this->__arrayAccess = null;

        return $out;
    }

    /**
     * Set a value to a specific offset of the current attribute.
     *
     * @access  public
     * @param   int    $offset    Offset.
     * @param   mixed  $value     Value.
     * @return  bool
     * @throw   \Hoa\Model\Exception
     */
    public function offsetSet ( $offset, $value ) {

        if(false === $this->isValidationEnabled()) {

            $old = $this->{$this->__arrayAccess}[$offset];
            $this->{$this->__arrayAccess}[$offset] = $value;

            return $old;
        }

        $oldOffset = false !== $this->_offsetExists($offset)
                         ? $this->{$this->__arrayAccess}[$offset]
                         : null;

        $this->{$this->__arrayAccess}[$offset] = $value;

        $name      = substr($this->__arrayAccess, 1);
        $attribute = $this->getAttribute($name);
        $verdict   = praspel($attribute['comment'])
                         ->getClause('invariant')
                         ->getVariable($name)
                         ->predicate($this->{$this->__arrayAccess});

        if(false === $verdict) {

            if(null !== $oldOffset)
                $this->{$this->__arrayAccess}[$offset] = $oldOffset;

            throw new Exception(
                'Try to set the %s attribute with an invalid data.', 2, $name);
        }

        if(   (null  !== $validator = $attribute['validator'])
           &&  false === $this->{$validator}($value)) {

            if(null !== $oldOffset)
                $this->{$this->__arrayAccess}[$offset] = $oldOffset;

            throw new Exception(
                'Try to set the %s attribute with an invalid data.',
                3, $name);
        }

        return $this->__arrayAccess = null;
    }

    /**
     * Get a value from a specific offset of the current attribute.
     *
     * @access  public
     * @param   int  $offset    Offset.
     * @return  mixed
     */
    public function offsetGet ( $offset ) {

        if(false === $this->_offsetExists($offset))
            return $this->__arrayAccess = null;

        $out                 = &$this->{$this->__arrayAccess}[$offset];
        $this->__arrayAccess = null;

        return $out;
    }

    /**
     * Unset a specific offset of the current attribute.
     *
     * @access  public
     * @param   int  $offset    Offset.
     * @return  void
     */
    public function offsetUnset ( $offset ) {

        if(false !== $this->_offsetExists($offset))
            unset($this->__arrayAccess[$offset]);

        $this->__arrayAccess = null;

        return;
    }

    /**
     * Iterate a relation.
     *
     * @access  public
     * @return  \ArrayIterator
     */
    public function getIterator ( ) {

        if(null === $this->__arrayAccess)
            return null;

        return new \ArrayIterator($this->{$this->__arrayAccess});
    }

    /**
     * Count number of attributes.
     *
     * @access  public
     * @return  int
     */
    public function count ( ) {

        if(null === $this->__arrayAccess)
            return count($this->__attributes);

        $out                 = count($this->{$this->__arrayAccess});
        $this->__arrayAccess = null;

        return $out;
    }

    /**
     * Get an attribute in the bucket.
     *
     * @access  public
     * @param   string  $name    Name.
     * @return  array
     */
    private function &getAttribute ( $name ) {

        if(!isset($this->$name)) {

            $out = null;

            return $out;
        }

        return $this->__attributes[$name];
    }

    /**
     * Enable validation or not (i.e. execute Praspel and validate*() methods).
     *
     * @access  public
     * @param   bool  $enable    Enable or not.
     * @return  bool
     */
    public function setEnableValidation ( $enable ) {

        $old                = $this->__validation;
        $this->__validation = $enable;

        return $old;
    }

    /**
     * Check if validation is enabled or not.
     *
     * @access  public
     * @return  bool
     */
    public function isValidationEnabled ( ) {

        return $this->__validation;
    }

    /**
     * Set a mapping layer.
     *
     * @access  public
     * @param   object  $layer    Layer (e.g. \Hoa\Database\Dal).
     * @param   string  $name     Name.
     * @return  object
     */
    protected static function setMappingLayer ( $layer, $name = '_default' ) {

        if(!array_key_exists($name, static::$__mappingLayer))
            return null;

        $old                           = static::$__mappingLayer[$name];
        static::$__mappingLayer[$name] = $layer;

        return $old;
    }

    /**
     * Get a mapping layer.
     *
     * @access  public
     * @param   string  $name    Name.
     * @return  object
     */
    public static function getMappingLayer ( $name = '_default' ) {

        if(!array_key_exists($name, static::$__mappingLayer))
            return null;

        return static::$__mappingLayer[$name];
    }
}

}
