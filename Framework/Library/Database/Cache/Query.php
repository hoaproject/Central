<?php

/**
 * Hoa Framework
 *
 *
 * @license
 *
 * GNU General Public License
 *
 * This file is part of Hoa Open Accessibility.
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
 * @copyright   Copyright (c) 2007, 2010 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
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
