<?php

/**
 * Hoa
 *
 *
 * @license
 *
 * New BSD License
 *
 * Copyright © 2007-2015, Hoa community. All rights reserved.
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

namespace Hoa\Database\Layer\Pdo;

use Hoa\Database;

/**
 * Class \Hoa\Database\Layer\Pdo\Iterator.
 *
 * Iterator Statement.
 *
 * @copyright  Copyright © 2007-2015 Hoa community
 * @license    New BSD License
 */
class Iterator implements Database\IDal\WrapperIterator
{
    /**
     * The statement instance.
     *
     * @var \PDOStatement
     */
    protected $_statement   = null;

    /**
     * The cursor orientation.
     *
     * @var int
     */
    protected $_orientation = 0;

    /**
     * The start cursor offset.
     *
     * @var int
     */
    protected $_offset      = 0;

    /**
     * The cursor row.
     *
     * @var array
     */
    protected $_row         = null;



    /**
     * Create an iterator instance.
     *
     * @param   \PDOStatement  $statement      The PDOStatement instance.
     * @param   int            $orientation    This value must be
     *                                         DalStatement::FORWARD or
     *                                         DalStatement::BACKWARD constant.
     * @param   int            $offset         This value can be one of the
     *                                         DalStatement::FROM_* constants
     *                                         or an arbitrary offset.
     * @param   int|array      $style          This value must be one of the
     *                                         DalStatement::AS_* constants.
     * @return  void
     */
    public function __construct(
        \PDOStatement $statement,
        $orientation,
        $offset,
        $style
    ) {
        $this->_statement   = $statement;
        $this->_orientation = $orientation;
        $this->_offset      = $offset;

        if (is_array($style)) {
            if (Database\DalStatement::AS_REUSABLE_OBJECT === $style[0]) {
                $this->getStatement()->setFetchMode(
                    $style[0],
                    $style[1]
                );
            }
            else { // Database\DalStatement::AS_CLASS
                $this->getStatement()->setFetchMode(
                    $style[0],
                    $style[1],
                    $style[2]
                );
            }
        } else {
            $this->getStatement()->setFetchMode($style);
        }

        return;
    }

    /**
     * Get the statement instance.
     *
     * @return  \PDOStatement
     */
    protected function getStatement()
    {
        return $this->_statement;
    }

    /**
     * Return the current element.
     *
     * @return  array
     */
    public function current()
    {
        return $this->_row;
    }

    /**
     * Move forward to next element.
     *
     * @return  void
     */
    public function next()
    {
        $this->_row = $this->getStatement()->fetch(
            null,
            $this->_orientation
        );

        return;
    }

    /**
     * Return always null.
     *
     * @return  null
     */
    public function key()
    {
        return null;
    }

    /**
     * Checks if current position is valid.
     *
     * @return  bool
     */
    public function valid()
    {
        return false !== $this->_row;
    }

    /**
     * Rewind the iterator to the first element.
     *
     * @return  void
     */
    public function rewind()
    {
        if (Database\DalStatement::FROM_END === $this->_offset) {
            $this->_row = $this->getStatement()->fetch(
                null,
                \PDO::FETCH_ORI_LAST
            );
        } else {
            $this->_row = $this->getStatement()->fetch(
                null,
                \PDO::FETCH_ORI_ABS,
                $this->_offset
            );
        }

        return;
    }
}
