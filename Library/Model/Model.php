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
 * Extending the \Hoa\Core\Exception class.
 *
 * @author     Ivan Enderlin <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright © 2007-2011 Ivan Enderlin.
 * @license    New BSD License
 */

abstract class Model {

    private $__validation            = true;
    private $__attributes            = array();
    protected static $__mappingLayer = array('_default' => null);



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

    public function construct ( ) {

        return;
    }

    public function openMany ( Array $constraints = array() ) {

        return;
    }

    abstract public function open ( Array $constraints = array() );

    abstract public function save ( );

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

    protected function getConstraints ( $defaultValues = false ) {

        $out = array();

        foreach($this->__attributes as $name => $attribute)
            if(   true === $defaultValues
               || $attribute['default'] != $attribute['value'])
                $out[$name] = $attribute['value'];

        return $out;
    }

    public function __isset ( $name ) {

        return isset($this->__attributes[$name]);
    }

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

    public function __get ( $name ) {

        if(!isset($this->$name))
            return null;

        return $this->{'_' . $name};
    }

    private function &getAttribute ( $name ) {

        if(!isset($this->$name)) {

            $out = null;

            return $out;
        }

        return $this->__attributes[$name];
    }

    public function setEnableValidation ( $enable ) {

        $old                = $this->__validation;
        $this->__validation = $enable;

        return $old;
    }

    public function isValidationEnabled ( ) {

        return $this->__validation;
    }

    protected static function setMappingLayer ( $layer, $name = '_default' ) {

        if(!array_key_exists($name, static::$__mappingLayer))
            return null;

        $old                           = static::$__mappingLayer[$name];
        static::$__mappingLayer[$name] = $layer;

        return $old;
    }

    public static function getMappingLayer ( $name = '_default' ) {

        if(!array_key_exists($name, static::$__mappingLayer))
            return null;

        return static::$__mappingLayer[$name];
    }
}

}
