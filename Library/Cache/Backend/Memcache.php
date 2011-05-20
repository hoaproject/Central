<?php

/**
 * Hoa
 *
 *
 * @license
 *
 * New BSD License
 *
 * Copyright © 2007-2011, Ivan Enderlin. All rights reserved.
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
 * @author     Ivan Enderlin <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright © 2007-2011 Ivan Enderlin.
 * @license    New BSD License
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

        if(true === $this->_parameters->getParameter('serialize_content'))
            $data = serialize($data);

        $flag     = $this->_parameters->getParameter('memcache.compress.active')
                        ? MEMCACHE_COMPRESSED
                        : 0;
        $lifetime = $this->_parameters->getParameter('lifetime');

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

        if(true === $this->_parameters->getParameter('serialize_content'))
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
                2, $this->_parameters->getParameter('memcache.database.host'));

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

        $this->_memcache = new \Memcache();
        $this->_memcache->addServer(
            $this->_parameters->getParameter('memcache.database.host'),
            $this->_parameters->getParameter('memcache.database.port'),
            $this->_parameters->getParameter('memcache.database.persistent')
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
