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
 * Copyright (c) 2007, 2010 Ivan ENDERLIN. All rights reserved.
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
 * @package     Hoa_Test
 * @subpackage  Hoa_Test_Praspel_DomainDisjunction
 *
 */

/**
 * Hoa_Test_Praspel_Exception
 */
import('Test.Praspel.Exception');

/**
 * Hoa_Test_Praspel_Domain
 */
import('Test.Praspel.Domain') and load();

/**
 * Hoa_Visitor_Element
 */
import('Visitor.Element');

/**
 * Class Hoa_Test_Praspel_DomainDisjunction.
 *
 * Represent a domains disjunction.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2010 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Test
 * @subpackage  Hoa_Test_Praspel_DomainDisjunction
 */

abstract class Hoa_Test_Praspel_DomainDisjunction
    implements Hoa_Visitor_Element {

    /**
     * Collection of domains.
     *
     * @var Hoa_Test_Praspel_DomainDisjunction array
     */
    protected $_domains = array();

    /**
     * Current defining domain.
     *
     * @var Hoa_Test_Praspel_Domain object
     */
    protected $_domain  = null;

    /**
     * Make a disjunction between two variables.
     *
     * @var Hoa_Test_Praspel_DomainDisjunction object
     */
    public $_or         = null;

    /**
     * Prefix of domain.
     *
     * @var Hoa_Test_Praspel_DomainDisjunction int
     */
    protected $_i       = 0;



    /**
     * Constructor.
     *
     * @access  public
     * @return  void
     */
    public function __construct ( ) {

        $this->_or = $this;

        return;
    }

    /**
     * Set a domain to the variable.
     *
     * @access  public
     * @param   string  $name    Domain name.
     * @return  Hoa_Test_Praspel_Domain
     */
    public function belongsTo ( $name ) {

        return $this->_domain = new Hoa_Test_Praspel_Domain($this, $name);
    }

    /**
     * Close the current defining domain.
     *
     * @access  public
     * @return  Hoa_Test_Praspel_DomainDisjunction
     */
    public function _ok ( ) {

        if(null === $this->_domain)
            return $this;

        $domain                  = $this->_domain->getDomain();
        $this->_domain           = null;
        $handle                  = $this->_i++ . $domain->getName();
        $this->_domains[$handle] = $domain;

        return $this;
    }

    /**
     * Check if the variable has a specific declared domain.
     *
     * @access  public
     * @param   string  $name    Domain name.
     * @return  bool
     */
    public function isBelongingTo ( $name ) {

        return true === array_key_exists($name, $this->_domains);
    }

    /**
     * Get all domains.
     *
     * @access  public
     * @return  array
     */
    public function getDomains ( ) {

        return $this->_domains;
    }

    /**
     * Accept a visitor.
     *
     * @access  public
     * @param   Hoa_Visitor_Visit  $visitor    Visitor.
     * @param   mixed              &$handle    Handle (reference).
     * @param   mixed              $eldnah     Handle (no reference).
     * @return  mixed
     */
    public function accept ( Hoa_Visitor_Visit $visitor,
                             &$handle = null, $eldnah = null ) {

        return $visitor->visit($this, $handle, $eldnah);
    }
}
