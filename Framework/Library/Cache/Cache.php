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
 * Copyright (c) 2007, 2009 Ivan ENDERLIN. All rights reserved.
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
 * Hoa_Framework
 */
require_once 'Framework.php';

/**
 * Hoa_Cache_Exception
 */
import('Cache.Exception');

/**
 * Class Hoa_Cache.
 *
 * Cache system for frontend and backend parts.
 * It allows you to capture output data, class and function data and
 * return, and save it into file, and save it with APC, or Memcache.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2009 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Cache
 */

class Hoa_Cache {

    /**
     * Cleaning file constants.
     *
     * @const int
     */
    const CLEANING_ALL     = -1;
    const CLEANING_EXPIRED =  0;

    /**
     * Cleaning APC constants.
     *
     * @const int
     */
    const CLEANING_USER    =  1;

    /**
     * Get ID in original form or in MD5 ?
     *
     * @const string
     */
    const GET_ID           = 'id';
    const GET_ID_MD5       = 'id_md5';

    /**
     * Usefull for first parameter of setOptions methods.
     *
     * @const string
     */
    const FRONTEND         = 'frontend';
    const BACKEND          = 'backend';

    /**
     * Backend object.
     *
     * @var Hoa_Cache object
     */
    protected $_backend         = null;

    /**
     * Frontend options.
     *
     * @var Hoa_Cache array
     */
    protected $_frontendOptions = array(
        'lifetime'              => 3600,
        'serialize_content'     => true,
        'make_id_with'          => array(
            'get'               => true,
            'post'              => true,
            'cookie'            => true,
            'session'           => true,
            'files'             => true
        )
    );

    /**
     * Backend options.
     *
     * @var Hoa_Cache array
     */
    protected $_backendOptions  = array(
        'cache_directory'       => '/tmp/',
        'compress'              => array(
            'active'            => true,
            'level'             => 9
        ),
        'database'              => array(
            'host'              => '127.0.0.1',
            // 'host'           => ':memory:' or '/tmp/sqlite.db' when using SQLite,
            'port'              => 11211,
            'persistent'        => true
        )
    );

    /**
     * Current ID (key : id, value : id_md5).
     *
     * @var Hoa_Cache array
     */
    protected $_id              = array();



    /**
     * Create frontend and backend object,
     * and set backend object for frontend interface.
     *
     * @access  public
     * @param   string  $frontend           Frontend name.
     * @param   string  $backend            Backend name.
     * @param   array   $frontendOptions    Frontend options.
     * @param   array   $backendOptions     Backend options.
     * @return  object
     * @throw   Hoa_Cache_Exception
     */
    public static function factory ( $frontend, $backend,
                                     Array $frontendOptions = array(),
                                     Array $backendOptions  = array()) {

        if(empty($frontend))
            throw new Hoa_Cache_Exception('Frontend could not be empty.', 0);
        if(empty($backend))
            throw new Hoa_Cache_Exception('Backend could not be empty.' , 1);

        $frontend       = ucfirst(strtolower($frontend));
        $backend        = ucfirst(strtolower($backend));

        $frontendClass  = 'Hoa_Cache_Frontend_' . $frontend;
        $backendClass   = 'Hoa_Cache_Backend_'  . $backend;

        import('Cache.Frontend.' . $frontend);
        import('Cache.Backend.'  . $backend);

        $frontendObject = new $frontendClass();
        $backendObject  = new $backendClass();
        $frontendObject->setBackend($backendObject);

        if(empty($frontendOptions) || empty($backendOptions)) {

            $options = require 'hoa://Data/Etc/Configuration/.Cache/Cache.php';

            if(empty($frontendOptions) && isset($options['frontend']))
                $frontendOptions = $options['frontend'];

            if(empty($backendOptions) && isset($options['backend']))
                $backendOptions  = $options['backend'];
        }

        if(empty($frontendOptions))
            $frontendOptions = $frontendObject->getOptions('frontend');
        if(empty($backendOptions))
            $backendOptions  = $frontendObject->getOptions('backend');

        $frontendObject->setOptions('frontend', $frontendOptions);
        $frontendObject->setOptions('backend',  $backendOptions);

        return $frontendObject;
    }

    /**
     * Set/attach backend object.
     *
     * @access  public
     * @param   object  $backend    Backend to set.
     * @return  object
     */
    public function setBackend ( Hoa_Cache_Backend_Abstract $backend ) {

        return $this->_backend = $backend;
    }

    /**
     * Set options of frontend and backend.
     * If options array is not totally filled, array will be filled with default
     * options.
     *
     * @access  public
     * @param   string  $end             Frontend or backend options ?
     * @param   array   $options         Options to apply.
     * @param   array   $recursiveEnd    Used for recursive options, do not be
     *                                   initialized.
     * @return  array
     * @throw   Hoa_Cache_Exception
     */
    public function setOptions ( $end, Array $options = array(),
                                 Array $recursiveEnd  = array() ) {

        if($end != self::FRONTEND && $end != self::BACKEND)
            throw new Hoa_Cache_Exception('%s is not valid options group, must ' .
                'be Hoa_Cache::FRONTEND or Hoa_Cache::BACKEND.', 2, $end);

        if(empty($recursiveEnd)) {

            $array       =& $this->{'_' . $end . 'Options'};
            $recursivity = false;
        }
        else {

            $array       =& $recursiveEnd;
            $recursivity = true;
        }

        if(empty($options))
            return $array;

        foreach($options as $option => $value) {

            if(is_array($value))
                if(!isset($array[$option]))
                    throw new Hoa_Cache_Exception(
                        '%s is not a valid option in %s options group.',
                        3, array($option, $end));
                else
                    $array[$option] = $this->setOptions($end, $value, $array[$option]);
            else
                if(!isset($array[$option]))
                    throw new Hoa_Cache_Exception(
                        '%s is not a valid option in %s options group.',
                        4, array($option, $end));
                else
                    $array[$option] = $value;
        }

        if($recursivity === false)
            $this->_backend->setOptions($end, $array);

        return $array;
    }

    /**
     * Get options of frontend or backend.
     *
     * @access  public
     * @param   string  $end       Frontend or backend.
     * @return  array
     * @throw   Hoa_Cache_Exception
     */
    public function getOptions ( $end ) {

        if($end != 'frontend' && $end != 'backend')
            throw new Hoa_Cache_Exception('%s is not valid options group.',
                5, $end);

        return $this->{'_' . $end . 'Options'};
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

        if(!preg_match('#([a-zA-Z0-9]+)#', $id))
            throw new Hoa_Cache_Exception('%s is not a valid ID.', 6, $id);

        if(method_exists($this, '_makeId'))
            $_id     = $this->_makeId($id);

        else {

            $options = $this->_frontendOptions['make_id_with'];

            $_id     = $options['get']     !== false && isset($_GET)
                           ? serialize($this->ksort($_GET))
                           : '';
            $_id    .= $options['post']    !== false && isset($_POST)
                           ? serialize($this->ksort($_POST))
                           : '';
            $_id    .= $options['cookie']  !== false && isset($_COOKIE)
                           ? serialize($this->ksort($_COOKIE))
                           : '';
            $_id    .= $options['session'] !== false && isset($_SESSION)
                           ? serialize($this->ksort($_SESSION))
                           : '';
            $_id    .= $options['files']   !== false && isset($_FILES)
                           ? serialize($this->ksort($_FILES))
                           : '';
        }

        $this->_id[$id] = md5($id . $_id);

        return $this->_id;
    }

    /**
     * Get current couple ID/ID_MD5.
     *
     * @access  protected
     * @param   string     $element     Returns ID or ID in MD5 ?
     * @param   string     $specific    Get a specific ID.
     * @return  array
     * @throw   Hoa_Cache_Exception
     */
    protected function getId ( $element = self::GET_ID, $specific = null ) {

        end($this->_id);

        if($element != self::GET_ID && $element != self::GET_ID_MD5)
            throw new Hoa_Cache_Exception(
                'Element could not be different of Hoa_Cache::GET_ID and ' .
                'Hoa_Cache::GET_ID_MD5.', 7);

        if($element == 'id') {

            if($specific !== null) {

                if(false === $out = array_search($specific, $this->_id))
                    throw new Hoa_Cache_Exception(
                        'ID encoded in MD5 %s is not found.', 8, $specific);

                return $out;
            }
            else
                return key($this->_id);
        }
        else {

            if($specific !== null)
                if(!isset($this->_id[$specific]))
                    throw new Hoa_Cache_Exception('ID %s is not found.',
                        9, $specific);
                else    
                    return $this->_id[$specific];
            else
                return current($this->_id);
        }
    }

    /**
     * Remove a couple of ID/ID_MD5.
     * By default, the last ID is removed.
     *
     * @access  protected
     * @param   string     $specific    Remove a specific ID.
     * @return  void
     */
    protected function removeId ( $specific = null ) {

        $id = $this->getId(self::GET_ID, $specific);

        unset($this->_id[$id]);
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


    /**
     * Backend methods.
     */


    /**
     * Load a file.
     *
     * @access  protected
     * @param   string     $id_md5         File ID encoded in MD5.
     * @param   bool       $unserialize    Enable unserializing of content.
     * @param   bool       $exists         Test if file exists or not.
     * @return  mixed
     * @throw   Hoa_Cache_Exception
     */
    protected function load ( $id_md5, $unserialize = true, $exists = false ) {

        return $this->_backend->load($id_md5, $unserialize, $exists);
    }

    /**
     * Save cache content into a file.
     *
     * @access  public
     * @param   string  $id        Cache ID.
     * @param   string  $data      Cache content.
     * @return  string
     * @throw   Hoa_Cache_Exception
     */
    protected function save ( $id_md5, $data ) {

        return $this->_backend->save($id_md5, $data);
    }

    /**
     * Clean expired cache files.
     *
     * @access  public
     * @param   string  $lifetime    Specific lifetime.
     * @return  mixed
     * @throw   Hoa_Cache_Exception
     */
    public function clean ( $lifetime = null ) {

        return $this->_backend->clean($lifetime);
    }

    /**
     * Remove a cache file.
     *
     * @access  public
     * @param   string  $id    Cache file ID.
     * @return  mixed
     * @throw   Hoa_Cache_Exception
     */
    public function remove ( $id ) {

        return $this->_backend->remove($id);
    }
}
