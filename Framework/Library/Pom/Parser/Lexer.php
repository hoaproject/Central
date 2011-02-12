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
 * Copyright (c) 2007, 2011 Ivan ENDERLIN. All rights reserved.
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
 * @subpackage  Hoa_Pom_Parser_Lexer
 *
 */

/**
 * Hoa_Pom_Exception
 */
import('Pom.Exception');

/**
 * Hoa_Pom
 */
import('Pom.~');

/**
 * Class Hoa_Pom_Parser_Lexer.
 *
 * Parse a PHP source code.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2011 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Pom
 * @subpackage  Hoa_Pom_Parser_Lexer
 */

class Hoa_Pom_Parser_Lexer {

    /**
     * Whether tokenize a source.
     *
     * @const int
     */
    const SOURCE = 0;

    /**
     * Whether tokenize a file.
     *
     * @const int
     */
    const FILE   = 1;

    /**
     * Whether consume a list of tokens (usefull for the late parser).
     *
     * @const int
     */
    const LATE   = 2;

    /**
     * Whether line numbers exist or not.
     *
     * @var Hoa_Pom_Parser_Lexer array
     */
    protected $_lineNumber = true;

    /**
     * Pom result.
     *
     * @var Hoa_Pom_Parser_Lexer array
     */
    protected $_token      = null;



    /**
     * Constructor. Redirect call to $this->token().
     *
     * @access  public
     * @param   mixed   $source    Source or path to source to tokenize or
     *                             tokens.
     * @param   int     $type      Given by constants self::SOURCE and
     *                             self::FILE.
     * @return  void
     * @throw   Hoa_Pom_Exception
     */
    public function __construct ( $source = null, $type = self::SOURCE ) {

        $this->_lineNumber = PHP_VERSION_ID >= 50202;
        $this->token($source, $type);

        return;
    }

    /**
     * Tokenize a file.
     *
     * @access  protected
     * @param   mixed      $source    Source or path to source to tokenize or
     *                                tokens.
     * @param   int        $type      Given by constants self::SOURCE and
     *                                self::FILE.
     * @return  array
     * @throw   Hoa_Pom_Exception
     */
    protected function token ( $source = null, $type = self::SOURCE ) {

        $old = $this->_token;

        if(self::FILE == $type) {

            if(!file_exists($source))
                throw new Hoa_Pom_Exception(
                    'Cannot parse file %s, because it does not exist.', 0,
                    $source);

            $this->_token = token_get_all(file_get_contents($source));
        }
        elseif(self::SOURCE == $type)
            $this->_token = token_get_all($source);
        else
            $this->_token = $source;

        $this->complete();

        return $old;
    }

    /**
     * Complete the tokenizer result.
     *
     * @access  protected
     * @return  void
     */
    protected function complete ( ) {

        foreach($this->_token as $i => &$t)
            if(is_string($t))
                $t = array($t, $t, -1);
            else
                if(false === $this->areLineNumbers())
                    $t[2] = -1;
    }

    /**
     * Get the tokenizer result.
     *
     * @access  public
     * @return  array
     */
    public function get ( ) {

        return $this->_token;
    }

    /**
     * Are line numbers activated ?
     *
     * @access  public
     * @return  bool
     */
    public function areLineNumbers ( ) {

        return $this->_lineNumber;
    }

    /**
     * Get a more friendly view of the plain tokenizer result.
     *
     * @access  public
     * @return  string
     */
    public function __toString ( ) {

        $out = null;

        foreach($this->get() as $i => $token)
            $out .= sprintf(
                        '%-6s%-28s%s',
                        ($token[2] < 0 ? '~' : $token[2]),
                        Hoa_Pom::tokenName($token[0]),
                        $token[1]
                    ) . "\n";

        return $out;
    }
}
