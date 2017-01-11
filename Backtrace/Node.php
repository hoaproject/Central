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

namespace Hoa\Log\Backtrace;

use Hoa\Tree;

/**
 * Class \Hoa\Log\Backtrace\Node.
 *
 * Node for the backtrace tree.
 *
 * @copyright  Copyright © 2007-2017 Hoa community
 * @license    New BSD License
 */
class Node implements Tree\ITree\Node
{
    /**
     * Node ID.
     *
     * @var string
     */
    protected $_id    = null;

    /**
     * Node value.
     *
     * @var array
     */
    protected $_value = null;



    /**
     * Build a node.
     *
     * @param   array   $trace    The trace.
     */
    public function __construct(array $trace = [])
    {
        $this->setId(md5(serialize($trace)));
        $this->setValue($trace);
    }

    /**
     * Set node ID.
     *
     * @param   string     $id    The node ID.
     * @return  string
     */
    protected function setId($id)
    {
        $old       = $this->_id;
        $this->_id = $id;

        return $old;
    }

    /**
     * Set node value.
     *
     * @param   array   $value    The node value.
     * @return  string
     */
    public function setValue(array $value = [])
    {
        $old          = $this->_value;
        $this->_value = $value;

        return $old;
    }

    /**
     * Get node ID, must be implement because of interface.
     *
     * @return  string
     */
    public function getId()
    {
        return $this->_id;
    }

    /**
     * Get the function name in the trace.
     *
     * @return  mixed
     */
    public function getFunction()
    {
        return @$this->_value['function'];
    }

    /**
     * Get the line number in the trace.
     *
     * @return  mixed
     */
    public function getLine()
    {
        return @$this->_value['line'];
    }

    /**
     * Get the filename in the trace.
     *
     * @return  mixed
     */
    public function getFilename()
    {
        return @$this->_value['file'];
    }

    /**
     * Get the classname in the trace.
     *
     * @return  mixed
     */
    public function getClassname()
    {
        return @$this->_value['class'];
    }

    /**
     * Get the object in the trace.
     *
     * @return  mixed
     */
    public function getObject()
    {
        return @$this->_value['object'];
    }

    /**
     * Get the type in the trace.
     *
     * @return  mixed
     */
    public function getType()
    {
        return @$this->_value['type'];
    }

    /**
     * Get the function or method arguments in the trace.
     *
     * @return  mixed
     */
    public function getArguments()
    {
        return @$this->_value['args'];
    }

    /**
     * Get the node string representation.
     *
     * @return  string
     */
    public function __toString()
    {
        return $this->getClassname() .
               $this->getType() .
               $this->getFunction();
    }
}
