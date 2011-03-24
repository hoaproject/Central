<?php

/**
 * Hoa
 *
 *
 * @license
 *
 * New BSD License
 *
 * Copyright (c) 2007-2011, Ivan Enderlin. All rights reserved.
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
 *
 *
 * @category    Framework
 * @package     Hoa_Database
 * @subpackage  Hoa_Database_Cache_Query
 *
 */

/**
 * Hoa_Database_Cache_Exception
 */
import('Database.Cache.Exception');

/**
 * Hoa_Database_Cache_Abstract
 */
import('Database.Cache.Abstract');

/**
 * Class Hoa_Database_Cache_Query.
 *
 * Cache a (string) query.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2011 Ivan ENDERLIN.
 * @license     New BSD License
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Database
 * @subpackage  Hoa_Database_Cache_Query
 */

class Hoa_Database_Cache_Query extends Hoa_Database_Cache_Abstract {

    /**
     * Temporize contexts for making cache ID.
     *
     * @var Hoa_Database_Cache_Query array
     */
    protected $context = array();



    /**
     * Get a cache.
     *
     * @access  public
     * @param   string  $name       Cache name.
     * @param   array   $context    Cache context.
     * @return  mixed
     */
    public function get ( $name, Array $context = array() ) {

        if(false === parent::isEnabled())
            return false;

        $this->context[$name] = $context;

        $id      = md5($name . serialize($context));
        $path    = parent::getDirectory() . DS . parent::getQueryFilename();
        $content = array();

        if(file_exists($path)) {

            $content = file_get_contents($path);

            if(strlen($content) > 0)
                $content = unserialize($content);
        }

        if(isset($content[$id])) {

            if(   isset($content[$id]['q'])
               && isset($content[$id]['p'])
               && isset($content[$id]['t']))
                return $content[$id];
        }

        return false;
    }

    /**
     * Set a cache.
     *
     * @access  public
     * @param   string  $name             Cache name.
     * @param   string  $query            Query string.
     * @param   array   $preparedValue    Prepared values.
     * @param   string  $qType            The query type.
     * @return  void
     */
    public function set ( $name = null,                   $query = null,
                          Array $preparedValue = array(), $qType = null ) {

        if(false === parent::isEnabled())
            return;

        if(null === $name)
            return;

        $context = isset($this->context[$name]) ? $this->context[$name] : array();
        $id      = md5($name . serialize($context));
        $path    = parent::getDirectory() . DS . parent::getQueryFilename();
        $content = array();

        if(!is_dir(dirname($path))) {

            $mask = umask(0000);
            @mkdir(dirname($path), 0755, true);
            umask($mask);
        }

        if(file_exists($path)) {

            $content = file_get_contents($path);

            if(strlen($content) > 0)
                $content = unserialize($content);
        }

        $content[$id] = array(
            'q' => $query,
            'p' => $preparedValue,
            't' => $qType
        );

        file_put_contents($path, serialize($content));

        return;
    }

    /**
     * Clean a specific cache.
     *
     * @access  public
     * @param   string  $name       Cache name (not used here, only for query).
     * @param   array   $context    Query context (not used here, only for query).
     * @return  void
     */
    public function clean ( $name, Array $context = array() ) {

        if(false === parent::isEnabled())
            return;

        $id      = md5($name . serialize($context));
        $path    = parent::getDirectory() . DS . parent::getQueryFilename();
        $content = array();

        if(!file_exists($path))
            return;

        $content = file_get_contents($path);

        if(strlen($content) > 0)
            $content = unserialize($content);

        unset($content[$id]);

        file_put_contents($path, serialize($content));

        return;
    }

    /**
     * Clean all caches.
     *
     * @access  public
     * @return  void
     */
    public function cleanAll ( ) {

        if(false === parent::isEnabled())
            return false;

        $path = parent::getDirectory() . DS . parent::getQueryFilename();
        
        @unlink($path);
    }
}
