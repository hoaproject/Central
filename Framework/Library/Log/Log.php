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
 * @package     Hoa_Log
 *
 */

/**
 * Hoa_Core
 */
require_once 'Core.php';

/**
 * Hoa_Log_Exception
 */
import('Log.Exception');

/**
 * Hoa_Log_Backtrace
 */
import('Log.Backtrace');

/**
 * Hoa_Tree_Visitor_Dump
 */
import('Tree.Visitor.Dump');

/**
 * Hoa_Stream
 */
import('Stream.~');

/**
 * Hoa_Stream_Interface_Out
 */
import('Stream.Interface.Out');

/**
 * Class Hoa_Log.
 *
 * Propose a log system.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2010 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     12.1
 * @package     Hoa_Log
 */

class Hoa_Log implements Hoa_Core_Event_Source {

    /**
     * Priority: emergency, system is unusable.
     * (Note: priorities from “The BSD Syslog Protocol” (RFC 3164, 4.1.1 PRI
     * Part).
     *
     * @const int
     */
    const EMERGENCY         =   1;

    /**
     * Priority: alert, action must be taken immediately.
     *
     * @const int
     */
    const ALERT             =   2;

    /**
     * Priority: critical, critical conditions.
     *
     * @const int
     */
    const CRITICAL          =   4;

    /**
     * Priority: error, error conditions.
     *
     * @const int
     */
    const ERROR             =   8;

    /**
     * Priority: warning, warning conditions.
     *
     * @const int
     */
    const WARNING           =  16;

    /**
     * Priority: notice, normal but significant condition.
     *
     * @const int
     */
    const NOTICE            =  32;

    /**
     * Priority: informational messages.
     *
     * @const int
     */
    const INFORMATIONAL     =  64;

    /**
     * Priority: debug-level messages.
     *
     * @const int
     */
    const DEBUG             = 128;

    /**
     * Priority: test messages.
     *
     * @const int
     */
    const TEST              = 256;

    /**
     * Stack index: timestamp.
     *
     * @const string
     */
    const STACK_TIMESTAMP   =   'timestamp';

    /**
     * Stack index: message.
     *
     * @const string
     */
    const STACK_MESSAGE     =   'message';

    /**
     * Stack index: priority.
     *
     * @const string
     */
    const STACK_PRIORITY    =   'priority';

    /**
     * Stack index: memory.
     *
     * @const string
     */
    const STACK_MEMORY      =   'memory';

    /**
     * Stack index: memory peak.
     *
     * @const string
     */
    const STACK_MEMORY_PEAK =   'memory_peak';

    /**
     * Multiton.
     *
     * @var Hoa_Log array
     */
    private static $_instances = null;

    /**
     * Current singleton index.
     *
     * @var Hoa_Log string
     */
    private static $_currentId = null;

    /**
     * Logs stack.
     *
     * @var Hoa_Log array
     */
    protected $_stack          = array();

    /**
     * Backtrace.
     *
     * @var Hoa_Log_Backtrace object
     */
    protected $_backtrace      = null;

    /**
     * Filters (combination of priorities constants, null means all).
     *
     * @var Hoa_Log int
     */
    protected $_filters        = null;

    /**
     * Extra stack informations.
     *
     * @var Hoa_Log array
     */
    protected $_stackInfos     = array();



    /**
     * Build a new log system.
     *
     * @access  private
     * @return  void
     */
    private function __construct ( ) {

        return;
    }

    /**
     * Make a multiton.
     *
     * @access  public
     * @param   string      $id        Channel ID (i.e. singleton ID)
     * @return  Hoa_Log
     * @throw   Hoa_Log_Exception
     */
    public static function getChannel ( $id = null ) {

        if(null === self::$_currentId && null === $id)
            throw new Hoa_Log_Exception(
                'Must precise a singleton index once.', 0);

        if(!isset(self::$_instances[$id])) {

            self::$_instances[$id] = new self();
            Hoa_Core_Event::register(
                'hoa://Event/Log/' . $id,
                self::$_instances[$id]
            );
        }

        if(null !== $id)
            self::$_currentId = $id;

        $handle = self::$_instances[self::$_currentId];

        return $handle;
    }

    /**
     * Accept a type of log on the outputstream.
     *
     * @access  public
     * @param   int     $filter    A filter (please, see the class constants).
     * @return  int
     */
    public function accept ( $filter ) {

        if(null === $this->_filters)
            return $this->_filters = $filter;

        $old            = $this->_filters;
        $this->_filters = $old | $filter;

        return $old;
    }

    /**
     * Accept all types of logs on the outputstream.
     *
     * @access  public
     * @return  int
     */
    public function acceptAll ( ) {

        $old            = $this->_filters;
        $this->_filters = null;

        return $old;
    }

    /**
     * Set filters directly.
     *
     * @access  public
     * @param   int     $filter    A filter (please, see the class constants).
     * @return  int
     */
    public function setFilter ( $filter ) {

        $old            = $this->_filters;
        $this->_filters = $filter;

        return $old;
    }

    /**
     * Drop a type of log on the outputstream.
     *
     * @access  public
     * @param   int     $filter    A filter (please, see the class constants).
     * @return  int
     */
    public function drop ( $filter ) {

        $old            = $this->_filters;
        $this->_filters = $old & ~$filter;

        return $old;
    }

    /**
     * Drop all type of logs on the outputstreams.
     *
     * @access  public
     * @return  int
     */
    public function dropAll ( ) {

        $old            = $this->_filters;
        $this->_filters =   self::EMERGENCY
                          & self::ALERT
                          & self::CRITICAL
                          & self::ERROR
                          & self::WARNING
                          & self::NOTICE
                          & self::INFORMATIONAL
                          & self::DEBUG
                          & self::TEST;

        return $old;
    }

    /**
     * Add extra stack informations.
     *
     * @access  public
     * @param   array   $stackInfos    Stack informations.
     * @return  array
     */
    public function addStackInformations ( Array $stackInfos ) {

        foreach($stackInfos as $key => $value)
            $this->addStackInformation($key, $value);

        return $this->getAddedStackInformations();
    }

    /**
     * Add an extra stack information.
     *
     * @access  public
     * @param   string  $key      Information name.
     * @param   string  $value    Information value.
     * @return  array
     */
    public function addStackInformation ( $key, $value ) {

        $this->_stackInfos[$key] = $value;

        return $this->getAddedStackInformations();
    }

    /**
     * Get extra stack informations.
     *
     * @access  public
     * @return  array
     */
    public function getAddedStackInformations ( ) {

        return $this->_stackInfos;
    }

    /**
     * Log a message with a type.
     *
     * @access  public
     * @param   string  $message    The log message.
     * @param   int     $type       Type of message (please, see the class
     *                              constants).
     * @param   array   $extra      Extra dynamic informations.
     * @return  void
     */
    public function log ( $message, $type = self::DEBUG, $extra = array() ) {

        $filters = $this->getFilters();

        if(null !== $filters && !($type & $filters))
            return;

        $handle = $this->_stack[] = array_merge(
            array(
                self::STACK_TIMESTAMP   => microtime(true),
                self::STACK_MESSAGE     => $message,
                self::STACK_PRIORITY    => $type,
                self::STACK_MEMORY      => memory_get_usage(),
                self::STACK_MEMORY_PEAK => memory_get_peak_usage()
            ),
            $this->getAddedStackInformations(),
            $extra
        );

        Hoa_Core_Event::notify(
            'hoa://Event/Log/' . self::$_currentId,
            $this,
            new Hoa_Core_Event_Bucket(array('log' => $handle))
        );

        if($type & self::DEBUG) {

            if(null === $this->_backtrace)
                $this->_backtrace = new Hoa_Log_Backtrace();

            $this->_backtrace->debug();
        }

        return;
    }

    /**
     * Get filters.
     *
     * @access  protected
     * @return  int
     */
    public function getFilters ( ) {

        return $this->_filters;
    }

    /**
     * Get the backtrace tree.
     *
     * @access  public
     * @return  array
     */
    public function getBacktrace ( ) {

        return $this->_backtrace;
    }

    /**
     * Get the log stack.
     *
     * @access  public
     * @return  array
     */
    public function getLogStack ( ) {

        return $this->_stack;
    }

    /**
     * Transform a log type into a string.
     *
     * @access  public
     * @param   int     $type    Log type (please, see the class constants).
     * @return  string
     */
    public function typeAsString ( $type ) {

        switch($type) {

            case self::EMERGENCY:
                return 'EMERGENCY';
              break;

            case self::ALERT:
                return 'ALERT';
              break;

            case self::CRITICAL:
                return 'CRITICAL';
              break;

            case self::ERROR:
                return 'ERROR';
              break;

            case self::WARNING:
                return 'WARNING';
              break;

            case self::NOTICE:
                return 'NOTICE';
              break;

            case self::INFORMATIONAL:
                return 'INFORMATIONAL';
              break;

            case self::DEBUG:
                return 'DEBUG';
              break;

            default:
                return 'unknown';
        }
    }

    /**
     * Transform the log into a string.
     *
     * @access  public
     * @return  string
     */
    public function __toString ( ) {

        return $this->getBacktrace()->__toString();
    }
}


/**
 * Alias of Hoa_Log::getInstance()->log().
 *
 * @access  public
 * @param   string  $message    The log message.
 * @param   int     $type       Type of message (please, see the class
 *                              constants).
 * @param   array   $extra      Extra dynamic informations.
 * @return  void
 */
function hlog ( $message, $type = Hoa_Log::DEBUG, $extra = array() ) {

    return Hoa_Log::getChannel()->log($message, $type, $extra);
}
