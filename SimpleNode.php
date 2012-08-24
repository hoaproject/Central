<?php

/**
 * Hoa
 *
 *
 * @license
 *
 * New BSD License
 *
 * Copyright © 2007-2012, Ivan Enderlin. All rights reserved.
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
 * \Hoa\Graph\IGraph\Node
 */
-> import('Graph.I~.Node');

}

namespace Hoa\Graph {

/**
 * Class \Hoa\Graph\SimpleNode.
 *
 * It's just a simple node demo (may be used for example and test).
 *
 * @author     Ivan Enderlin <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright © 2007-2012 Ivan Enderlin.
 * @license    New BSD License
 */

class SimpleNode implements IGraph\Node {

    /**
     * Node ID.
     *
     * @var \Hoa\Graph\SimpleNode string
     */
    protected $nodeId = null;

    /**
     * Node value.
     *
     * @var \Hoa\Graph\SimpleNode string
     */
    protected $nodeValue = null;



    /**
     * Build a node that contains a string.
     *
     * @access  public
     * @param   string  $nodeId       The node ID.
     * @param   string  $nodeValue    The node value.
     * @return  void
     */
    public function __construct ( $nodeId, $nodeValue = null ) {

        $this->setNodeId($nodeId);
        $this->setNodeValue($nodeValue);
    }

    /**
     * Set node ID.
     *
     * @access  protected
     * @param   string     $nodeId    The node ID.
     * @return  string
     */
    protected function setNodeId ( $nodeId ) {

        $old          = $this->nodeId;
        $this->nodeId = $nodeId;

        return $old;
    }

    /**
     * Set node value.
     *
     * @access  public
     * @param   string  $nodeValue    The node value.
     * @return  string
     */
    public function setNodeValue ( $nodeValue = null ) {

        $old             = $this->nodeValue;
        $this->nodeValue = $nodeValue;

        return $old;
    }

    /**
     * Get node ID, must be implement because of interface.
     *
     * @access  public
     * @return  string
     */
    public function getNodeId ( ) {

        return $this->nodeId;
    }

    /**
     * Get node value.
     *
     * @access  public
     * @return  string
     */
    public function getNodeValue ( ) {

        return $this->nodeValue;
    }
}

}
