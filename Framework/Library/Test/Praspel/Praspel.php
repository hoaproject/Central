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
 * @subpackage  Hoa_Test_Praspel
 *
 */

/**
 * Hoa_Core
 */
require_once 'Core.php';

/**
 * Hoa_Test_Praspel_Exception
 */
import('Test.Praspel.Exception');

/**
 * Hoa_Test_Praspel_Contract
 */
import('Test.Praspel.Contract');

/**
 * Class Hoa_Test_Praspel.
 *
 * .
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2010 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Test
 * @subpackage  Hoa_Test_Praspel
 */

class Hoa_Test_Praspel {

    /**
     * Log channel ID.
     *
     * @const string
     */
    const LOG_CHANNEL = 'Test/Praspel';

    /**
     * Registry of contracts.
     *
     * @var Hoa_Test_Praspel array
     */
    protected $_register = array();

    /**
     * Singleton.
     *
     * @var Hoa_Test_Praspel object
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
     * @return  Hoa_Test_Praspel
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
     * @param   Hoa_Test_Praspel_Contract  $contract    Contract to add.
     * @return  void
     */
    public function addContract ( Hoa_Test_Praspel_Contract $contract ) {

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
     * @return  Hoa_Test_Praspel_Contract
     * @throw   Hoa_Test_Praspel_Exception
     */
    public function getContract ( $contractId ) {

        if(false === $this->contractExists($contractId))
            throw new Hoa_Test_Praspel_Exception(
                'Contract %s does not exist.', 0, $contractId);

        return $this->_register[$contractId];
    }
}
