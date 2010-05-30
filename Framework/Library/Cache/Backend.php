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
 * @subpackage  Hoa_Cache_Backend
 *
 */

/**
 * Hoa_Framework
 */
require_once 'Framework.php';

/**
 * Hoa_Cache_Exception
 */
import('Cache.Exception');

/**
 * Hoa_Cache
 */
import('Cache.~');

/**
 * Class Hoa_Cache_Backend.
 *
 *
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2010 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Cache
 * @subpackage  Hoa_Cache_Backend
 *
 */

abstract class Hoa_Cache_Backend extends Hoa_Cache {

    /**
     * Save data.
     *
     * @access  public
     * @param   mixed  $data    Data to store.
     * @return  void
     */
    abstract public function store ( $data );

    /**
     * Load data.
     *
     * @access  public
     * @return  mixed
     */
    abstract public function load ( );

    /**
     * Clean data.
     *
     * @access  public
     * @param   int  $lifetime    Lifetime of caches.
     * @return  void
     */
    abstract public function clean ( $lifetime = Hoa_Cache::CLEAN_EXPIRED );

    /**
     * Remove data.
     *
     * @access  public
     * @return  void
     */
    abstract public function remove ( );
}
