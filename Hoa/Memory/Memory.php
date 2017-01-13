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

namespace {

from('Hoa')

/**
 * \Hoa\Memory\Exception
 */
-> import('Memory.Exception');

}

namespace Hoa\Memory {

/**
 * Class \Hoa\Memory.
 *
 * Work with memory.
 *
 * @copyright  Copyright © 2007-2017 Hoa community
 * @license    New BSD License
 */
class Memory
{
    /**
     * Get the amount of memory allocated to PHP.
     *
     * @param   bool    $realUsage    Set this to true to get the real size of
     *                                memory allocated from system; if not set
     *                                or false, only the memory used by emalloc()
     *                                is reported.
     * @return  int
     */
    public static function getUsage($realUsage = false)
    {
        return memory_get_usage($realUsage);
    }

    /**
     * Get the peak of memory allocated by PHP.
     *
     * @param   bool    $realUsage    Set this to true to get the real size of
     *                                memory allocated from system; if not set
     *                                or false, only the memory used by emalloc()
     *                                is reported.
     * @return  int
     */
    public static function getPeakUsage($realUsage = false)
    {
        return memory_get_peak_usage($realUsage);
    }

    /**
     * Set the memory limit.
     *
     * @param   mixed   $size    If an integer is given, the value is measured in
     *                           bytes. If a string is given, it must be a
     *                           shorthand notation (like '128M' for instance).
     * @return  mixed
     */
    public static function setLimit($size)
    {
        return ini_set('memory_limit', $size);
    }

    /**
     * Get the memory limit.
     *
     * @return  mixed
     */
    public static function getLimit()
    {
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
     * @param   int     $who    If set to 1, it will be called with
     *                          RUSAGE_CHILDREN.
     * @return  array
     * @throws  \Hoa\Memory\Exception
     */
    public static function getRUsage($who = 0)
    {
        if (OS_WIN) {
            throw new Exception(
                'Cannot get the current resource usages on Windows.', 0);
        }

        return getrusage($who);
    }
}

}

namespace {

/**
 * Flex entity.
 */
Hoa\Consistency::flexEntity('Hoa\Memory\Memory');

}
