<?php

/**
 * Hoa
 *
 *
 * @license
 *
 * New BSD License
 *
 * Copyright (c) 2007-2011, Ivan Enderlin. All rights reserved.
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
 * \Hoa\Tree\ITree\Node
 */
-> import('Tree.I~.Node');

}

namespace Hoa\Log\Backtrace {

/**
 * Class \Hoa\Log\Backtrace\Node.
 *
 * Node for the backtrace tree.
 *
 * @author     Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright (c) 2007, 2011 Ivan ENDERLIN.
 * @license    New BSD License
 */

class Node implements \Hoa\Tree\ITree\Node {

    /**
     * Node ID.
     *
     * @var \Hoa\Log\Backtrace\Node string
     */
    protected $_id    = null;

    /**
     * Node value.
     *
     * @var \Hoa\Log\Backtrace\Node array
     */
    protected $_value = null;



    /**
     * Build a node.
     *
     * @access  public
     * @param   array   $trace    The trace.
     * @return  void
     */
    public function __construct ( Array $trace = array() ) {

        $this->setId(md5(serialize($trace)));
        $this->setValue($trace);
    }

    /**
     * Set node ID.
     *
     * @access  protected
     * @param   string     $id    The node ID.
     * @return  string
     */
    protected function setId ( $id ) {

        $old       = $this->_id;
        $this->_id = $id;

        return $old;
    }

    /**
     * Set node value.
     *
     * @access  public
     * @param   array   $value    The node value.
     * @return  string
     */
    public function setValue ( Array $value = array() ) {

        $old          = $this->_value;
        $this->_value = $value;

        return $old;
    }

    /**
     * Get node ID, must be implement because of interface.
     *
     * @access  public
     * @return  string
     */
    public function getId ( ) {

        return $this->_id;
    }

    /**
     * Get the function name in the trace.
     *
     * @access  public
     * @return  mixed
     */
    public function getFunction ( ) {

        return @$this->_value['function'];
    }

    /**
     * Get the line number in the trace.
     *
     * @access  public
     * @return  mixed
     */
    public function getLine ( ) {

        return @$this->_value['line'];
    }

    /**
     * Get the filename in the trace.
     *
     * @access  public
     * @return  mixed
     */
    public function getFilename ( ) {

        return @$this->_value['file'];
    }

    /**
     * Get the classname in the trace.
     *
     * @access  public
     * @return  mixed
     */
    public function getClassname ( ) {

        return @$this->_value['class'];
    }

    /**
     * Get the object in the trace.
     *
     * @access  public
     * @return  mixed
     */
    public function getObject ( ) {

        return @$this->_value['object'];
    }

    /**
     * Get the type in the trace.
     *
     * @access  public
     * @return  mixed
     */
    public function getType ( ) {

        return @$this->_value['type'];
    }

    /**
     * Get the function or method arguments in the trace.
     *
     * @access  public
     * @return  mixed
     */
    public function getArguments ( ) {

        return @$this->_value['args'];
    }

    /**
     * Get the node string representation.
     *
     * @access  public
     * @return  string
     */
    public function __toString ( ) {

        return $this->getClassname() .
               $this->getType() .
               $this->getFunction();
    }
}

}
