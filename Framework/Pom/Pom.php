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
 *
 */

/**
 * Hoa_Framework
 */
require_once 'Framework.php';

/**
 * Hoa_Pom_Parser_ParserLr
 */
import('Pom.Parser.ParserLr');

/**
 * Hoa_Pom_Parser_Lexer
 */
import('Pom.Parser.Lexer');

/**
 * Hoa_Pom_Token_Util_Visitor_Tokenize
 */
import('Pom.Token.Util.Visitor.Tokenize');

/**
 * Hoa_Pom_Token_Util_Visitor_PrettyPrint
 */
import('Pom.Token.Util.Visitor.PrettyPrint');

/**
 * Class Hoa_Pom.
 *
 * Describe all token constants and propose alias for parsing and building.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2008 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Pom
 */

abstract class Hoa_Pom {

    /**
     * List of all PHP tokens.
     *
     * @const int
     */
    const _ABSTRACT      = T_ABSTRACT;      // abstract
    const _AND_EQUAL     = T_AND_EQUAL;     // &=
    const _ARRAY         = T_ARRAY;         // array()
    const _ARRAY_CAST    = T_ARRAY_CAST;    // (array)
    const _AS            = T_AS;            // as (in foreach).
    const _BAD_CHARACTER = T_BAD_CHARACTER; // all characters < 0x32, except
                                            // 0x09, 0x0a and 0x0d
    const _BOOLEAN_AND   = T_BOOLEAN_AND;   // &&
    const _BOOLEAN_OR    = T_BOOLEAN_OR;    // ||
    const _BOOL_CAST     = T_BOOL_CAST;     // (bool) or (boolean)
    const _BREAK         = T_BREAK;         // break
    const _CASE          = T_CASE;          // case
    const _CATCH         = T_CATCH;         // catch
    const _CHARACTER     = T_CHARACTER;
    const _CLASS         = T_CLASS;         // class
    const _CLASS_C       = T_CLASS_C;       // __CLASS_
    const _CLONE         = T_CLONE;         // clone
    const _CLOSE_TAG     = T_CLOSE_TAG;     // (?|%)>
    const _COMMENT       = T_COMMENT;       // //, #, or /* */
    const _CONCAT_EQUAL  = T_CONCAT_EQUAL;  // .=
    const _CONST         = T_CONST;         // const
    const _CONSTANT_ENCAPSED_STRING =
                           T_CONSTANT_ENCAPSED_STRING; // "foo" or 'bar'
    const _CONTINUE      = T_CONTINUE;      // continue
    const _CURLY_OPEN    = T_CURLY_OPEN;
    const _DEC           = T_DEC;           // --
    const _DECLARE       = T_DECLARE;       // declare
    const _DEFAULT       = T_DEFAULT;       // default
    // in PHP 5.3
    //const _DIR         = T_DIR;           // __DIR__
    const _DIV_EQUAL     = T_DIV_EQUAL;     // /=
    const _DNUMBER       = T_DNUMBER;       // 0.12 etc.
    const _DOC_COMMENT   = T_DOC_COMMENT;   // /** */
    const _DO            = T_DO;            // do
    const _DOLLAR_OPEN_CURLY_BRACES =
                           T_DOLLAR_OPEN_CURLY_BRACES; // ${
    const _DOUBLE_ARROW  = T_DOUBLE_ARROW;  // =>
    const _DOUBLE_CAST   = T_DOUBLE_CAST;   // (real), (double) or (float)
    const _DOUBLE_COLON  = T_DOUBLE_COLON;  // ::
    const _ECHO          = T_ECHO;          // echo
    const _ELSE          = T_ELSE;          // else
    const _ELSEIF        = T_ELSEIF;        // elseif
    const _EMPTY         = T_EMPTY;         // empty
    const _ENCAPSED_AND_WHITESPACE =
                           T_ENCAPSED_AND_WHITESPACE;
    const _ENDDECLARE    = T_ENDDECLARE;    // enddeclare
    const _ENDFOR        = T_ENDFOR;        // endfor
    const _ENDFOREACH    = T_ENDFOREACH;    // endforeach
    const _ENDIF         = T_ENDIF;         // endif
    const _ENDSWITCH     = T_ENDSWITCH;     // endswitch
    const _ENDWHILE      = T_ENDWHILE;      // endwhile
    const _END_HEREDOC   = T_END_HEREDOC;
    const _EVAL          = T_EVAL;          // eval
    const _EXIT          = T_EXIT;          // exit
    const _EXTENDS       = T_EXTENDS;       // extends
    const _FILE          = T_FILE;          // __FILE__
    const _FINAL         = T_FINAL;         // final
    const _FOR           = T_FOR;           // for
    const _FOREACH       = T_FOREACH;       // foreach
    const _FUNCTION      = T_FUNCTION;      // function or cfunction
    const _FUNC_C        = T_FUNC_C;        // __FUNCTION__
    const _GLOBAL        = T_GLOBAL;        // global
    const _GOTO          = T_GOTO;          // goto
    const _HALT_COMPILER = T_HALT_COMPILER; // __halt_compiler
    const _IF            = T_IF;            // if
    const _IMPLEMENTS    = T_IMPLEMENTS;    // implements
    const _INC           = T_INC;           // ++
    const _INCLUDE       = T_INCLUDE;       // include
    const _INCLUDE_ONCE  = T_INCLUDE_ONCE;  // include_once
    const _INLINE_HTML   = T_INLINE_HTML;
    const _INSTANCEOF    = T_INSTANCEOF;    // instanceof
    const _INT_CAST      = T_INT_CAST;      // (int) or (integer)
    const _INTERFACE     = T_INTERFACE;     // interface
    const _ISSET         = T_ISSET;         // isset
    const _IS_EQUAL      = T_IS_EQUAL;      // ==
    const _IS_GREATER_OR_EQUAL =
                           T_IS_GREATER_OR_EQUAL; // >=
    const _IS_IDENTICAL  = T_IS_IDENTICAL;  // ===
    const _IS_NOT_EQUAL  = T_IS_NOT_EQUAL;  // != or <>
    const _IS_NOT_IDENTICAL =
                           T_IS_NOT_IDENTICAL; // !==
    const _IS_SMALLER_OR_EQUAL =
                           T_IS_SMALLER_OR_EQUAL; // <=
    const _LINE          = T_LINE;          // __LINE__
    const _LIST          = T_LIST;          // list
    const _LNUMBER       = T_LNUMBER;       // 123, 012, 0x1AC etc.
    const _LOGICAL_AND   = T_LOGICAL_AND;   // and
    const _LOGICAL_OR    = T_LOGICAL_OR;    // or
    const _LOGICAL_XOR   = T_LOGICAL_XOR;   // xor
    const _METHOD_C      = T_METHOD_C;      // __METHOD__
    const _MINUS_EQUAL   = T_MINUS_EQUAL;   // -=
    const _ML_COMMENT    = T_ML_COMMENT;    // /* */
    const _MOD_EQUAL     = T_MOD_EQUAL;     // %=
    const _MUL_EQUAL     = T_MUL_EQUAL;     // *=
    // in PHP 5.3
    //const _NS_C        = T_NS_C;          // __NAMESPACE__
    //const _NAMESPACE   = T_NAMESPACE;     // __NAMESPACE__
    const _NEW           = T_NEW;           // new
    const _NUM_STRING    = T_NUMB_STRING;
    const _OBJECT_CAST   = T_OBJECT_CAST;   // (object)
    const _OBJECT_OPERATOR =
                           T_OBJECT_OPERATOR; // ->
    const _OLD_FUNCTION  = T_OLD_FUNCTION;  // old_function
    const _OPEN_TAG      = T_OPEN_TAG;      // <(?php|?|%)
    const _OPEN_TAG_WITH_ECHO =
                           T_OPEN_TAG_WITH_ECHO; // <(?|%)=
    const _OR_EQUAL      = T_OR_EQUAL;      // |=
    const _PAAMAYIM_NEKUDOTAYIM =
                           T_PAAMAYIM_NEKUDOTAYIM; // ::
    const _PLUS_EQUAL    = T_PLUS_EQUAL;    // +=
    const _PRINT         = T_PRINT;         // print
    const _PRIVATE       = T_PRIVATE;       // private
    const _PUBLIC        = T_PUBLIC;        // public
    const _PROTECTED     = T_PROTECTED;     // protected
    const _REQUIRE       = T_REQUIRE;       // require
    const _REQUIRE_ONCE  = T_REQUIRE_ONCE;  // require_once
    const _RETURN        = T_RETURN;        // return
    const _SL            = T_SL;            // <<
    const _SL_EQUAL      = T_SL_EQUAL;      // <<=
    const _SR            = T_SR;            // >>
    const _SR_EQUAL      = T_SR_EQUAL;      // >>=
    const _START_HEREDOC = T_START_HEREDOC; // <<<
    const _STATIC        = T_STATIC;        // static
    const _STRING        = T_STRING;
    const _STRING_CAST   = T_STRING_CAST;   // (string)
    const _STRING_VARNAME =
                           T_STRING_VARNAME;
    const _SWITCH        = T_SWITCH;        // switch
    const _THROW         = T_THROW;         // throw
    const _TRY           = T_TRY;           // try
    const _UNSET         = T_UNSET;         // unset
    const _UNSET_CAST    = T_UNSET_CAST;    // (unset)
    // in PHP 5.3
    //const _USE         = T_USE;           // use
    const _VAR           = T_VAR;           // var
    const _VARIABLE      = T_VARIABLE;      // $foo
    const _WHILE         = T_WHILE;         // while
    const _WHITESPACE    = T_WHITESPACE;
    const _XOR_EQUAL     = T_XOR_EQUAL;     // ^=

    /**
     * List of other token characters.
     *
     * @const string
     */
    const _AT_SIGN           = '@';
    const _BITWISE_AND       = '&';
    const _BITWISE_OR        = '|';
    const _BITWISE_XOR       = '^';
    const _BITWISE_NOT       = '~';
    const _CLOSE_BRACE       = '}';
    const _CLOSE_BRACKET     = ']';
    const _CLOSE_PARENTHESES = ')';
    const _COMMA             = ',';
    const _COLON             = ':';
    const _DIV               = '/';
    const _DOUBLE_QUOTES     = '"';
    const _EQUAL             = '=';
    const _EXECUTION         = '`';
    const _IS_GREATER        = '>';
    const _IS_SMALLER        = '<';
    const _QUESTION_MARK     = '?';
    const _LOGICAL_NOT       = '!';
    const _MINUS             = '-';
    const _MOD               = '%';
    const _MUL               = '*';
    const _OPEN_BRACE        = '{';
    const _OPEN_BRACKET      = '[';
    const _OPEN_PARENTHESES  = '(';
    const _PLUS              = '+';
    const _POINT             = '.';
    const _REFERENCE         = '&';
    const _SEMI_COLON        = ';';

    /**
     * Whether tokenize a source.
     *
     * @const int
     */
    const TOKENIZE_SOURCE = Hoa_Pom_Parser_Lexer::SOURCE;

    /**
     * Whether tokenize a file.
     *
     * @const int
     */
    const TOKENIZE_FILE   = Hoa_Pom_Parser_Lexer::FILE;

    /**
     * Whether consume a list of tokens (usefull for the late parser).
     *
     * @const int
     */
    const TOKENIZE_LATE   = Hoa_Pom_Parser_Lexer::LATE;



    /**
     * Parse a PHP source code.
     *
     * @access  public
     * @param   string  $source    Source or filename.
     * @param   int     $type      Given by constants self::TOKENIZE_*.
     * @return  Hoa_Pom_Token_Root
     */
    public static function parse ( $source = null,
                                   $type   = self::TOKENIZE_SOURCE ) {

        $parser = new Hoa_Pom_Parser_ParserLr($source, $type);

        return $parser->getRoot();
    }

    /**
     * Tokenize the root of object model, i.e. get a “tokenize array”.
     *
     * @access  public
     * @param   Hoa_Pom_Token_Root  $root    Root of object model.
     * @return  array
     */
    public static function tokenize ( Hoa_Pom_Token_Root $root ) {

        $tokenize = new Hoa_Pom_Token_Util_Visitor_Tokenize();

        return $tokenize->visit($root);
    }

    /**
     * Print the root of object model.
     *
     * @access  public
     * @param   Hoa_Pom_Token_Root  $root    Root of object model.
     * @return  string
     */
    public static function dump ( Hoa_Pom_Token_Root $root ) {

        $print = new Hoa_Pom_Token_Util_Visitor_PrettyPrint();

        return $print->visit($root);
    }

    /**
     * Get token name.
     *
     * @access  public
     * @param   mixed   $token    Token identifier, could be an integer or a
     *                            string.
     * @return  string
     * @throw   Hoa_Pom_Exception
     */
    public static function tokenName ( $token ) {

        if(is_int($token))
            return substr(token_name($token), 2);

        switch($token) {

            case '@':
                return 'AT_SIGN';
              break;

            case '&':
                return 'BITWISE_AND';
              break;

            case '|':
                return 'BITWISE_OR';
              break;

            case '^':
                return 'BITWISE_XOR';
              break;

            case '~':
                return 'BITWISE_NOT';
              break;

            case '}':
                return 'CLOSE_BRACE';
              break;

            case ']':
                return 'CLOSE_BRACKET';
              break;

            case ')':
                return 'CLOSE_PARENTHESES';
              break;

            case ':':
                return 'COLON';
              break;

            case ',':
                return 'COMMA';
              break;

            case '/':
                return 'DIV';
              break;

            case '"':
                return 'DOUBLE_QUOTES';
              break;

            case '=':
                return 'EQUAL';
              break;

            case '`':
                return 'EXECUTION';
              break;

            case '>':
                return 'IS_GREATER';
              break;

            case '<':
                return 'IS_SMALLER';
              break;

            case '?':
                return 'QUESTION_MARK';
              break;

            case '!':
                return 'LOGICAL_NOT';
              break;

            case '-':
                return 'MINUS';
              break;

            case '%':
                return 'MOD';
              break;

            case '*':
                return 'MUL';
              break;

            case '{':
                return 'OPEN_BRACE';
              break;

            case '[':
                return 'OPEN_BRACKET';
              break;

            case '(':
                return 'OPEN_PARENTHESES';
              break;

            case '+':
                return 'PLUS';
              break;

            case '.':
                return 'POINT';
              break;

            case '&':
                return 'REFERENCE';
              break;

            case ';':
                return 'SEMI_COLON';
              break;

            default:
                throw new Hoa_Pom_Exception(
                    'Token %s does not exist.', 0, $token);
        }
    }
}
