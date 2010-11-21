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
 * @package     Hoa_Cache
 * @subpackage  Hoa_Cache_Frontend
 *
 */

/**
 * Hoa_Cache_Exception
 */
import('Cache.Exception');

/**
 * Hoa_Cache
 */
import('Cache.~');

/**
 * Class Hoa_Cache_Frontend.
 *
 *
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2010 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Cache
 * @subpackage  Hoa_Cache_Frontend
 *
 */

abstract class Hoa_Cache_Frontend extends Hoa_Cache {

    /**
     * Backend object.
     *
     * @var Hoa_Cache_Backend object
     */
    protected $_backend = null;



    /**
     * Constructor.
     *
     * @access  public
     * @param   Hoa_Cache_Backend  $backend    Backend instance.
     * @return  void
     */
    public function __construct ( Hoa_Cache_Backend $backend ) {

        parent::__construct();

        $this->_backend = $backend;

        return;
    }

    /**
     * Clean cache.
     *
     * @access  public
     * @param   int  $lifetime    Lifetime of caches.
     * @return  void
     */
    public function cleanCache ( $lifetime = Hoa_Cache::CLEAN_EXPIRED ) {

        if(null === $this->_backend)
            return;

        return $this->_backend->clean($lifetime);
    }

    /**
     * Get the backend.
     *
     * @access  public
     * @return  Hoa_Cache_Backend
     */
    public function getBackend ( ) {

        return $this->_backend;
    }
}
