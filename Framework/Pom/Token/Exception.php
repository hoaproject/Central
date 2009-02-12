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
 * @subpackage  Hoa_Pom_Token_Exception
 *
 */

/**
 * Hoa_Framework
 */
require_once 'Framework.php';

/**
 * Hoa_Pom_Token_Util_Exception
 */
import('Pom.Token.Util.Exception');

/**
 * Hoa_Pom_Token_Util_Interface_Tokenizable
 */
import('Pom.Token.Util.Interface.Tokenizable');

/**
 * Hoa_Pom
 */
import('Pom.~');

/**
 * Class Hoa_Pom_Token_Exception.
 *
 * Represent a thrown exception.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2008 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Pom
 * @subpackage  Hoa_Pom_Token_Exception
 */

class Hoa_Pom_Token_Exception implements Hoa_Pom_Token_Util_Interface_Tokenizable {

    /**
     * Exception wrapper.
     *
     * @var Hoa_Pom_Token_Exception mixed
     */
    protected $_exception = null;



    /**
     * Constructor.
     *
     * @access  public
     * @param   mixed   $exception    Exception.
     * @return  void
     */
    public function __construct ( $exception ) {

        $this->setException($exception);

        return;
    }

    /**
     * Set exception.
     *
     * @access  public
     * @param   mixed   $exception    Exception.
     * @return  mixed
     * @throw   Hoa_Pom_Token_Util_Exception
     */
    public function setException  ( $exception ) {

        if(   !($exception instanceof Hoa_Pom_Token_Call)
           && !($exception instanceof Hoa_Pom_Token_New)
           && !($exception instanceof Hoa_Pom_Token_Variable))
            throw new Hoa_Pom_Token_Util_Exception(
                'Cannot throw a class that represents a %s.', 0,
                get_class($exception));

        $this->_exception = $exception;

        return $this->_exception;
    }

    /**
     * Get exception.
     *
     * @access  public
     * @return  mixed
     */
    public function getException ( ) {

        return $this->_exception;
    }

    /**
     * Transform token to “tokenizer array”.
     *
     * @access  public
     * @return  array
     */
    public function tokenize ( ) {

        return array_merge(
            array(array(
                0 => Hoa_Pom::_THROW,
                1 => 'throw',
                2 => -1
            )),
            $this->getException()->tokenize()
        );
    }
}
