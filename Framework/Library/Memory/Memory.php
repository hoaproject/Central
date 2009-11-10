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
 * Copyright (c) 2007, 2009 Ivan ENDERLIN. All rights reserved.
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
 * @package     Hoa_Memory
 *
 */

/**
 * Hoa_Framework
 */
require_once 'Framework.php';

/**
 * Hoa_Memory_Exception
 */
import('Memory.Exception');

/**
 * Class Hoa_Memory.
 *
 * Work with memory.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2009 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Memory
 */

class Hoa_Memory {

    /**
     * Get the amount of memory allocated to PHP.
     *
     * @access  public
     * @param   bool    $realUsage    Set this to true to get the real size of
     *                                memory allocated from system; if not set
     *                                or false, only the memory used by emalloc()
     *                                is reported.
     * @return  int
     */
    public static function getUsage ( $realUsage = false ) {

        return memory_get_usage($realUsage);
    }

    /**
     * Get the peak of memory allocated by PHP.
     *
     * @access  public
     * @param   bool    $realUsage    Set this to true to get the real size of
     *                                memory allocated from system; if not set
     *                                or false, only the memory used by emalloc()
     *                                is reported.
     * @return  int
     */
    public static function getPeakUsage ( $realUsage = false ) {

        return memory_get_peak_usage($realUsage);
    }

    /**
     * Set the memory limit.
     *
     * @access  public
     * @param   mixed   $size    If an integer is given, the value is measured in
     *                           bytes. If a string is given, it must be a
     *                           shorthand notation (like '128M' for instance).
     * @return  mixed
     */
    public static function setLimit ( $size) {

        return ini_set('memory_limit', $size);
    }

    /**
     * Get the memory limit.
     *
     * @access  public
     * @return  mixed
     */
    public static function getLimit ( ) {

        return ini_get('memory_limit');
    }

    /**
     * Get the current resource usages.
     * Notes about returned array: it has the same indexes that the C structure
     * rusage, i.e.:
     *     ru_utime.tv_usec => user time used (in seconds);
     *     ru_stime.tv_usec => system time used (in microseconds);
     *     ru_stime.tv_sec  => system time used (in seconds);
     *     ru_maxrss        => integral max resident set size;
     *     ru_ixrss         => integral shared text memory size;
     *     ru_idrss         => integral unshared data size;
     *     ru_isrss         => integral unshared stack size;
     *     ru_minflt        => page reclaims;
     *     ru_majflt        => page faults;
     *     ru_nswap         => swaps;
     *     ru_inblock       => block input operations;
     *     ru_oublock       => block output operations;
     *     ru_msgsnd        => messages sent;
     *     ru_msgrcv        => messages received;
     *     ru_nsignals      => signals received;
     *     ru_nvcsw         => voluntary context switches;
     *     ru_nivcsw        => involuntary context switches.
     *
     * Please, see your system's man page on getrusage(2) to get more
     * informations. It is very interesting.
     *
     * @access  public
     * @param   int     $who    If set to 1, it will be called with
     *                          RUSAGE_CHILDREN.
     * @return  array
     * @throw   Hoa_Memory_Exception
     */
    public static function getRUsage ( $who = 0 ) {

        if(OS_WIN)
            throw new Hoa_Memory_Exception(
                'Cannot get the current resource usages on Windows.', 0);

        return getrusage($who);
    }
}
