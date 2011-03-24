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
 * @subpackage  Hoa_Database_Cache_Table
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
 * Class Hoa_Database_Cache_Table.
 *
 * Cache an instance of a table.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2011 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Database
 * @subpackage  Hoa_Database_Cache_Table
 */

class Hoa_Database_Cache_Table extends Hoa_Database_Cache_Abstract {

    /**
     * Get a cache.
     *
     * @access  public
     * @param   string  $name       Cache name (not used here, only for query).
     * @param   array   $context    Cache context (not used here, only for query).
     * @return  mixed
     */
    public function get ( $name, Array $context = array() ) {

        if(false === parent::isEnabled())
            return false;

        $path      = parent::getDirectory() . DS . parent::getTableFilename();
        $content   = null;
        $directory = parent::getBaseDirectory();
        $file      = parent::getRealTableFilename();
        $class     = parent::getTableClassname();

        if(!file_exists($directory . DS . $file))
            return false;

        require_once $directory . DS . $file;

        if(!class_exists($class))
            return false;

        if(file_exists($path)) {

            $content     = file_get_contents($path);

            if(strlen($content) > 0)
                $content = unserialize($content);
        }

        if($content instanceof Hoa_Database_Model_Table)
            return $content;

        return false;
    }

    /**
     * Set a cache.
     *
     * @access  public
     * @param   string  $name             Cache name.
     * @param   string  $table            Table instance.
     * @param   array   $preparedValue    Prepared values (not used, only for
     *                                    query).
     * @param   string  $qType            The query type (not used, only for
     *                                    query).
     * @return  void
     */
    public function set ( $name = null, $table = null,
                          Array $preparedValue = array(), $qType = null ) {

        if(false === parent::isEnabled())
            return;

        if(null === $name)
            return;

        $path = parent::getDirectory() . DS . parent::getTableFilename();

        if(!is_dir(dirname($path))) {

            $mask = umask(0000);
            @mkdir(dirname($path), 0755, true);
            umask($mask);
        }

        file_put_contents($path, serialize($table));

        return;
    }

    /**
     * Clean a specific cache.
     *
     * @access  public
     * @param   string  $name       Cache name.
     * @param   array   $context    Query context.
     * @return  void
     */
    public function clean ( $name, Array $context = array() ) {

        if(false === parent::isEnabled())
            return;

        $path = parent::getDirectory() . DS . parent::getTableFilename();

        if(!file_exists($path))
            return;

        unlink($path);

        return;
    }

    /**
     * Clean all caches.
     *
     * @access  public
     * @return  void
     * @todo    Need to be written ? I don't think …
     */
    public function cleanAll ( ) { }
}
