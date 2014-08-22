<?php

/**
 * Hoa
 *
 *
 * @license
 *
 * New BSD License
 *
 * Copyright © 2007-2014, Ivan Enderlin. All rights reserved.
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
 * \Hoa\Bench\Exception
 */
-> import('Bench.Exception')

/**
 * \Hoa\Bench\Mark
 */
-> import('Bench.Mark');

}

namespace Hoa\Bench {

/**
 * Class \Hoa\Bench.
 *
 * The \Hoa\Bench class allows to manage marks easily, and to make some
 * statistics.
 * The \Hoa\Bench class implements Iterator and Countable interfaces to iterate
 * marks, or count the number of marks.
 *
 * @author     Ivan Enderlin <ivan.enderlin@hoa-project.net>
 * @author     Julien Clauzel <julien.clauzel@hoa-project.net>
 * @copyright  Copyright © 2007-2014 Ivan Enderlin, Julien Clauzel.
 * @license    New BSD License
 */

class Bench implements \Iterator, \Countable {

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
     * @var \Hoa\Bench array
     */
    protected static $_mark = array();

    /**
     * Collection of filters.
     *
     * @var \Hoa\Bench array
     */
    protected $_filters     = array();



    /**
     * Get a mark.
     * If the mark does not exist, it will be automatically create.
     *
     * @access  public
     * @param   string  $id    The mark ID.
     * @return  \Hoa\Bench\Mark
     * @throw   \Hoa\Bench\Exception
     */
    public function __get ( $id ) {

        if(true === empty(self::$_mark)) {

            $global                  = new Mark('__global');
            self::$_mark['__global'] = $global;
            $global->start();
        }

        if(true === $this->markExists($id))
            return self::$_mark[$id];

        $mark = new Mark($id);
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

        return;
    }

    /**
     * Destroy all mark.
     *
     * @access  public
     * @return  void
     */
    public function unsetAll ( ) {

        self::$_mark = array();

        return;
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
     * @return  \Hoa\Bench\Mark
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
     * @return  \Hoa\Bench\Mark
     */
    public function next ( ) {

        return next(self::$_mark);
    }

    /**
     * Rewind the internal mark collection pointer, and return the first mark.
     *
     * @access  public
     * @return  \Hoa\Bench\Mark
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
     * Add a filter.
     * Used in the self::getStatistic() method, no in iterator.
     * A filter is a callable that will receive 3 values about a mark: ID, time
     * result, and time pourcent. The callable must return a boolean.
     *
     * @access  public
     * @param   mixed  $callable    Callable.
     * @return  void
     */
    public function filter ( $callable ) {

        $this->_filters[] = xcallable($callable);

        return $this;
    }

    /**
     * Return all filters.
     *
     * @access  public
     * @return  array
     */
    public function getFilters ( ) {

        return $this->_filters;
    }

    /**
     * Get statistic.
     * Return an associative array : id => sub-array. The sub-array contains the
     * result time in second (given by the constant self::STAT_RESULT), and the
     * result pourcent (given by the constant self::START_POURCENT).
     *
     * @access  public
     * @param   bool  $considerFilters    Whether we should consider filters or
     *                                    not.
     * @return  array
     */
    public function getStatistic ( $considerFilters = true ) {

        if(empty(self::$_mark))
            return array();

        $max = $this->getLongest()->diff();
        $out = array();

        foreach($this as $id => $mark) {

            $result   = $mark->diff();
            $pourcent = ($result * 100) / $max;

            if(true === $considerFilters)
                foreach($this->getFilters() as $filter)
                    if(true !== $filter($id, $result, $pourcent))
                        continue 2;

            $out[$id] = array(
                self::STAT_RESULT   => $result,
                self::STAT_POURCENT => $pourcent
            );
        }

        return $out;
    }

    /**
     * Get the maximum, i.e. the longest mark in time.
     *
     * @access  public
     * @return  \Hoa\Bench\Mark
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
     * Draw statistic in text mode.
     *
     * @access  public
     * @param   int  $width    The graphic width.
     * @return  string
     * @throw   \Hoa\Bench\Exception
     */
    public function drawStatistic ( $width = 80 ) {

        if(empty(self::$_mark))
            return '';

        if($width < 1)
            throw new Exception(
                'The graphic width must be positive, given %d.', 0, $width);

        $out    = null;
        $stats  = $this->getStatistic();
        $margin = 0;

        foreach($stats as $id => $foo)
            strlen($id) > $margin and $margin = strlen($id);

        $width   = $width - $margin - 18;
        $format  = '%-' . $margin . 's  %-' . $width . 's %5dms, %5.1f%%' . "\n";

        foreach($stats as $id => $stat)
            $out .= sprintf(
                $format,
                $id,
                str_repeat(
                    '|', round(($stat[self::STAT_POURCENT] * $width) / 100)
                ),
                round(1000 * $stat[self::STAT_RESULT]),
                round($stat[self::STAT_POURCENT], 3)
            );

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

}

namespace {

/**
 * Flex entity.
 */
Hoa\Core\Consistency::flexEntity('Hoa\Bench\Bench');

}
