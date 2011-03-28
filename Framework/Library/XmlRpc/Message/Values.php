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
 * \Hoa\XmlRpc\Exception
 */
-> import('XmlRpc.Exception');

}

namespace Hoa\XmlRpc\Message {

/**
 * Class \Hoa\XmlRpc\Message.
 *
 * 
 *
 * @author     Ivan Enderlin <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright © 2007-2011 Ivan Enderlin.
 * @license    New BSD License
 */

class Values {

    const VALUE          =  0;
    const TYPE           =  1;
    const TYPE_ARRAY     =  2;
    const TYPE_BASE64    =  3;
    const TYPE_BOOLEAN   =  4;
    const TYPE_DATETIME  =  5;
    const TYPE_FLOAT     =  6;
    const TYPE_INTEGER   =  7;
    const TYPE_STRING    =  8;
    const TYPE_STRUCTURE =  9;
    const TYPE_NULL      = 10;
    const IS_SCALAR      = 11;
    const IS_ARRAY       = 12;
    const IS_STRUCTURE   = 13;

    protected $_values = array();
    protected $_return = null;
    protected $_parent = null;
    protected $_name   = 'undefined';
    protected $_is     = self::IS_SCALAR;



    public function __construct ( $is = self::IS_SCALAR,
                                  $parent = null ) {

        $this->_is = $is;
        $this->setParent($parent);

        return;
    }

    protected function _with ( $value, $type ) {

        switch($this->_is) {

            case self::IS_SCALAR:
                $this->_values[] = array($value, $type);
              break;

            case self::IS_ARRAY:
              break;

            case self::IS_STRUCTURE:
                $this->_values[$this->_name] = array($value, $type);
              break;
        }

        return $this;
    }

    public function withArray ( $array ) {

        return $this->getReturn();
    }

    public function withBase64 ( $data ) {

        return $this->_with(
            base64_encode($data),
            self::TYPE_BASE64
        );
    }

    public function withBoolean ( $data ) {

        return $this->_with(
            true == $data ? '1' : '0',
            self::TYPE_BOOLEAN
        );
    }

    public function withDateTime ( $timestamp ) {

        return $this->_with(
            date('c', $timestamp),
            self::TYPE_DATETIME
        );
    }

    public function withFloat ( $float ) {

       return $this->_with(
            (string) (float) $float,
            self::TYPE_FLOAT
       );
    }

    public function withInteger ( $integer ) {

        return $this->_with(
            (string) (int) $integer,
            self::TYPE_INTEGER
        );
    }

    public function withString ( $string ) {

        return $this->_with(
            (string) $string,
            self::TYPE_STRING
        );
    }

    public function withStructure ( ) {

        $self = __CLASS__;

        return new $self(self::IS_STRUCTURE, $this);
    }

    public function withName ( $name ) {

        if(self::IS_STRUCTURE !== $this->_is)
            return $this;

        $this->_name = $name;

        return $this;
    }

    public function endStructure ( ) {

        $parent = $this->getParent();
        $parent->_with(
            $this->getValues(),
            self::TYPE_STRUCTURE
        );

        return $parent;
    }

    public function withNull ( ) {

        return $this->_with(
            null,
            self::TYPE_NULL
        );
    }

    public function setParent ( $parent ) {

        $old           = $this->_parent;
        $this->_parent = $parent;

        return $old;
    }

    public function getParent ( ) {

        return $this->_parent;
    }

    public function getValues ( ) {

        return $this->_values;
    }

    public function getValueAsString ( $value, $type ) {

        switch($type) {

            case self::TYPE_BASE64:
                return '<base64>' . $value . '</base64>';
              break;

            case self::TYPE_BOOLEAN:
                return '<boolean>' . $value . '</boolean>';
              break;

            case self::TYPE_DATETIME:
                return '<dateTime.iso8601>' . $value . '</dateTime.iso8601>';
              break;

            case self::TYPE_FLOAT:
                return '<double>' . $value . '</double>';
              break;

            case self::TYPE_INTEGER:
                return '<i4>' . $value . '</i4>';
              break;

            case self::TYPE_STRING:
                return '<string>' . $value . '</string>';
              break;

            case self::TYPE_STRUCTURE:
                $out = '<struct>' . "\n";

                foreach($value as $name => $v) {

                    $out .= '  <member>' . "\n" .
                            '    <name>' . $name . '</name>' . "\n" .
                            '    <value>' . $this->getValueAsString(
                                $v[self::VALUE],
                                $v[self::TYPE]
                            ) . '</value>' . "\n" .
                            '  </member>' . "\n";
                }

                return $out . '</struct>';
              break;

            case self::TYPE_NULL:
                return '<nil />';
              break;
        }
    }
}

}
