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

namespace Hoa\Database;

/**
 * Class \Hoa\Database\DalStatement.
 *
 * The higher class that represents a DAL statement.
 *
 * @copyright  Copyright © 2007-2017 Hoa community
 * @license    New BSD License
 */
class DalStatement implements IDal\WrapperStatement
{
    /**
     * Start at the first offset.
     *
     * @var int
     */
    const FROM_START                  = 0;

    /**
     * Start at the last offset.
     *
     * @var int
     */
    const FROM_END                    = -1;

    /**
     * Fetch the next row in the result set.
     *
     * @var int
     */
    const FORWARD                     = 0;

    /**
     * Fetch the previous row in the result set.
     *
     * @var int
     */
    const BACKWARD                    = 1;

    /**
     * Specifies that the fetch method shall return each row as an object with
     * variable names that correspond to the column names returned in the result
     * set. `AS_LAZY_OBJECT` creates the object variable names as they are
     * accessed.
     *
     * @var int
     */
    const AS_LAZY_OBJECT              = 1;

    /**
     * Specifies that the fetch method shall return each row as an array indexed
     * by column name as returned in the corresponding result set. If the result
     * set contains multiple columns with the same name, `AS_MAP` returns only a
     * single value per column name.
     *
     * @var int
     */
    const AS_MAP                      = 2;

    /**
     * Specifies that the fetch method shall return each row as an array indexed
     * by column number as returned in the corresponding result set, starting at
     * column 0.
     *
     * @var int
     */
    const AS_SET                      = 3;

    /**
     * Specifies that the fetch method shall return each row as an object with
     * property names that correspond to the column names returned in the result
     * set.
     *
     * @var int
     */
    const AS_OBJECT                   = 5;

    /**
     * Specifies that the fetch method shall return a new instance of the
     * requested class, mapping the columns to named properties in the class.
     * The magic `__set` method is called if the property doesn't exist in the
     * requested class.
     *
     * @var int
     */
    const AS_CLASS                    = 8;

    /**
     * Specifies that the fetch method shall update an existing instance of the
     * requested class, mapping the columns to named properties in the class.
     *
     * @var int
     */
    const AS_REUSABLE_OBJECT          = 9;

    /**
     * Specifies that the fetch method shall return each row as an array indexed
     * by column name as returned in the corresponding result set. If the result
     * set contains multiple columns with the same name, `AS_DEBUG_MAP` returns
     * an array of values per column name.
     *
     * @var int
     */
    const AS_DEBUG_MAP                = 11;

    /**
     * The start cursor offset.
     *
     * @var int
     */
    const STYLE_OFFSET                = 0;

    /**
     * The cursor orientation.
     *
     * @var int
     */
    const STYLE_ORIENTATION           = 1;

    /**
     * The fetching style.
     *
     * @var int
     */
    const STYLE_MODE                  = 2;

    /**
     * The class name for `AS_CLASS`.
     *
     * @var int
     */
    const STYLE_CLASS_NAME            = 3;

    /**
     * The constructor arguments for `AS_CLASS`.
     *
     * @var int
     */
    const STYLE_CONSTRUCTOR_ARGUMENTS = 4;

    /**
     * The reused object for `AS_REUSABLE_OBJECT`.
     *
     * @var int
     */
    const STYLE_OBJECT                = 5;

    /**
     * The statement instance.
     *
     * @var \Hoa\Database\IDal\WrapperStatement
     */
    protected $statement = null;



    /**
     * Create a statement instance.
     *
     * @param   \Hoa\Database\IDal\WrapperStatement  $statement    The
     *                                                             statement
     *                                                             instance.
     */
    public function __construct(IDal\WrapperStatement $statement)
    {
        $this->setStatement($statement);

        return;
    }

    /**
     * Set the statement instance.
     *
     * @param   \Hoa\Database\IDal\WrapperStatement  $statement    The
     *                                                             statement
     *                                                             instance.
     * @return  \Hoa\Database\IDal\WrapperStatement
     */
    protected function setStatement(IDal\WrapperStatement $statement)
    {
        $old             = $this->statement;
        $this->statement = $statement;

        return $old;
    }

    /**
     * Get the statement instance.
     *
     * @return  \Hoa\Database\IDal\WrapperStatement
     */
    protected function getStatement()
    {
        return $this->statement;
    }

    /**
     * Execute a prepared statement.
     *
     * @param   array   $bindParameters    Bind parameters values if bindParam is
     *                                     not called.
     * @return  \Hoa\Database\DalStatement
     * @throws  \Hoa\Database\Exception
     */
    public function execute(array $bindParameters = [])
    {
        if (empty($bindParameters)) {
            return $this->getStatement()->execute();
        }

        $this->getStatement()->execute($bindParameters);

        return $this;
    }

    /**
     * Bind a parameter to te specified variable name.
     *
     * @param   mixed   $parameter    Parameter name.
     * @param   mixed   $value        Parameter value.
     * @param   int     $type         Type of value.
     * @param   int     $length       Length of data type.
     * @return  bool
     * @throws  \Hoa\Database\Exception
     */
    public function bindParameter(
        $parameter,
        &$value,
        $type = null,
        $length = null
    ) {
        if (null === $type) {
            return $this->getStatement()->bindParameter($parameter, $value);
        }

        if (null === $length) {
            return $this->getStatement()->bindParameter(
                $parameter,
                $value,
                $type
            );
        }

        return $this->getStatement()->bindParameter(
            $parameter,
            $value,
            $type,
            $length
        );
    }

    /**
     * Return an array containing all of the result set rows.
     *
     * @return  array
     * @throws  \Hoa\Database\Exception
     */
    public function fetchAll()
    {
        return $this->getStatement()->fetchAll();
    }

    /**
     * Set the iterator fetching style.
     *
     * @param   int    $offset         This value must be one of the
     *                                 DalStatement::FROM_* constants or an
     *                                 arbitrary offset.
     * @param   int    $orientation    This value must be DalStatement::FORWARD
     *                                 or DalStatement::BACKWARD constant.
     * @param   int    $style          This value must be one of the
     *                                 DalStatement::AS_* constants.
     * @param   mixed  $arg1           For AS_CLASS: The class name.
     *                                 For AS_REUSABLE_OBJECT: An object.
     * @param   array  $arg2           For AS_CLASS: Constructor arguments.
     * @return  \Hoa\Database\DalStatement
     */
    public function setFetchingStyle(
        $offset      = self::FROM_START,
        $orientation = self::FORWARD,
        $style       = self::AS_MAP,
        $arg1        = null,
        $arg2        = null
    ) {
        $this->getStatement()->setFetchingStyle(
            $offset,
            $orientation,
            $style,
            $arg1,
            $arg2
        );

        return $this;
    }

    /**
     * Get an Iterator.
     *
     * @return  \Hoa\Database\IDal\WrapperIterator
     */
    public function getIterator()
    {
        return $this->getStatement()->getIterator();
    }

    /**
     * Fetch the first row in the result set.
     *
     * @param   int  $style    Must be one of the DalStatement::AS_* constants.
     * @return  mixed
     */
    public function fetchFirst($style = null)
    {
        return $this->getStatement()->fetchFirst($style);
    }

    /**
     * Fetch the last row in the result set.
     *
     * @param   int  $style    Must be one of the DalStatement::AS_* constants.
     * @return  mixed
     */
    public function fetchLast($style = null)
    {
        return $this->getStatement()->fetchLast($style);
    }

    /**
     * Return a single column from the next row of the result set or false if
     * there is no more row.
     *
     * @param   int  $column    Column index.
     * @return  mixed
     * @throws  \Hoa\Database\Exception
     */
    public function fetchColumn($column = 0)
    {
        return $this->getStatement()->fetchColumn($column);
    }

    /**
     * Close the cursor, enabling the statement to be executed again.
     *
     * @return  bool
     * @throws  \Hoa\Database\Exception
     */
    public function closeCursor()
    {
        return $this->getStatement()->closeCursor();
    }

    /**
     * Fetch the SQLSTATE associated with the last operation on the statement
     * handle.
     *
     * @return  string
     * @throws  \Hoa\Database\Exception
     */
    public function errorCode()
    {
        return $this->getStatement()->errorCode();
    }

    /**
     * Fetch extends error information associated with the last operation on the
     * statement handle.
     *
     * @return  array
     * @throws  \Hoa\Database\Exception
     */
    public function errorInfo()
    {
        return $this->getStatement()->errorInfo();
    }
}
