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
 * @subpackage  Hoa_Cache_Frontend_Class
 *
 */

/**
 * Hoa_Framework
 */
require_once 'Framework.php';

/**
 * Hoa_Cache_Frontend
 */
import('Cache.Frontend');

/**
 * Class Hoa_Cache_Frontend_Class.
 *
 * Class catching system for frontend cache.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2010 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Cache
 * @subpackage  Hoa_Cache_Frontend_Class
 */

class Hoa_Cache_Frontend_Class extends Hoa_Cache_Frontend {

    /**
     * Object to cache.
     *
     * @var Hoa_Cache_Frontend_Class mixed
     */
    protected $_object    = null;

    /**
     * Method arguments.
     *
     * @var Hoa_Cache_Frontend_Class array
     */
    protected $_arguments = array();



    /**
     * Redirect constructor call to __call method if necessary. Else, it's like
     * the parent constructor.
     *
     * @access  public
     * @return  mixed
     */
    public function __construct ( ) {

        $arguments = func_get_args();

        if(null === $this->_object)
            if(isset($arguments[1]))
                return parent::__construct($arguments[0], $arguments[1]);
            else
                return parent::__construct($arguments[0]);

        return $this->__call('__construct', $arguments);
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

        $gc = is_string($this->_object)
                  ? $this->_object
                  : get_class($this->_object);

        if(!method_exists($this->_object, $method))
            throw new Hoa_Cache_Exception(
                'Method %s of %s object does not exists.',
                0, array($method, $gc));

        $this->_arguments = $this->ksort($arguments);
        $idExtra          = serialize($this->_arguments);
        $this->makeId($gc . '::' . $method . '/' .  $idExtra);
        $content          = $this->_backend->load();

        if(false !== $content) {

            echo $content[0];   // output

            return $content[1]; // return
        }

        ob_start();
        ob_implicit_flush(false);

        if(is_string($this->_object) && $method == '__construct') {

            $reflection = new ReflectionClass($this->_object);

            if(!$reflection->isInstantiable())
                throw new Hoa_Cache_Exception(
                    'Class %s is not instanciable.', 1, $this->_object);

            $this->_object = $reflection->newInstanceArgs($arguments);
            $return        = $this->_object;
        }
        else
            $return = call_user_func_array(
                array($this->_object, $method),
                $arguments
            );

        $output = ob_get_contents();
        ob_end_clean();

        $this->_backend->store(array($output, $return));
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
    public function setCacheObject ( $object = null ) {

        if(is_string($object) || is_object($object)) {

            $this->_object = $object;

            return $this;
        }

        throw new Hoa_Cache_Exception('%s could be a string or a object.',
            2, $object);
    }
}
