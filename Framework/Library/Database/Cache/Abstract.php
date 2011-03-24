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
 *
 *
 * @category    Framework
 * @package     Hoa_Database
 * @subpackage  Hoa_Database_Cache_Abstract
 *
 */

/**
 * Hoa_Database_Cache_Exception
 */
import('Database.Cache.Exception');

/**
 * Hoa_Database
 */
import('Database.~');

/**
 * Class Hoa_Database_Cache_Abstract.
 *
 * Abstract class for the cache system.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright © 2007-2011 Ivan ENDERLIN.
 * @license     New BSD License
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Database
 * @subpackage  Hoa_Database_Cache_Abstract
 */

abstract class Hoa_Database_Cache_Abstract {

    /**
     * The base name.
     *
     * @var Hoa_Database_Cache_Abstract string
     */
    protected $base    = null;

    /**
     * The table name.
     *
     * @var Hoa_Database_Cache_Abstract string
     */
    protected $table   = null;



    /**
     * Constructor. Set the base and table name.
     *
     * @access  public
     * @param   string  $name    <base>.<table> name.
     * @return  void
     * @throw   Hoa_Database_Cache_Exception
     */
    public function __construct ( $name ) {

        $this->setName($name);
    }

    /**
     * Set the base and table name.
     *
     * @access  protected
     * @param   string     $name    <base>.<table> name.
     * @return  void
     * @throw   Hoa_Database_Cache_Exception
     */
    protected function setName ( $name ) {

        if(false === strpos($name, '.'))
            throw new Hoa_Database_Cache_Exception(
                'The table name must match with <base>.<table> ; given %s.',
                0, $name);

        list($this->base, $this->table) = explode('.', $name);

        return;
    }

    /**
     * Get the base name.
     *
     * @access  public
     * @return  string
     */
    public function getBaseName ( ) {

        return $this->base;
    }

    /**
     * Get the table name.
     *
     * @access  public
     * @return  string
     */
    public function getTableName ( ) {

        return $this->table;
    }


    /**
     * Get the base directory.
     *
     * @access  public
     * @return  string
     */
    public function getBaseDirectory ( ) {

        return Hoa_Database::getInstance()
                   ->getParameter('base.directory', $this->getBaseName());
    }

    /**
     * Get the table filename (not the cache filename, the real table filename).
     *
     * @access  public
     * @return  string
     */
    public function getRealTableFileName ( ) {

        return Hoa_Database::getInstance()
                   ->getParameter('table.filename', $this->getTableName());
    }

    /**
     * Get the table classname.
     *
     * @access  public
     * @return  string
     */
    public function getTableClassname ( ) {

        return Hoa_Database::getInstance()
                   ->getParameter('table.classname', $this->getTableName());
    }

    /**
     * Check if the cache is enabled.
     *
     * @access  public
     * @return  bool
     */
    public function isEnabled ( ) {

        return Hoa_Database::getInstance()
                   ->getParameter('cache.enable', null);
    }

    /**
     * Get the filename that contains the table cache.
     *
     * @access  public
     * @return  string
     */
    public function getTableFilename ( ) {

        return Hoa_Database::getInstance()
                   ->getParameter('cache.filename.table', $this->getTableName());
    }

    /**
     * Get the filename that contains the query cache.
     *
     * @access  public
     * @return  string
     */
    public function getQueryFilename ( ) {

        return Hoa_Database::getInstance()
                   ->getParameter('cache.filename.query', $this->getTableName());
    }

    /**
     * Get the directory that contains caches.
     *
     * @acccess  public
     * @return   string
     */
    public function getDirectory ( ) {

        return Hoa_Database::getInstance()
                   ->getParameter('cache.directory', $this->getBaseName());
    }

    /**
     * Get a cache.
     *
     * @access  public
     * @param   string  $name       Cache name.
     * @param   array   $context    Cache context.
     * @return  mixed
     */
    abstract public function get ( $name, Array $context = array() );

    /**
     * Set a cache.
     *
     * @access  public
     * @param   string  $name             Cache name.
     * @param   string  $subject          Query string or table instance.
     * @param   array   $preparedValue    Prepared values (if $subject is a
     *                                    query string)
     * @param   string  $qType            The query type (if $subject is a query
     *                                    string).
     * @return  void
     */
    abstract public function set ( $name = null, $subject = null,
                                   Array $preparedValue = array(), $qType = null );

    /**
     * Clean a specific cache.
     *
     * @access  public
     * @param   string  $name       Cache name.
     * @param   array   $context    Query context.
     * @return  void
     */
    abstract public function clean ( $name, Array $context = array() );

    /**
     * Clean all caches.
     *
     * @access  public
     * @return  void
     */
    abstract public function cleanAll ( );
}
