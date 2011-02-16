<?php

/**
 * Hoa
 *
 *
 * @license
 *
 * GNU General Public License
 *
 * This file is part of Hoa Open Accessibility.
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
