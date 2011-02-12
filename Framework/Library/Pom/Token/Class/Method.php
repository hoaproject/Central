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
 * @subpackage  Hoa_Pom_Token_Class_Method
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
 * Hoa_Pom_Token_Function_Named
 */
import('Pom.Token.Function.Named');

/**
 * Hoa_Pom_Token_Class_Access
 */
import('Pom.Token.Class.Access');

/**
 * Hoa_Visitor_Element
 */
import('Visitor.Element');

/**
 * Class Hoa_Pom_Token_Class_Method.
 *
 * Represent a method.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2011 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Pom
 * @subpackage  Hoa_Pom_Token_Class_Method
 */

class Hoa_Pom_Token_Class_Method extends    Hoa_Pom_Token_Function_Named
                                 implements Hoa_Visitor_Element {

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
     * Attribute is static (STATIC_M_, M means MEMORY).
     *
     * @cons bool
     */
    const STATICM         = true;

    /**
     * Attribute is dynamic (DYNAMIC_M_, M means MEMORY).
     *
     * @const bool
     */
    const DYNAMICM        = false;

    /**
     * Access.
     *
     * @var Hoa_Pom_Token_Class_Access object
     */
    protected $_access     = null;

    /**
     * Whether attribute is static.
     *
     * @var Hoa_Pom_Token_Class_Method bool
     */
    protected $_static     = false;

    /**
     * Whether method is final.
     *
     * @var Hoa_Pom_Token_Clas_Method bool
     */
    protected $_isFinal    = false;

    /**
     * Whether method is abstract.
     *
     * @var Hoa_Pom_Token_Class_Method bool
     */
    protected $_isAbstract = false;



    /**
     * Constructor.
     *
     * @access  public
     * @param   Hoa_Pom_Token_String  $name    Method name.
     * @return  void
     */
    public function __construct ( Hoa_Pom_Token_String $name ) {

        $this->setAccess(new Hoa_Pom_Token_Class_Access('public'));
        parent::enableComment(false);

        return parent::__construct($name);
    }

    /**
     * Set access.
     *
     * @access  public
     * @param   Hoa_Pom_Token_Class_Access  $access    Method access.
     * @return  Hoa_Pom_Token_Class_Access
     */
    public function setAccess ( Hoa_Pom_Token_Class_Access $access ) {

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

        $old               = $this->_isFinal;
        $this->_isFinal    = $final;
        $this->_isAbstract = self::CONCRET_METHOD;

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

        $old               = $this->_isAbstract;
        $this->_isAbstract = $abstract;
        $this->_isFinal    = self::MEMBER_METHOD;

        return $old;
    }

    /**
     * Set if attribute is static or not.
     *
     * @access  public
     * @param   bool    $static    Static or not (given by constants *M).
     * @return  bool
     */
    public function staticMe ( $static = self::STATICM ) {

        $old           = $this->_static;
        $this->_static = $static;

        return $old;
    }

    /**
     * Set if attribute is dynamic or not.
     *
     * @access  public
     * @param   bool    $dynamique    Dynamique or not (given by constants *M).
     * @return  bool
     */
    public function dynamicMe ( $dynamic = self::DYNAMICM ) {

        return !$this->staticMe(!$dynamic);
    }

    /**
     * Get access.
     *
     * @access  public
     * @return  Hoa_Pom_Token_Class_Access
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
     * Check if attribute is static or not.
     *
     * @access  public
     * @return   bool
     */
    public function isStatic ( ) {

        return $this->_static;
    }

    /**
     * Check if attribute is dynamic or not.
     *
     * @access  public
     * @return  bool
     */
    public function isDynamic ( ) {

        return !$this->isStatic();
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
