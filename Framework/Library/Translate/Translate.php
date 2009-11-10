<?php

/**
 * Hoa Framework
 *
 *
 * @license
 *
 * GNU General Public License
 *
 * This file is part of HOA Open Accessibility.
 * Copyright (c) 2007, 2009 Ivan ENDERLIN. All rights reserved.
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
 * @package     Hoa_Translate
 *
 */

/**
 * Hoa_Framework
 */
require_once 'Framework.php';

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
 * @copyright   Copyright (c) 2007, 2009 Ivan ENDERLIN.
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
