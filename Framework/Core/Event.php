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
 * Manage events. It is simply an observer design-pattern, except that we have a
 * multiton of events.
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

        if(false === self::eventExists($eventId))
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
     * @param   mixed  $first     First parameter.
     * @param   mixed  $second    Second parameter.
     * @return  \Hoa\Core\Event
     */
    public function attach ( $first, $second = '' ) {

        $callable                              = callable($first, $second);
        $this->_callable[$callable->getHash()] = $callable;

        return $this;
    }

    /**
     * Detach an object to an event.
     * Please see $this->attach() method.
     *
     * @access  public
     * @param   mixed  $first     First parameter.
     * @param   mixed  $second    Second parameter.
     * @return  \Hoa\Core\Event
     */
    public function detach ( $first, $second = '' ) {

        unset($this->_callable[callable($first, $second)->getHash()]);

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
