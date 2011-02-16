<?php

/**
 * Hoa
 *
 *
 * @license
 *
 * GNU General Public License
 *
 * This file is part of Hoa Open Accessibility.
 * Copyright (c) 2007, 2011 Ivan ENDERLIN. All rights reserved.
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
