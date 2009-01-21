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
 * @subpackage  Hoa_Tokenizer_Parser
 *
 */

/**
 * Hoa_Framework
 */
require_once 'Framework.php';

/**
 * Hoa_Tokenizer_Exception
 */
import('Tokenizer.Exception');

/**
 * Hoa_Tokenizer_Token_Util_Exception
 */
import('Tokenizer.Token.Util.Exception');

/**
 * Hoa_Tokenizer_Token_Util_Interface_Tokenizable
 */
import('Tokenizer.Token.Util.Interface.Tokenizable');

/**
 * Hoa_Tokenizer
 */
import('Tokenizer.~');

/**
 * Hoa_Tokenizer_Parser_Token
 */
import('Tokenizer.Parser.Token');

/**
 * Hoa_Tokenizer_Token_Root
 */
import('Tokenizer.Token.Root');

/**
 * Class Hoa_Tokenizer_Parser.
 *
 * .
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2008 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Tokenizer
 * @subpackage  Hoa_Tokenizer_Parser
 */

abstract class Hoa_Tokenizer_Parser implements Hoa_Tokenizer_Token_Util_Interface_Tokenizable {

    /**
     * Token collection.
     *
     * @var Hoa_Tokenizer_Parser array
     */
    protected $_token = null;

    /**
     * Root of object model.
     *
     * @var Hoa_Tokenizer_Token_Root object
     */
    protected $_root  = null;

    /**
     * Maximum of tokens.
     *
     * @var Hoa_Tokenizer_Parser int
     */
    protected $_max   = 0;

    /**
     * Current token position.
     *
     * @var Hoa_Tokenizer_Parser int
     */
    protected $_i     = 0;



    /**
     * Constructor.
     *
     * @access  public
     * @param   string  $source    Source or filename.
     * @param   int     $type      Given by constants
     *                             Hoa_Tokenizer_Parser_Token::SOURCE and
     *                             Hoa_Tokenizer_Parser_Token::FILE.
     * @return  void
     */
    public function __construct ( $source = null,
                                  $type   = Hoa_Tokenizer_Parser_Token::SOURCE ) {

        $this->setToken($source, $type);
        $this->setRoot();

        return;
    }

    /**
     * Set token.
     *
     * @access  protected
     * @param   string     $source    Source or filename.
     * @param   int        $type      Given by constants
     *                                Hoa_Tokenizer_Parser_Token::SOURCE and
     *                                Hoa_Tokenizer_Parser_Token::FILE.
     * @return  Hoa_Tokenizer_Parser_Token
     */
    protected function setToken ( $source = null,
                                  $type   = Hoa_Tokenizer_Parser_Token::SOURCE ) {

        $old          = $this->_token;
        $handle       = new Hoa_Tokenizer_Parser_Token($source, $type);
        $this->_token = $handle->get();
        $this->_max   = count($this->_token);

        return $old;
    }

    /**
     * Set root.
     *
     * @access  protected
     * @return  Hoa_Tokenizer_Token_Root
     */
    protected function setRoot ( ) {

        $old         = $this->_root;
        $this->_root = new Hoa_Tokenizer_Token_Root();

        return $old;
    }

    /**
     * Get token.
     *
     * @access  protected
     * @return  Hoa_Tokenizer_Parser_Token
     */
    protected function t ( ) {

        return $this->_token;
    }

    /**
     * Get root.
     *
     * @access  protected
     * @return  Hoa_Tokenizer_Token_Root
     */
    protected function r ( ) {

        return $this->_root;
    }

    /**
     * Control : go to previous token.
     *
     * @access  protected
     * @param   int        $n    Number of whitespaces to skip.
     * @return  void
     */
    protected function p ( $n = -1 ) {

        while( --$this->_i
              && $this->_i >= 0
              && $n-- != 0
              && $this->ct() == Hoa_Tokenizer::_WHITESPACE);
    }

    /**
     * Control : go to next token.
     *
     * @access  protected
     * @param   int        $n    Number of whitespace to skip.
     * @return  void
     */
    protected function n ( $n = -1 ) {

        while( ++$this->_i
              && $this->end()
              && $n-- != 0
              && $this->ct() == Hoa_Tokenizer::_WHITESPACE);
    }

    /**
     * Control : get the current position.
     *
     * @access  protected
     * @return  int
     */
    protected function i ( ) {

        return $this->_i;
    }

    /**
     * Control : get the current token array.
     *
     * @access  protected
     * @return  array
     */
    protected function c ( ) {

        return $this->_token[$this->_i];
    }

    /**
     * Control : get the current token token.
     *
     * @access  protected
     * @return  mixed
     */
    protected function ct ( ) {

        return $this->_token[$this->_i][0];
    }

    /**
     * Control : get the current token value.
     *
     * @access  protected
     * @return  string
     */
    protected function cv ( ) {

        return $this->_token[$this->_i][1];
    }

    /**
     * Control : get the current token line.
     *
     * @access  protected
     * @return  int
     */
    protected function cl ( ) {

        return $this->_token[$this->_i][2];
    }

    /**
     * Control : check if we are at the end or not.
     *
     * @access  protected
     * @return  bool
     */
    protected function end ( ) {

        return $this->_i < $this->_max;
    }

    /**
     * Control : get the maximum of token.
     *
     * @access  protected
     * @return  int
     */
    protected function max ( ) {

        return count($this->_token);
    }

    /**
     * Transform root to “tokenizer array”.
     *
     * @access  public
     * @return  array
     */
    public function tokenize ( ) {

        return $this->r()->tokenize();
        //return array();
    }
}
