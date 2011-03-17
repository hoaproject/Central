<?php

/**
 * Hoa
 *
 *
 * @license
 *
 * GNU General Public License
 *
 * This file is part of HOA Open Accessibility.
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

namespace Hoa\Core\Event {

/**
 * Interface \Hoa\Core\Event\Source.
 *
 * Each object which is observable must implement this interface.
 *
 * @author     Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright (c) 2007, 2011 Ivan ENDERLIN.
 * @license    http://gnu.org/licenses/gpl.txt GNU GPL
 */

interface Source { }

/**
 * Class \Hoa\Core\Event\Bucket.
 *
 * This class is the object which is transmit through event channels.
 *
 * @author     Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright (c) 2007, 2011 Ivan ENDERLIN.
 * @license    http://gnu.org/licenses/gpl.txt GNU GPL
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
 * @author     Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright (c) 2007, 2011 Ivan ENDERLIN.
 * @license    http://gnu.org/licenses/gpl.txt GNU GPL
 */

class Event {

    /**
     * Attachement constant: index for object.
     *
     * @const int
     */
    const CALLBACK_OBJECT = 0;

    /**
     * Attachement constant: index for method (if needed).
     *
     * @const int
     */
    const CALLBACK_METHOD = 1;

    /**
     * Static register of all observable objects, i.e. \Hoa\Core\Event\Source
     * object, i.e. object that can send event.
     *
     * @var \Hoa\Core\Event array
     */
    private static $_register = array();

    /**
     * Attachements, i.e. oberserver objects, for all objects in the
     * register.
     *
     * @var \Hoa\Core\Event array
     */
    private $_attachement     = array();



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
     * @param   \Hoa\Core\Event\Source  $source     Obversable object.
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
     * The object can be a class with a method or an object with a method, or a
     * stream name or instance with or without a method (if without, the type of
     * the event data will decide of the method to call), or a closure.
     *
     * @access  public
     * @param   mixed   $class     Class name or instance, or a closure.
     * @param   string  $method    Method on the object (if $class is a class).
     * @return  \Hoa\Core\Event
     */
    public function attach ( $class, $method = null ) {

        $index = (is_object($class) ? get_class($class) : $class) .
                 '::' . $method;

        $this->_attachement[$index] = array(
            self::CALLBACK_OBJECT => $class,
            self::CALLBACK_METHOD => $method
        );

        return $this;
    }

    /**
     * Detach an object to an event.
     * Please see $this->attach() method.
     *
     * @access  public
     * @param   mixed   $class     Class name or instance, or a closure.
     * @param   string  $method    Method on the object (if $class is a class).
     * @return  \Hoa\Core\Event
     */
    public function detach ( $class, $method = null ) {

        $index = (is_object($class) ? get_class($class) : $class) .
                 '::' . $method;

        unset($this->_attachement[$index]);

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
        $handle = $data->getData();
        $method = 'writeAll';

        switch($type = gettype($handle)) {

            case 'string':
                if(1 === strlen($handle))
                    $method = 'writeCharacter';
                else
                    $method = 'writeString';
              break;

            case 'boolean':
            case 'integer':
            case 'array':
                $method = 'write' . ucfirst($type);
              break;

            case 'double':
                $method = 'writeFloat';
              break;
        }

        $event = self::getEvent($eventId);

        foreach($event->_attachement as $index => $callback)
            if(     null === $callback[self::CALLBACK_METHOD]
                && ($callback[self::CALLBACK_OBJECT] instanceof \Hoa\Stream\IStream\Out))
                $callback[self::CALLBACK_OBJECT]->$method($handle);

            elseif($callback[self::CALLBACK_OBJECT] instanceof \Closure)
                $callback[self::CALLBACK_OBJECT]($data);

            else
                call_user_func_array(
                    array(
                        $callback[self::CALLBACK_OBJECT],
                        $callback[self::CALLBACK_METHOD]
                    ),
                    array($data)
                );

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
