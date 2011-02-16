<?php

/**
 * Hoa
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
 * @subpackage  Hoa_Pom_Token_Exception
 *
 */

/**
 * Hoa_Pom_Token_Util_Exception
 */
import('Pom.Token.Util.Exception');

/**
 * Hoa_Pom
 */
import('Pom.~');

/**
 * Hoa_Visitor_Element
 */
import('Visitor.Element');

/**
 * Class Hoa_Pom_Token_Exception.
 *
 * Represent a thrown exception.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2011 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Pom
 * @subpackage  Hoa_Pom_Token_Exception
 */

class Hoa_Pom_Token_Exception implements Hoa_Visitor_Element {

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
     * Accept a visitor.
     *
     * @access  public
     * @param   Hoa_Visitor_Visit  $visitor    Visitor.
     * @param   mixed              &$handle    Handle (reference).
     * @param   mixed              $eldnah     Handle (not reference).
     * @return  mixed
     */
    public function accept ( Hoa_Visitor_Visit $visitor,
                             &$handle = null,
                              $eldnah = null ) {

        return $visitor->visit($this, $handle, $eldnah);
    }
}
