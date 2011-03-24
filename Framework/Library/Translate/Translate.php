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
 *
 *
 * @category    Framework
 * @package     Hoa_Translate
 *
 */

/**
 * Hoa_Translate_Exception
 */
import('Translate.Exception');

/**
 * Hoa_Registry
 */
import('Registry.~');

/**
 * Hoa_Factory
 */
import('Factory.~');

/**
 * Class Hoa_Translate.
 *
 * Translate text with different methods.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2011 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Translate
 */

class Hoa_Translate {

    /**
     * _adapter.
     *
     * @var Hoa_Translate_Adapter_Abstract object
     */
    protected $_adapter = null;

    /**
     * Domain.
     *
     * @var Hoa_Translate_Adapter_Abstract string
     */
    protected $_domain = null;

    /**
     * Registry identifier.
     *
     * @var Hoa_Translate_Adapter_Abstract mixed
     */
    protected $_registry = null;



    /**
     * __construct
     * Set adapter.
     *
     * @access  public
     * @param   adapter   string    Adapter.
     * @param   path      string    Path to locale directory.
     * @param   locale    string    Locale value (xx_XX).
     * @param   domain    string    Domain.
     * @param   registry  mixed     Registry identifier.
     * @return  void
     * @throw   Hoa_Translate_Exception
     */
    public function __construct ( $adapter  = null, $path   = null,
                                  $locale   = null, $domain = null,
                                  $registry = null) {

        $domain   = $domain   === null ? $this->_domain   : $domain;
        $registry = $registry === null ? $this->_registry : $registry;

        if($registry !== null && is_string($registry)) {

            $this->_adapter = Hoa_Registry::get($registry);

            if($domain !== null)
                $this->setDomain($domain);

            return $this->_adapter;
        }

        try {
            $parameters     = array($path, $locale, $domain);
            $this->_adapter = Hoa_Factory::get('Translate.Adapter', $adapter, $parameters);
        }
        catch ( Hoa_Factory_Exception $e ) {
            throw new Hoa_Translate_Exception($e->getMessage(), $e->getCode());
        }
    }

    /**
     * getAdapter
     * Get adapter.
     *
     * @access  public
     * @return  mixed
     */
    public function getAdapter ( ) {

        return $this->_adapter;
    }

    /**
     * setDomain
     * Call setDomain of adapter object.
     *
     * @access  public
     * @param   domain  string    Domain.
     * @return  mixed
     * @throw   Hoa_Translate_Exception
     */
    public function setDomain ( $domain = '' ) {

        return $this->_adapter->setDomain($domain);
    }

    /**
     * _
     * Translate a message.
     *
     * @access  public
     * @param   message  string    Message.
     * @param   -        -         For printf.
     * @return  string
     */
    public function _ ( $message = '' ) {

        $parameters = func_get_args();
        return call_user_func_array(array($this->_adapter, 'get'), $parameters);
    }

    /**
     * _
     * Translate a message in plurial mode.
     *
     * @access  public
     * @param   message         string    Message.
     * @param   message_plural  string    Message in plurial.
     * @param   n               int       n.
     * @param   -               -         For printf.
     * @return  string
     */
    public function _n ( $message = '', $message_plural = '', $n = 2 ) {

        $parameters = func_get_args();
        return call_user_func_array(array($this->_adapter, 'getn'), $parameters);
    }
}
