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
 * @subpackage  Hoa_Tokenizer_Token_Class_Access
 *
 */

/**
 * Hoa_Framework
 */
require_once 'Framework.php';

/**
 * Hoa_Tokenizer_Token_Util_Exception
 */
import('Tokenizer.Token.Util.Exception');

/**
 * Hoa_Tokenizer_Token_Util_Interface
 */
import('Tokenizer.Token.Util.Interface');

/**
 * Hoa_Tokenizer
 */
import('Tokenizer.~');

/**
 * Class Hoa_Tokenizer_Token_Class_Access.
 *
 * Represent an access : public, protected or private.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2008 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Tokenizer
 * @subpackage  Hoa_Tokenizer_Token_Class_Access
 */

class Hoa_Tokenizer_Token_Class_Access implements Hoa_Tokenizer_Token_Util_Interface {

    /**
     * Access.
     *
     * @var Hoa_Tokenizer_Token_Class_Access string
     */
    protected $_access = 'public';

    /**
     * Type of access.
     *
     * @var Hoa_Tokenizer_Token_Class_Access  int
     */
    protected $_type   = Hoa_Tokenizer::_PUBLIC;



    /**
     * Constructor.
     *
     * @access  public
     * @param   string  $access    Access.
     * @return  void
     */
    public function __construct ( $access ) {

        $this->setAccess();

        return;
    }

    /**
     * Set access.
     *
     * @access  public
     * @param   string  $access  Access.
     * @return  string
     * @throw   Hoa_Tokenizer_Token_Util_Exception
     */
    public function setAccess ( $access ) {

        $old = $this->_access;

        switch($access) {

            case 'public':
                $this->_access = 'public';
                $this->_type   = Hoa_Tokenizer::_PUBLIC;
              break;

            case 'protected':
                $this->_access = 'protected';
                $this->_type   = Hoa_Tokenizer::_PROTECTED;
              break;

            case 'private':
                $this->_access = 'private';
                $this->_type   = Hoa_Tokenizer::_PRIVATE;
              break;

            default:
                throw new Hoa_Tokenizer_Token_Util_Exception(
                    'Access %s does not exist.', 0, $access);
        }

        return $old;
    }

    /**
     * Get access.
     *
     * @access  public
     * @return  string
     */
    public function getAccess ( ) {

        return $this->_access;
    }

    /**
     * Get type.
     *
     * @access  protected
     * @return  int
     */
    protected function getType ( ) {

        return $this->_type;
    }

    /**
     * Transform token to “tokenizer array”.
     *
     * @access  public
     * @return  array
     */
    public function toArray ( ) {

        return array(array(
            $this->getType(),
            $this->getAccess(),
            -1
        ));
    }
}
