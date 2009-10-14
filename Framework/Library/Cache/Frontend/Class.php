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
 * Copyright (c) 2007, 2008 Ivan ENDERLIN. All rights reserved.
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
 * @subpackage  Hoa_Cache_Frontend_Class
 *
 */

/**
 * Hoa_Framework
 */
require_once 'Framework.php';

/**
 * Hoa_Cache
 */
import('Cache.~');

/**
 * Class Hoa_Cache_Frontend_Class.
 *
 * Class catching system for frontend cache.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2008 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Cache
 * @subpackage  Hoa_Cache_Frontend_Class
 */

class Hoa_Cache_Frontend_Class extends Hoa_Cache {

    /**
     * Object to cache.
     *
     * @var Hoa_Cache_Frontend_Class mixed
     */
    protected $object = null;

    /**
     * Method arguments.
     *
     * @var Hoa_Cache_Frontend_Class array
     */
    protected $arguments = array();



    /**
     * Redirect constructor call to __call method.
     *
     * @access  public
     * @return  mixed
     */
    public function __construct ( ) {

        $array = func_get_args();
        if($array != array())
            return $this->__call('__construct', $array);
    }

    /**
     * Overload member class with __call.
     * When we call method on this object, all should be redirected to set
     * object.
     *
     * @access  public
     * @param   string  $method       Method called.
     * @param   array   $arguments    Arguments of method.
     * @return  mixed
     * @throw   Hoa_Cache_Exception
     */
    public function __call ( $method, Array $arguments ) {

        if(!method_exists($this->object, $method))
            throw new Hoa_Cache_Exception(
                'Method %s of %s object does not exists.',
                0, array($method, $this->getClass($this->object)));

        $this->arguments = $this->ksort($arguments);

        $id = $this->getClass($this->object) . '::' . $method;
        $this->makeId($id);

        if(false !== $content = $this->load($this->getId(Hoa_Cache::GET_ID_MD5, $id))) {
            echo $content[0];   // output
            return $content[1]; // return
        }

        ob_start();
        ob_implicit_flush(false);

        if(is_string($this->object) && $method == '__construct') {

            $reflection = new ReflectionClass($this->object);

            if(!$reflection->isInstantiable())
                throw new Hoa_Cache_Exception(
                    'Class %s is not instanciable.', 1, $this->object);

            $return = $reflection->newInstanceArgs($arguments);
        }
        else
            $return = call_user_func_array(array($this->object, $method), $arguments);

        $output = ob_get_contents();
        ob_end_clean();

        $this->save($this->getId(Hoa_Cache::GET_ID_MD5), array($output, $return));
        $this->removeId();

        echo $output;
        return $return;
    }

    /**
     * Set object to call.
     *
     * @access  public
     * @param   mixed  $object    Could be an instance or a string for static call.
     * @return  ojbect
     * @throw   Hoa_Cache_Exception
     */
    public function setObject ( $object = null ) {

        if(is_string($object) || is_object($object))
            return $this->object = $object;

        throw new Hoa_Cache_Exception('%s could be a string or a object.',
            2, $object);
    }

    /**
     * Adapted get_class function.
     *
     * @access  protected
     * @param   mixed      $object    Could be a string or an object.
     * @return  string
     */
    protected function getClass ( $object = null ) {

        if($object === null)
            $object = $this;

        if(is_string($object))
            return $object;

        elseif(is_object($object))
            return get_class($object);

        return false;
    }

    /**
     * Own _makeId class method.
     *
     * @access  public
     * @param   string  $id    ID.
     * @return  string
     * @throw   Hoa_Cache_Exception
     */
    public function _makeId ( $id = null ) {

        if(!preg_match('#([a-zA-Z0-9]+)#', $id))
            throw new Hoa_Cache_Exception('%s is not a valid ID.', 3, $id);

        return serialize($this->arguments);
    }
}
