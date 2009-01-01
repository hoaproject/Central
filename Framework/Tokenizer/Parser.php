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
 * Hoa_Tokenizer
 */
import('Tokenizer.~');

/**
 * Class Hoa_Tokenizer_Parser.
 *
 * Parse a PHP source code.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2008 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Tokenizer
 * @subpackage  Hoa_Tokenizer_Parser
 */

class Hoa_Tokenizer_Parser {

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
     * Whether line numbers exist or not.
     *
     * @var Hoa_Tokenizer_Parser array
     */
    protected $_lineNumber = true;

    /**
     * Tokenizer result.
     *
     * @var Hoa_Tokenizer_Parser array
     */
    protected $_token      = null;



    /**
     * Constructor. Redirect call to $this->token().
     *
     * @access  public
     * @param   string  $source    Source of file to tokenize.
     * @param   int     $type      Given by constants self::SOURCE and
     *                             self::FILE.
     * @return  void
     * @throw   Hoa_Tokenizer_Exception
     */
    public function __construct ( $source = null, $type = self::SOURCE ) {

        $this->_lineNumber = version_compare(phpversion(), '5.2.2', '>=');
        $this->token($source, $type);

        return;
    }

    /**
     * Tokenize a file.
     *
     * @access  protected
     * @param   string     $source    Source of file to tokenize.
     * @param   int        $type      Given by constants self::SOURCE and
     *                                self::FILE.
     * @return  array
     * @throw   Hoa_Tokenizer_Exception
     */
    protected function token ( $source = null, $type = self::SOURCE ) {

        $old = $this->_token;

        if(self::FILE == $type) {

            if(!file_exists($source))
                throw new Hoa_Tokenizer_Exception(
                    'Cannot parse file %s, because it does not exist.', 0,
                    $source);

            $this->_token = token_get_all(file_get_contents($source));
        }
        else
            $this->_token = token_get_all($source);

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

        foreach($this->_token as $i => &$t) {

            if(is_string($t))
                $t = array($t, $t, -1);
            else
                if(false === $this->areLineNumbers())
                    $t[2] = -1;
        }
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
     * @todo    Ameliorate.
     */
    public function __toString ( ) {

        $out = null;

        foreach($this->get() as $i => $token)
            $out .= sprintf(
                       '%-6s%-28s%s',
                       ($token[2] < 0 ? '~' : $token[2]),
                       Hoa_Tokenizer::tokenName($token[0]),
                       $token[1]
                   ) . "\n";

        return $out;
    }
}
