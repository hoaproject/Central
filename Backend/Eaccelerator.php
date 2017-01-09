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
 * Class \Hoa\Cache\Backend\Eaccelerator.
 *
 * Eaccelerator backend manager.
 * EAccelerator is an extension, take care that EAccelerator is loaded.
 *
 * @copyright  Copyright © 2007-2017 Hoa community
 * @license    New BSD License
 */
class Eaccelerator extends Backend
{
    /**
     * Check if EAccelerator is loaded, else an exception is thrown.
     *
     * @param   array   $parameters    Parameters.
     * @throws  \Hoa\Cache\Exception
     */
    public function __construct(array $parameters = [])
    {
        if (!extension_loaded('eaccelerator')) {
            throw new Cache\Exception(
                'EAccelerator is not loaded on server.',
                0
            );
        }

        parent::__construct($parameters);

        return;
    }

    /**
     * Save cache content in EAccelerator store.
     * All data is obligatory serialized.
     *
     * @param   mixed  $data    Data to store.
     * @return  void
     */
    public function store($data)
    {
        $this->clean();

        return eaccelerator_put(
            $this->getIdMd5(),
            serialize($data),
            $this->_parameters->getParameter('lifetime')
        );
    }

    /**
     * Load data from EAccelerator cache.
     * Data is obligatory unseralized, please see the self::save() method.
     *
     * @return  void
     */
    public function load()
    {
        $this->clean();

        $content = eaccelerator_get($this->getIdMd5());

        return unserialize($content);
    }

    /**
     * Clean expired cache.
     * Note : \Hoa\Cache::CLEAN_USER is not supported, it's reserved for APC
     * backend.
     *
     * @param   int  $lifetime    Lifetime of caches.
     * @return  void
     * @throws  \Hoa\Cache\Exception
     */
    public function clean($lifetime = Cache::CLEAN_EXPIRED)
    {
        switch ($lifetime) {
            case Cache::CLEAN_ALL:
                $infos = eaccelerator_list_keys();

                // EAccelerator bug (http://eaccelerator.net/ticket/287).
                foreach ($infos as $foo => $info) {
                    $key =
                        0 === strpos($info['name'], ':')
                            ? substr($info['name'], 1)
                            : $info['name'];

                    if (false === eaccelerator_rm($key)) {
                        throw new \Hoa\Cache\Exception(
                            'Remove all existing cache file failed ' .
                            '(maybe for the %s cache).',
                            1,
                            $key
                        );
                    }
                }

                break;

            case Cache::CLEAN_EXPIRED:
                // Manage by EAccelerator.
                break;

            case Cache::CLEAN_USER:
                throw new \Hoa\Cache\Exception(
                    '\Hoa\Cache::CLEAN_USER constant is not supported by ' .
                    'EAccelerator backend.',
                    2
                );
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
        eaccelerator_rm($this->getIdMd5());

        return;
    }
}
