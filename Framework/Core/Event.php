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
 * @package     Hoa_Core
 * @subpackage  Hoa_Core_Event
 *
 */

/**
 *
 */
interface Hoa_Core_Event_Source { }

/**
 *
 */
class Hoa_Core_Event_Bucket {

    protected $_source = null;

    protected $_value  = null;

    public function __construct ( $value ) {

        $this->setValue($value);

        return;
    }

    public function send ( $eventId, Hoa_Core_Event_Source $source) {

        return Hoa_Core_Event::notify($eventId, $source, $this);
    }

    public function setSource ( Hoa_Core_Event_Source $source ) {

        $old           = $this->_source;
        $this->_source = $source;

        return $old;
    }

    public function getSource ( ) {

        return $this->_source;
    }

    public function setValue ( $value ) {

        $old          = $this->_value;
        $this->_value = $value;

        return $old;
    }

    public function getValue ( ) {

        return $this->_value;
    }
}

/**
 * Class Hoa_Core_Event.
 *
 * Foobar.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2010 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP5
 * @version     0.1
 * @package     Hoa_Core_Event
 */

class Hoa_Core_Event {

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
     * Static register of all observable objects, i.e. Hoa_Core_Event_Source
     * object, i.e. object that can send event.
     *
     * @var Hoa_Core_Event array
     */
    private static $_register    = array();

    /**
     * Static attachements, i.e. oberserver objects, for all objects in the
     * register.
     *
     * @var Hoa_Core_Event array
     */
    private static $_attachement = array();



    /**
     * Declare a new object in the observable collection.
     * Note: Hoa's libraries use hoa://Event/AnID for their observable objects;
     *
     * @access  public
     * @param   string                   $eventId    Event ID.
     * @param   Hoa_Core_Event_Source    $source     Obversable object.
     * @return  void
     * @throws  Hoa_Exception
     */
    public static function register ( $eventId, Hoa_Core_Event_Source $source ) {

        if(true === self::eventExists($eventId))
            throw new Hoa_Exception(
                'Cannot redeclare an event with the same ID, i.e. the event ' .
                'ID %s already exists.', 0, $eventId);

        self::$_register[$eventId] = $source;

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

    public static function attach ( $eventId, $class, $method = null ) {

        if(false === self::eventExists($eventId))
            throw new Hoa_Exception(
                'Event ID %s does not exist, cannot attach something to it.',
                1, $eventId);

        return self::attachAsynchronously($eventId, $class, $method);
    }

    public static function attachAsynchronously ( $eventId, $class,
                                                  $method = null ) {

        $index = (is_object($class) ? get_class($class) : $class) .
                 '::' . $method;

        self::$_attachement[$eventId][$index] = array(
            self::CALLBACK_OBJECT => $class,
            self::CALLBACK_METHOD => $method
        );

        return;
    }

    public static function detach ( $eventId, $class, $method = null ) {

        if(false === self::eventExists($eventId))
            return;

        $index = (is_object($class) ? get_class($class) : $class) .
                 '::' . $method;

        unset(self::$_attachement[$eventId][$index]);

        return;
    }

    public static function notify ( $eventId,
                                    Hoa_Core_Event_Source $source,
                                    Hoa_Core_Event_Bucket $data ) {

        if(false === self::eventExists($eventId))
            throw new Hoa_Exception(
                'Event ID does not exist, cannot send notification.',
                2, $eventId);

        // TODO
        // verify if the source is already affected to the eventId.

        $data->setSource($source);
        $handle = $data->getValue();
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

        foreach(self::$_attachement[$eventId] as $index => $callback)
            if(     null === $callback[self::CALLBACK_METHOD]
                && ($callback[self::CALLBACK_OBJECT] instanceof Hoa_Stream_Io_Out))
                $callback[self::CALLBACK_OBJECT]->$method($handle);
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

    public static function eventExists ( $eventId ) {

        return array_key_exists($eventId, self::$_register);
    }
}
