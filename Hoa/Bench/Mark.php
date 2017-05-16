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

namespace Hoa\Bench;

/**
 * Class \Hoa\Bench\Mark.
 *
 * The \Hoa\Bench class contains a collection of \Hoa\Bench\Mark.
 * Each mark can be start, pause, stop, reset, or compare to an other mark.
 *
 * @copyright  Copyright © 2007-2017 Hoa community
 * @license    New BSD License
 */
class Mark
{
    /**
     * Name of the global mark.
     *
     * @const string
     */
    const GLOBAL_NAME = '__global__';

    /**
     * Mark ID.
     *
     * @var string
     */
    protected $_id      = null;

    /**
     * Start time.
     *
     * @var float
     */
    protected $start    = 0.0;

    /**
     * Stop time.
     *
     * @var float
     */
    protected $stop     = 0.0;

    /**
     * Addition of pause time.
     *
     * @var float
     */
    protected $pause    = 0.0;

    /**
     * Whether the mark is started.
     *
     * @var bool
     */
    protected $_started = false;

    /**
     * Whether the mark is in pause.
     *
     * @var bool
     */
    protected $_pause   = false;



    /**
     * Built a mark (and set the ID).
     *
     * @param   string  $id    The mark ID.
     */
    public function __construct($id)
    {
        $this->setId($id);

        return;
    }

    /**
     * Set the mark ID.
     *
     * @param   string     $id    The mark ID.
     * @return  string
     */
    protected function setId($id)
    {
        $old       = $this->_id;
        $this->_id = $id;

        return $old;
    }

    /**
     * Get the mark ID.
     *
     * @return  string
     */
    public function getId()
    {
        return $this->_id;
    }

    /**
     * Start the mark.
     * A mark can be started if it is in pause, stopped, or if it is the first start.
     * Else, an exception will be thrown.
     *
     * @return  \Hoa\Bench\Mark
     * @throws  \Hoa\Bench\Exception
     */
    public function start()
    {
        if (true === $this->isStarted()) {
            if (false === $this->isPause()) {
                throw new Exception(
                    'Cannot start the %s mark, because it is started.',
                    0,
                    $this->getId()
                );
            }
        }

        if (true === $this->isPause()) {
            $this->pause += microtime(true) - $this->stop;
        } else {
            $this->reset();
            $this->start  = microtime(true);
        }

        $this->_started = true;
        $this->_pause   = false;

        return $this;
    }

    /**
     * Stop the mark.
     * A mark can be stopped if it is in pause, or started. Else, an exception
     * will be thrown (or not, according to the $silent argument).
     *
     * @param   bool    $silent    If set to true and if the mark is not started,
     *                             no exception will be thrown.
     * @return  \Hoa\Bench\Mark
     * @throws  \Hoa\Bench\Exception
     */
    public function stop($silent = false)
    {
        if (false === $this->isStarted()) {
            if (false === $silent) {
                throw new Exception(
                    'Cannot stop the %s mark, because it is not started.',
                    1,
                    $this->getId()
                );
            } else {
                return $this;
            }
        }

        $this->stop     = microtime(true);
        $this->_started = false;
        $this->_pause   = false;

        return $this;
    }

    /**
     * Reset the mark.
     *
     * @return  \Hoa\Bench\Mark
     */
    public function reset()
    {
        $this->start    = 0.0;
        $this->stop     = 0.0;
        $this->pause    = 0.0;
        $this->_started = false;
        $this->_pause   = false;

        return $this;
    }

    /**
     * Pause the mark.
     * A mark can be in pause if it is started. Else, an exception will be
     * thrown (or not, according to the $silent argument).
     *
     * @param   bool    $silent    If set to true and the mark is not started,
     *                             no exception will be throw. Idem if the mark
     *                             is in pause.
     * @return  \Hoa\Bench\Mark
     * @throws  \Hoa\Bench\Exception
     */
    public function pause($silent = false)
    {
        if (false === $this->isStarted()) {
            if (false === $silent) {
                throw new Exception(
                    'Cannot stop the %s mark, because it is not started.',
                    2,
                    $this->getId()
                );
            } else {
                return $this;
            }
        }

        if (true  === $this->isPause()) {
            if (false === $silent) {
                throw new Exception(
                    'The %s mark is still in pause. Cannot pause it again.',
                    3,
                    $this->getId()
                );
            } else {
                return $this;
            }
        }

        $this->stop   = microtime(true);
        $this->_pause = true;

        return $this;
    }

    /**
     * Get the difference between $stop and $start.
     * If the mark is still running (it contains the pause case), the current
     * microtime  will be used in stay of $stop.
     *
     * @return  float
     */
    public function diff()
    {
        if (false === $this->isStarted() || true === $this->isPause()) {
            return $this->stop - $this->start - $this->pause;
        }

        return microtime(true) - $this->start - $this->pause;
    }

    /**
     * Compare to mark.
     * $a op $b : return -1 if $a < $b, 0 if $a == $b, and 1 if $a > $b. We
     * compare the difference between $start and $stop, i.e. we call the diff()
     * method.
     *
     * @param   \Hoa\Bench\Mark  $mark    The mark to compare to.
     * @return  int
     */
    public function compareTo(Mark $mark)
    {
        $a = $this->diff();
        $b = $mark->diff();

        if ($a < $b) {
            return -1;
        } elseif ($a == $b) {
            return 0;
        } else {
            return 1;
        }
    }

    /**
     * Check if the mark is running.
     *
     * @deprecated use `isStarted` instead
     * @return  bool
     */
    public function isRunning()
    {
        return $this->isStarted();
    }

    /**
     * Check if the mark is started.
     *
     * @return  bool
     */
    public function isStarted()
    {
        return $this->_started;
    }

    /**
     * Check if the mark is in pause.
     *
     * @return  bool
     */
    public function isPause()
    {
        return $this->_pause;
    }

    /**
     * Alias of the diff() method, but return a string, not a float.
     *
     * @return  string
     */
    public function __toString()
    {
        return (string) $this->diff();
    }
}
