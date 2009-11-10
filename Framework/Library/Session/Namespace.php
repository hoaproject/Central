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
 * @package     Hoa_Session
 * @subpackage  Hoa_Session_Namespace
 *
 */

/**
 * Hoa_Framework
 */
require_once 'Framework.php';

/**
 * Hoa_Session
 */
import('Session.~');

/**
 * Hoa_Session_Exception
 */
import('Session.Exception');

/**
 * Hoa_Session_Exception_NamespaceIsExpired
 */
import('Session.Exception.NamespaceIsExpired');

/**
 * Class Hoa_Session_Namespace.
 *
 * A namespace is a variable of a session.
 * This class allows to manage many namespaces (one per instance), and allows to
 * have more access controls, time controls, etc., on namespace.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2009 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Session
 * @subpackage  Hoa_Session_Namespace
 */

class Hoa_Session_Namespace {

    /**
     * Namespace value.
     *
     * @var Hoa_Session_Namespace string
     */
    protected $namespace = null;



    /**
     * Built a new session namespace.
     *
     * @access  public
     * @param   string  $namespace    Namespace value.
     * @param   bool    $strict       Whether session must be started by
     *                                Hoa_Session::start() before declare a new
     *                                namespace. 
     * @return  void
     */
    public function __construct ( $namespace, $strict = true ) {

        Hoa_Session::setStrictMode($strict);
        Hoa_Session::start();

        $this->setNewNamespace($namespace);
    }

    /**
     * Set a new namespace and prepare it.
     *
     * @access  protected
     * @param   string     $namespace    Namespace value.
     * @return  void
     * @throw   Hoa_Session_Exception
     */
    protected function setNewNamespace ( $namespace ) {

        if(empty($namespace))
            throw new Hoa_Session_Exception(
                'Namespace value could not be empty.', 0);

        if(0 === preg_match('#^[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*$#', $namespace))
            throw new Hoa_Session_Exception(
                'Namespace %s is not well-formed ; must match with ' .
                '^[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]$*', 1, $namespace);
        
        if($namespace == '__Hoa')
            throw new Hoa_Session_Exception('__Hoa is a reserved namespace.', 2);

        $this->namespace = $namespace;

        if(   !isset($_SESSION[$namespace])
           && !isset($_SESSION['__Hoa']['namespace'][$namespace])) {

            $_SESSION[$namespace] = array();
            $_SESSION['__Hoa']['namespace'][$namespace] = array(
                'lock'          => false,  // per defaut, but should be parametrable.
                'expire_second' => null,   // idem
                'expire_access' => null
            );

            return;
        }

        if($this->isExpiredSecond())
            throw new Hoa_Session_Exception_NamespaceIsExpired(
                'Namespace %s is expired.', 3, $this->getNamespace());
    }

    /**
     * Get namespace name.
     *
     * @access  public
     * @return  string
     */
    public function getNamespaceName ( ) {

        return $this->namespace;
    }

    /**
     * Overload property.
     *
     * @access  public
     * @param   string  $name     Variable name.
     * @param   mixed   $value    Vaariable value.
     * @return  mixed
     * @throw   Hoa_Session_Exception
     */
    public function __set ( $name, $value ) {

        if(false === Hoa_Session::isWritable())
            throw new Hoa_Session_Exception(
                'Session is closed, cannot write data.', 4);

        if(false === Hoa_Session::isNamespaceSet($this->getNamespaceName()))
            throw new Hoa_Session_Exception(
                'Namespace %s is not set. Should not be used.',
                5, $this->getNamespaceName());

        if($this->isLocked())
            throw new Hoa_Session_Exception('Namespace is locked.', 6);

        $old = null;

        if(isset($_SESSION[$this->getNamespaceName()][$name]))
            $old = $_SESSION[$this->getNamespaceName()][$name];

        $_SESSION[$this->getNamespaceName()][$name] = $value;

        return $old;
    }

    /**
     * Overload property.
     *
     * @access  public
     * @param   string  $name    Variable name.
     * @return  mixed
     * @throw   Hoa_Session_Exception
     * @throw   Hoa_Session_Exception_NamespaceIsExpired
     */
    public function __get ( $name ) {

        if(!isset($_SESSION[$this->getNamespaceName()][$name]))
            return null;

        if($this->isLocked())
            throw new Hoa_Session_Exception('Namespace %s is locked.', 7, $name);

        if($this->isExpiredAccess())
            throw new Hoa_Session_Exception_NamespaceIsExpired(
                'Namespace %s has no more access.', 8, $name);

        $_SESSION['__Hoa']['namespace'][$this->getNamespaceName()]['expire_access']--;

        $value = $_SESSION[$this->getNamespaceName()][$name];
        $cast  = gettype($value);

        switch($cast) {

            case 'array':
                return (array) $value;
              break;

            default:
                return $value;
        }
    }

    /**
     * Overload property.
     *
     * @access  public
     * @param   string  $name    Variable name.
     * @return  bool
     */
    public function __isset ( $name ) {

        return isset($_SESSION[$this->getNamespaceName()][$name]);
    }

    /**
     * Overload property.
     *
     * @access  public
     * @param   string  $name    Variable name.
     * @return  void
     */
    public function __unset ( $name ) {

        if($this->isLocked())
            throw new Hoa_Session_Exception('Namespace %s is locked.', 9, $name);

        unset($_SESSION[$this->getNamespaceName()][$name]);
    }

    /**
     * Lock a namespace.
     *
     * @access  public
     * @return  bool
     */
    public function lock ( ) {

        if(false === Hoa_Session::isNamespaceSet($this->getNamespaceName()))
            throw new Hoa_Session_Exception(
                'Namespace %s is not set. Should not be used.',
                10, $this->getNamespaceName());

        $old = $_SESSION['__Hoa']['namespace'][$this->getNamespaceName()]['lock'];
        $_SESSION['__Hoa']['namespace'][$this->getNamespaceName()]['lock'] = true;

        return $old;
    }

    /**
     * Check if a namespace is locked or not.
     *
     * @access  public
     * @return  bool
     */
    public function isLocked ( ) {

        if(false === Hoa_Session::isNamespaceSet($this->getNamespaceName()))
            throw new Hoa_Session_Exception(
                'Namespace %s is not set. Should not be used.',
                11, $this->getNamespaceName());

        return $_SESSION['__Hoa']['namespace'][$this->getNamespaceName()]['lock'];
    }

    /**
     * Unlock a namespace.
     *
     * @access  public
     * @return  bool
     */
    public function unlock ( ) {

        if(false === Hoa_Session::isNamespaceSet($this->getNamespaceName()))
            throw new Hoa_Session_Exception(
                'Namespace %s is not set. Should not be used.',
                12, $this->getNamespaceName());

        $old = $_SESSION['__Hoa']['namespace'][$this->getNamespaceName()]['lock'];
        $_SESSION['__Hoa']['namespace'][$this->getNamespaceName()]['lock'] = false;

        return $old;
    }

    /**
     * Set expire second time.
     *
     * @access  public
     * @param   int     $time    Time before expire.
     * @return  int
     */
    public function setExpireSecond ( $time ) {

        if(false === Hoa_Session::isNamespaceSet($this->getNamespaceName()))
            throw new Hoa_Session_Exception(
                'Namespace %s is not set. Should not be used.',
                13, $this->getNamespaceName());

        if(null !== $_SESSION['__Hoa']['namespace'][$this->getNamespaceName()]
                    ['expire_second'])
            return;

        if(!is_int($time))
            throw new Hoa_Session_Exception(
                'The expiration time must be an int, that represents seconds. ' .
                'Given %s.', 14, gettype($time));

        $old = $_SESSION['__Hoa']['namespace'][$this->getNamespaceName()]['expire_second'];
        $_SESSION['__Hoa']['namespace'][$this->getNamespaceName()]['expire_second']
            = time() + $time;

        return $old;
    }

    /**
     * Get expire second time.
     *
     * @access  public
     * @return  int
     */
    public function getExpireSecond ( ) {

        if(false === Hoa_Session::isNamespaceSet($this->getNamespaceName()))
            throw new Hoa_Session_Exception(
                'Namespace %s is not set. Should not be used.',
                15, $this->getNamespaceName());

        return $_SESSION['__Hoa']['namespace'][$this->getNamespaceName()]['expire_second'];
    }

    /**
     * Check if a session is expired according to time.
     *
     * @access  public
     * @return  bool
     */
    public function isExpiredSecond ( ) {

        if(false === Hoa_Session::isNamespaceSet($this->getNamespaceName()))
            throw new Hoa_Session_Exception(
                'Namespace %s is not set. Should not be used.',
                16, $this->getNamespaceName());

        if(null === $this->getExpireSecond())
            return false;

        return time() > $this->getExpireSecond();
    }

    /**
     * Set expire access.
     *
     * @access  public
     * @param   int     $access    Number of access before expire.
     * @return  int
     */
    public function setExpireAccess ( $access ) {

        if(false === Hoa_Session::isNamespaceSet($this->getNamespaceName()))
            throw new Hoa_Session_Exception(
                'Namespace %s is not set. Should not be used.',
                17, $this->getNamespaceName());

        if(null !== $_SESSION['__Hoa']['namespace'][$this->getNamespaceName()]
                    ['expire_access'])
            return;

        if(!is_int($access))
            throw new Hoa_Session_Exception(
                'The expiration access must be an int. ' .
                'Given %s.', 18, gettype($access));

        $old = $_SESSION['__Hoa']['namespace'][$this->getNamespaceName()]['expire_access'];
        $_SESSION['__Hoa']['namespace'][$this->getNamespaceName()]['expire_access']
             = $access;

        return $old;
    }

    /**
     * Get expire access.
     *
     * @access  public
     * @return  int
     */
    public function getExpireAccess ( ) {

        if(false === Hoa_Session::isNamespaceSet($this->getNamespaceName()))
            throw new Hoa_Session_Exception(
                'Namespace %s is not set. Should not be used.',
                19, $this->getNamespaceName());

        return $_SESSION['__Hoa']['namespace'][$this->getNamespaceName()]['expire_access'];
    }

    /**
     * Check if a session is expired according to access number.
     *
     * @access  public
     * @return  bool
     */
    public function isExpiredAccess ( ) {

        if(false === Hoa_Session::isNamespaceSet($this->getNamespaceName()))
            throw new Hoa_Session_Exception(
                'Namespace %s is not set. Should not be used.',
                20, $this->getNamespaceName());

        if(null === $this->getExpireAccess())
            return false;

        return $this->getExpireAccess() <= 0;
    }
}
