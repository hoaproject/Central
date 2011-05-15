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

namespace {

from('Hoa')

/**
 * \Hoa\Worker\Exception
 */
-> import('Worker.Exception');

}

namespace Hoa\Worker {

/**
 * Class \Hoa\Worker\Run.
 *
 * Manipulate .wid files.
 *
 * @author     Ivan Enderlin <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright © 2007-2011 Ivan Enderlin.
 * @license    New BSD License
 */

class Run {

    /**
     * Register a socketable object as a worker ID.
     *
     * @access  public
     * @param   string                  $workerId    Worker ID.
     * @param   \Hoa\Socket\Socketable  $socket      Socketable object.
     * @return  bool
     * @throw   \Hoa\Worker\Exception
     */
    public static function register ( $workerId, \Hoa\Socket\Socketable $socket ) {

        if(true === self::widExists($workerId))
            throw new Exception(
                'Worker ID %s already exists, we cannot create it again.',
                0, $workerId);

        file_put_contents(static::find($workerId), serialize($socket));

        return true;
    }

    /**
     * Unregister a worker ID.
     *
     * @access  public
     * @param   string  $workerId    Worker ID.
     * @return  bool
     */
    public static function unregister ( $workerId ) {

        if(false === self::widExists($workerId))
            return true;

        return @unlink(static::find($workerId));
    }

    /**
     * Get a worker ID data (i.e. a socketable object).
     *
     * @access  public
     * @param   string  $workerId    Worker ID.
     * @return  \Hoa\Socket\Socketable
     */
    public static function get ( $workerId ) {

        if(false === self::widExists($workerId))
            throw new Exception(
                'Worker ID %s does not exist.', 1, $workerId);

        return unserialize(file_get_contents(static::find($workerId)));
    }

    /**
     * Check if a .wid exists.
     *
     * @access  public
     * @param   string  $workerId    Worker ID.
     * @return  bool
     */
    public static function widExists ( $workerId ) {

        return true === file_exists(static::find($workerId));
    }

    /**
     * Find a .wid.
     *
     * @access  public
     * @param   string  $workerId    Worker ID.
     * @return  string
     * @throw   \Hoa\Worker\Exception
     */
    public static function find ( $workerId ) {

        if(   false !== strpos($workerId, '/')
           || false !== strpos($workerId, '\\'))
            throw new Exception(
                'Worker ID must not contain / or \ character; given %s.',
                2, $workerId);

        return 'hoa://Data/Variable/Run/' . $workerId . '.wid';
    }
}

}
