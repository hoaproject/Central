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
 */

namespace {

from('Hoa')

/**
 * \Hoa\Cache\Exception
 */
-> import('Cache.Exception')

/**
 * \Hoa\Cache\Backend
 */
-> import('Cache.Backend.~');

}

namespace Hoa\Cache\Backend {

/**
 * Class \Hoa\Cache\Backend\Xcache.
 *
 * Xcache manager.
 * XCache is an extension, take care that XCache is loaded.
 *
 * @author     Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright (c) 2007, 2010 Ivan ENDERLIN.
 * @license    http://gnu.org/licenses/gpl.txt GNU GPL
 */

class Xcache extends Backend {

    /**
     * Check if XCache is loaded, else an exception is thrown.
     *
     * @access  public
     * @param   array  $parameters    Parameters.
     * @return  void
     * @throw   \Hoa\Cache\Exception
     */
    public function __construct ( Array $parameters = array() ) {

        if(!extension_loaded('xcache'))
            throw new \Hoa\Cache\Exception(
                'XCache is not loaded on server.', 0);

        parent::__construct($parameters);

        return;
    }

    /**
     * Save cache content in XCache store.
     *
     * @access  public
     * @param   string  $data    Data to store.
     * @return  void
     * @throw   \Hoa\Cache\Exception
     */
    public function store ( $data ) {

        $this->clean();

        if(true === $this->getParameter('serialize_content'))
            $data = serialize($data);

        xcache_set(
            $this->getIdMd5(),
            $data,
            $this->getParameter('lifetime')
        );

        return;
    }

    /**
     * Load data from XCache cache.
     *
     * @access  public
     * @return  mixed
     */
    public function load ( ) {

        $this->clean();

        $content = xcache_get($this->getIdMd5());

        if(true === $this->getParameter('serialize_content'))
            $content = unserialize($content);

        return $content;
    }

    /**
     * Clean expired cache.
     * Note : \Hoa\Cache::CLEAN_USER is not supported, it's reserved for APC
     * backend.
     *
     * @access  public
     * @param   int  $lifetime    Lifetime of caches.
     * @return  void
     * @throw   \Hoa\Cache\Exception
     */
    public function clean ( $lifetime = \Hoa\Cache::CLEAN_EXPIRED ) {

        switch($lifetime) {

            case \Hoa\Cache::CLEAN_ALL:
                for($i = 0, $n = xcache_count(XC_TYPE_VAR); $i < $n; $i++)
                    if(false !== xcache_clear_cache(XC_TYPE_VAR, $i))
                        throw new \Hoa\Cache\Exception(
                            'Clear all cache of XCache failed '. 
                            '(maybe for the cache number %s).',
                            1, $i);
              break;

            case \Hoa\Cache::CLEAN_EXPIRED:
                // Manage by XCache.
              break;

            case \Hoa\Cache::CLEAN_USER:
                throw new \Hoa\Cache\Exception(
                    '\Hoa\Cache::CLEAN_USER constant is not supported by ' .
                    'XCache backend.', 2);

            default:
                return;
        }

        return;
    }

    /**
     * Remove a cache data.
     *
     * @access  public
     * @return  void
     * @throw   \Hoa\Cache\Exception
     */
    public function remove ( ) {

        xcache_clear_cache(XC_TYPE_VAR, $this->getId());

        return;
    }
}

}
