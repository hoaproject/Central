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
 * @package     Hoa_Pom
 * @subpackage  Hoa_Pom_Parser_ParserLr
 *
 */

/**
 * Hoa_Framework
 */
require_once 'Framework.php';

/**
 * Hoa_Pom_Parser_Exception
 */
import('Pom.Parser.Exception');

/**
 * Hoa_Pom
 */
import('Pom.~');

/**
 * Hoa_Pom_Parser
 */
import('Pom.Parser');

/**
 * Hoa_Pom_Parser_Lexer
 */
import('Pom.Parser.Lexer');

//

/**
 * Hoa_Pom_Token_Array
 */
import('Pom.Token.Array');

/**
 * Hoa_Pom_Token_Call_Attribute
 */
import('Pom.Token.Call.Attribute');

/**
 * Hoa_Pom_Token_Call_ClassConstant
 */
import('Pom.Token.Call.ClassConstant');

/**
 * Hoa_Pom_Token_Call_Function
 */
import('Pom.Token.Call.Function');

/**
 * Hoa_Pom_Token_Call_Method
 */
import('Pom.Token.Call.Method');

/**
 * Hoa_Pom_Token_Call_StaticAttribute
 */
import('Pom.Token.Call.StaticAttribute');

/**
 * Hoa_Pom_Token_Call_StaticMethod
 */
import('Pom.Token.Call.StaticMethod');

/**
 * Hoa_Pom_Token_Cast
 */
import('Pom.Token.Cast');

/**
 * Hoa_Pom_Token_Class
 */
import('Pom.Token.Class');

/**
 * Hoa_Pom_Token_Class_Access
 */
import('Pom.Token.Class.Access');

/**
 * Hoa_Pom_Token_Class_Attribute
 */
import('Pom.Token.Class.Attribute');

/**
 * Hoa_Pom_Token_Class_Constant
 */
import('Pom.Token.Class.Constant');

/**
 * Hoa_Pom_Token_Class_Method
 */
import('Pom.Token.Class.Method');

/**
 * Hoa_Pom_Token_Comment
 */
import('Pom.Token.Comment');

/**
 * Hoa_Pom_Token_Function_Named
 */
import('Pom.Token.Function.Named');

/**
 * Hoa_Pom_Token_LateParsing
 */
import('Pom.Token.LateParsing');

/**
 * Hoa_Pom_Token_Function_Argument
 */
import('Pom.Token.Function.Argument');

/**
 * Hoa_Pom_Token_Number_DNumber
 */
import('Pom.Token.Number.DNumber');

/**
 * Hoa_Pom_Token_Number_LNumber
 */
import('Pom.Token.Number.LNumber');

/**
 * Hoa_Pom_Token_String
 */
import('Pom.Token.String');

/**
 * Hoa_Pom_Token_String_Boolean
 */
import('Pom.Token.String.Boolean');

/**
 * Hoa_Pom_Token_String_Constant
 */
import('Pom.Token.String.Constant');

/**
 * Hoa_Pom_Token_String_EncapsedConstant
 */
import('Pom.Token.String.EncapsedConstant');

/**
 * Hoa_Pom_Token_String_Null
 */
import('Pom.Token.String.Null');

/**
 * Hoa_Pom_Token_Variable
 */
import('Pom.Token.Variable');

/**
 * Class Hoa_Pom_Parser_ParserLr.
 *
 * .
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2008 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Pom
 * @subpackage  Hoa_Pom_Parser_ParserLr
 */

class Hoa_Pom_Parser_ParserLr extends Hoa_Pom_Parser {

    /**
     * Constructor.
     *
     * @...
     */
    public function __construct ( $source = null,
                                  $type   = Hoa_Pom_Parser_Lexer::SOURCE ) {

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

        $lateBuffer = new Hoa_Pom_Token_LateParsing();
        $in         = false;

        for(; $this->end(); $this->n(0)) {

            $handle = null;

            switch($this->ct()) {

                case Hoa_Pom::_CLASS:
                    $this->r()->addElement($lateBuffer);
                    $handle = $this->clas();
                  break;

                case Hoa_Pom::_FUNCTION:
                    $this->r()->addElement($lateBuffer);
                    $handle = $this->func();
                  break;

                default:
                    $lateBuffer->addToken($this->c());
                    $in = true;
            }

            if(null !== $handle) {

                if(true === $in) {

                    $lateBuffer = new Hoa_Pom_Token_LateParsing();
                    $in         = false;
                }

                $this->r()->addElement($handle);
            }
        }

        if(true === $in)
            $this->r()->addElement($lateBuffer);
    }

    /**
     *
     */
    public function arra ( ) {

        $array = new Hoa_Pom_Token_Array();
        $i     = 1;

        $this->n();

        while($i > 0) {

            $this->n();
            $key   = null;
            $value = null;

            switch($this->ct()) {

                case Hoa_Pom::_OPEN_PARENTHESES:
                    $i++;
                  break;

                case Hoa_Pom::_CLOSE_PARENTHESES:
                    $i--;
                  break;

                case Hoa_Pom::_COMMA:
                  break;

                default:
                    if($this->ct() == Hoa_Pom::_CONSTANT_ENCAPSED_STRING)
                        $key = new Hoa_Pom_Token_String_EncapsedConstant($this->cv());
                    elseif($this->ct() == Hoa_Pom::_DNUMBER)
                        $key = new Hoa_Pom_Token_Number_DNumber($this->cv());
                    elseif($this->ct() == Hoa_Pom::_LNUMBER)
                        $key = new Hoa_Pom_Token_Number_LNumber($this->cv());

                    $this->n();

                    if($this->ct() != Hoa_Pom::_DOUBLE_ARROW) {

                        $array->addElement(null, $key);
                        $this->p();
                        continue;
                    }
                    else
                        $this->n();

                    if($this->ct() == Hoa_Pom::_CONSTANT_ENCAPSED_STRING)
                        $value = new Hoa_Pom_Token_String_EncapsedConstant($this->cv());
                    elseif($this->ct() == Hoa_Pom::_DNUMBER)
                        $value = new Hoa_Pom_Token_Number_DNumber($this->cv());
                    elseif($this->ct() == Hoa_Pom::_LNUMBER)
                        $value = new Hoa_Pom_Token_Number_LNumber($this->cv());
                    elseif($this->ct() == Hoa_Pom::_ARRAY)
                        $value = $this->arra();

                    $array->addElement($key, $value);
            }
        }

        return $array;
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
        $this->r()->getLastElement()->pop();

        if($this->ct() == Hoa_Pom::_ABSTRACT) {

            $abstract = true;
            $this->p(1);
            $this->r()->getLastElement()->pop();
        }
        elseif($this->ct() == Hoa_Pom::_FINAL) {

            $final    = true;
            $this->p(1);
            $this->r()->getLastElement()->pop();
        }

        if(   $this->ct() == Hoa_Pom::_COMMENT
           || $this->ct() == Hoa_Pom::_DOC_COMMENT) {

            $comment = $this->comm();
            $this->r()->getLastElement()->pop();
        }

        while($this->n() && $this->ct() != Hoa_Pom::_CLASS);
        $this->n();

        $name = new Hoa_Pom_Token_String($this->cv());
        $this->n();

        if($this->ct() == Hoa_Pom::_EXTENDS) {

            $this->n();
            $parent = new Hoa_Pom_Token_String($this->cv());
            $this->n();
        }

        if($this->ct() == Hoa_Pom::_IMPLEMENTS) {

            $this->n();

            while($this->ct() != Hoa_Pom::_OPEN_BRACE) {

                $interfaces[] = new Hoa_Pom_Token_String($this->cv());
                $this->n();

                if($this->ct() == Hoa_Pom::_COMMA)
                    $this->n();
            }

            $this->p();
        }

        $i = 1;

        while($i > 0) {

            $this->n();
            $ct = $this->ct();

            switch($ct) {

                case Hoa_Pom::_OPEN_BRACE:
                    $i++;
                  break;

                case Hoa_Pom::_CLOSE_BRACE:
                    $i--;
                  break;
            }

            if($ct == Hoa_Pom::_CONST) {

                $com = null;
                $nam = null;
                $val = null;
                $con = null;

                $this->p(1);

                if(   $this->ct() == Hoa_Pom::_COMMENT
                   || $this->ct() == Hoa_Pom::_DOC_COMMENT)
                    $com = $this->comm();

                while($this->n() + 1 && $this->ct() == Hoa_Pom::_CONST);

                $nam = new Hoa_Pom_Token_String($this->cv());
                $this->n();
                $this->n();

                $val = $this->defv();
                $this->n();
                $this->n();

                $con = new Hoa_Pom_Token_Class_Constant($nam);

                if(null !== $com)
                    $con->setComment($com);

                $con->setValue($val);

                $constants[] = $con;
            }

            elseif(   $ct == Hoa_Pom::_PUBLIC
                   || $ct == Hoa_Pom::_PROTECTED
                   || $ct == Hoa_Pom::_PRIVATE
                   || $ct == Hoa_Pom::_STATIC) {

                $com = null;
                $acc = null;
                $sta = false;
                $nam = null;
                $val = null;

                $this->p(1);

                if(   $this->ct() == Hoa_Pom::_COMMENT
                   || $this->ct() == Hoa_Pom::_DOC_COMMENT)
                    $com = $this->comm();

                $this->n(1);

                if($ct == Hoa_Pom::_STATIC)
                    $sta = true;

                if($ct == Hoa_Pom::_PUBLIC)
                    $acc = new Hoa_Pom_Token_Class_Access('public');
                elseif($ct == Hoa_Pom::_PROTECTED)
                    $acc = new Hoa_Pom_Token_Class_Access('protected');
                elseif($ct == Hoa_Pom::_PRIVATE)
                    $acc = new Hoa_Pom_Token_Class_Access('private');

                $this->n();

                if($this->ct() == Hoa_Pom::_STATIC) {

                    $sta = true;
                    $this->n();
                }

                if($this->ct() == Hoa_Pom::_VARIABLE) {

                    $nam = new Hoa_Pom_Token_String(substr($this->cv(), 1));
                    $nam = new Hoa_Pom_Token_Variable($nam);
                    $this->n();

                    if($this->ct() == Hoa_Pom::_EQUAL) {

                        $this->n();
                        $val = $this->defv();
                        $this->n();
                    }

                    $att = new Hoa_Pom_Token_Class_Attribute($nam);

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

                            case Hoa_Pom::_ABSTRACT:
                                $abs = true;
                              break 2;

                            case Hoa_Pom::_FINAL:
                                $fin = true;
                              break 2;
                        }

                    while($this->ct() != Hoa_Pom::_FUNCTION && $this->n() + 1);
                    $this->n();

                    if($this->ct() == Hoa_Pom::_REFERENCE) {

                        $ref = true;
                        $this->n();
                    }

                    $nam = new Hoa_Pom_Token_String($this->cv());
                    $this->n();
                    $this->n();

                    while($this->ct() != Hoa_Pom::_CLOSE_PARENTHESES) {

                        $ty = null;
                        $re = false;
                        $va = null;
                        $de = null;
                        $ar = null;

                        if(   $this->ct() == Hoa_Pom::_STRING
                           || $this->ct() == Hoa_Pom::_ARRAY) {

                            $ty = new Hoa_Pom_Token_String($this->cv());
                            $this->n();
                        }

                        if($this->ct() == Hoa_Pom::_REFERENCE) {

                            $re = true;
                            $this->n();
                        }

                        $va = new Hoa_Pom_Token_String(substr($this->cv(), 1));
                        $va = new Hoa_Pom_Token_Variable($va);
                        $this->n();

                        if($this->ct() == Hoa_Pom::_EQUAL) {

                            $this->n();
                            $de = $this->defv();
                            $this->n();
                        }

                        if($this->ct() == Hoa_Pom::_COMMA)
                            $this->n();

                        $ar = new Hoa_Pom_Token_Function_Argument($va);
                        $ar->referenceMe($re);

                        if(null !== $ty)
                            $ar->setType($ty);

                        if(null !== $de)
                            $ar->setDefaultValue($de);

                        $arg[] = $ar;
                    }

                    $this->n();

                    if(false === $abs) {

                        $bod = new Hoa_Pom_Token_LateParsing();

                        $ii = 1;

                        while($ii > 0) {

                            $this->n(0);

                            switch($this->ct()) {

                                case Hoa_Pom::_OPEN_BRACE:
                                    $ii++;
                                  break;

                                case Hoa_Pom::_CLOSE_BRACE:
                                    $ii--;
                                  break;
                            }

                            if($ii > 0)
                                $bod->addToken($this->c());
                        }
                    }

                    $met = new Hoa_Pom_Token_Class_Method($nam);

                    if(null !== $com)
                        $met->setComment($com);

                    $met->setAccess($acc);
                    $met->finalMe($fin);
                    $met->abstractMe($abs);
                    $met->staticMe($sta);
                    $met->referenceMe($ref);
                    $met->addArguments($arg);

                    if(null !== $bod)
                        $met->addBody($bod);

                    $methods[] = $met;
                }
            }
        }

        $class = new Hoa_Pom_Token_Class($name);

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

        return new Hoa_Pom_Token_Comment($this->cv());
    }

    /**
     * Default value.
     */
    public function defv ( ) {

        $default = null;
        $cv      = strtolower($this->cv());

        if(   $cv == 'true'
           || $cv == 'false')
            $default = new Hoa_Pom_Token_String_Boolean($this->cv());

        elseif($cv == 'null')
            $default = new Hoa_Pom_Token_String_Null($this->cv());

        elseif($this->ct() == Hoa_Pom::_CONSTANT_ENCAPSED_STRING)
            $default = new Hoa_Pom_Token_String_EncapsedConstant($this->cv());

        elseif($this->ct() == Hoa_Pom::_DNUMBER)
            $default = new Hoa_Pom_Token_Number_DNumber($this->cv());

        elseif($this->ct() == Hoa_Pom::_LNUMBER)
            $default = new Hoa_Pom_Token_Number_LNumber($this->cv());

        elseif($this->ct() == Hoa_Pom::_STRING) {

            $tmp = $this->cv();
            $this->n();

            if($this->ct() == Hoa_Pom::_DOUBLE_COLON) {

                $this->n();
                $default = new Hoa_Pom_Token_Call_ClassConstant(
                    new Hoa_Pom_Token_String($tmp)
                );
                $default->setConstant(new Hoa_Pom_Token_String($this->cv()));
            }
            else
                $default = new Hoa_Pom_Token_String_Constant($tmp);
        }

        elseif($this->ct() == Hoa_Pom::_ARRAY)
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
        $this->r()->getLastElement()->pop();

        if(   $this->ct() == Hoa_Pom::_COMMENT
           || $this->ct() == Hoa_Pom::_DOC_COMMENT) {

            $comment = $this->comm();
            $this->r()->getLastElement()->pop();
        }

        while($this->n() + 1 && $this->ct() != Hoa_Pom::_FUNCTION);
        $this->n();

        if($this->ct() == Hoa_Pom::_REFERENCE) {

            $reference = true;
            $this->n();
        }

        $name = new Hoa_Pom_Token_String($this->cv());
        $this->n();
        $this->n();

        while($this->ct() != Hoa_Pom::_CLOSE_PARENTHESES) {

            $type     = null;
            $ref      = false;
            $variable = null;
            $default  = null;
            $arg      = null;

            if(   $this->ct() == Hoa_Pom::_STRING
               || $this->ct() == Hoa_Pom::_ARRAY) {

                $type = new Hoa_Pom_Token_String($this->cv());
                $this->n();
            }

            if($this->ct() == Hoa_Pom::_REFERENCE) {

                $ref = true;
                $this->n();
            }

            $variable = new Hoa_Pom_Token_String(substr($this->cv(), 1));
            $variable = new Hoa_Pom_Token_Variable($variable);
            $this->n();

            if($this->ct() == Hoa_Pom::_EQUAL) {

                $this->n();
                $default = $this->defv();
                $this->n();
            }

            if($this->ct() == Hoa_Pom::_COMMA)
                $this->n();

            $arg = new Hoa_Pom_Token_Function_Argument($variable);
            $arg->referenceMe($reference);

            if(null !== $type)
                $arg->setType($type);

            if(null !== $default)
                $arg->setDefaultValue($default);

            $arguments[] = $arg;
        }

        $this->n();

        $body = new Hoa_Pom_Token_LateParsing();
        $i    = 1;

        while($i > 0) {

            $this->n(0);

            switch($this->ct()) {

                case Hoa_Pom::_OPEN_BRACE:
                    $i++;
                  break;

                case Hoa_Pom::_CLOSE_BRACE:
                    $i--;
                  break;
            }

            if($i > 0)
                $body->addToken($this->c());
        }

        $function = new Hoa_Pom_Token_Function_Named($name);

        if(null !== $comment)
            $function->setComment($comment);

        $function->referenceMe($reference);
        $function->addArguments($arguments);
        $function->addBody($body);

        return $function;
    }

    /**
     *
     */
    public function goToEndBlock ( ) {

        if($this->ct() != Hoa_Pom::_OPEN_BRACE)
            return;

        $i = 1;

        while($i > 0) {

            $this->n();

            switch($this->ct()) {

                case Hoa_Pom::_OPEN_BRACE:
                    $i++;
                  break;

                case Hoa_Pom::_CLOSE_BRACE:
                    $i--;
                  break;
            }
        }
    }
}
