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
 * @license    New BSD License
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
