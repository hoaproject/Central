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

namespace Hoa\Praspel\Iterator\Coverage;

use Hoa\Consistency;
use Hoa\Iterator;
use Hoa\Praspel;

/**
 * Class \Hoa\Praspel\Iterator\Coverage.
 *
 * Coverage recursive iterator.
 *
 * @copyright  Copyright © 2007-2017 Hoa community
 * @license    New BSD License
 */
class Coverage implements Iterator\Aggregate
{
    /**
     * Criteria: normal (@requires and @ensures).
     *
     * @const int
     */
    const CRITERIA_NORMAL      = 1;

    /**
     * Criteria: exceptional (@requires and @throwable).
     *
     * @const int
     */
    const CRITERIA_EXCEPTIONAL = 2;

    /**
     * Criteria: domain (all disjunctions).
     *
     * @const int
     */
    const CRITERIA_DOMAIN      = 4;

    /**
     * Iterator (of the specification to cover).
     *
     * @var \Hoa\Praspel\Iterator\Coverage\Structural
     */
    protected $_iterator = null;



    /**
     * Constructor.
     *
     * @param   \Hoa\Praspel\Model\Specification  $specification    Specification.
     */
    public function __construct(Praspel\Model\Specification $specification)
    {
        $this->_iterator = new Structural($specification);

        return;
    }

    /**
     * Set coverage criteria.
     *
     * @param   int  $criteria    Criteria (please, see self::CRITERIA_*
     *                            constants).
     * @return  int
     */
    public function setCriteria($criteria)
    {
        return $this->_iterator->setCriteria($criteria);
    }

    /**
     * Get iterator.
     *
     * @return  \Hoa\Iterator\Recursive\Iterator
     */
    public function getIterator()
    {
        return new Iterator\Recursive\Iterator($this->_iterator);
    }
}

/**
 * Flex entity.
 */
Consistency::flexEntity('Hoa\Praspel\Iterator\Coverage\Coverage');
