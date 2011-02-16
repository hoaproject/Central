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
 */

namespace {

from('Hoa')

/**
 * \Hoa\Test\Praspel\Exception
 */
-> import('Test.Praspel.Exception')

/**
 * \Hoa\Test\Praspel\Contract
 */
-> import('Test.Praspel.Contract');

}

namespace Hoa\Test\Praspel {

/**
 * Class \Hoa\Test\Praspel.
 *
 * Useful to manage different contracts, it's like a multiton of contracts.
 *
 * @author     Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright (c) 2007, 2011 Ivan ENDERLIN.
 * @license    http://gnu.org/licenses/gpl.txt GNU GPL
 */

class Praspel {

    /**
     * Log channel ID.
     *
     * @const string
     */
    const LOG_CHANNEL        = 'Test/Praspel';

    /**
     * Log type: pre-condition.
     *
     * @const int
     */
    const LOG_TYPE_PRE       = 0;

    /**
     * Log type: post-condition.
     *
     * @const int
     */
    const LOG_TYPE_POST      = 1;

    /**
     * Log type: exceptional condition.
     *
     * @const int
     */
    const LOG_TYPE_EXCEPTION = 2;

    /**
     * Log type: invariant.
     *
     * @const int
     */
    const LOG_TYPE_INVARIANT = 3;

    /**
     * Registry of contracts.
     *
     * @var \Hoa\Test\Praspel array
     */
    protected $_register      = array();

    /**
     * Singleton.
     *
     * @var \Hoa\Test\Praspel object
     */
    private static $_instance = null;



    /**
     * Singleton.
     *
     * @access  public
     * @return  void
     */
    private function __construct ( ) {

        return;
    }

    /**
     * Singleton.
     *
     * @access  public
     * @return  \Hoa\Test\Praspel
     */
    public static function getInstance ( ) {

        if(null === self::$_instance)
            self::$_instance = new self();

        return self::$_instance;
    }

    /**
     * Add a contract to the registry.
     *
     * @access  public
     * @param   \Hoa\Test\Praspel\Contract  $contract    Contract to add.
     * @return  void
     */
    public function addContract ( Contract $contract ) {

        $this->_register[$contract->getId()] = $contract;

        return;
    }

    /**
     * Check if the contract exists or not.
     *
     * @access  public
     * @param   string  $contractId    Contract ID.
     * @return  bool
     */
    public function contractExists ( $contractId ) {

        return isset($this->_register[$contractId]);
    }

    /**
     * Get a contract from the registry.
     *
     * @access  public
     * @param   string  $contractId    Contract ID.
     * @return  \Hoa\Test\Praspel\Contract
     * @throw   \Hoa\Test\Praspel\Exception
     */
    public function getContract ( $contractId ) {

        if(false === $this->contractExists($contractId))
            throw new Exception(
                'Contract %s does not exist.', 0, $contractId);

        return $this->_register[$contractId];
    }
}

}
