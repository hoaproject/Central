<?php

/**
 * Hoa
 *
 *
 * @license
 *
 * New BSD License
 *
 * Copyright © 2007-2013, Ivan Enderlin. All rights reserved.
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

namespace Hoa\Praspel\Iterator {

/**
 * Class \Hoa\Praspel\Iterator\Sampler.
 *
 * An easy way to iterate over data described by Praspel.
 *
 * @author     Ivan Enderlin <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright © 2007-2013 Ivan Enderlin.
 * @license    New BSD License
 */

class Sampler implements \Iterator {

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
     * @var \Hoa\Praspel\Model\Declaration object
     */
    protected $_declaration = null;

    /**
     * Max number of data to generate.
     *
     * @var \Hoa\Praspel\Iterator\Sampler int
     */
    protected $_maxData     = null;

    /**
     * Key type (please, see self::KEY_AS_* constants).
     *
     * @var \Hoa\Praspel\Iterator\Sampler int
     */
    protected $_keyType     = null;

    /**
     * Variables to consider.
     *
     * @var \Hoa\Praspel\Iterator\Sampler array
     */
    protected $_variables   = array();

    /**
     * Current key.
     *
     * @var \Hoa\Praspel\Iterator\Sampler int
     */
    protected $_key         = -1;

    /**
     * Current value.
     *
     * @var \Hoa\Praspel\Iterator\Sampler array
     */
    protected $_current     = null;



    /**
     * Construct.
     *
     * @access  public
     * @param   \Hoa\Praspel\Model\Declaration  $declaration    Declaration.
     * @param   int                             $maxData        Maximum data to
     *                                                          sample.
     * @param   int                             $keyType        Key type (plese,
     *                                                          see
     *                                                          self::KEY_AS*
     *                                                          constants).
     * @return  void
     */
    public function __construct ( \Hoa\Praspel\Model\Declaration $declaration,
                                  $maxData = 5,
                                  $keyType = self::KEY_AS_VARIABLE_NAME ) {

        $this->_declaration = $declaration;
        $this->_maxData     = $maxData;
        $this->_keyType     = $keyType;

        return;
    }

    /**
     * Consider some variables.
     * Example:
     *     $this->extract('x', 'y', 'z')
     *
     * @access  public
     * @param   string  $variable    Variable name.
     * @param   ...     ...          ...
     * @return  \Hoa\Praspel\Iterator\Sampler
     */
    public function extract ( ) {

        foreach(func_get_args() as $variable)
            $this->_variables[] = $this->_declaration[$variable];

        return $this;
    }

    /**
     * Get current value.
     *
     * @access  public
     * @return  array
     */
    public function current ( ) {

        return $this->_current;
    }

    /**
     * Get current key.
     *
     * @access  public
     * @return  int
     */
    public function key ( ) {

        return $this->_key;
    }

    /**
     * Compute the next value and return it.
     *
     * @access  public
     * @return  array
     */
    public function next ( ) {

        $handle = array();

        if(empty($this->_variables))
            $this->_variables = $this->_declaration->getLocalVariables();

        if(self::KEY_AS_VARIABLE_NAME === $this->_keyType)
            foreach($this->_variables as $variable)
                $handle[$variable->getName()] = $variable->sample();
        else
            foreach($this->_variables as $variable)
                $handle[] = $variable->sample();

        ++$this->_key;
        $this->_current = $handle;

        return $this->current();
    }

    /**
     * Rewind the iterator.
     *
     * @access  public
     * @return  void
     */
    public function rewind ( ) {

        $this->_key     = -1;
        $this->_current = null;
        $this->next();

        return;
    }

    /**
     * Check if there is enough data to continue.
     *
     * @access  public
     * @return  bool
     */
    public function valid ( ) {

        return $this->_key < $this->_maxData;
    }
}

}
