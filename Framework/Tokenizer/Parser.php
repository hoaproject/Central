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
 * Hoa_Tokenize_Token_Util_Exception
 */
import('Tokenize.Token.Util.Exception');

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

abstract class Hoa_Tokenizer_Parser implements Hoa_Tokenizable_Token_Util_Interface_Tokenizable {

    /**
     * Token collection.
     *
     * @var Hoa_Tokenizer_Parser_Token object
     */
    protected $_token = null;

    /**
     * Root of object model.
     *
     * @var Hoa_Tokenizer_Token_Root object
     */
    protected $_root  = null;



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
        $this->_token = new Hoa_Tokenizer_Parser_Token($source, $type);

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
     * Transform root to “tokenizer array”.
     *
     * @access  public
     * @return  array
     */
    public function tokenize ( ) {

        return $this->r()->tokenize();
    }
}
