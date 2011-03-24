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

namespace Hoa\Tree {

/**
 * Class \Hoa\Tree\SimpleNode.
 *
 * It's just a simple node demo (may be used for example and test).
 *
 * @author     Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright (c) 2007, 2011 Ivan ENDERLIN.
 * @license    http://gnu.org/licenses/gpl.txt GNU GPL
 */

class SimpleNode implements ITree\Node {

    /**
     * Node ID.
     *
     * @var \Hoa\Tree\SimpleNode string
     */
    protected $_id    = null;

    /**
     * Node value.
     *
     * @var \Hoa\Tree\SimpleNode string
     */
    protected $_value = null;



    /**
     * Build a node that contains a string.
     *
     * @access  public
     * @param   string  $id       The node ID.
     * @param   string  $value    The node value.
     * @return  void
     */
    public function __construct ( $id, $value = null ) {

        $this->setId($id);
        $this->setValue($value);
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
     * @param   string  $value    The node value.
     * @return  string
     */
    public function setValue ( $value = null ) {

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
     * Get node value.
     *
     * @access  public
     * @return  string
     */
    public function getValue ( ) {

        return $this->_value;
    }

    /**
     * Get the node string representation.
     *
     * @access  public
     * @return  string
     */
    public function __toString ( ) {

        return (string) $this->getValue();
    }
}

}
