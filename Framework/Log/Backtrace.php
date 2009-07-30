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
 * @package     Hoa_Log
 * @subpackage  Hoa_Log_Backtrace
 *
 */

/**
 * Hoa_Framework
 */
require_once 'Framework.php';

/**
 * Hoa_Tree
 */
import('Tree.~');

/**
 * Hoa_Tree_Node_SimpleNode
 */
import('Tree.Node.SimpleNode');

/**
 * Hoa_Tree_Visitor_Dot
 */
import('Tree.Visitor.Dot');

/**
 * Class Hoa_Log_Backtrace.
 *
 * Build a backtrace tree. Please, read the API documentation of the class
 * attributes to well-understand.
 * A DOT output is available.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2008 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Log
 * @subpackage  Hoa_Log_Backtrace
 */

class Hoa_Log_Backtrace {

    /**
     * Backtrace tree.
     *
     * @var Hoa_Tree object
     */
    protected $_tree = null;



    /**
     * Build an empty backtrace tree, and set the root.
     *
     * @access  public
     * @return  void
     */
    public function __construct ( ) {

        $this->_tree = new Hoa_Tree(
            new Hoa_Tree_Node_SimpleNode('root', 'root')
        );

        return;
    }

    /**
     * Compute the tree with a backtrace stack.
     *
     * @access  protected
     * @param   array      $array    Backtrace stack.
     * @return  void
     */
    protected function computeTree ( Array $array = array() ) {

        $node        = null;
        $child       = null;
        $currentNode = $this->_tree;

        foreach($array as $i => $trace) {

            $node = new Hoa_Tree_Node_SimpleNode(
                md5(serialize($trace)),
                @$trace['class'] . @$trace['type'] . @$trace['function']
            );

            if(true === $currentNode->childExists($node->getId()))
                $currentNode = $currentNode->getChild($node->getId());
            else {

                $child       = new Hoa_Tree($node);
                $currentNode->insert($child);
                $currentNode = $child;
            }
        }

        return;
    }

    /**
     * Run a debug trace, i.e. build a new branche in the backtrace tree.
     *
     * @access  public
     * @return  void
     */
    public function debug ( ) {

        $array = debug_backtrace();
        array_shift($array); // Hoa_Log_Backtrace::debug().

        if(isset($array[0]['class']) && $array[0]['class'] == 'Hoa_Log')
            array_shift($array); // Hoa_Log::log().

        if(isset($array[0]['function']) && $array[0]['function'] == 'hlog')
            array_shift($array); // hlog().

        $this->computeTree(array_reverse($array));

        return;
    }

    /**
     * Get the backtrace tree.
     *
     * @access  public
     * @return  array
     */
    public function getTree ( ) {

        return $this->_tree;
    }

    /**
     * Print the tree in DOT language.
     *
     * @access  public
     * @return  string
     */
    public function __toString ( ) {

        $out = new Hoa_Tree_Visitor_Dot();

        return $out->visit($this->getTree());
    }
}
