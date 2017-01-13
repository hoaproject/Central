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

namespace Hoa\Log;

use Hoa\Consistency;

/**
 * Class \Hoa\Log.
 *
 * Propose a log system.
 *
 * @copyright  Copyright © 2007-2017 Hoa community
 * @license    New BSD License
 */
class Log implements Event\Source
{
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
     * @var array
     */
    private static $_instances = null;

    /**
     * Current singleton index.
     *
     * @var string
     */
    private static $_currentId = null;

    /**
     * Logs stack.
     *
     * @var array
     */
    protected $_stack          = [];

    /**
     * Backtrace.
     *
     * @var \Hoa\Log\Backtrace
     */
    protected $_backtrace      = null;

    /**
     * Filters (combination of priorities constants, null means all).
     *
     * @var int
     */
    protected $_filters        = null;

    /**
     * Extra stack informations.
     *
     * @var array
     */
    protected $_stackInfos     = [];



    /**
     * Build a new log system.
     *
     */
    private function __construct()
    {
        return;
    }

    /**
     * Make a multiton.
     *
     * @param   string      $id        Channel ID (i.e. singleton ID)
     * @return  \Hoa\Log
     * @throws  \Hoa\Log\Exception
     */
    public static function getChannel($id = null)
    {
        if (null === self::$_currentId && null === $id) {
            throw new Exception(
                'Must precise a singleton index once.', 0);
        }

        if (!isset(self::$_instances[$id])) {
            self::$_instances[$id] = new self();
            Event::register(
                'hoa://Event/Log/' . $id,
                self::$_instances[$id]
            );
        }

        if (null !== $id) {
            self::$_currentId = $id;
        }

        $handle = self::$_instances[self::$_currentId];

        return $handle;
    }

    /**
     * Accept a type of log on the outputstream.
     *
     * @param   int     $filter    A filter (please, see the class constants).
     * @return  int
     */
    public function accept($filter)
    {
        if (null === $this->_filters) {
            return $this->_filters = $filter;
        }

        $old            = $this->_filters;
        $this->_filters = $old | $filter;

        return $old;
    }

    /**
     * Accept all types of logs on the outputstream.
     *
     * @return  int
     */
    public function acceptAll()
    {
        $old            = $this->_filters;
        $this->_filters = null;

        return $old;
    }

    /**
     * Set filters directly.
     *
     * @param   int     $filter    A filter (please, see the class constants).
     * @return  int
     */
    public function setFilter($filter)
    {
        $old            = $this->_filters;
        $this->_filters = $filter;

        return $old;
    }

    /**
     * Drop a type of log on the outputstream.
     *
     * @param   int     $filter    A filter (please, see the class constants).
     * @return  int
     */
    public function drop($filter)
    {
        $old            = $this->_filters;
        $this->_filters = $old & ~$filter;

        return $old;
    }

    /**
     * Drop all type of logs on the outputstreams.
     *
     * @return  int
     */
    public function dropAll()
    {
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
     * @param   array   $stackInfos    Stack informations.
     * @return  array
     */
    public function addStackInformations(array $stackInfos)
    {
        foreach ($stackInfos as $key => $value) {
            $this->addStackInformation($key, $value);
        }

        return $this->getAddedStackInformations();
    }

    /**
     * Add an extra stack information.
     *
     * @param   string  $key      Information name.
     * @param   string  $value    Information value.
     * @return  array
     */
    public function addStackInformation($key, $value)
    {
        $this->_stackInfos[$key] = $value;

        return $this->getAddedStackInformations();
    }

    /**
     * Get extra stack informations.
     *
     * @return  array
     */
    public function getAddedStackInformations()
    {
        return $this->_stackInfos;
    }

    /**
     * Log a message with a type.
     *
     * @param   string  $message    The log message.
     * @param   int     $type       Type of message (please, see the class
     *                              constants).
     * @param   array   $extra      Extra dynamic informations.
     * @return  void
     */
    public function log($message, $type = self::DEBUG, $extra = [])
    {
        $filters = $this->getFilters();

        if (null !== $filters && !($type & $filters)) {
            return;
        }

        $handle = $this->_stack[] = array_merge(
            [
                self::STACK_TIMESTAMP   => microtime(true),
                self::STACK_MESSAGE     => $message,
                self::STACK_PRIORITY    => $type,
                self::STACK_MEMORY      => memory_get_usage(),
                self::STACK_MEMORY_PEAK => memory_get_peak_usage()
            ],
            $this->getAddedStackInformations(),
            $extra
        );

        Event::notify(
            'hoa://Event/Log/' . self::$_currentId,
            $this,
            new Event\Bucket(['log' => $handle])
        );

        if ($type & self::DEBUG) {
            if (null === $this->_backtrace) {
                $this->_backtrace = new Backtrace();
            }

            $this->_backtrace->debug();
        }

        return;
    }

    /**
     * Get filters.
     *
     * @return  int
     */
    public function getFilters()
    {
        return $this->_filters;
    }

    /**
     * Get the backtrace tree.
     *
     * @return  array
     */
    public function getBacktrace()
    {
        return $this->_backtrace;
    }

    /**
     * Get the log stack.
     *
     * @return  array
     */
    public function getLogStack()
    {
        return $this->_stack;
    }

    /**
     * Transform a log type into a string.
     *
     * @param   int     $type    Log type (please, see the class constants).
     * @return  string
     */
    public function typeAsString($type)
    {
        switch ($type) {

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
     * @return  string
     */
    public function __toString()
    {
        return $this->getBacktrace()->__toString();
    }
}

/**
 * Flex entity.
 */
Consistency::flexEntity('Hoa\Log\Log');
