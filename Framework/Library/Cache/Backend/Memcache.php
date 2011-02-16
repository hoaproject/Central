<?php

/**
 * Hoa
 *
 *
 * @license
 *
 * GNU General Public License
 *
 * This file is part of HOA Open Accessibility.
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
 * Class \Hoa\Cache\Backend\Memcache.
 *
 * Memcache backend manager.
 * Memcache is PECL extension, so it's not installed in PHP. Take care
 * that Memcache module is loaded.
 *
 * @author     Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright (c) 2007, 2011 Ivan ENDERLIN.
 * @license    http://gnu.org/licenses/gpl.txt GNU GPL
 */

class Memcache extends Backend {

    /**
     * Memcache object.
     *
     * @var Memcache object
     */
    protected $_memcache = null;



    /**
     * Check if Memcache is loaded and prepare variables.
     *
     * @access  public
     * @param   array  $parameters    Parameters.
     * @return  void
     * @throw   \Hoa\Cache\Exception
     */
    public function __construct ( Array $parameters = array() ) {

        if(!extension_loaded('memcache'))
            throw new \Hoa\Cache\Exception(
                'Memcache module extension is not loaded on server.', 0);

        parent::__construct($parameters);

        return;
    }

    /**
     * Save cache content into a Memcache items.
     *
     * @access  public
     * @param   mixed  $data    Data to store.
     * @return  void
     */
    public function store ( $data ) {

        $this->setMemcache();
        $this->clean();

        if(true === $this->getParameter('serialize_content'))
            $data = serialize($data);

        $flag     = $this->getParameter('memcache.compress.active')
                        ? MEMCACHE_COMPRESSED
                        : 0;
        $lifetime = $this->getParameter('lifetime');

        if($lifetime > 2592000) // 30 days.
            $lifetime = 2592000;

        return $this->_memcache->set(
            $this->getIdMd5(),
            $data,
            $flag,
            $lifetime
        );
    }

    /**
     * Load a Memcache items.
     *
     * @access  public
     * @return  mixed
     */
    public function load ( ) {

        $this->setMemcache();
        $this->clean();

        $content = $this->_memcache->get($this->getIdMd5());

        if(true === $this->getParameter('serialize_content'))
            $content = unserialize($content);

        return $content;
    }

    /**
     * Flush all existing items on Memcache server.
     * Note : only \Hoa\Cache::CLEAN_ALL is supported by Memcache.
     *
     * @access  public
     * @param   int  $lifetime    Specific lifetime.
     * @return  void
     * @throw   \Hoa\Cache\Exception
     */
    public function clean ( $lifetime = \Hoa\Cache::CLEAN_ALL ) {

        $this->setMemcache();

        if($lifetime != \Hoa\Cache::CLEAN_ALL)
            throw new \Hoa\Cache\Exception(
                'Only \Hoa\Cache::CLEAN_ALL constant is supported by ' .
                'Memcache backend.', 1);

        if(false === @$this->_memcache->flush())
            throw new \Hoa\Cache\Exception(
                'Flush all existing items on Memcache server %s failed.',
                2, $this->_backendOptions['database']['host']);

        return;
    }

    /**
     * Remove a memcache items.
     *
     * @access  public
     * @return  void
     */
    public function remove ( ) {

        $this->setMemcache();
        $this->_memcache->delete($this->getIdMd5());

        return;
    }

    /**
     * Set Memcache object.
     *
     * @access  protected
     * @return  bool
     */
    protected function setMemcache ( ) {

        if(is_object($this->_memcache))
            return true;

        $this->_memcache = new Memcache();
        $this->_memcache->addServer(
            $this->getParameter('memcache.database.host'),
            $this->getParameter('memcache.database.port'),
            $this->getParameter('memcache.database.persistent')
        );

        return true;
    }

    /**
     * Close connection to Memcache.
     *
     * @access  public
     * @return  void
     */
    public function __destruct ( ) {

        if($this->_memcache !== null) {

            $this->_memcache->close();
            unset($this->_memcache);
        }

        return;
    }
}

}
