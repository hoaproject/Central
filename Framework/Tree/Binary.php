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
 * Copyright (c) 2007, 2008 Ivan ENDERLIN. All rights reserved.
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
 * @subpackage  Hoa_Tree_Binary
 *
 */

/**
 * Hoa_Framework
 */
require_once 'Framework.php';

/**
 * Hoa_Tree_Exception
 */
import('Tree.Exception');

/**
 * Hoa_Tree_Abstract
 */
import('Tree.Abstract');

/**
 * Class Hoa_Tree_Binary.
 *
 * Manipulate a binary tree.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2008 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Tree
 * @subpackage  Hoa_Tree_Binary
 */

class Hoa_Tree_Binary extends Hoa_Tree_Abstract {

    /**
     * Insert a child.
     * Fill the child list from left to right.
     *
     * @access  public
     * @param   Hoa_Tree_Binary  $child    Child to insert.
     * @return  Hoa_Tree_Binary
     * @throw   Hoa_Tree_Exception
     */
    public function insert ( Hoa_Tree_Abstract $child ) {

        if(!($child instanceof Hoa_Tree_Binary))
            throw new Hoa_Tree_Exception(
                'Child must be an instance of Hoa_Tree_Binary; given %s.',
                0, get_class($child));

        if(false === $this->isSimpleLeft()) {

            $this->_childs[0] = $child;

            return $this;
        }

        if(true === $this->isSimpleLeft()) {

            $this->_childs[1] = $child;

            return $this;
        }

        throw new Hoa_Tree_Exception(
            'Cannot insert a new element: left and right child are ' .
            'already set.', 1);
    }

    /**
     * Insert the left child.
     *
     * @access  public
     * @param   Hoa_Tree_Binary  $child    Child to insert.
     * @return  Hoa_Tree_Binary
     * @throw   Hoa_Tree_Exception
     */
    public function insertLeft ( Hoa_Tree_Binary $child ) {

        if(false === $this->isSimpleLeft())
            throw new Hoa_Tree_Exception(
                'Left child is already set.', 2);

        $this->_childs[0] = $child;

        return $this;
    }

    /**
     * Insert the right child.
     *
     * @access  public
     * @param   Hoa_Tree_Binary  $child    Child to insert.
     * @return  Hoa_Tree_Binary
     * @throw   Hoa_Tree_Exception
     */
    public function insertRight ( Hoa_Tree_Binary $child ) {

        if(true === $this->isSimpleRight())
            throw new Hoa_Tree_Exception(
                'Right child is already set.', 3);

        $this->_childs[1] = $child;

        return $this;
    }

    /**
     * Delete a child.
     *
     * @access  public
     * @param   int     $i    Child index.
     * @return  Hoa_Tree_Binary
     */
    public function delete ( $i ) {

        if($i === 0) {

            $this->_childs[0] = null;

            return;
        }

        $this->_childs[1] = null;

        return $this;
    }

    /**
     * Delete the left child.
     *
     * @access  public
     * @return  Hoa_Tree_Binary
     */
    public function deleteLeft ( ) {

        return $this->delete(0);
    }

    /**
     * Delete the right child.
     *
     * @access  public
     * @return  Hoa_Tree_Binary
     */
    public function deleteRight ( ) {

        return $this->delete(1);
    }

    /**
     * Check if the node is simple left, i.e. if the left child is set and not
     * the right child.
     *
     * @access  public
     * @return  bool
     */
    public function isSimpleLeft ( ) {

        return    null !== $this->getLeft()
               && null === $this->getRight();
    }

    /**
     * Check if the node is simple right, i.e. if the right child is set and not
     * the left child.
     *
     * @access  public
     * @return  bool
     */
    public function isSimpleRight ( ) {

        return    null === $this->getLeft()
               && null !== $this->getRight();
    }

    /**
     * Check if the node is double, i.e. if left and right child are set.
     *
     * @access  public
     * @return  bool
     */
    public function isDouble ( ) {

        return    null !== $this->getLeft()
               && null !== $this->getRight();
    }

    /**
     * Check if the node is a leaf.
     *
     * @access  public
     * @return  bool
     */
    public function isLeaf ( ) {

        return    null === $this->getLeft()
               && null === $this->getRight();
    }

    /**
     * Check if the node is a noe (i.e. not a leaf).
     *
     * @access  public
     * @return  bool
     */
    public function isNode ( ) {

        return    null !== $this->getLeft()
               || null !== $this->getRight();
    }

    /**
     * Get the left child.
     *
     * @access  public
     * @return  bool
     */
    public function getLeft ( ) {

        return $this->_childs[0];
    }

    /**
     * Get the right child.
     *
     * @access  public
     * @return  bool
     */
    public function getRight ( ) {

        return $this->_childs[1];
    }

    /**
     * Accept a visitor.
     *
     * @access  public
     * @param   Hoa_Visitor_Visit  $visitor    Visitor.
     * @param   mixed              &$handle    Handle (reference).
     * @return  mixed
     */
    public function accept ( Hoa_Visitor_Visit $visitor, &$handle = null ) {

        return $visitor->visit($this);
    }
}
