<?php

/**
 * Hoa Framework
 *
 *
 * @license
 *
 * GNU General Public License
 *
 * This file is part of Hoa Open Accessibility.
 * Copyright (c) 2007, 2010 Ivan ENDERLIN. All rights reserved.
 *
 * HOA Open Accessibility is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * HOA Open Accessibility is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with HOA Open Accessibility; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
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
 * @copyright  Copyright (c) 2007, 2010 Ivan ENDERLIN.
 * @license    http://gnu.org/licenses/gpl.txt GNU GPL
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
