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
 */

namespace {

from('Hoa')

/**
 * \Hoa\Session
 */
-> import('Session.~')

/**
 * \Hoa\Session\Exception
 */
-> import('Session.Exception.~')

/**
 * \Hoa\Session\Exception\NamespaceIsExpired
 */
-> import('Session.Exception.NamespaceIsExpired');

}

namespace Hoa\Session {

/**
 * Class \Hoa\Session\QNamespace.
 *
 * A namespace is a variable of a session.
 * This class allows to manage many namespaces (one per instance), and allows to
 * have more access controls, time controls, etc., on namespace.
 *
 * @author     Ivan Enderlin <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright © 2007-2011 Ivan Enderlin.
 * @license    New BSD License
 */

class QNamespace {

    /**
     * Namespace value.
     *
     * @var \Hoa\Session\QNamespace string
     */
    protected $namespace = null;



    /**
     * Built a new session namespace.
     *
     * @access  public
     * @param   string  $namespace    Namespace value.
     * @param   bool    $strict       Whether session must be started by
     *                                \Hoa\Session::start() before declare a new
     *                                namespace. 
     * @return  void
     */
    public function __construct ( $namespace, $strict = true ) {

        Session::setStrictMode($strict);
        Session::start();

        $this->setNewNamespace($namespace);

        return;
    }

    /**
     * Set a new namespace and prepare it.
     *
     * @access  protected
     * @param   string     $namespace    Namespace value.
     * @return  void
     * @throw   \Hoa\Session\Exception
     */
    protected function setNewNamespace ( $namespace ) {

        if(empty($namespace))
            throw new Exception(
                'Namespace value could not be empty.', 0);

        if(0 === preg_match('#^[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*$#', $namespace))
            throw new Exception(
                'Namespace %s is not well-formed ; must match with ' .
                '^[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]$*', 1, $namespace);
        
        if($namespace == '__Hoa')
            throw new Exception(
                '__Hoa is a reserved namespace.', 2);

        $this->namespace = $namespace;

        if(   !isset($_SESSION[$namespace])
           && !isset($_SESSION['__Hoa']['namespace'][$namespace])) {

            $_SESSION[$namespace] = array();
            $_SESSION['__Hoa']['namespace'][$namespace] = array(
                'lock'          => false, // by defaut, but should be parametrable.
                'expire_second' => null,  // idem
                'expire_access' => null
            );

            return;
        }

        if($this->isExpiredSecond())
            throw new Exception\NamespaceIsExpired(
                'Namespace %s is expired.', 3, $this->getNamespaceName());

        return;
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
     * @throw   \Hoa\Session\Exception
     */
    public function __set ( $name, $value ) {

        if(false === Session::isWritable())
            throw new Exception(
                'Session is closed, cannot write data.', 4);

        if(false === Session::isNamespaceSet($this->getNamespaceName()))
            throw new Exception(
                'Namespace %s is not set. Should not be used.',
                5, $this->getNamespaceName());

        if($this->isLocked())
            throw new Exception(
                'Namespace is locked.', 6);

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
     * @throw   \Hoa\Session\Exception
     * @throw   \Hoa\Session\Exception\NamespaceIsExpired
     */
    public function __get ( $name ) {

        if(!isset($_SESSION[$this->getNamespaceName()][$name]))
            return null;

        if($this->isLocked())
            throw new Exception('Namespace %s is locked.', 7, $name);

        if($this->isExpiredAccess())
            throw new Exception\NamespaceIsExpired(
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
            throw new Exception(
                'Namespace %s is locked.', 9, $name);

        unset($_SESSION[$this->getNamespaceName()][$name]);
    }

    /**
     * Lock a namespace.
     *
     * @access  public
     * @return  bool
     */
    public function lock ( ) {

        if(false === Session::isNamespaceSet($this->getNamespaceName()))
            throw new Exception(
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

        if(false === Session::isNamespaceSet($this->getNamespaceName()))
            throw new Exception(
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

        if(false === Session::isNamespaceSet($this->getNamespaceName()))
            throw new Exception(
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

        if(false === Session::isNamespaceSet($this->getNamespaceName()))
            throw new Exception(
                'Namespace %s is not set. Should not be used.',
                13, $this->getNamespaceName());

        if(null !== $_SESSION['__Hoa']['namespace'][$this->getNamespaceName()]
                    ['expire_second'])
            return;

        if(!is_int($time))
            throw new Exception(
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

        if(false === Session::isNamespaceSet($this->getNamespaceName()))
            throw new Exception(
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

        if(false === Session::isNamespaceSet($this->getNamespaceName()))
            throw new Exception(
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

        if(false === Session::isNamespaceSet($this->getNamespaceName()))
            throw new Exception(
                'Namespace %s is not set. Should not be used.',
                17, $this->getNamespaceName());

        if(null !== $_SESSION['__Hoa']['namespace'][$this->getNamespaceName()]
                    ['expire_access'])
            return;

        if(!is_int($access))
            throw new Exception(
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

        if(false === Session::isNamespaceSet($this->getNamespaceName()))
            throw new Exception(
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

        if(false === Session::isNamespaceSet($this->getNamespaceName()))
            throw new Exception(
                'Namespace %s is not set. Should not be used.',
                20, $this->getNamespaceName());

        if(null === $this->getExpireAccess())
            return false;

        return $this->getExpireAccess() <= 0;
    }
}

}
