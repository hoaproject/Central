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
 * @package     Hoa_Bench
 * @subpackage  Hoa_Bench_Mark
 *
 */

/**
 * Hoa_Framework
 */
require_once 'Framework.php';

/**
 * Hoa_Bench_Exception
 */
import('Bench.Exception');

/**
 * Class Hoa_Bench_Mark.
 *
 * The Hoa_Bench class contains a collection of Hoa_Bench_Mark.
 * Each mark can be start, pause, stop, reset, or compare to an other mark.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2008 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Bench
 * @subpackage  Hoa_Bench_Mark
 */

class Hoa_Bench_Mark {

    /**
     * Mark ID.
     *
     * @var Hoa_Bench_Mark string
     */
    protected $_id      = null;

    /**
     * Start time.
     *
     * @var Hoa_Bench_Mark float
     */
    protected $start    = 0.0;

    /**
     * Stop time.
     *
     * @var Hoa_Bench_Mark float
     */
    protected $stop     = 0.0;

    /**
     * Addition of pause time.
     *
     * @var Hoa_Bench_Mark float
     */
    protected $pause    = 0.0;

    /**
     * Whether the mark is running.
     *
     * @var Hoa_Bench_Mark bool
     */
    protected $_running = false;

    /**
     * Whether the mark is in pause.
     *
     * @var Hoa_Bench_Mark bool
     */
    protected $_pause   = false;



    /**
     * Built a mark (and set the ID).
     *
     * @access  public
     * @param   string  $id    The mark ID.
     * @return  void
     */
    public function __construct ( $id ) {

        $this->setId($id);
    }

    /**
     * Set the mark ID.
     *
     * @access  protected
     * @param   string     $id    The mark ID.
     * @return  string
     */
    protected function setId ( $id ) {

        $old       = $this->_id;
        $this->_id = $id;

        return $old;
    }

    /**
     * Get the mark ID.
     *
     * @access  public
     * @return  string
     */
    public function getId ( ) {

        return $this->_id;
    }

    /**
     * Start the mark.
     * A mark can be started if it is in pause, stopped, or if it is the first start.
     * Else, an exception will be thrown.
     *
     * @access  public
     * @return  Hoa_Bench_Mark
     * @throw   Hoa_Bench_Exception
     */
    public function start ( ) {

        if(true === $this->isRunning())
            if(false === $this->isPause())
                throw new Hoa_Bench_Exception(
                    'Cannot start the %s mark, because it is running.',
                    0, $this->getId());

        if(true === $this->isPause())
            $this->pause += microtime(true) - $this->stop;
        else {

            $this->reset();
            $this->start  = microtime(true);
        }

        $this->_running   = true;
        $this->_pause     = false;

        return $this;
    }

    /**
     * Stop the mark.
     * A mark can be stopped if it is in pause, or started. Else, an exception
     * will be thrown (or not, according to the $silent argument).
     *
     * @access  public
     * @param   bool    $silent    If set to true and if the mark is not running,
     *                             no exception will be thrown.
     * @return  Hoa_Bench_Mark
     * @throw   Hoa_Bench_Exception
     */
    public function stop ( $silent = false ) {

        if(false === $this->isRunning() && false === $silent)
            throw new Hoa_Bench_Exception(
                'Cannot stop the %s mark, because it is not running.',
                1, $this->getId());

        if(false === $this->isRunning())
            return $this;

        $this->stop     = microtime(true);
        $this->_running = false;
        $this->_pause   = false;

        return $this;
    }

    /**
     * Reset the mark.
     *
     * @access  public
     * @return  Hoa_Bench_Mark
     */
    public function reset ( ) {

        $this->start    = 0.0.
        $this->stop     = 0.0;
        $this->pause    = 0.0;
        $this->_running = false;
        $this->_pause   = false;

        return $this;
    }

    /**
     * Pause the mark.
     * A mark can be in pause if it is started. Else, an exception will be
     * thrown (or not, according to the $silent argument).
     *
     * @access  public
     * @param   bool    $silent    If set to true and the mark is not running,
     *                             no exception will be throw. Idem if the mark
     *                             is in pause.
     * @return  Hoa_Bench_Mark
     * @throw   Hoa_Bench_Exception
     */
    public function pause ( $silent = false ) {

        if(false === $this->isRunning() && false === $silent)
            throw new Hoa_Bench_Exception(
                'Cannot stop the %s mark, because it is not running.',
                2, $this->getId());

        if(false === $this->isRunning())
            return $this;

        if(true  === $this->isPause()   && false === $silent)
            throw new Hoa_Bench_Exception(
                'The %s mark is still in pause. Cannot pause it again.',
                3, $this->getId());

        if(true  === $this->isPause())
            return $this;

        $this->stop   = microtime(true);
        $this->_pause = true;

        return $this;
    }

    /**
     * Get the difference between $stop and $start.
     * If the mark is still running (it contains the pause case), the current
     * microtime  will be used in stay of $stop.
     *
     * @access  public
     * @return  float
     */
    public function diff ( ) {

        if(false === $this->isRunning() || true === $this->isPause())
            return $this->stop - $this->start - $this->pause;

        return microtime(true) - $this->start - $this->pause;
    }

    /**
     * Compare to mark.
     * $a op $b : return -1 if $a < $b, 0 if $a == $b, and 1 if $a > $b. We
     * compare the difference between $start and $stop, i.e. we call the diff()
     * method.
     *
     * @access  public
     * @param   Hoa_Bench_Mark  $mark    The mark to compare to.
     * @return  int
     */
    public function compareTo ( Hoa_Bench_Mark $mark ) {

        $a = $this->diff();
        $b = $mark->diff();

        if($a < $b)
            return -1;

        elseif($a == $b)
            return 0;

        else
            return 1;
    }

    /**
     * Check if the mark is running.
     *
     * @access  public
     * @return  bool
     */
    public function isRunning ( ) {

        return $this->_running;
    }

    /**
     * Check if the mark is in pause.
     *
     * @access  public
     * @return  bool
     */
    public function isPause ( ) {

        return $this->_pause;
    }

    /**
     * Alias of the diff() method, but return a string, not a float.
     *
     * @access  public
     * @return  string
     */
    public function __toString ( ) {

        return (string) $this->diff();
    }
}
