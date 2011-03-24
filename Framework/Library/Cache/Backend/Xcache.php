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
 * Class \Hoa\Cache\Backend\Xcache.
 *
 * Xcache manager.
 * XCache is an extension, take care that XCache is loaded.
 *
 * @author     Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright © 2007-2011 Ivan ENDERLIN.
 * @license    New BSD License
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
