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
 * @subpackage  Hoa_Pom_Token_ControlStructure_Conditional_If
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
 * Hoa_Pom
 */
import('Pom.~');

/**
 * Hoa_Pom_Token_ControlStructure_Conditional
 */
import('Pom.Token.ControlStructure.Conditional');

/**
 * Class Hoa_Pom_Token_ControlStructure_Conditional_If.
 *
 * Represent an if structure.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2008 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Pom
 * @subpackage  Hoa_Pom_Token_ControlStructure_Conditional_If
 */

class       Hoa_Pom_Token_ControlStructure_Conditional_If
    extends Hoa_Pom_Token_ControlStructure_Conditional {

    /**
     * If.
     *
     * @var Hoa_Pom_Token_ControlStructure_Conditional_If_If object
     */
    protected $_if     = null;

    /**
     * Collection of Elseif.
     *
     * @var Hoa_Pom_Token_ControlStructure_Conditional_If array
     */
    protected $_elseif = array();

    /**
     * Else.
     *
     * @var Hoa_Pom_Token_ControlStructure_Conditional_If_Else object
     */
    protected $_else   = null;



    /**
     * Constructor.
     *
     * @access  public
     * @param   Hoa_Pom_Token_ControlStructure_Conditional_If_If  $if    If.
     * @return  void
     */
    public function __construct ( Hoa_Pom_Token_ControlStructure_Conditional_If_If $if ) {

        $this->setIf($if);

        return;
    }

    /**
     * Set if.
     *
     * @access  public
     * @param   Hoa_Pom_Token_ControlStructure_Conditional_If_If  $if    If.
     * @return  Hoa_Pom_Token_ControlStructure_Conditional_If_If
     */
    public function setIf ( Hoa_Pom_Token_ControlStructure_Conditional_If_If $if ) {

        $old       = $this->_if;
        $this->_if = $if;

        return $old;
    }

    /**
     * Add many elseifs.
     *
     * @access  public
     * @param   array   $elseifs    Elseifs to add.
     * @return  array
     */
    public function addElseifs ( Array $elseifs = array() ) {

        foreach($elseifs as $i => $elseif)
            $this->addElseif($elseif);

        return $this->_elseif;
    }

    /**
     * Add an elseif.
     *
     * @access  public
     * @param   Hoa_Pom_Token_ControlStructure_Conditional_If_Elseif  $elseif    Elseif to add.
     * @return  Hoa_Pom_Token_ControlStructure_Conditional_If_Elseif
     */
    public function addElseif ( Hoa_Pom_Token_ControlStructure_Conditional_If_Elseif $elseif ) {

        return $this->_elseif[] = $elseif;
    }

    /**
     * Remove all elseifs.
     *
     * @access  public
     * @return  array
     */
    public function removeElseifs ( ) {

        $old = $this->_elseif;

        foreach($this->_elseif as $i => $elseif)
            unset($this->elseif[$i]);

        return $old;
    }

    /**
     * Remove an elseif.
     *
     * @access  public
     * @param   int     $i    Elseif number.
     * @return  array
     */
    public function removeElseif ( $i ) {

        unset($this->_elseif[$i]);

        return $this->_elseif;
    }

    /**
     * Set else.
     *
     * @access  public
     * @param   Hoa_Pom_Token_ControlStructure_Conditional_If_Else  $else    Else to add.
     * @return  Hoa_Pom_Token_ControlStructure_Conditional_If_Else
     */
    public function setElse ( Hoa_Pom_Token_ControlStructure_Conditional_If_Elseif $else ) {

        $old         = $this->_else;
        $this->_else = $else;

        return $old;
    }

    /**
     * Remove else.
     *
     * @access  public
     * @return  Hoa_Pom_Token_ControlStructure_Conditional_If_Elseif
     */
    public function removeElse ( ) {

        $old         = $this->_else;
        $this->_else = null;

        return $old;
    }

    /**
     * Get if.
     *
     * @access  public
     * @return  Hoa_Pom_Token_ControlStructure_Conditional_If_If
     */
    public function getIf ( ) {

        return $this->_if;
    }

    /**
     * Get all elseifs.
     *
     * @access  public
     * @return  array
     */
    public function getElseifs ( ) {

        return $this->_elseif;
    }

    /**
     * Get an elseif.
     *
     * @access  public
     * @param   int     $i    Elseif number.
     * @return  Hoa_Pom_Token_ControlStructure_Conditional_If_Elseif
     * @throw   Hoa_Pom_Token_Util_Exception
     */
    public function getElseif ( $i ) {

        if(!isset($this->_elseif[$i]))
            throw new Hoa_Pom_Token_Util_Exception(
                'Elseif number %d does not exist.', 0, $i);

        return $this->_elseif[$i];
    }

    /**
     * Check if some elseifs have been declared.
     *
     * @access  public
     * @return  bool
     */
    public function hasElseif ( ) {

        return $this->_elseif != array();
    }

    /**
     * Get else.
     *
     * @access  public
     * @return  Hoa_Pom_Token_ControlStructure_Conditional_If_Else
     */
    public function getElse ( ) {

        return $this->_else;
    }

    /**
     * Check if an else exists.
     *
     * @access  public
     * @return  bool
     */
    public function hasElse ( ) {

        return $this->_else !== null;
    }

    /**
     * Transform token to “tokenizer array”.
     *
     * @access  public
     * @return  array
     */
    public function tokenize ( ) {

        $if     = $this->getIf()->tokenize();
        $elseif = array();

        foreach($this->getElseifs() as $i => $ei)
            foreach($ei->tokenize() as $key => $value)
                $elseif[] = $value;

        $else   = true === $this->hasElse()
                      ? $this->getElse()->tokenize()
                      : array();

        return array_merge(
            $if,
            $elseif,
            $else
        );
    }
}
