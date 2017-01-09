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

namespace Hoa\Database\Layer\Pdo;

use Hoa\Database;

/**
 * Class \Hoa\Database\Layer\Pdo\Iterator.
 *
 * Iterator Statement.
 *
 * @copyright  Copyright © 2007-2017 Hoa community
 * @license    New BSD License
 */
class Iterator implements Database\IDal\WrapperIterator
{
    /**
     * The statement instance.
     *
     * @var \PDOStatement
     */
    protected $_statement = null;

    /**
     * The fetching style options.
     *
     * @var array
     */
    protected $_style     = [];

    /**
     * The cursor row.
     *
     * @var array
     */
    protected $_row       = null;



    /**
     * Create an iterator instance.
     *
     * @param   object  $statement    The underlying statement instance.
     * @param   array   $style        An array of fetching style options.
     */
    public function __construct($statement, $style)
    {
        $this->_statement = $statement;
        $this->_style     = $style;

        switch ($style[Database\DalStatement::STYLE_MODE]) {
            case Database\DalStatement::AS_CLASS:
                $this->_statement->setFetchMode(
                    $style[Database\DalStatement::STYLE_MODE],
                    $style[Database\DalStatement::STYLE_CLASS_NAME],
                    $style[Database\DalStatement::STYLE_CONSTRUCTOR_ARGUMENTS]
                );

                break;

            case Database\DalStatement::AS_REUSABLE_OBJECT:
                $this->_statement->setFetchMode(
                    $style[Database\DalStatement::STYLE_MODE],
                    $style[Database\DalStatement::STYLE_OBJECT]
                );

                break;

            default:
                $this->_statement->setFetchMode(
                    $style[Database\DalStatement::STYLE_MODE]
                );
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
            $this->_style[Database\DalStatement::STYLE_ORIENTATION]
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
        if (Database\DalStatement::FROM_END === $this->_style[Database\DalStatement::STYLE_OFFSET]) {
            $this->_row = $this->getStatement()->fetch(
                null,
                \PDO::FETCH_ORI_LAST
            );
        } else {
            $this->_row = $this->getStatement()->fetch(
                null,
                \PDO::FETCH_ORI_ABS,
                $this->_style[Database\DalStatement::STYLE_OFFSET]
            );
        }

        return;
    }
}
