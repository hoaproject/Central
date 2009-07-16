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
 * Copyright (c) 2007, 2008 Ivan ENDERLIN. All rights reserved.
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
 * Hoa_Framework
 */
require_once 'Framework.php';

/**
 * Hoa_Log_Exception
 */
import('Log.Exception');

/**
 * Hoa_Stream
 */
import('Stream.~');

/**
 * Hoa_Stream_Io_Out
 */
import('Stream.Io.Out');

/**
 * Class Hoa_Log.
 *
 * .
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2008 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Log
 */

class Hoa_Log {

    /**
     * Priority: emergency, system is unusable.
     * (Note: priorities from “The BSD Syslog Protocol” (RFC 3164, 4.1.1 PRI
     * Part).
     *
     * @const int
     */
    const EMERGENCY     =  0;

    /**
     * Priority: alert, action must be taken immediately.
     *
     * @const int
     */
    const ALERT         =  1;

    /**
     * Priority: critical, critical conditions.
     *
     * @const int
     */
    const CRITICAL      =  2;

    /**
     * Priority: error, error conditions.
     *
     * @const int
     */
    const ERROR         =  4;

    /**
     * Priority: warning, warning conditions.
     *
     * @const int
     */
    const WARNING       =  8;

    /**
     * Priority: notice, normal but significant condition.
     *
     * @const int
     */
    const NOTICE        = 16;

    /**
     * Priority: informational messages.
     *
     * @const int
     */
    const INFORMATIONAL = 32;

    /**
     * Priority: debut-level messages.
     *
     * @const int
     */
    const DEBUG         = 64;

    /**
     * Logs stack. The structure is:
     *     * timestamp;
     *     * message;
     *     * priority;
     *     * memory
     *     * memoryPeak
     *     * extra, dependent of the priority:
     *         - debug
     *
     * @var Hoa_Log array
     */
    protected $_stack  = array();

    /**
     * Output stream array.
     *
     * @var Hoa_Log array
     */
    protected $_output = array();

    /**
     * Filters (combination of priorities constants, null means all).
     *
     * @var Hoa_Log int
     */
    protected $_filter = null;



    /**
     *
     */
    public function __construct ( Hoa_Stream $stream ) {

        $this->addOutputStream($stream);
    }

    /**
     *
     *
     * @access  public
     * @param   Hoa_Stream  $stream    A stream (must implement the
     *                                 Hoa_Stream_Io_Out).
     * @return  array
     */
    public function addOutputStream ( Hoa_Stream $stream ) {

        if(!($stream instanceof Hoa_Stream_Io_Out))
            throw new Hoa_Log_Exception(
                'Stream log must implement the Hoa_Stream_Io_Out interface.', 0);

        if(false === $stream->isOpened())
            throw new Hoa_Log_Exception(
                'Stream log is not opened, maybe it failed.', 1);

        $this->_output[$stream->__toString()] = $stream;

        return $this->_output;
    }
}
