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
 * @subpackage  Hoa_Tokenizer_Token_Class_Method
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
 * Hoa_Tokenizer
 */
import('Tokenizer.~');

/**
 * Hoa_Tokenizer_Token_Function_Named
 */
import('Tokenizer.Token.Function.Named');

/**
 * Hoa_Tokenizer_Token_Class_Access
 */
import('Tokenizer.Token.Class.Access');

/**
 * Class Hoa_Tokenizer_Token_Class_Method.
 *
 * .
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2008 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Tokenizer
 * @subpackage  Hoa_Tokenizer_Token_Class_Method
 */

class Hoa_Tokenizer_Token_Class_Method extends Hoa_Tokenizer_Token_Function_Named {

    /**
     * Method is final.
     *
     * @const bool
     */
    const FINAL_METHOD    = true;

    /**
     * Method is a member, i.e. not a final.
     *
     * @const bool
     */
    const MEMBER_METHOD   = false;

    /**
     * Method is abstract.
     *
     * @const bool
     */
    const ABSTRACT_METHOD = true;

    /**
     * Method is concret, i.e. not abstract.
     *
     * @const bool
     */
    const CONCRET_METHOD  = false;

    /**
     * Access.
     *
     * @var Hoa_Tokenizer_Token_Class_Access object
     */
    protected $_access     = null;

    /**
     * Whether method is final.
     *
     * @var Hoa_Tokenizer_Token_Clas_Method bool
     */
    protected $_isFinal    = false;

    /**
     * Whether method is abstract.
     *
     * @var Hoa_Tokenizer_Token_Class_Method bool
     */
    protected $_isAbstract = false;



    /**
     * Constructor.
     *
     * @access  public
     * @param   Hoa_Tokenizer_Token_String  $name    Method name.
     * @return  void
     */
    public function __construct ( Hoa_Tokenizer_Token_String $name ) {

        $this->setAccess(new Hoa_Tokenizer_Token_Class_Access('public'));

        return parent::__construct($name);
    }

    /**
     * Set access.
     *
     * @access  public
     * @param   Hoa_Tokenizer_Token_Class_Access  $access    Method access.
     * @return  Hoa_Tokenizer_Token_Class_Access
     */
    public function setAccess ( Hoa_Tokenizer_Token_Class_Access $access ) {

        $old           = $this->_access;
        $this->_access = $access;

        return $old;
    }

    /**
     * Final method.
     *
     * @access  public
     * @param   bool    $final    Whether method is final, given by
     *                            constants self::FINAL_METHOD or
     *                            self::MEMBER_METHOD.
     * @return  bool
     */
    public function finalMe ( $final = self::FINAL_METHOD ) {

        $this->abstractMe(self::CONCRET_METHOD);

        $old            = $this->_isFinal;
        $this->_isFinal = $final;

        return $old;
    }

    /**
     * Abstract method.
     *
     * @access  public
     * @param   bool    $abstract    Whether method is abstract, given by
     *                               constants self::ABSTRACT_METHOD or
     *                               self::CONCRET_METHOD.
     * @return  bool
     */
    public function abstractMe ( $abstract = self::ABSTRACT_METHOD ) {

        $this->finalMe(self::MEMBER_METHOD);

        $old               = $this->_isAbstract;
        $this->_isAbstract = $abstract;

        return $old;
    }

    /**
     * Get access.
     *
     * @access  public
     * @return  Hoa_Tokenizer_Token_Class_Access
     */
    public function getAccess ( ) {

        return $this->_access;
    }

    /**
     * Whether method is final.
     *
     * @access  public
     * @return  bool
     */
    public function isFinal ( ) {

        return $this->_isFinal;
    }

    /**
     * Whether method is abstract.
     *
     * @access  public
     * @return  bool
     */
    public function isAbstract ( ) {

        return $this->_isAbstract;
    }

    /**
     * Transform token to “tokenizer array”.
     *
     * @access  public
     * @return  array
     */
    public function tokenize ( ) {

        return array_merge(
            (true === $this->isFinal()
                 ? array(array(
                       0 => Hoa_Tokenizer::_FINAL,
                       1 => 'final',
                       2 => -1
                   ))
                 : array(array())
            ),
            (true === $this->isAbstract()
                 ? array(array(
                       0 => Hoa_Tokenizer::_ABSTRACT,
                       1 => 'abstract',
                       2 => -1
                   ))
                 : array(array())
            ),
            $this->getAccess()->tokenize(),
            parent::tokenize()
        );
    }
}
