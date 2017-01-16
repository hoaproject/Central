<?php

/**
 * Hoa
 *
 *
 * @license
 *
 * New BSD License
 *
 * Copyright © 2007-2017, Hoa community. All rights reserved.
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

namespace Hoa\Session;

use Hoa\Consistency;
use Hoa\Event;
use Hoa\Iterator;

/**
 * Class \Hoa\Session.
 *
 * Object represents a “namespace” in a session, i.e. an entry in the $_SESSION
 * global array.
 * Class represents some useful operations (or aliases) on sessions.
 *
 * @copyright  Copyright © 2007-2017 Hoa community
 * @license    New BSD License
 */
class          Session
    implements Event\Source,
               \ArrayAccess,
               \Countable,
               Iterator\Aggregate
{
    /**
     * Event channel.
     *
     * @const string
     */
    const EVENT_CHANNEL           = 'hoa://Event/Session/';

    /**
     * Top-namespace: entry where namespaces are located in $_SESSION, i.e.
     * $_SESSION[static::TOP_NAMESPACE][<namespace>].
     *
     * @const string
     */
    const TOP_NAMESPACE           = '__Hoa__';

    /**
     * Profile of the namespace, i.e.
     * $_SESSION[static::TOP_NAMESPACE][<namespace>][static::PROFILE].
     *
     * @const string
     */
    const PROFILE                 = 0;

    /**
     * Data bucket of the namespace, i.e.
     * $_SESSION[static::TOP_NAMESPACE][<namespace>][static::BUCKET].
     *
     * @const string
     */
    const BUCKET                  = 1;

    /**
     * HTTP cache control: no cache.
     *
     * @const string
     */
    const NO_CACHE                = 'nocache';

    /**
     * HTTP cache control: public.
     *
     * @const string
     */
    const CACHE_PUBLIC            = 'public';

    /**
     * HTTP cache control: private.
     *
     * @const string
     */
    const CACHE_PRIVATE           = 'private';

    /**
     * HTTP cache control: private and no expiration.
     *
     * @const string
     */
    const CACHE_PRIVATE_NO_EXPIRE = 'private_no_expire';

    /**
     * Whether the session is started or not.
     *
     * @var bool
     */
    protected static $_started   = false;

    /**
     * Current namespace.
     *
     * @var string
     */
    protected $_namespace        = null;

    /**
     * Profile data.
     *
     * @var array
     */
    protected $_profile          = null;

    /**
     * Bucket data.
     *
     * @var array
     */
    protected $_bucket           = null;

    /**
     * Lock status (for all namespaces).
     *
     * @var array
     */
    protected static $_lock      = [];



    /**
     * Manipulate a namespace.
     * If session has not been previously started, it will be done
     * automatically.
     *
     * @param   string  $namespace      Namespace.
     * @param   string  $cache          Cache value (please, see static::*CACHE*
     *                                  constants).
     * @param   int     $cacheExpire    Cache expire (in seconds).
     * @throws  \Hoa\Session\Exception
     * @throws  \Hoa\Session\Exception\Locked
     */
    public function __construct(
        $namespace   = '_default',
        $cache       = null,
        $cacheExpire = null
    ) {
        if (false !== strpos($namespace, '/')) {
            throw new Exception(
                'Namespace must not contain a slash (/); given %s.',
                0,
                $namespace
            );
        }

        $this->_namespace = $namespace;

        if (false === array_key_exists($namespace, static::$_lock)) {
            static::$_lock[$namespace] = false;
        }

        if (true === $this->isLocked()) {
            throw new Exception\Locked(
                'Namespace %s is locked because it has been unset.',
                1,
                $namespace
            );
        }

        static::start($cache, $cacheExpire);
        $this->initialize();

        $channel = static::EVENT_CHANNEL . $namespace;
        $expired = $channel . ':expired';

        if (false === Event::eventExists($channel)) {
            Event::register($channel, 'Hoa\Session');
        }

        if (false === Event::eventExists($expired)) {
            Event::register($expired, 'Hoa\Session');
        }

        if (true === $this->isExpired()) {
            $this->hasExpired();
        }

        $this->_profile['last_used']->setTimestamp(time());

        return;
    }

    /**
     * Start the session.
     *
     * @param   string  $cache          Cache value (please, see static::*CACHE*
     *                                  constants).
     * @param   int     $cacheExpire    Cache expire (in seconds).
     * @return  void
     * @throws  \Hoa\Session\Exception
     */
    public static function start($cache = null, $cacheExpire = null)
    {
        if (null === $cache) {
            $cache = session_cache_limiter();
        }

        if (null === $cacheExpire) {
            $cacheExpire = session_cache_expire();
        }

        if (true === static::$_started) {
            return;
        }

        if (headers_sent($filename, $line)) {
            throw new Exception(
                'Session must be started before any ouput; ' .
                'output started in %s at line %d.',
                2,
                [$filename, $line]
            );
        }

        if (false === defined('SID')) {
            session_cache_limiter($cache);

            if (static::NO_CACHE !== $cache) {
                session_cache_expire($cacheExpire);
            }

            if (false === session_start()) {
                throw new Exception(
                    'Error when starting session. Cannot send session cookie.',
                    3
                );
            }
        }

        static::$_started = true;

        if (!isset($_SESSION[static::TOP_NAMESPACE])) {
            $_SESSION[static::TOP_NAMESPACE] = [];
        }

        return;
    }

    /**
     * Initialize the namespace.
     *
     * @param   bool  $reset    Re-initialize.
     * @return  void
     */
    protected function initialize($reset = false)
    {
        $namespace = $this->getNamespace();

        if (true === $reset) {
            unset($_SESSION[static::TOP_NAMESPACE][$namespace]);
        }

        if (!isset($_SESSION[static::TOP_NAMESPACE][$namespace])) {
            $_SESSION[static::TOP_NAMESPACE][$namespace] = [
                static::PROFILE => [
                    'started'   => new \DateTime(),
                    'last_used' => new \DateTime(),
                    'lifetime'  => new \DateTime(
                        '+' . ini_get('session.gc_maxlifetime') . ' second'
                    )
                ],
                static::BUCKET  => []
            ];
        }

        $handle         = &$_SESSION[static::TOP_NAMESPACE][$namespace];
        $this->_profile = &$handle[static::PROFILE];
        $this->_bucket  = &$handle[static::BUCKET];

        return;
    }

    /**
     * Get current namespace.
     *
     * @return  string
     */
    public function getNamespace()
    {
        return $this->_namespace;
    }

    /**
     * Check if the session is started or not.
     *
     * @return  bool
     */
    public static function isStarted()
    {
        return static::$_started;
    }

    /**
     * Check if the namespace is empty, i.e. if it does not contain data.
     *
     * @return  bool
     */
    public function isEmpty()
    {
        if (true === $this->isLocked()) {
            $this->__destruct();
        }

        return empty($this->_bucket);
    }

    /**
     * Check if the namespace is expired or not (test the lifetime).
     *
     * @return  bool
     */
    public function isExpired()
    {
        if (true === $this->isLocked()) {
            $this->__destruct();

            return true;
        }

        $lifetime = $this->_profile['lifetime'];
        $current  = new \DateTime();

        if ($lifetime > $current) {
            return false;
        }

        return true;
    }

    /**
     * Declare the session as “expired”. It will fire an event on
     * static::EVENT_CHANNEL . $namespace . ':expired' if this channel is
     * listened, else it will throw an exception. Moreover, it will
     * re-initialize the namespace.
     *
     * @param   bool  $exception    Whether throw an exception if needed or not.
     * @return  void
     * @throws  \Hoa\Session\Exception\Expired
     * @throws  \Hoa\Session\Exception\Locked
     */
    public function hasExpired($exception = true)
    {
        $namespace = $this->getNamespace();

        if (true === $this->isLocked()) {
            throw new Exception\Locked(
                'Namespace %s is locked because it has been unset.',
                4,
                $namespace
            );
        }

        $this->initialize(true);
        $expired = static::EVENT_CHANNEL . $namespace . ':expired';

        if (true  === $exception &&
            false === Event::getEvent($expired)->isListened()) {
            throw new Exception\Expired(
                'Namespace %s has expired. All data belonging to this ' .
                'namespace are lost.',
                5,
                $namespace
            );
        }

        Event::notify(
            $expired,
            $this,
            new Event\Bucket()
        );

        return;
    }

    /**
     * Check if the namespace is not locked.
     *
     * @return  bool
     */
    public function isLocked()
    {
        return static::$_lock[$this->getNamespace()];
    }

    /**
     * Get profile of the namespace.
     *
     * @return  array
     * @throws  \Hoa\Session\Exception\Locked
     */
    public function getProfile()
    {
        if (true === $this->isLocked()) {
            throw new Exception\Locked(
                'Namespace %s is locked because it has been unset.',
                6,
                $this->getNamespace()
            );
        }

        return $this->_profile;
    }

    /**
     * Modify the lifetime of the namespace.
     * Reference value: session.gc_maxlifetime seconds.
     *
     * @param   string  $modify    Please, see \DateTime::modify().
     * @return  \DateTime
     * @throws  \Hoa\Session\Exception\Locked
     */
    public function rememberMe($modify)
    {
        if (true === $this->isLocked()) {
            throw new Exception\Locked(
                'Namespace %s is locked because it has been unset.',
                7,
                $this->getNamespace()
            );
        }

        return $this->_profile['lifetime']->modify($modify);
    }

    /**
     * Set lifetime to 0.
     * This method is different from self::hasExpired() because it will only
     * modify the lifetime and it will not throw or fire anything.
     *
     * @return  \DateTime
     */
    public function forgetMe()
    {
        if (true === $this->isLocked()) {
            return null;
        }

        return $this->_profile['lifetime']->setTimestamp(time() - 1);
    }

    /**
     * Check if a data exists.
     *
     * @param   mixed  $offset    Data name.
     * @return  bool
     * @throws  \Hoa\Session\Exception\Locked
     */
    public function offsetExists($offset)
    {
        if (true === $this->isLocked()) {
            throw new Exception\Locked(
                'Namespace %s is locked because it has been unset.',
                8,
                $this->getNamespace()
            );
        }

        return array_key_exists($offset, $this->_bucket);
    }

    /**
     * Get a data.
     *
     * @param   mixed  $offset    Data name.
     * @return  mixed
     * @throws  \Hoa\Session\Exception\Locked
     */
    public function offsetGet($offset)
    {
        if (true === $this->isLocked()) {
            throw new Exception\Locked(
                'Namespace %s is locked because it has been unset.',
                9,
                $this->getNamespace()
            );
        }

        if (false === $this->offsetExists($offset)) {
            return null;
        }

        return $this->_bucket[$offset];
    }

    /**
     * Set a data.
     *
     * @param   mixed  $offset    Data name.
     * @param   mixed  $value     Data value.
     * @return  \Hoa\Session
     * @throws  \Hoa\Session\Exception\Locked
     */
    public function offsetSet($offset, $value)
    {
        if (true === $this->isLocked()) {
            throw new Exception\Locked(
                'Namespace %s is locked because it has been unset.',
                10,
                $this->getNamespace()
            );
        }

        if (null === $offset) {
            $this->_bucket[] = $value;
        } else {
            $this->_bucket[$offset] = $value;
        }

        return $this;
    }

    /**
     * Unset a data.
     *
     * @param   mixed  $offset    Data name.
     * @return  void
     * @throws  \Hoa\Session\Exception\Locked
     */
    public function offsetUnset($offset)
    {
        if (true === $this->isLocked()) {
            throw new Exception\Locked(
                'Namespace %s is locked because it has been unset.',
                11,
                $this->getNamespace()
            );
        }

        unset($this->_bucket[$offset]);

        return;
    }

    /**
     * Count number of data.
     *
     * @return  int
     */
    public function count()
    {
        if (true === $this->isLocked()) {
            return 0;
        }

        return count($this->_bucket);
    }

    /**
     * Iterate over data in the namespace.
     *
     * @return  \Hoa\Iterator\Map
     * @throws  \Hoa\Session\Exception\Locked
     */
    public function getIterator()
    {
        if (true === $this->isLocked()) {
            throw new Exception\Locked(
                'Namespace %s is locked because it has been unset.',
                12,
                $this->getNamespace()
            );
        }

        return new Iterator\Map($this->_bucket);
    }

    /**
     * Write and close the session (including all namespaces).
     *
     * @return  bool
     * @throws  \Hoa\Session\Exception\Locked
     */
    public function writeAndClose()
    {
        if (true === $this->isLocked()) {
            throw new Exception\Locked(
                'Namespace %s is locked because it has been unset.',
                13,
                $this->getNamespace()
            );
        }

        if (false === static::$_started) {
            return false;
        }

        session_write_close();

        return true;
    }

    /**
     * Remove all data from the namespace. It does not touch the profile or the
     * session, only the data.
     *
     * @return  void
     */
    public function clean()
    {
        $this->_bucket = [];

        return;
    }

    /**
     * Destroy the namespace.
     * The namespace will be locked and considered as expired (only the event
     * will be fired, not the exception).
     *
     * @return  void
     */
    public function delete()
    {
        $namespace = $this->getNamespace();
        $channel   = static::EVENT_CHANNEL . $namespace;
        $this->hasExpired(false);
        unset($_SESSION[static::TOP_NAMESPACE][$namespace]);
        Event::unregister($channel);
        Event::unregister($channel . ':expired');
        static::$_lock[$namespace] = true;

        return;
    }

    /**
     * Destroy the session (including all namespaces and cookie).
     * If session has not been previously started, it will be done
     * automatically. It won't modify current lock, be careful.
     *
     * @return  void
     * @throws  \Hoa\Session\Exception
     */
    public static function destroy()
    {
        static::start();

        if (true == ini_get('session.use_cookies')) {
            if (headers_sent($filename, $line)) {
                throw new Exception(
                    'Headers have been already sent, cannot destroy cookie; ' .
                    'output started in %s at line %d.',
                    14,
                    [$filename, $line]
                );
            }

            $parameters = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 1,
                $parameters['path'],
                $parameters['domain'],
                $parameters['secure'],
                $parameters['httponly']
            );
        }

        session_destroy();
        static::$_started = false;
        // let locks unchanged.

        return;
    }

    /**
     * Get current session ID.
     *
     * @return  string
     */
    public static function getId()
    {
        return session_id();
    }

    /**
     * Update the current session ID with a newly generated one.
     *
     * @param   bool  $deleteOldSession    Delete the old session file or not.
     * @return  bool
     */
    public static function newId($deleteOldSession = false)
    {
        return session_regenerate_id($deleteOldSession);
    }
}

/**
 * Flex entity.
 */
Consistency::flexEntity('Hoa\Session\Session');
