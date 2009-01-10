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
 * @package     Hoa_Cache
 * @subpackage  Hoa_Cache_Backend_Abstract
 *
 */

/**
 * Hoa_Framework
 */
require_once 'Framework.php';

/**
 * Hoa_Cache
 */
import('Cache.~');

/**
 * Hoa_Cache_Exception
 */
import('Cache.Exception');

/**
 * Class Hoa_Cache_Backend_Abstract.
 *
 * Abstract object for backend cache.
 * Each backend class must extend this abstract class.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2008 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.2
 * @package     Hoa_Cache
 * @subpackage  Hoa_Cache_Backend_Abstract
 */

abstract class Hoa_Cache_Backend_Abstract {

    /**
     * Frontend options.
     *
     * @var Hoa_Cache array
     */
    protected $_frontendOptions = array();

    /**
     * Backend options.
     *
     * @var Hoa_Cache array
     */
    protected $_backendOptions  = array();



    /**
     * Set options simply.
     * Set options of frontend or backend.
     *
     * @access  public
     * @param   string  $end        Frontend or backend.
     * @param   array   $options    Options.
     * @return  array
     * @throw   Hoa_Cache_Exception
     */
    public function setOptions ( $end, Array $options ) {

        if($end != Hoa_Cache::FRONTEND && $end != Hoa_Cache::BACKEND)
            throw new Hoa_Cache_Exception(
                'End must be Hoa_Cache::FRONTEND or Hoa_Cache::BACKEND.', 0);

        return $this->{'_' . $end . 'Options'} = $options;
    }

    /**
     * Make load method abstract.
     * If a cache needs to be loaded, this method will be called.
     *
     * @access  protected
     * @param   string     $id_md5         ID encoded in MD5.
     * @param   bool       $unserialize    Enable unserializing of content.
     * @param   bool       $exists         Test if cache exists or not.
     * @return  mixed
     * @throw   Hoa_Cache_Exception
     */
    abstract protected function load ( $id_md5, $unserialize = true,
                                       $exists = false );

    /**
     * Make save method abstract.
     * If a cache needs to be saved, this method will be called.
     *
     * @access  protected
     * @param   string     $id_md5    Cache ID encoded in MD5.
     * @param   string     $data      Cache content.
     * @return  string
     * @throw   Hoa_Cache_Exception
     */
    abstract protected function save ( $id_md5, $data );

    /**
     * Make clean method abstract.
     * If caches need to be cleaned, this method will be called.
     *
     * @access  protected
     * @param   int        $lifetime    Lifetime of caches.
     * @return  bool
     * @throw   Hoa_Cache_Exception
     */
    abstract protected function clean ( $lifetime = null );

    /**
     * Make remove methode abstract.
     * If a cache needs to be removed (deleted), this method will be called.
     *
     * @access  protected
     * @param   string     $id_md5    ID of cache to remove (encoded in MD5).
     * @return  bool
     * @throw   Hoa_Cache_Exception
     */
    abstract protected function remove ( $id_md5 );
}
