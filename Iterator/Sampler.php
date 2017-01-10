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

namespace Hoa\Praspel\Iterator;

use Hoa\Iterator;
use Hoa\Praspel;

/**
 * Class \Hoa\Praspel\Iterator\Sampler.
 *
 * An easy way to iterate over data described by Praspel.
 *
 * @copyright  Copyright © 2007-2017 Hoa community
 * @license    New BSD License
 */
class Sampler implements Iterator
{
    /**
     * Key value is variable name.
     *
     * @const int
     */
    const KEY_AS_VARIABLE_NAME     = 0;

    /**
     * Key value is variable position.
     *
     * @const int
     */
    const KEY_AS_VARIABLE_POSITION = 1;

    /**
     * Declaration.
     *
     * @var \Hoa\Praspel\Model\Declaration
     */
    protected $_declaration = null;

    /**
     * Key type (please, see self::KEY_AS_* constants).
     *
     * @var int
     */
    protected $_keyType     = null;

    /**
     * Variables to consider.
     *
     * @var array
     */
    protected $_variables   = [];

    /**
     * Current key.
     *
     * @var int
     */
    protected $_key         = -1;

    /**
     * Current value.
     *
     * @var array
     */
    protected $_current     = null;

    /**
     * Coverage iterator.
     *
     * @var \Hoa\Praspel\Iterator\Coverage\Domain
     */
    protected $_coverage    = null;



    /**
     * Construct.
     *
     * @param   \Hoa\Praspel\Model\Declaration  $declaration    Declaration.
     * @param   int                             $keyType        Key type (plese,
     *                                                          see
     *                                                          self::KEY_AS*
     *                                                          constants).
     */
    public function __construct(
        Praspel\Model\Declaration $declaration,
        $keyType = self::KEY_AS_VARIABLE_NAME
    ) {
        $this->_declaration = $declaration;
        $this->_keyType     = $keyType;

        return;
    }

    /**
     * Consider some variables.
     * Example:
     *     $this->extract('x', 'y', 'z')
     *
     * @param   string  $variable    Variable name.
     * @param   ...     ...          ...
     * @return  \Hoa\Praspel\Iterator\Sampler
     */
    public function extract()
    {
        foreach (func_get_args() as $variable) {
            $this->_variables[] = $this->_declaration[$variable];
        }

        return $this;
    }

    /**
     * Get current value.
     *
     * @return  array
     */
    public function current()
    {
        return $this->_current;
    }

    /**
     * Get current key.
     *
     * @return  int
     */
    public function key()
    {
        return $this->_key;
    }

    /**
     * Compute the next value and return it.
     *
     * @return  array
     */
    public function next()
    {
        $this->_coverage->next();
        $this->_current();

        return $this->current();
    }

    /**
     * Prepare the current value.
     *
     * @return  void
     */
    protected function _current()
    {
        $current = $this->_coverage->current();
        $handle  = [];

        if (self::KEY_AS_VARIABLE_NAME === $this->_keyType) {
            foreach ($current as $name => $domain) {
                $handle[$name] = $domain->sample();
            }
        } else {
            foreach ($current as $domain) {
                $handle[] = $domain->sample();
            }
        }

        ++$this->_key;
        $this->_current = $handle;

        return;
    }

    /**
     * Rewind the iterator.
     *
     * @return  void
     */
    public function rewind()
    {
        $this->_key     = -1;
        $this->_current = null;

        if (null === $this->_coverage) {
            if (empty($this->_variables)) {
                $this->_variables = $this->_declaration->getLocalVariables();
            }

            $this->_coverage = new Coverage\Domain($this->_variables);
        }

        $this->_coverage->rewind();
        $this->_current();

        return;
    }

    /**
     * Check if there is enough data to continue.
     *
     * @return  bool
     */
    public function valid()
    {
        return $this->_coverage->valid();
    }
}
