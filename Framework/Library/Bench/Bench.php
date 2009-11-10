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
 * @package     Hoa_Bench
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
 * Hoa_Bench_Mark
 */
import('Bench.Mark');

/**
 * Class Hoa_Bench.
 *
 * The Hoa_Bench class allows to manage marks easily, and to make some
 * statistics.
 * The Hoa_Bench class implements Iterator and Countable interfaces to iterate
 * marks, or count the number of marks.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2009 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Bench
 */

class Hoa_Bench implements Iterator, Countable {

    /**
     * Statistic : get the result.
     *
     * @const int
     */
    const STAT_RESULT   = 0;

    /**
     * Statistic : get the pourcent.
     *
     * @const int
     */
    const STAT_POURCENT = 1;

    /**
     * Collection of marks.
     *
     * @var Hoa_Bench array
     */
    protected static $_mark = array();



    /**
     * Get a mark.
     * If the mark does not exist, it will be automatically create.
     *
     * @access  public
     * @param   string  $id    The mark ID.
     * @return  Hoa_Bench_Mark
     * @throw   Hoa_Bench_Exception
     */
    public function __get ( $id ) {

        if(true === $this->markExists($id))
            return self::$_mark[$id];

        $mark = new Hoa_Bench_Mark($id);
        self::$_mark[$id] = $mark;

        return $mark;
    }

    /**
     * Check if a mark exists.
     * Alias of the protected markExist method.
     *
     * @access  public
     * @param   string  $id    The mark ID.
     * @return  bool
     */
    public function __isset ( $id ) {

        return $this->markExists($id);
    }

    /**
     * Destroy a mark.
     *
     * @access  public
     * @param   string  $id    The mark ID.
     * @return  void
     */
    public function __unset ( $id ) {

        unset(self::$_mark[$id]);
    }

    /**
     * Destroy all mark.
     *
     * @access  public
     * @return  void
     */
    public function unsetAll ( ) {

        self::$_mark = array();
    }

    /**
     * Check if a mark already exists.
     *
     * @access  protected
     * @param   string     $id    The mark ID.
     * @return  bool
     */
    protected function markExists ( $id ) {

        return isset(self::$_mark[$id]);
    }

    /**
     * Get the current mark for the iterator.
     *
     * @access  public
     * @return  Hoa_Bench_Mark
     */
    public function current ( ) {

        return current(self::$_mark);
    }

    /**
     * Get the current mark ID for the iterator.
     *
     * @access  public
     * @return  string
     */
    public function key ( ) {

        return key(self::$_mark);
    }

    /**
     * Advance the internal mark collection pointer, and return the current
     * mark.
     *
     * @access  public
     * @return  Hoa_Bench_Mark
     */
    public function next ( ) {

        return next(self::$_mark);
    }

    /**
     * Rewind the internal mark collection pointer, and return the first mark.
     *
     * @access  public
     * @return  Hoa_Bench_Mark
     */
    public function rewind ( ) {

        return reset(self::$_mark);
    }

    /**
     * Check if there is a current element after calls the rewind or the next
     * methods.
     *
     * @access  public
     * @return  bool
     */
    public function valid ( ) {

        if(empty(self::$_mark))
            return false;

        $key    = key(self::$_mark);
        $return = (next(self::$_mark) ? true : false);
        prev(self::$_mark);

        if(false === $return) {

            end(self::$_mark);
            if($key === key(self::$_mark))
                $return = true;
        }

        return $return;
    }

    /**
     * Get statistic.
     * Return an associative array : id => sub-array. The sub-array contains the
     * result time in second (given by the constant self::STAT_RESULT), and the
     * result pourcent (given by the constant self::START_POURCENT).
     *
     * @access  public
     * @return  array
     */
    public function getStatistic ( ) {

        if(empty(self::$_mark))
            return array();

        $max = $this->getLongest()->diff();
        $out = array();

        foreach($this as $id => $mark)
            $out[$id] = array(
                self::STAT_RESULT   =>  $mark->diff(),
                self::STAT_POURCENT => ($mark->diff() * 100) / $max
            );

        return $out;
    }

    /**
     * Get the maximum, i.e. the longest mark in time.
     *
     * @access  public
     * @return  Hoa_Bench_Mark
     */
    public function getLongest ( ) {

        $max     = 0;
        $outMark = null;

        foreach($this as $id => $mark)
            if($mark->diff() > $max) {

                $outMark = $mark;
                $max     = $mark->diff();
            }

        return $outMark;
    }

    /**
     * Draw statistic in text mode (yep, totally useless, but funny to
     * develop :D).
     *
     * @access  public
     * @param   int     $width    The graphic width.
     * @return  string
     * @throw   Hoa_Bench_Exception
     */
    public function drawStatistic ( $width = 70 ) {

        if(empty(self::$_mark))
            return null;

        if($width < 1)
            throw new Hoa_Bench_Exception(
                'The graphic width must be positive, given %d.', 0, $width);

        $out         = null;
        $stats       = $this->getStatistic();
        $idMaxLength = 0;

        foreach($stats as $id => $foo)
            strlen($id) > $idMaxLength and $idMaxLength = strlen($id);

        foreach($stats as $id => $stat)
            $out .= str_pad(
                        $id,
                        $idMaxLength
                    ) .
                    '  ' .
                    str_pad(
                        str_repeat(
                            '|',
                            round(($stat[self::STAT_POURCENT] * $width) / 100)
                        ),
                        $width
                    ) .
                    ' ' . round(1000 * $stat[self::STAT_RESULT], 3) . ' ms,' .
                    ' ' . round($stat[self::STAT_POURCENT], 3) . ' %' . "\n";

        return $out;
    }

    /**
     * Count the number of mark.
     *
     * @access  public
     * @return  int
     */
    public function count ( ) {

        return count(self::$_mark);
    }

    /**
     * Alias of drawStatistic() method.
     *
     * @access  public
     * @return  string
     */
    public function __toString ( ) {

        return $this->drawStatistic();
    }
}
