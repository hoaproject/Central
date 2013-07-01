<?php

/**
 * Hoa
 *
 *
 * @license
 *
 * New BSD License
 *
 * Copyright © 2007-2013, Ivan Enderlin. All rights reserved.
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

namespace {

from('Hoa')

/**
 * \Hoa\Registry\Exception
 */
-> import('Registry.Exception');

}

namespace Hoa\Registry {

/**
 * Class \Hoa\Registry.
 *
 * Hold a register of something.
 *
 * @author     Ivan Enderlin <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright © 2007-2013 Ivan Enderlin.
 * @license    New BSD License
 */

class Registry extends \ArrayObject {

    /**
     * Instance.
     *
     * @var \Hoa\Registry object
     */
    private static $_instance = null;



    /**
     * Private constructor.
     *
     * @access  public
     * @return  void
     * @throw   \Hoa\Registry\Exception
     */
    public function __construct ( ) {

        throw new Exception(
            'Cannot instance the \Hoa\Registry object. Use set, get, remove ' .
            'and isRegistered static methods.', 0);

        return;
    }

    /**
     * Get instance of \Hoa\Registry.
     *
     * @access  protected
     * @return  object
     */
    protected static function getInstance ( ) {

        if(null === static::$_instance)
            static::$_instance = new parent();

        return static::$_instance;
    }

    /**
     * Set a new registry.
     *
     * @access  public
     * @param   mixed   $index     Index of registry.
     * @param   mixed   $value     Value of registry.
     * @return  void
     */
    public static function set ( $index, $value ) {

        static::getInstance()->offsetSet($index, $value);

        return;
    }

    /**
     * Get a registry.
     *
     * @access  public
     * @param   mixed   $index     Index of registry.
     * @return  mixed
     * @throw   \Hoa\Registry\Exception
     */
    public static function get ( $index ) {

        $registry = static::getInstance();

        if(false === $registry->offsetExists($index))
            throw new Exception('Registry %s does not exist.',
                1, $index);

        return $registry->offsetGet($index);
    }

    /**
     * Check if an index is registered.
     *
     * @access  public
     * @param   mixed   $index     Index of registry.
     * @return  bool
     */
    public static function isRegistered ( $index ) {

        return static::getInstance()->offsetExists($index);
    }

    /**
     * Unset an registry.
     *
     * @access  public
     * @param   mixed   $index    Index of registry.
     * @return  void
     */
    public static function remove ( $index ) {

        static::getInstance()->offsetUnset($index);

        return;
    }
}

/**
 * Class \Hoa\Registry\_Protocol.
 *
 * hoa://Library/Registry component.
 *
 * @author     Ivan Enderlin <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright © 2007-2013 Ivan Enderlin.
 * @license    New BSD License
 */

class _Protocol extends \Hoa\Core\Protocol {

    /**
     * Component's name.
     *
     * @var \Hoa\Core\Protocol string
     */
    protected $_name = 'Registry';



    /**
     * ID of the component.
     *
     * @access  public
     * @param   string  $id    ID of the component.
     * @return  mixed
     */
    public function reachId ( $id ) {

        return Registry::get($id);
    }
}

}

namespace {

/**
 * Add the hoa://Library/Registry component. Should be use to reach/get an entry
 * in the \Hoa\Registry, e.g.: resolve('hoa://Library/Registry#AnID').
 */
$protocol              = \Hoa\Core::getInstance()->getProtocol();
$protocol['Library'][] = new \Hoa\Registry\_Protocol();

}
