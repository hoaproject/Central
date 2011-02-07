<?php

/**
 * Hoa Framework
 *
 *
 * @license
 *
 * GNU General Public License
 *
 * This file is part of HOA Open Accessibility.
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
 * @package     Hoa_Cache
 *
 */

/**
 * Hoa_Cache_Exception
 */
import('Cache.Exception');

/**
 * Class Hoa_Cache.
 *
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2010 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Cache
 */

abstract class Hoa_Cache implements Hoa_Core_Parameterizable {

    /**
     * Clean all entries.
     *
     * @const int
     */
    const CLEAN_ALL     = -1;

    /**
     * Clean expired entries.
     *
     * @const int
     */
    const CLEAN_EXPIRED =  0;

    /**
     * Clean (only for the APC backend).
     *
     * @const int
     */
    const CLEAN_USER    =  1;

    /**
     * The Hoa_Controller parameters.
     *
     * @var Hoa_Core_Parameter object
     */
    private $_parameters  = null;

    /**
     * Current ID (key : id, value : id_md5).
     *
     * @var Hoa_Cache array
     */
    protected static $_id = array();



    /**
     * Constructor.
     *
     * @access  public
     * @param   array   $parameters    Parameters.
     * @return  void
     */
    public function __construct ( Array $parameters = array() ) {

        $this->_parameters = new Hoa_Core_Parameter(
            $this,
            array(
                'id' => null
            ),
            array(
                'lifetime'                     => 3600,
                'serialize_content'            => true,
                'make_id_with.cookie'          => true,
                'make_id_with.files'           => true,
                'make_id_with.get'             => true,
                'make_id_with.post'            => true,
                'make_id_with.server'          => false,
                'make_id_with.session'         => true,

                'apc'                          => '',

                'eaccelerator'                 => '',

                'file.cache_directory'         => 'hoa://Data/Variable/Cache/(:id:).ca',
                'file.compress.active'         => true,
                'file.compress.level'          => true,

                'memcache.compress.active'     => true,
                'memcache.database.host'       => '127.0.0.1',
                'memcache.database.port'       => 11211,
                'memcache.database.persistent' => true,

                'sqlite.cache_directory'       => 'hoa://Data/Variable/Cache/Cache.db',
                /**
                 * Example with a SQLite database loaded in memory:
                 *
                 * 'sqlite.cache_directory'    => ':memory:',
                 */
                'sqlite.database.host'         => '127.0.0.1',

                'xcache'                       => '',

                'zendplatform'                 => ''
            ),
            'Hoa_Cache'
        );

        $this->setParameters($parameters);

        return;
    }

    /**
     * Set many parameters to a class.
     *
     * @access  public
     * @param   array   $in    Parameters to set.
     * @return  void
     * @throw   Hoa_Core_Exception
     */
    public function setParameters ( Array $in ) {

        return $this->_parameters->setParameters($this, $in);
    }

    /**
     * Get many parameters from a class.
     *
     * @access  public
     * @return  array
     * @throw   Hoa_Core_Exception
     */
    public function getParameters ( ) {

        return $this->_parameters->getParameters($this);
    }

    /**
     * Set a parameter to a class.
     *
     * @access  public
     * @param   string  $key      Key.
     * @param   mixed   $value    Value.
     * @return  mixed
     * @throw   Hoa_Core_Exception
     */
    public function setParameter ( $key, $value ) {

        return $this->_parameters->setParameter($this, $key, $value);
    }

    /**
     * Get a parameter from a class.
     *
     * @access  public
     * @param   string  $key    Key.
     * @return  mixed
     * @throw   Hoa_Core_Exception
     */
    public function getParameter ( $key ) {

        return $this->_parameters->getParameter($this, $key);
    }

    /**
     * Get a formatted parameter from a class (i.e. zFormat with keywords and
     * other parameters).
     *
     * @access  public
     * @param   string  $key    Key.
     * @return  mixed
     * @throw   Hoa_Core_Exception
     */
    public function getFormattedParameter ( $key ) {

        return $this->_parameters->getFormattedParameter($this, $key);
    }

    /**
     * Make an ID according to frontend options.
     * We privilage _makeId to built suffixe of ID.
     * As an idenfier shoud be unique, we add environments variables values. In
     * this way, the identifier represents the current state of application.
     *
     * @access  protected
     * @param   string    $id    Identifier.
     * @return  string
     * @throw   Hoa_Cache_Exception
     */
    protected function makeId ( $id = null ) {

        $_id = $id;

        if(   true === $this->getParameter('make_id_with.cookie')
           && isset($_COOKIE))
            $_id .= serialize($this->ksort($_COOKIE));

        if(   true === $this->getParameter('make_id_with.files')
           && isset($_FILES))
            $_id .= serialize($this->ksort($_FILES));

        if(   true === $this->getParameter('make_id_with.get')
           && isset($_GET))
            $_id .= serialize($this->ksort($_GET));

        if(   true === $this->getParameter('make_id_with.post')
           && isset($_POST))
            $_id .= serialize($this->ksort($_POST));

        if(   true === $this->getParameter('make_id_with.server')
           && isset($_SERVER))
            $_id .= serialize($this->ksort($_SERVER));

        if(   true === $this->getParameter('make_id_with.session')
           && isset($_SESSION))
            $_id .= serialize($this->ksort($_SESSION));

        return self::$_id[$id] = md5($id . $_id);
    }

    /**
     * Set the current ID.
     *
     * @access  protected
     * @param   string  $id    ID.
     * @return  string
     */
    protected function setId ( $id ) {

        $old = $this->_parameters->getKeyword($this, 'id');
        $this->_parameters->setKeyword($this, 'id', $id);

        return $old;
    }

    /**
     * Get last ID.
     *
     * @access  protected
     * @return  string
     * @throw   Hoa_Cache_Exception
     */
    protected function getId ( ) {

        end(self::$_id);

        return key(self::$_id);
    }

    /**
     * Get last ID in MD5 format.
     *
     * @access  protected
     * @return  string
     * @throw   Hoa_Cache_Exception
     */
    protected function getIdMd5 ( ) {

        end(self::$_id);

        return current(self::$_id);
    }

    /**
     * Remove a couple of ID/ID_MD5.
     * By default, the last ID is removed.
     *
     * @access  protected
     * @return  void
     */
    protected function removeId  () {

        unset(self::$_id[$this->getId()]);

        return;
    }

    /**
     * Sort array of arrays etc., according to keys, recursively.
     *
     * @access  public
     * @param   array   $array    Array to sort.
     * @return  array
     */
    public function ksort ( Array &$array ) {

        ksort($array);
        foreach($array as $key => $value)
            if(is_array($value))
                $array[$key] = $this->ksort($value);

        return $array;
    }
}
