<?php

/**
 * Hoa
 *
 *
 * @license
 *
 * New BSD License
 *
 * Copyright (c) 2007-2011, Ivan Enderlin. All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *     * Redistributions of source code must retain the above copyright
 *       notice, this list of conditions and the following disclaimer.
 *     * Redistributions in binary form must reproduce the above copyright
 *       notice, this list of conditions and the following disclaimer in the
 *       documentation and/or other materials provided with the distribution.
 *     * Neither the name of the Hoa nor the names of its contributors may be
 *       used to endorse or promote products derived from this software without
 *       specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDERS AND CONTRIBUTORS BE
 * LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 */

namespace {

from('Hoa')

/**
 * \Hoa\Test\Praspel\Exception
 */
-> import('Test.Praspel.Exception')

/**
 * \Hoa\Test\Praspel\Domain
 */
-> import('Test.Praspel.Domain')

/**
 * \Hoa\Visitor\Element
 */
-> import('Visitor.Element');

}

namespace Hoa\Test\Praspel {

/**
 * Class \Hoa\Test\Praspel\DomainDisjunction.
 *
 * Represent a domains disjunction.
 *
 * @author     Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright (c) 2007, 2011 Ivan ENDERLIN.
 * @license    http://gnu.org/licenses/gpl.txt GNU GPL
 */

abstract class DomainDisjunction implements \Hoa\Visitor\Element {

    /**
     * Collection of domains.
     *
     * @var \Hoa\Test\Praspel\DomainDisjunction array
     */
    protected $_domains = array();

    /**
     * Current defining domain.
     *
     * @var \Hoa\Test\Praspel\Domain object
     */
    protected $_domain  = null;

    /**
     * Make a disjunction between two variables.
     *
     * @var \Hoa\Test\Praspel\DomainDisjunction object
     */
    public $_or         = null;

    /**
     * Prefix of domain.
     *
     * @var \Hoa\Test\Praspel\DomainDisjunction int
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
     * @return  \Hoa\Test\Praspel\Domain
     */
    public function belongsTo ( $name ) {

        return $this->_domain = new Domain($this, $name);
    }

    /**
     * Close the current defining domain.
     *
     * @access  public
     * @return  \Hoa\Test\Praspel\DomainDisjunction
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
     * @param   \Hoa\Visitor\Visit  $visitor    Visitor.
     * @param   mixed              &$handle    Handle (reference).
     * @param   mixed              $eldnah     Handle (no reference).
     * @return  mixed
     */
    public function accept ( \Hoa\Visitor\Visit $visitor,
                             &$handle = null, $eldnah = null ) {

        return $visitor->visit($this, $handle, $eldnah);
    }
}

}
