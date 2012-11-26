<?php

/**
 * Hoa
 *
 *
 * @license
 *
 * New BSD License
 *
 * Copyright © 2007-2012, Ivan Enderlin. All rights reserved.
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
 * \Hoa\Session\Exception
 */
-> import('Session.Exception.~')

/**
 * \Hoa\Session\Exception\Expired
 */
-> import('Session.Exception.Expired');

}

namespace Hoa\Session {

/**
 * Class \Hoa\Session.
 *
 * Object represents a “namespace” in a session, i.e. an entry in the $_SESSION
 * global array.
 * Class represents some useful operations (or aliases) on sessions.
 *
 * @author     Ivan Enderlin <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright © 2007-2012 Ivan Enderlin.
 * @license    New BSD License
 */

class          Session
    implements \Hoa\Core\Event\Source,
               \ArrayAccess,
               \Countable,
               \IteratorAggregate {

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
     * Control destruction behavior.
     *
     * @var \Hoa\Session bool
     */
    private static $_destruction = true;

    /**
     * Whether the session is started or not.
     *
     * @var \Hoa\Session bool
     */
    protected static $_started   = false;

    /**
     * Current namespace.
     *
     * @var \Hoa\Session string
     */
    protected $_namespace        = null;

    /**
     * Profile data.
     *
     * @var \Hoa\Session array
     */
    protected $_profile          = null;

    /**
     * Bucket data.
     *
     * @var \Hoa\Session array
     */
    protected $_bucket           = null;



    /**
     * Manipulate a namespace.
     * If session has not been previously started, it will be done
     * automatically.
     *
     * @access  public
     * @param   string  $namespace      Namespace.
     * @param   string  $cache          Cache value (please, see static::*CACHE*
     *                                  constants).
     * @param   int     $cacheExpire    Cache expire (in seconds).
     * @return  void
     */
    public function __construct ( $namespace = '_default', $cache = null,
                                  $cacheExpire = null ) {

        static::start($cache, $cacheExpire);
        $this->_namespace = $namespace;
        $this->initialize();

        $channel = 'hoa://Event/Session/' . $namespace;
        $expired = $channel . ':expired';

        if(false === \Hoa\Core\Event::eventExists($channel))
            \Hoa\Core\Event::register($channel, 'Hoa\Session');

        if(false === \Hoa\Core\Event::eventExists($expired))
            \Hoa\Core\Event::register($expired, 'Hoa\Session');

        if(true === $this->isExpired())
            $this->hasExpired();

        $this->_profile['last_used']->setTimestamp(time());

        return;
    }

    /**
     * Start the session.
     *
     * @access  public
     * @param   string  $cache          Cache value (please, see static::*CACHE*
     *                                  constants).
     * @param   int     $cacheExpire    Cache expire (in seconds).
     * @return  void
     * @throw   \Hoa\Session\Exception
     */
    public static function start ( $cache = null, $cacheExpire = null ) {

        if(null === $cache)
            $cache = session_cache_limiter();

        if(null === $cacheExpire)
            $cacheExpire = session_cache_expire();

        if(true === static::$_started)
            return;

        if(headers_sent($filename, $line))
            throw new Exception(
                'Session must be started before any ouput; ' .
                'output started in %s at line %d.',
                0, array($filename, $line));

        if(false === defined('SID')) {

            session_cache_limiter($cache);

            if(static::NO_CACHE !== $cache)
                session_cache_expire($cacheExpire);

            if(false === session_start())
                throw new Exception(
                    'Error when starting session. Cannot send session cookie.',
                    1);
        }

        static::$_started = true;

        if(!isset($_SESSION[static::TOP_NAMESPACE]))
            $_SESSION[static::TOP_NAMESPACE] = array();

        return;
    }

    /**
     * Initialize the namespace.
     *
     * @access  public
     * @param   bool  $reset    Re-initialize.
     * @return  void
     */
    protected function initialize ( $reset = false ) {

        $namespace = $this->getNamespace();

        if(true === $reset)
            unset($_SESSION[static::TOP_NAMESPACE][$namespace]);

        if(!isset($_SESSION[static::TOP_NAMESPACE][$namespace]))
            $_SESSION[static::TOP_NAMESPACE][$namespace] = array(
                static::PROFILE => array(
                    'started'   => new \DateTime(),
                    'last_used' => new \DateTime(),
                    'lifetime'  => new \DateTime(
                        '+' . ini_get('session.gc_maxlifetime') . ' second'
                    )
                ),
                static::BUCKET  => array()
            );

        $handle         = &$_SESSION[static::TOP_NAMESPACE][$namespace];
        $this->_profile = &$handle[static::PROFILE];
        $this->_bucket  = &$handle[static::BUCKET];

        return;
    }

    /**
     * Get current namespace.
     *
     * @access  public
     * @return  string
     */
    public function getNamespace ( ) {

        return $this->_namespace;
    }

    /**
     * Check if the session is started or not.
     *
     * @access  public
     * @return  bool
     */
    public static function isStarted ( ) {

        return static::$_started;
    }

    /**
     * Check if the namespace is empty, i.e. if it does not contain data.
     *
     * @access  public
     * @return  bool
     */
    public function isEmpty ( ) {

        return empty($this->_bucket);
    }

    /**
     * Check if the namespace is expired or not (test the lifetime).
     *
     * @access  public
     * @return  bool
     */
    public function isExpired ( ) {

        $lifetime = $this->_profile['lifetime'];
        $current  = new \DateTime();

        if($lifetime > $current)
            return false;

        return true;
    }

    /**
     * Declare the session as “expired”. It will fire an event on
     * hoa://Event/Session/<namespace>:expired if this channel is listened, else
     * it will throw an exception. Moreover, it will reset the namespace.
     *
     * @access  public
     * @param   bool  $exception    Whether throw an exception if needed or not.
     * @return  void
     * @throw   \Hoa\Session\Exception\Expired
     */
    public function hasExpired ( $exception = true ) {

        $this->initialize(true);
        $namespace = $this->getNamespace();
        $expired   = 'hoa://Event/Session/' . $namespace . ':expired';

        if(   true === $exception
           && false === event($expired)->isListened())
            throw new Exception\Expired(
                'Session %s has expired. All data belonging to this ' .
                'namespace are lost.',
                2, $namespace);

        \Hoa\Core\Event::notify(
            $expired,
            $this,
            new \Hoa\Core\Event\Bucket()
        );

        return;
    }

    /**
     * Get profile of the namespace.
     *
     * @access  public
     * @return  array
     */
    public function getProfile ( ) {

        return $this->_profile;
    }

    /**
     * Modify the lifetime of the namespace.
     * Reference value: session.gc_maxlifetime seconds.
     *
     * @access  public
     * @param   string  $modify    Please, see \DateTime::modify().
     * @return  \DateTime
     */
    public function rememberMe ( $modify ) {

        return $this->_profile['lifetime']->modify($modify);
    }

    /**
     * Set lifetime to 0.
     *
     * @access  public
     * @return  \DateTime
     */
    public function forgetMe ( ) {

        return $this->_profile['lifetime']->setTimestamp(time() - 60);
    }

    /**
     * Check if a data exists.
     *
     * @access  public
     * @param   mixed  $offset    Data name.
     * @return  bool
     */
    public function offsetExists ( $offset ) {

        return array_key_exists($offset, $this->_bucket);
    }

    /**
     * Get a data.
     *
     * @access  public
     * @param   mixed  $offset    Data name.
     * @return  mixed
     */
    public function offsetGet ( $offset ) {

        if(false === $this->offsetExists($offset))
            return null;

        return $this->_bucket[$offset];
    }

    /**
     * Set a data.
     *
     * @access  public
     * @param   mixed  $offset    Data name.
     * @param   mixed  $value     Data value.
     * @return  \Hoa\Session
     */
    public function offsetSet ( $offset, $value ) {

        if(null === $offset)
            $this->_bucket[]        = $value;
        else
            $this->_bucket[$offset] = $value;

        return $this;
    }

    /**
     * Unset a data.
     *
     * @access  public
     * @param   mixed  $offset    Data name.
     * @return  void
     */
    public function offsetUnset ( $offset ) {

        unset($this->_bucket[$offset]);

        return;
    }

    /**
     * Count number of data.
     *
     * @access  public
     * @return  int
     */
    public function count ( ) {

        return count($this->_bucket);
    }

    /**
     * Iterate over data in the namespace.
     *
     * @access  public
     * @return  \ArrayIterator
     */
    public function getIterator ( ) {

        return new \ArrayIterator($this->_bucket);
    }

    /**
     * Write and close the session (including all namespaces).
     *
     * @access  public
     * @return  bool
     */
    public function writeAndClose ( ) {

        if(false === static::$_started)
            return false;

        session_write_close();

        return true;
    }

    /**
     * Destroy the session (including all namespaces and cookie).
     * If session has not been previously started, it will be done
     * automatically.
     *
     * @access  public
     * @return  void
     * @throw   \Hoa\Session\Exception
     */
    public static function destroy ( ) {

        static::start();

        if(true == ini_get('session.use_cookies')) {

            if(headers_sent($filename, $line))
                throw new Exception(
                    'Headers have been already sent, cannot destroy cookie; ' .
                    'output started in %s at line %d.',
                    3, array($filename, $line));

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

        return;
    }

    /**
     * Get current session ID.
     *
     * @access  public
     * @return  string
     */
    public static function getId ( ) {

        return session_id();
    }

    /**
     * Update the current session ID with a newly generated one.
     *
     * @access  public
     * @param   bool  $deleteOldSession    Delete the old session file or not.
     * @return  bool
     */
    public static function newId ( $deleteOldSession = false ) {

        return session_regenerate_id($deleteOldSession);
    }

    /**
     * Control destruction behavior.
     * Not for user, but for a global callback.
     *
     * @access  public
     * @return  void
     */
    public static function _avoidDestruction ( ) {

        self::$_destruction = false;

        return;
    }

    /**
     * Destructor.
     * If called by PHP, nothing special will happen. If called by user with the
     * help of unset(), it will delete the namespace.
     *
     * @access  public
     * @return  void
     */
    public function __destruct ( ) {

        // If exit.
        if(false === self::$_destruction)
            return;

        // If unset $this.
        $namespace = $this->getNamespace();
        $channel   = 'hoa://Event/Session/' . $namespace;
        unset($_SESSION[static::TOP_NAMESPACE][$namespace]);
        \Hoa\Core\Event::unregister($channel);
        \Hoa\Core\Event::unregister($channel . ':expired');

        return;
    }
}

}

namespace {

/**
 * Control namespace destruction behavior.
 */
\Hoa\Core::registerShutDownFunction('\Hoa\Session\Session', '_avoidDestruction');

/**
 * Session shutdown function.
 * Offering a PHP5.4 feature to lower version.
 *
 * @access  public
 * @return  void
 */
if(!ƒ('session_register_shutdown')) {
function session_register_shutdown ( ) {

    return register_shutdown_function('session_write_close');
}}

}
