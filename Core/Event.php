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

namespace Hoa\Core\Event {

/**
 * Interface \Hoa\Core\Event\Source.
 *
 * Each object which is observable must implement this interface.
 *
 * @author     Ivan Enderlin <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright © 2007-2011 Ivan Enderlin.
 * @license    New BSD License
 */

interface Source { }

/**
 * Class \Hoa\Core\Event\Bucket.
 *
 * This class is the object which is transmit through event channels.
 *
 * @author     Ivan Enderlin <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright © 2007-2011 Ivan Enderlin.
 * @license    New BSD License
 */

class Bucket {

    /**
     * Source object.
     *
     * @var \Hoa\Core\Event\Source object
     */
    protected $_source = null;

    /**
     * Data.
     *
     * @var \Hoa\Core\Event\Bucket mixed
     */
    protected $_data   = null;



    /**
     * Set data.
     *
     * @access  public
     * @param   mixed   $data    Data.
     * @return  void
     */
    public function __construct ( $data = null ) {

        $this->setData($data);

        return;
    }

    /**
     * Send this object on the event channel.
     *
     * @access  public
     * @param   string                  $eventId    Event ID.
     * @param   \Hoa\Core\Event\Source  $source     Source.
     * @return  void
     * @throws  \Hoa\Core\Exception
     */
    public function send ( $eventId, Source $source) {

        return Event::notify($eventId, $source, $this);
    }

    /**
     * Set source.
     *
     * @access  public
     * @param   \Hoa\Core\Event\Source  $source    Source.
     * @return  \Hoa\Core\Event\Source
     */
    public function setSource ( Source $source ) {

        $old           = $this->_source;
        $this->_source = $source;

        return $old;
    }

    /**
     * Get source.
     *
     * @access  public
     * @return  \Hoa\Core\Event\Source
     */
    public function getSource ( ) {

        return $this->_source;
    }

    /**
     * Set data.
     *
     * @access  public
     * @param   mixed   $data    Data.
     * @return  mixed
     */
    public function setData ( $data ) {

        $old         = $this->_data;
        $this->_data = $data;

        return $old;
    }

    /**
     * Get data.
     *
     * @access  public
     * @return  mixed
     */
    public function getData ( ) {

        return $this->_data;
    }
}

/**
 * Class \Hoa\Core\Event.
 *
 * Events are asynchronous at registration, anonymous at use (until we
 * receive a bucket) and useful to largely spread data through components
 * without any known connection between them.
 *
 * @author     Ivan Enderlin <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright © 2007-2011 Ivan Enderlin.
 * @license    New BSD License
 */

class Event {

    /**
     * Static register of all observable objects, i.e. \Hoa\Core\Event\Source
     * object, i.e. object that can send event.
     *
     * @var \Hoa\Core\Event array
     */
    private static $_register = array();

    /**
     * Callable, i.e. observer objects.
     *
     * @var \Hoa\Core\Event array
     */
    protected $_callable      = array();



    /**
     * Privatize the constructor.
     *
     * @access  private
     * @return  void
     */
    private function __construct ( ) {

        return;
    }

    /**
     * Manage multiton of events, with the principle of asynchronous attachements.
     *
     * @access  public
     * @param   string  $eventId    Event ID.
     * @return  \Hoa\Core\Event
     */
    public static function getEvent ( $eventId ) {

        if(!isset(self::$_register[$eventId][0]))
            self::$_register[$eventId] = array(
                0 => new self(),
                1 => null
            );

        return self::$_register[$eventId][0];
    }

    /**
     * Declare a new object in the observable collection.
     * Note: Hoa's libraries use hoa://Event/AnID for their observable objects;
     *
     * @access  public
     * @param   string                  $eventId    Event ID.
     * @param   \Hoa\Core\Event\Source  $source     Observable object.
     * @return  void
     * @throws  \Hoa\Core\Exception
     */
    public static function register ( $eventId, $source ) {

        if(true === self::eventExists($eventId))
            throw new \Hoa\Core\Exception(
                'Cannot redeclare an event with the same ID, i.e. the event ' .
                'ID %s already exists.', 0, $eventId);

        if(is_object($source) && !($source instanceof Source))
            throw new \Hoa\Core\Exception(
                'The source must implement \Hoa\Core\Event\Source ' .
                'interface; given %s.', 1, get_class($source));
        else {

            $reflection = new \ReflectionClass($source);

            if(false === $reflection->implementsInterface('\Hoa\Core\Event\Source'))
                throw new \Hoa\Core\Exception(
                    'The source must implement \Hoa\Core\Event\Source ' .
                    'interface; given %s.', 2, $source);
        }

        if(!isset(self::$_register[$eventId][0]))
            self::$_register[$eventId][0] = new self();

        self::$_register[$eventId][1] = $source;

        return;
    }

    /**
     * Undeclare an object in the observable collection.
     *
     * @access  public
     * @param   string  $eventId    Event ID.
     * @return  void
     */
    public static function unregister ( $eventId ) {

        unset(self::$_register[$eventId]);

        return;
    }

    /**
     * Attach an object to an event.
     * It can be a callable or an accepted callable form (please, see the
     * \Hoa\Core\Consistency\Callable class).
     *
     * @access  public
     * @param   mixed   $call    First callable part.
     * @param   mixed   $able    Second callable part (if needed).
     * @return  \Hoa\Core\Event
     */
    public function attach ( $call, $able = '' ) {

        $callable                              = callable($call, $able);
        $this->_callable[$callable->getHash()] = $callable;

        return $this;
    }

    /**
     * Detach an object to an event.
     * Please see $this->attach() method.
     *
     * @access  public
     * @param   mixed   $call    First callable part.
     * @param   mixed   $able    Second callable part (if needed).
     * @return  \Hoa\Core\Event
     */
    public function detach ( $call, $able = '' ) {

        unset($this->_callable[callable($call, $able)->getHash()]);

        return $this;
    }

    /**
     * Notify, i.e. send data to observers.
     *
     * @access  public
     * @param   string                             Event ID.
     * @param   \Hoa\Core\Event\Source  $source    Source.
     * @param   \Hoa\Core\Event\Bucket  $data      Data.
     * @return  void
     * @throws  \Hoa\Core\Exception
     */
    public static function notify ( $eventId, Source $source, Bucket $data ) {

        if(false === self::eventExists($eventId))
            throw new \Hoa\Core\Exception(
                'Event ID %s does not exist, cannot send notification.',
                3, $eventId);

        $sourceRef = self::$_register[$eventId][1];

        if(!($source instanceof $sourceRef))
            throw new \Hoa\Core\Exception(
                'Source cannot create a notification because it\'s the ' .
                'source or source\'s child (source reference: %s, given %s.',
                4, array(
                    is_object($sourceRef) ? get_class($sourceRef) : $sourceRef,
                    get_class($source)
                ));

        $data->setSource($source);
        $event = self::getEvent($eventId);

        foreach($event->_callable as $callable)
            $callable($data);

        return;
    }

    /**
     * Check whether an event exists.
     *
     * @access  public
     * @param   string  $eventId    Event ID.
     * @return  bool
     */
    public static function eventExists ( $eventId ) {

        return    array_key_exists($eventId, self::$_register)
               && self::$_register[$eventId][1] !== null;
    }
}

/**
 * Interface \Hoa\Core\Event\Listenable.
 *
 * Each object which is listenable must implement this interface.
 *
 * @author     Ivan Enderlin <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright © 2007-2011 Ivan Enderlin.
 * @license    New BSD License
 */

interface Listenable extends Source {

    /**
     * Attach a callable to a listenable component.
     *
     * @access  public
     * @param   string  $listenerId    Listener ID.
     * @param   mixed   $call          First callable part.
     * @param   mixed   $able          Second callable part (if needed).
     * @return  \Hoa\Core\Event\Listenable
     * @throw   \Hoa\Core\Exception
     */
    public function on ( $listenerId, $call, $able = '' );
}

/**
 * Class \Hoa\Core\Event\Listener.
 *
 * A contrario of events, listeners are synchronous, identified at use and
 * useful for close interactions between one or some components.
 *
 * @author     Ivan Enderlin <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright © 2007-2011 Ivan Enderlin.
 * @license    New BSD License
 */

class Listener {

    /**
     * Source of listener (for Bucket).
     *
     * @var \Hoa\Core\Event\Listenable object
     */
    protected $_source = null;

    /**
     * All listener IDs and associated listeners.
     *
     * @var \Hoa\Core\Event\Listener array
     */
    protected $_listen = null;



    /**
     * Build a listener.
     *
     * @access  public
     * @param   \Hoa\Core\Event\Listenable  $source    Source (for Bucket).
     * @param   array                       $ids       Accepted ID.
     * @return  void
     */
    public function __construct ( Listenable $source, Array $ids ) {

        $this->_source = $source;

        foreach($ids as $id)
            $this->_listen[$id] = array();

        return;
    }

    /**
     * Attach a callable to a listenable component.
     *
     * @access  public
     * @param   string  $listenerId    Listener ID.
     * @param   mixed   $call          First callable part.
     * @param   mixed   $able          Second callable part (if needed).
     * @return  \Hoa\Core\Event\Listener
     * @throw   \Hoa\Core\Exception
     */
    public function attach ( $listenerId, $call, $able = '' ) {

        if(false === $this->listenerExists($listenerId))
            throw new \Hoa\Core\Exception(
                'Cannot listen %s because it is not defined.', 0);

        $callable = callable($call, $able);
        $this->_listen[$listenerId][$callable->getHash()] = $callable;

        return $this;
    }

    /**
     * Detach a callable from a listenable component.
     *
     * @access  public
     * @param   string  $listenerId    Listener ID.
     * @param   mixed   $call          First callable part.
     * @param   mixed   $able          Second callable part (if needed).
     * @return  \Hoa\Core\Event\Listener
     */
    public function detach ( $listenerId, $call, $able = '' ) {

        unset($this->_callable[$listenerId][callable($call, $able)->getHash()]);

        return $this;
    }

    /**
     * Check if a listener exists.
     *
     * @access  public
     * @param   string  $listenerId    Listener ID.
     * @return  bool
     */
    public function listenerExists ( $listenerId ) {

        return array_key_exists($listenerId, $this->_listen);
    }

    /**
     * Send/fire a bucket to a listener.
     *
     * @access  public
     * @param   string                  $listenerId    Listener ID.
     * @param   \Hoa\Core\Event\Bucket  $data          Data.
     * @return  array
     * @throw   \Hoa\Core\Exception
     */
    public function fire ( $listenerId, Bucket $data ) {

        if(false === $this->listenerExists($listenerId))
            throw new \Hoa\Core\Exception(
                'Cannot fire on %s because it is not defined.', 1);

        $data->setSource($this->_source);
        $out = array();

        foreach($this->_listen[$listenerId] as $callable)
            $out[] = $callable($data);

        return $out;
    }
}

}

namespace {

/**
 * Alias of the \Hoa\Core\Event::getEvent() method.
 *
 * @access  public
 * @param   string  $eventId    Event ID.
 * @return  \Hoa\Core\Event
 */
if(!ƒ('event')) {
function event ( $eventId ) {

    return \Hoa\Core\Event::getEvent($eventId);
}}

/**
 * Make the alias automatically (because it's not imported with the import()
 * function).
 */
class_alias('Hoa\Core\Event\Event', 'Hoa\Core\Event');

}
