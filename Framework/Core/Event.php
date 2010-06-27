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

    const CALLBACK_OBJECT = 0;
    const CALLBACK_METHOD = 1;

    private static $_register    = array();

    private static $_attachement = array();

    public static function register ( $eventId, Hoa_Core_Event_Source $source ) {

        if(true === self::eventExists($eventId))
            throw new Hoa_Exception(
                'Cannot redeclare an event with the same ID, i.e. the event ' .
                'ID %s already exists.', 0, $eventId);

        self::$_register[$eventId] = $source;

        return;
    }

    public static function unregister ( $eventId ) {

        unset(self::$_register[$eventId]);

        return;
    }

    public static function attach ( $eventId, $class, $method = null ) {

        if(false === self::eventExists($eventId))
            throw new Hoa_Exception(
                'Event ID does not exist, cannot attach something to it.',
                1, $eventId);

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

        $data->setSource($source);

        foreach(self::$_attachement[$eventId] as $index => $callback)
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
