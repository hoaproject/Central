<?php

/**
 * Hoa Framework
 *
 *
 * @license
 *
 * GNU General Public License
 *
 * This file is part of Hoa Open Accessibility.
 * Copyright (c) 2007, 2008 Ivan ENDERLIN. All rights reserved.
 *
 * HOA Open Accessibility is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * HOA Open Accessibility is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with HOA Open Accessibility; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 *
 *
 * @category    Framework
 * @package     Hoa_Tokenizer
 * @subpackage  Hoa_Tokenizer_Parser_LR
 *
 */

/**
 * Hoa_Framework
 */
require_once 'Framework.php';

/**
 * Hoa_Tokenizer_Parser_Exception
 */
import('Tokenizer.Parser.Exception');

/**
 * Hoa_Tokenizer
 */
import('Tokenizer.~');

/**
 * Hoa_Tokenizer_Parser
 */
import('Tokenizer.Parser');

/**
 * Hoa_Tokenizer_Parser_Token
 */
import('Tokenizer.Parser.Token');

//

/**
 * Hoa_Tokenizer_Token_Array
 */
import('Tokenizer.Token.Array');

/**
 * Hoa_Tokenizer_Token_Call_Attribute
 */
import('Tokenizer.Token.Call.Attribute');

/**
 * Hoa_Tokenizer_Token_Call_ClassConstant
 */
import('Tokenizer.Token.Call.ClassConstant');

/**
 * Hoa_Tokenizer_Token_Call_Function
 */
import('Tokenizer.Token.Call.Function');

/**
 * Hoa_Tokenizer_Token_Call_Method
 */
import('Tokenizer.Token.Call.Method');

/**
 * Hoa_Tokenizer_Token_Call_StaticAttribute
 */
import('Tokenizer.Token.Call.StaticAttribute');

/**
 * Hoa_Tokenizer_Token_Call_StaticMethod
 */
import('Tokenizer.Token.Call.StaticMethod');

/**
 * Hoa_Tokenizer_Token_Cast
 */
import('Tokenizer.Token.Cast');

/**
 * Hoa_Tokenizer_Token_Class
 */
import('Tokenizer.Token.Class');

/**
 * Hoa_Tokenizer_Token_Class_Access
 */
import('Tokenizer.Token.Class.Access');

/**
 * Hoa_Tokenizer_Token_Class_Attribute
 */
import('Tokenizer.Token.Class.Attribute');

/**
 * Hoa_Tokenizer_Token_Class_Constant
 */
import('Tokenizer.Token.Class.Constant');

/**
 * Hoa_Tokenizer_Token_Class_Method
 */
import('Tokenizer.Token.Class.Method');

/**
 * Hoa_Tokenizer_Token_Number_DNumber
 */
import('Tokenizer.Token.Number.DNumber');

/**
 * Hoa_Tokenizer_Token_Number_LNumber
 */
import('Tokenizer.Token.Number.LNumber');

/**
 * Hoa_Tokenizer_Token_String
 */
import('Tokenizer.Token.String');

/**
 * Hoa_Tokenizer_Token_String_Boolean
 */
import('Tokenizer.Token.String.Boolean');

/**
 * Hoa_Tokenizer_Token_String_Constant
 */
import('Tokenizer.Token.String.Constant');

/**
 * Hoa_Tokenizer_Token_String_EncapsedConstant
 */
import('Tokenizer.Token.String.EncapsedConstant');

/**
 * Hoa_Tokenizer_Token_String_Null
 */
import('Tokenizer.Token.String.Null');

/**
 * Hoa_Tokenizer_Token_Comment
 */
import('Tokenizer.Token.Comment');

/**
 * Hoa_Tokenizer_Token_Function_Named
 */
import('Tokenizer.Token.Function.Named');

/**
 * Hoa_Tokenizer_Token_Function_Argument
 */
import('Tokenizer.Token.Function.Argument');

/**
 * Hoa_Tokenizer_Token_Variable
 */
import('Tokenizer.Token.Variable');

/**
 * Class Hoa_Tokenizer_Parser_LR.
 *
 * .
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2008 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Tokenizer
 * @subpackage  Hoa_Tokenizer_Parser_LR
 */

class Hoa_Tokenizer_Parser_LR extends Hoa_Tokenizer_Parser {

    /**
     * Constructor.
     *
     * @...
     */
    public function __construct ( $source = null,
                                  $type   = Hoa_Tokenizer_Parser_Token::SOURCE ) {

        parent::__construct($source, $type);

        // Take a deep breath, and here we go …
        $this->axiome();
    }

    /**
     * Axiom.
     *
     * @access  public
     * @return  void
     */
    public function axiome ( ) {

        for(; $this->end(); $this->n()) {

            $handle = null;

            switch($this->ct()) {

                case Hoa_Tokenizer::_CLASS:
                    var_dump('go compile class');
                    $handle = $this->clas();
                  break;

                case Hoa_Tokenizer::_FUNCTION:
                    var_dump('go compile function');
                    $handle = $this->func();
                  break;
            }

            if(null !== $handle)
                $this->r()->addElement($handle);
        }
    }

    /**
     *
     */
    public function arra ( ) {

        var_dump('ARRAAAAAYYYYYYY');
        $this->n();
        $this->n();
    }

    /**
     *
     */
    public function clas ( ) {

        $comment    = null;
        $name       = null;
        $final      = false;
        $abstract   = false;
        $parent     = null;
        $interfaces = array();
        $constants  = array();
        $attributes = array();
        $methods    = array();
        $class      = null;

        $this->p(1);

        if($this->ct() == Hoa_Tokenizer::_ABSTRACT) {

            $abstract = true;
            $this->p(1);
        }
        elseif($this->ct() == Hoa_Tokenizer::_FINAL) {

            $final    = true;
            $this->p(1);
        }

        if(   $this->ct() == Hoa_Tokenizer::_COMMENT
           || $this->ct() == Hoa_Tokenizer::_DOC_COMMENT)
            $comment = $this->comm();

        while($this->n() && $this->ct() != Hoa_Tokenizer::_CLASS);
        $this->n();

        $name = new Hoa_Tokenizer_Token_String($this->cv());
        $this->n();

        if($this->ct() == Hoa_Tokenizer::_EXTENDS) {

            $this->n();
            $parent = new Hoa_Tokenizer_Token_String($this->cv());
            $this->n();
        }

        if($this->ct() == Hoa_Tokenizer::_IMPLEMENTS) {

            $this->n();

            while($this->ct() != Hoa_Tokenizer::_OPEN_BRACE) {

                $interfaces[] = new Hoa_Tokenizer_Token_String($this->cv());
                $this->n();

                if($this->ct() == Hoa_Tokenizer::_COMMA)
                    $this->n();
            }

            $this->p();
        }

        $i = 1;

        while($i > 0) {

            $this->n();
            $ct = $this->ct();

            switch($ct) {

                case Hoa_Tokenizer::_OPEN_BRACE:
                    $i++;
                  break;

                case Hoa_Tokenizer::_CLOSE_BRACE:
                    $i--;
                  break;
            }

            if($ct == Hoa_Tokenizer::_CONST) {

                $com = null;
                $nam = null;
                $val = null;
                $con = null;

                $this->p(1);

                if(   $this->ct() == Hoa_Tokenizer::_COMMENT
                   || $this->ct() == Hoa_Tokenizer::_DOC_COMMENT)
                    $com = $this->comm();

                while($this->n() && $this->ct == Hoa_Tokenizer::_CONST);
                $this->n();

                $nam = new Hoa_Tokenizer_Token_String($this->cv());
                $this->n();
                $this->n();

                $val = $this->defv();
                $this->n();
                $this->n();

                $con = new Hoa_Tokenizer_Token_Class_Constant($nam);

                if(null !== $com)
                    $con->setComment($com);

                $con->setValue($val);

                $constants[] = $con;
            }

            elseif(   $ct == Hoa_Tokenizer::_PUBLIC
                   || $ct == Hoa_Tokenizer::_PROTECTED
                   || $ct == Hoa_Tokenizer::_PRIVATE
                   || $ct == Hoa_Tokenizer::_STATIC) {

                $com = null;
                $acc = null;
                $sta = false;
                $nam = null;
                $val = null;

                $this->p(1);

                if(   $this->ct() == Hoa_Tokenizer::_COMMENT
                   || $this->ct() == Hoa_Tokenizer::_DOC_COMMENT)
                    $com = $this->comm();

                $this->n(1);

                if($ct == Hoa_Tokenizer::_STATIC)
                    $sta = true;

                if($ct == Hoa_Tokenizer::_PUBLIC)
                    $acc = new Hoa_Tokenizer_Token_Class_Access('public');
                elseif($ct == Hoa_Tokenizer::_PROTECTED)
                    $acc = new Hoa_Tokenizer_Token_Class_Access('protected');
                elseif($ct == Hoa_Tokenizer::_PRIVATE)
                    $acc = new Hoa_Tokenizer_Token_Class_Access('private');

                $this->n();

                if($this->ct() == Hoa_Tokenizer::_STATIC) {

                    $sta = true;
                    $this->n();
                }

                if($this->ct() == Hoa_Tokenizer::_VARIABLE) {

                    $nam = new Hoa_Tokenizer_Token_String(substr($this->cv(), 1));
                    $nam = new Hoa_Tokenizer_Token_Variable($nam);
                    $this->n();

                    if($this->ct() == Hoa_Tokenizer::_EQUAL) {

                        $this->n();
                        $val = $this->defv();
                        $this->n();
                    }

                    $att = new Hoa_Tokenizer_Token_Class_Attribute($nam);

                    if(null !== $com)
                        $att->setComment($com);

                    $att->setAccess($acc);
                    $att->staticMe($sta);

                    if(null !== $val)
                        $att->setValue($val);

                    $attributes[] = $att;
                }
                else { // METHOD

                    $fin = false;
                    $abs = false;
                    $ref = false;
                    $arg = array();
                    $bod = array();
                    $met = null;

                    for($e = 2; $e >= 0; $e--, $this->p())
                        switch($this->ct()) {

                            case Hoa_Tokenizer::_ABSTRACT:
                                $abs = true;
                              break 2;

                            case Hoa_Tokenizer::_FINAL:
                                $fin = true;
                              break 2;
                        }

                    while($this->ct() != Hoa_Tokenizer::_FUNCTION && $this->n() + 1);
                    $this->n();

                    if($this->ct() == Hoa_Tokenizer::_REFERENCE) {

                        $ref = true;
                        $this->n();
                    }

                    $nam = new Hoa_Tokenizer_Token_String($this->cv());
                    $this->n();
                    $this->n();

                    while($this->ct() != Hoa_Tokenizer::_CLOSE_PARENTHESES) {

                        $ty = null;
                        $re = false;
                        $va = null;
                        $de = null;
                        $ar = null;

                        if(   $this->ct() == Hoa_Tokenizer::_STRING
                           || $this->ct() == Hoa_Tokenizer::_ARRAY) {

                            $ty = new Hoa_Tokenizer_Token_String($this->cv());
                            $this->n();
                        }

                        if($this->ct() == Hoa_Tokenizer::_REFERENCE) {

                            $re = true;
                            $this->n();
                        }

                        $va = new Hoa_Tokenizer_Token_String(substr($this->cv(), 1));
                        $va = new Hoa_Tokenizer_Token_Variable($va);
                        $this->n();

                        if($this->ct() == Hoa_Tokenizer::_EQUAL) {

                            $this->n();
                            $de = $this->defv();
                            $this->n();
                        }

                        if($this->ct() == Hoa_Tokenizer::_COMMA)
                            $this->n();

                        $ar = new Hoa_Tokenizer_Token_Function_Argument($va);
                        $ar->referenceMe($re);

                        if(null !== $ty)
                            $ar->setType($ty);

                        if(null !== $de)
                            $ar->setDefaultValue($de);

                        $arg[] = $ar;
                    }

                    $this->n();
                    $this->goToEndBlock();

                    $met = new Hoa_Tokenizer_Token_Class_Method($nam);

                    if(null !== $com)
                        $met->setComment($com);

                    $met->setAccess($acc);
                    $met->finalMe($fin);
                    $met->abstractMe($abs);
                    $met->staticMe($sta);
                    $met->referenceMe($ref);
                    $met->addArguments($arg);

                    // !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
                    // body (pay attention to abstract).
                    // !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!

                    $methods[] = $met;
                }
            }
        }

        $class = new Hoa_Tokenizer_Token_Class($name);

        if(null !== $comment)
            $class->setComment($comment);

        $class->finalMe($final);
        $class->abstractMe($abstract);

        if(null !== $parent)
            $class->setParent($parent);

        $class->addInterfaces($interfaces);
        $class->addConstants($constants);
        $class->addAttributes($attributes);
        $class->addMethods($methods);

        return $class;
    }

    /**
     *
     */
    public function comm ( ) {

        return new Hoa_Tokenizer_Token_Comment($this->cv());
    }

    /**
     *
     */
    public function defv ( ) {

        $default = null;
        $cv      = strtolower($this->cv());

        if(   $cv == 'true'
           || $cv == 'false')
            $default = new Hoa_Tokenizer_Token_String_Boolean($this->cv());

        elseif($cv == 'null')
            $default = new Hoa_Tokenizer_Token_String_Null($this->cv());

        elseif($this->ct() == Hoa_Tokenizer::_CONSTANT_ENCAPSED_STRING)
            $default = new Hoa_Tokenizer_Token_String_EncapsedConstant($this->cv());

        elseif($this->ct() == Hoa_Tokenizer::_DNUMBER)
            $default = new Hoa_Tokenizer_Token_Number_DNumber($this->cv());

        elseif($this->ct() == Hoa_Tokenizer::_LNUMBER)
            $default = new Hoa_Tokenizer_Token_Number_LNumber($this->cv());

        elseif($this->ct() == Hoa_Tokenizer::_STRING) {

            $tmp = $this->cv();
            $this->n();

            if($this->ct() == Hoa_Tokenizer::_DOUBLE_COLON) {

                $this->n();
                $default = new Hoa_Tokenizer_Token_Call_ClassConstant(
                    new Hoa_Tokenizer_Token_String($tmp)
                );
                $default->setConstant(new Hoa_Tokenizer_Token_String($this->cv()));
            }
            else
                $default = new Hoa_Tokenizer_Token_String_Constant($tmp);
        }

        // !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
        // array
        // !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!

        elseif($this->ct() == Hoa_Tokenizer::_ARRAY)
            $default = $this->arra();

        return $default;
    }

    /**
     *
     */
    public function func ( ) {

        $comment   = null;
        $reference = false;
        $name      = null;
        $arguments = array();
        $body      = array();
        $function  = null;

        $this->p(1);

        if(   $this->ct() == Hoa_Tokenizer::_COMMENT
           || $this->ct() == Hoa_Tokenizer::_DOC_COMMENT)
            $comment = $this->comm();

        while($this->n() && $this->ct != Hoa_Tokenizer::_FUNCTION);
        $this->n();

        if($this->ct() == Hoa_Tokenizer::_REFERENCE) {

            $reference = true;
            $this->n();
        }

        $name = new Hoa_Tokenizer_Token_String($this->cv());
        $this->n();
        $this->n();

        while($this->ct() != Hoa_Tokenizer::_CLOSE_PARENTHESES) {

            $type     = null;
            $ref      = false;
            $variable = null;
            $default  = null;
            $arg      = null;

            if(   $this->ct() == Hoa_Tokenizer::_STRING
               || $this->ct() == Hoa_Tokenizer::_ARRAY) {

                $type = new Hoa_Tokenizer_Token_String($this->cv());
                $this->n();
            }

            if($this->ct() == Hoa_Tokenizer::_REFERENCE) {

                $ref = true;
                $this->n();
            }

            $variable = new Hoa_Tokenizer_Token_String(substr($this->cv(), 1));
            $variable = new Hoa_Tokenizer_Token_Variable($variable);
            $this->n();

            if($this->ct() == Hoa_Tokenizer::_EQUAL) {

                $this->n();
                $default = $this->defv();
                $this->n();
            }

            if($this->ct() == Hoa_Tokenizer::_COMMA)
                $this->n();

            $arg = new Hoa_Tokenizer_Token_Function_Argument($variable);
            $arg->referenceMe($reference);

            if(null !== $type)
                $arg->setType($type);

            if(null !== $default)
                $arg->setDefaultValue($default);

            $arguments[] = $arg;
        }

        $this->n();
        $this->goToEndBlock();

        $function = new Hoa_Tokenizer_Token_Function_Named($name);

        if(null !== $comment)
            $function->setComment($comment);

        $function->referenceMe($reference);
        $function->addArguments($arguments);

        // !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
        // body
        // !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!

        return $function;
    }

    /**
     *
     */
    public function goToEndBlock ( ) {

        if($this->ct() != Hoa_Tokenizer::_OPEN_BRACE)
            return;

        $i = 1;

        while($i > 0) {

            $this->n();

            switch($this->ct()) {

                case Hoa_Tokenizer::_OPEN_BRACE:
                    $i++;
                  break;

                case Hoa_Tokenizer::_CLOSE_BRACE:
                    $i--;
                  break;
            }
        }
    }
}
