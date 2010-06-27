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
 *
 *
 * @category    Framework
 * @package     Hoa_Tree
 *
 */

/**
 * Hoa_Core
 */
require_once 'Core.php';

/**
 * Hoa_Tree_Exception
 */
import('Tree.Exception');

/**
 * Hoa_Tree_Abstract
 */
import('Tree.Abstract');

/**
 * Class Hoa_Tree.
 *
 * Manipule a tree.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2010 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Tree
 */

class Hoa_Tree extends Hoa_Tree_Abstract {

    /**
     * Insert a child.
     * Fill the child list from left to right.
     *
     * @access  public
     * @param   Hoa_Tree  $child    Child to insert.
     * @return  Hoa_Tree
     * @throw   Hoa_Tree_Exception
     */
    public function insert ( Hoa_Tree_Abstract $child ) {

        if(!($child instanceof Hoa_Tree))
            throw new Hoa_Tree_Exception(
                'Child must be an instance of Hoa_Tree; given %s.',
                0, get_class($child));

        $this->_childs[$child->getValue()->getId()] = $child;

        return $this;
    }

    /**
     * Delete a child.
     *
     * @access  public
     * @param   mixed   $nodeId    Node ID.
     * @return  Hoa_Tree_Abstract
     * @throw   Hoa_Tree_Exception
     */
    public function delete ( $nodeId ) {

        unset($this->_childs[$nodeId]);

        return $this;
    }

    /**
     * Check if the node is a leaf.
     *
     * @access  public
     * @return  bool
     */
    public function isLeaf ( ) {

        return empty($this->_childs);
    }

    /**
     * Check if the node is a node (i.e. not a leaf).
     *
     * @access  public
     * @return  bool
     */
    public function isNode ( ) {

        return !empty($this->_childs);
    }
}
