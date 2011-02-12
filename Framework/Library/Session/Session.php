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
 */

namespace {

from('Hoa')

/**
 * \Hoa\Session\Exception
 */
-> import('Session.Exception.~')

/**
 * \Hoa\Session\Exception\SessionIsExpired
 */
-> import('Session.Exception.SessionIsExpired')

/**
 * \Hoa\Session\QNamespace;
 */
-> import('Session.QNamespace')

/**
 * \Hoa\Session\Option
 */
-> import('Session.Option')

/**
 * \Hoa\Session\Flash
 */
-> import('Session.Flash')

/**
 * \Hoa\Session\ISession\SaveHandler
 */
-> import('Session.I~.SaveHandler');

}

namespace Hoa\Session {

/**
 * Class \Hoa\Session.
 *
 * This class allows to do different action on session (start, close, identify,
 * expire etc.).
 *
 * @author     Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright (c) 2007, 2011 Ivan ENDERLIN.
 * @license    http://gnu.org/licenses/gpl.txt GNU GPL
 */

abstract class Session {

    /**
     * Whether session is stared or not.
     *
     * @var \Hoa\Session bool
     */
    protected static $_start    = false;

    /**
     * Whether session is in strict mode, i.e. \Hoa\Session::start must be
     * called before all new namespace declaration.
     *
     * @var \Hoa\Session bool
     */
    protected static $_strict   = false;

    /**
     * Whether session is writable or not.
     *
     * @var \Hoa\Session bool
     */
    protected static $_writable = false;

    /**
     * Whether session is readable or not.
     *
     * @var \Hoa\Session bool
     */
    protected static $_readable = false;



    /**
     * Start a session.
     *
     * @access  public
     * @param   array   $option     Option for Session\Option.
     * @return  bool
     * @throw   \Hoa\Session\Exception
     * @throw   \Hoa\Session\Exception\SessionIsExpired
     */
    public static function start ( Array $option = array() ) {

        if(true === self::getStrictMode())
            if(true === self::isStarted())
                return;
            else
                throw new Exception(
                    'A session must be started by \Hoa\Session::start() before ' .
                    'declare a new namespace.', 0);
        else
            if(true === self::isStarted())
                return;


        if(headers_sent($filename, $line))
            throw new Exception(
                'Session must be started before any output ; ' .
                'output started in %s at line %d.', 1,
                array($filename, $line));

        if(defined('SID'))
            throw new Exception(
                'Session has been already auto or manually started (by ' .
                'session.auto_start or session_start()).', 2);

        Option::set($option);

        if(false === session_start())
            throw new \Hoa\Session\Exception(
                'Error when starting session. Cannot send session cookie. ' .
                'Headers already sent.', 3);

        self::setStart(true);
        self::setWritable(true);
        self::setReadable(true);
        self::prepareSecretPart();
        self::regenerateId();
        self::identifyMe();

        if(self::isExpiredSecond())
            throw new Exception\SessionIsExpired(
                'Session is expired.', 4);

        return;
    }

    /**
     * Prepare secret part of the session (private namespace).
     *
     * @access  protected
     * @return  void
     * @throw   \Hoa\Session\Exception
     */
    protected static function prepareSecretPart ( ) {

        if(true === self::isNamespaceSet('__Hoa'))
            return;

        if(!isset($_SERVER['REMOTE_ADDR']))
            throw new Exception(
                'Cannot prepare the session identity, because the ' .
                '$_SERVER[\'REMOTE_ADDR\'] variable is not found.', 5);

        $_SESSION['__Hoa'] = array(
            'namespace'     => array(),
            'expire_second' => null,   // should be parameterizable, aye?
            'flash'         => array(),
            'identity'      => array(
                'id'        => md5(session_id()),
                'ip'        => md5($_SERVER['REMOTE_ADDR'])
            )
        );

        return;
    }

    /**
     * Regenerate the session ID.
     *
     * @access  public
     * @param   bool    $getNew    Whether the method returns the new session ID
     *                             (false) or the new session ID (true).
     * @return  string
     * @throw   \Hoa\Session\Exception
     */
    public static function regenerateId ( $getNew = false ) {

        if(headers_sent($filename, $line))
            throw new Exception(
                'Cannot regenerate session ID ; headers already sent in %s ' .
                'on line %d.', 6,
                array($filename, $line));

        if(true !== self::isNamespaceSet('__Hoa'))
            throw new Exception(
                'Cannot regenerate ID, because the session was not ' .
                'well-started.', 7);

        $old = session_id();
        session_regenerate_id();

        $_SESSION['__Hoa']['identity']['id'] = md5(session_id());

        return false === $getNew ? $old : session_id();
    }

    /**
     * Identify a session, i.e. check if the owner's session is the right
     * person.
     *
     * @access  public
     * @return  bool
     * @throw   \Hoa\Session\Exception
     */
    public static function identifyMe ( ) {

        if(false === self::isStarted())
            throw new Exception(
                'Cannot identify a no-started session.', 8);

        if(!isset($_SESSION['__Hoa']['identity']))
            throw new Exception(
                'Cannot identify the current session.', 9);

        $identity = $_SESSION['__Hoa']['identity'];

        if(!isset($identity['id']))
            throw new Exception(
                'Cannot identify the current session ; session ID missing.', 10);

        if(!isset($identity['ip']))
            throw new Exception(
                'Cannot identify the current session ; session IP missing.', 11);

        if($identity['id'] !== md5(session_id()))
            throw new Exception(
                'Session is not well-identify ; session ID is not the right ' .
                'ID.', 12);

        if($identity['ip'] !== md5($_SERVER['REMOTE_ADDR']))
            throw new Exception(
                'Session is not well-identify ; IP is not the right IP.', 13);

        return true;
    }

    /**
     * Check if a namespace already exists.
     *
     * @access  public
     * @param   mixed   $namespace    The namespace name or object.
     * @return  bool
     */
    public static function isNamespaceSet ( $namespace ) {

        if($namespace instanceof QNamespace)
            $namespace = $namespace->getNamespaceName();

        return isset($_SESSION[$namespace]);
    }

    /**
     * Unset a namespace.
     *
     * @access  public
     * @param   mixed   $namespace    The namespace name or instance.
     * @return  void
     * @throw   \Hoa\Session\Exception
     */
    public static function unsetNamespace ( $namespace ) {

        if(false === self::isStarted())
            throw new Exception(
                'Cannot unset a namespace on a no-starded session.', 14);

        $name     = $namespace;
        if($namespace instanceof QNamespace)
            $name = $namespace->getNamespaceName();

        if(true === $_SESSION['__Hoa']['namespace'][$name]['lock'])
            throw new Exception(
                'Namespace %s is locked.', 15, $name);

        unset($_SESSION[$name]);
        unset($_SESSION['__Hoa']['namespace'][$name]);
        $namespace = null;

        return;
    }

    /**
     * Unset all namespaces.
     *
     * @access  public
     * @return  void
     * @throw   \Hoa\Session\Exception
     */
    public static function unsetAllNamespaces ( ) {

        if(false === self::isStarted())
            throw new Exception(
                'Cannot unset namespaces on a no-starded session.', 16);

        $nsidList = $_SESSION['__Hoa']['namespace'];

        foreach($_SESSION['__Hoa']['flash'] as $id => $foo)
            unset($nsidList[$id]);

        foreach($nsidList as $id => $foo) {

            if(true === $foo['lock'])
                throw new Exception(
                    'Namespace %s is locked.', 17, $flash);

            unset($_SESSION[$id]);
            unset($_SESSION['__Hoa']['namespace'][$id]);
        }

        return;
    }

    /**
     * Check if a flash already exists.
     *
     * @access  public
     * @param   string  $flash    The flash message ID. Should be the original
     *                            ID or the prefixed and encoded ID.
     * @return  bool
     */
    public static function isFlashSet ( $flash ) {

        return    isset($_SESSION[$flash])
               || isset($_SESSION['_flashMessage_' . md5($flash)]);
    }

    /**
     * Unset a flash.
     *
     * @access  public
     * @param   string  $flash    The flash message ID.
     * @return  void
     * @throw   \Hoa\Session\Exception
     */
    public static function unsetFlash ( $flash ) {

        if(false === self::isStarted())
            throw new Exception(
                'Cannot unset a flash on a no-starded session.', 18);

        $flashId = '_flashMessage_' . md5($flash);

        if(true === $_SESSION['__Hoa']['namespace'][$flashId]['lock'])
            throw new Exception(
                'Namespace %s is locked.', 19, $flash);

        unset($_SESSION[$flashId]);
        unset($_SESSION['__Hoa']['namespace'][$flashId]);
        unset($_SESSION['__Hoa']['flash'][$flashId]);

        return;
    }

    /**
     * Unset all flashes.
     *
     * @access  public
     * @return  void
     * @throw   \Hoa\Session\Exception
     */
    public static function unsetAllFlashes ( ) {

        if(false === self::isStarted())
            throw new Exception(
                'Cannot unset flashes on a no-starded session.', 20);

        $fidList = $_SESSION['__Hoa']['flash'];
        $_SESSION['__Hoa']['flash'] = array();

        foreach($fidList as $id => $foo) {

            if(true === $_SESSION['__Hoa']['namespace'][$id]['lock'])
                throw new \Hoa\Session\Exception(
                    'Namespace %s is locked.', 21, $flash);

            unset($_SESSION[$id]);
            unset($_SESSION['__Hoa']['namespace'][$id]);
        }

        return;
    }

    /**
     * Get self::$_start value.
     *
     * @access  public
     * @return  bool
     */
    public static function isStarted ( ) {

        return self::$_start;
    }

    /**
     * Write and close a session.
     *
     * @access  public
     * @return  void
     * @throw   \Hoa\Session\Exception
     */
    public static function writeAndClose ( ) {

        if(false === self::isWritable())
            throw new Exception(
                'Cannot write and close the session, because it is not ' .
                'writable.', 22);

        set_error_handler(array('\Hoa\Session\Exception', 'handleWriteAndCloseError'),
                          E_ALL);
        @session_write_close();
        restore_error_handler();

        if(true === Exception::hasWriteAndCloseError())
            throw new Exception(
                Exception::getWriteAndCloseErrorMessage());

        self::$_writable = false;

        return;
    }

    /**
     * Check if a session is writable or not.
     *
     * @access  public
     * @return  bool
     */
    public static function isWritable ( ) {

        return self::$_writable;
    }

    /**
     * Check if a session is readable or not.
     *
     * @access  public
     * @return  bool
     */
    public static function isReadable ( ) {

        return self::$_readable;
    }

    /**
     * Check if a session is not writable but readable (i.e. in read-only
     * access) or not.
     *
     * @access  public
     * @return  bool
     */
    public static function isOnlyReadable ( ) {

        return false === self::isWritable() && true === self::isReadable();
    }

    /**
     * Destroy a session.
     *
     * @access  public
     * @return  void
     * @throw   \Hoa\Session\Exception
     */
    public static function destroy ( ) {

        if(true === self::isOnlyReadable())
            throw new Exception(
                'Trying to destroy uninitialized session.', 23);

        set_error_handler(array('\Hoa\Session\Exception', 'handleDestroyError'),
                          E_ALL);
        @session_destroy();
        restore_error_handler();

        if(true === Exception::hasDestroyError())
            throw new Exception(
                Exception::getDestroyErrorMessage());

        self::setWritable(false);
        self::setReadable(true);
        self::setStart(false);

        if(true !== Option::isUsingCookie())
            return;

        if(true !== Option::isCookieSet())
            return;

        $cookieParams = session_get_cookie_params();
        setcookie(
            self::getName(),
            false,
            0,
            $cookieParams['path'],
            $cookieParams['domain'],
            $cookieParams['secure']
        );

        return;
    }

    /**
     * Set self::$_start.
     *
     * @access  protected
     * @param   bool       $value    Value of start variable.
     * @return  bool
     */
    protected static function setStart ( $value ) {

        $old          = self::$_start;
        self::$_start = $value;

        return $old;
    }

    /**
     * Set strict mode.
     *
     * @access  public
     * @param   bool    $strict    Strict mode.
     * @return  bool
     */
    public static function setStrictMode ( $strict ) {

        $old           = self::$_strict;
        self::$_strict = $strict;

        return $old;
    }

    /**
     * Set writable.
     *
     * @access  protected
     * @param   bool       $writable    Value of write access.
     * @return  bool
     */
    protected static function setWritable ( $writable ) {

        $old             = self::$_writable;
        self::$_writable = $writable;

        return $old;
    }

    /**
     * Set readable.
     *
     * @access  protected
     * @param   bool       $readable    Value of read access.
     * @return  bool
     */
    protected static function setReadable ( $readable ) {

        $old             = self::$_readable;
        self::$_readable = $readable;

        return $old;
    }

    /**
     * Get strict mode.
     *
     * @access  public
     * @return  bool
     */
    public static function getStrictMode ( ) {

        return self::$_strict;
    }

    /**
     * Set expire second time.
     *
     * @access  public
     * @param   int     $time    Time before expire.
     * @return  int
     * @throw   \Hoa\Session\Exception
     */
    public static function setExpireSecond ( $time ) {

        if(false === self::isStarted())
            throw new Exception(
                'Cannot force a no-started session to expire.', 24);

        if(!is_int($time))
            throw new Exception(
                'The expiration time must be an int, that represents seconds. ' .
                'Given %s.', 25, gettype($time));

        if(null !== $_SESSION['__Hoa']['expire_second'])
            return;

        $old                                = $_SESSION['__Hoa']['expire_second'];
        $_SESSION['__Hoa']['expire_second'] = time() + $time;

        if(true !== Option::isUsingCookie())
            return $old;

        if(true !== Option::isCookieSet())
            return $old;

        $cookieParams = session_get_cookie_params();
        session_set_cookie_params(
            $time,
            $cookieParams['path'],
            $cookieParams['domain'],
            $cookieParams['secure']
        );

        return $old;
    }

    /**
     * Get expire second time.
     *
     * @access  public
     * @return  int
     * @throw   \Hoa\Session\Exception
     */
    public static function getExpireSecond ( ) {

        if(false === self::isStarted())
            throw new Exception(
                'Cannot get the expiration time for a no-started session.', 26);

        return $_SESSION['__Hoa']['expire_second'];
    }

    /**
     * Get the number of second before expiring.
     *
     * @access  public
     * @return  int
     * @throw   \Hoa\Session\Exception
     */
    public static function getSecondBeforeExpiring ( ) {

        if(false === self::isStarted())
            throw new Exception(
                'Cannot get the expiration time for a no-started session.', 27);

        return self::getExpireSecond() - time();
    }

    /**
     * Check if a session is expired according to time.
     *
     * @access  public
     * @return  bool
     * @throw   \Hoa\Session\Exception
     */
    public static function isExpiredSecond ( ) {

        if(null === self::getExpireSecond())
            return false;

        return time() > self::getExpireSecond();
    }

    /**
     * Force a session to not expire before a long time (2 weeks).
     *
     * @access  public
     * @param   bool    $overwrite    Force to overwrite previous expire time.
     * @return  bool
     * @throw   \Hoa\Session\Exception
     */
    public static function rememberMe ( $overwrite = false ) {

        if(false === self::isStarted())
            throw new Exception(
                'Cannot remember a no-started session.', 28);

        if(   false !== $overwrite
           || null  === self::getExpireSecond()) {

            $_SESSION['__Hoa']['expire_second'] = time() + 1209600;
            return true;
        }

        return false;
    }

    /**
     * Force a session to expire.
     *
     * @access  public
     * @return  void
     * @throw   \Hoa\Session\Exception
     */
    public static function forgetMe ( ) {

        if(false === self::isStarted())
            throw new Exception(
                'Cannot forget a no-started session.', 29);

        $_SESSION['__Hoa']['expire_second'] = time() - 1;
    }

    /**
     * Add a save handler interface.
     *
     * @access  public
     * @param   \Hoa\Session\ISession\SaveHandler  $savehandler    The save
     *                                                             handler
     *                                                             interface.
     * @return  void
     */
    public static function setSaveHandler ( ISession\SaveHandler $savehandler ) {

        return session_set_save_handler(
            array(&$savehandler, 'open'),
            array(&$savehandler, 'close'),
            array(&$savehandler, 'read'),
            array(&$savehandler, 'write'),
            array(&$savehandler, 'destroy'),
            array(&$savehandler, 'gc')
        );
    }

    /**
     * Get the session ID.
     *
     * @access  public
     * @return  string
     */
    public static function getId ( ) {

        if(false === self::isStarted())
            return null;

        return session_id();
    }

    /**
     * Get the session name.
     *
     * @access  public
     * @return  string
     */
    public static function getName ( ) {

        if(false === self::isStarted())
            return null;

        return session_name();
    }

    /**
     * Get an iterator, based on an ArrayObject and an ArrayIterator.
     *
     * @access  public
     * @return  void
     */
    public static function getIterator ( ) {

        if(false === self::isStarted())
            return new \ArrayObject();

        $array = $_SESSION;
        unset($array['__Hoa']);
        foreach($_SESSION['__Hoa']['flash'] as $id => $foo)
            unset($array[$id]);

        return new \ArrayObject(
            $array,
            ArrayObject::ARRAY_AS_PROPS
        );
    }
}

}
