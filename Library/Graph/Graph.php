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
 * @package     Hoa_Graph
 *
 */

/**
 * Hoa_Framework
 */
require_once 'Framework.php';

/**
 * Hoa_Graph_Exception
 */
import('Graph.Exception');

/**
 * Hoa_Factory
 */
import('Factory.~');

/**
 * Class Hoa_Graph.
 *
 * Get instance of different graph type.
 * When getting an instance of a type of a graph, the graph type (e.g.
 * Hoa_Graph_AdjacencyList) extends this class. It is like an abstract
 * factory …
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2008 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Graph
 */

abstract class Hoa_Graph {

    /**
     * Graph type.
     *
     * @const string
     */
    const TYPE_ADJACENCYLIST = 'AdjacencyList';

    /**
     * Allow loop when building graph (when adding nodes).
     *
     * @const bool
     */
    const ALLOW_LOOP         = true;

    /**
     * Disallow loop when building graph (when adding nodes).
     *
     * @const bool
     */
    const DISALLOW_LOOP      = false;

    /**
     * Propagate delete.
     *
     * @const bool
     */
    const DELETE_CASCADE     = true;

    /**
     * Restrict delete.
     *
     * @const bool
     */
    const DELETE_RESTRICT    = false;

    /**
     * All nodes.
     *
     * @var Hoa_Graph array
     */
    protected $nodes = array();

    /**
     * If allow loop when building graph, it is set to ALLOW_LOOP (true),
     * DISALLOW_LOOP (false) else.
     *
     * @var Hoa_Graph bool
     */
    protected $loop  = self::DISALLOW_LOOP;



    /**
     * Get an empty graph.
     *
     * @access  protected
     * @param   bool       $loop    Allow or not loop.
     * @return  void
     */
    protected function __construct ( $loop = self::DISALLOW_LOOP ) {

        $this->allowLoop($loop);
    }

    /**
     * Make an instance of a specific graph.
     *
     * @access  public
     * @param   string  $type    Type of graph needed.
     * @return  void
     * @throw   Hoa_Graph_Exception
     */
    public static function getInstance ( $type = self::TYPE_ADJACENCYLIST ) {

        if($type != self::TYPE_ADJACENCYLIST)
            throw new Hoa_Graph_Exception(
                'Type %s is not supported. Only self:TYPE_ADJACENCYLIST is ' .
                'supported.', 0, $type);

        $args = func_get_args();
        array_shift($args);

        $handler = null;

        try {

            $handler = Hoa_Factory::get('Graph', $type, $args);
        }
        catch ( Hoa_Factory_Exception $e ) {

            throw new Hoa_Graph_Exception($e->getMessage(), $e->getCode());
        }

        return $handler;
    }

    /**
     * Add a node.
     *
     * @access  public
     * @param   Hoa_Graph_Node_Interface  $node      Node to add.
     * @param   mixed                     $parent    Parent of node.
     * @return  void
     * @throw   Hoa_Graph_Exception
     */
    abstract public function addNode ( Hoa_Graph_Node_Interface $node,
                                       $parent = array() );

    /**
     * Check if a node does already exist or not.
     *
     * @access  public
     * @param   mixed   $nodeId    The node ID or the node instance.
     * @return  bool
     */
    abstract public function nodeExists ( $nodeId );

    /**
     * Get a node.
     *
     * @access  public
     * @param   mixed   $nodeId    The node ID or the node instance.
     * @return  object
     * @throw   Hoa_Graph_Exception
     */
    abstract public function getNode ( $nodeId );

    /**
     * Get all nodes.
     *
     * @access  protected
     * @return  array
     */
    protected function getNodes ( ) {

        return $this->nodes;
    }

    /**
     * Get parent of a specific node.
     *
     * @access  public
     * @param   mixed   $nodeId    The node ID or the node instance.
     * @return  object
     * @throw   Hoa_Graph_Exception
     */
    abstract public function getParent ( $nodeId );

    /**
     * Get child of a specific node.
     *
     * @access  public
     * @param   mixed   $nodeId    The node ID or the node instance.
     * @return  object
     * @throw   Hoa_Graph_Exception
     */
    abstract public function getChild ( $nodeId );

    /**
     * Delete a node.
     *
     * @access  public
     * @param   mixed   $nodeId       The node ID or the node instance.
     * @param   bool    $propagate    Propagate the erasure.
     * @return  void
     * @throw   Hoa_Graph_Exception
     */
    abstract public function deleteNode ( $nodeId, $propagate = self::DELETE_RESTRICT );

    /**
     * Whether node is a leaf, i.e. does not have any child.
     *
     * @access  public
     * @param   mixed   $nodeId    The node ID or the node instance.
     * @return  bool
     * @throw   Hoa_Graph_Exception
     */
    abstract public function isLeaf ( $nodeId );

    /**
     * Whether node is a root, i.e. does not have any parent.
     *
     * @access  public
     * @param   mixed   $nodeId    The node ID or the node instance.
     * @return  bool
     * @throw   Hoa_Graph_Exception
     */
    abstract public function isRoot ( $nodeId );

    /**
     * Set the loop mode (self::ALLOW_LOOP or self::DISALLOW_LOOP).
     *
     * @access  public
     * @param   bool    $loop    Allow or not loop.
     * @return  bool
     */
    public function allowLoop ( $loop = self::DISALLOW_LOOP ) {

        $old        = $this->loop;
        $this->loop = $loop;

        return $old;
    }

    /**
     * Get the loop mode.
     *
     * @access  public
     * @return  bool
     */
    public function isLoopAllow ( ) {

        return $this->loop;
    }

    /**
     * Print the graph in the DOT language.
     *
     * @access  public
     * @return  string
     */
    abstract public function __toString ( );
}
