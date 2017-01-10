<?php

/**
 * Hoa
 *
 *
 * @license
 *
 * New BSD License
 *
 * Copyright © 2007-2017, Hoa community. All rights reserved.
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

namespace Hoa\Cache\Backend;

use Hoa\Cache;

/**
 * Class \Hoa\Cache\Backend\Apc.
 *
 * Alternative PHP Cache backend manager.
 * APC is a PECL extension, so it's not installed in PHP. Take care that APC
 * module is loaded.
 *
 * @copyright  Copyright © 2007-2017 Hoa community
 * @license    New BSD License
 */
class Apc extends Backend
{
    /**
     * Check if APC is loaded, else an exception is thrown.
     *
     * @param   array   $parameters    Parameters.
     * @throws  \Hoa\Cache\Exception
     */
    public function __construct(array $parameters = [])
    {
        if (!extension_loaded('apc')) {
            throw new Cache\Exception(
                'APC (PECL extension) is not loaded on server.',
                0
            );
        }

        parent::__construct($parameters);

        return;
    }

    /**
     * Save cache content in APC store.
     *
     * @param   mixed  $data    Data to store.
     * @return  mixed
     */
    public function store($data)
    {
        $this->clean();

        if (false !== $this->_parameters->getParameter('serialize_content')) {
            $data  = serialize($data);
        }

        return apc_store(
            $this->getIdMd5(),
            $data,
            $this->_parameters->getParameter('lifetime')
        );
    }

    /**
     * Load data from APC cache.
     *
     * @return  void
     */
    public function load()
    {
        $this->clean();

        $content = apc_fetch($this->getIdMd5());

        if (true === $this->_parameters->getParameter('serialize_content')) {
            $content = unserialize($content);
        }

        return $content;
    }

    /**
     * Clean expired cache.
     * Note : \Hoa\Cache::CLEAN_EXPIRED is not supported with APC.
     *
     * @param   int  $lifetime    Lifetime of caches.
     * @return  void
     * @throws  \Hoa\Cache\Exception
     */
    public function clean($lifetime = Cache::CLEAN_USER)
    {
        switch ($lifetime) {
            case Cache::CLEAN_ALL:
                return apc_clear_cache();

            case Cache::CLEAN_EXPIRED:
                throw new Cache\Exception(
                    '\Hoa\Cache::CLEAN_EXPIRED constant is not supported by ' .
                    'APC cache backend.',
                    1
                );

            case Cache::CLEAN_USER:
            default:
                return apc_clear_cache('user');
        }

        return;
    }

    /**
     * Remove a cache data.
     *
     * @return  void
     */
    public function remove()
    {
        apc_delete($this->getIdMd5());

        return;
    }
}
