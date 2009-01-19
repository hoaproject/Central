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

    protected $max = 0;

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

        $this->max = $this->max();
        reset($this->_token);
    }
}
