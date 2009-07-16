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
 * Class Hoa_Log_Backtrace.
 *
 * .
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
     * The backtrace tree.
     * The root is the bootstrap, and each node is a function or method call.
     * The tree is descending, i.e. leafs represent deepest functions or methods
     * calls.
     *
     * @var Hoa_Log_Backtrace array
     */
    private $_tree        = array();

    /**
     * The backtrace hashes is built with hashes.
     * Its structure is:
     *     hash =>
     *         checkpoint => trace
     * The hash is made with trace and md5.
     * It allows to retrieve a trace, for a given checkpoint, for a given hash.
     *
     * @var Hoa_Log_Backtrace array
     */
    private $_hashes      = array();

    /**
     * List of checkpoints.
     * A checkpoint (formatted as a hash) is the trace that ran the debug
     * method, i.e. that ran a new computing of the backtrace tree.
     *
     * @var Hoa_Log_Backtrace array
     */
    private $_checkpoints = array();

    /**
     * Current checkpoint.
     *
     * @var Hoa_Log_Backtrace string
     */
    private $_checkpoint  = null;



    /**
     * Compute the backtrace tree.
     *
     * @access  protected
     * @param   array      $trace    Current trace stack.
     * @param   array      &$tree    The tree itself or a branche of the tree.
     * @return  array
     */
    protected function computeTree ( $array, &$tree ) {

        $trace = array_pop($array);
        $hash  = md5(
            $trace['file'] . '-' . $trace['line'] . ':' .
            @$trace['class'] . @$trace['type'] . @$trace['function']
        );

        $this->_hashes[$hash][$this->_checkpoint] = $trace;

        if(empty($array)) {

            if(!isset($tree[$hash]))
                $tree[$hash] = null;

            return $tree;
        }

        if(isset($tree[$hash]))
            $handle  = &$tree[$hash];
        else
            $handle  = array();

        $tree[$hash] = $this->computeTree($array, $handle);
        unset($handle);

        return $tree;
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
        //array_shift($array); // Hoa_Log::log().
        $trace = $array[0];

        $this->_checkpoints[] = $this->_checkpoint = md5(
            $trace['file'] . '-' . $trace['line'] . ':' . 
            @$trace['class'] . @$trace['type'] . @$trace['function']
        );

        $this->_tree = $this->computeTree($array, $this->_tree);

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
     * Get the hashes array.
     *
     * @access  public
     * @return  array
     */
    public function getHashes ( ) {

        return $this->_hashes;
    }

    /**
     *
     */
    public function getTrace ( $hash, $checkpoint = null ) {

        if(!isset($this->_hashes[$hash]))
            throw new Hoa_Log_Exception(
                'Cannot reach trace from hash %s, because it does not exist.',
                0, $hash);

        if(null !== $checkpoint) {

            if(!isset($this->_hashes[$hash][$checkpoint]))
                throw new Hoa_Log_Exception(
                    'Cannot reach trace from hash %s for the checkpoint %s, ' .
                    'because the checkpoint does not exist.',
                    1, array($hash, $checkpoint));

            return $this->_hashes[$hash][$checkpoint];
        }

        reset($this->_hashes[$hash]);

        return current($this->_hashes[$hash]);
    }

    /**
     * Get the checkpoints array.
     *
     * @access  public
     * @return  array
     */
    public function getCheckpoints ( ) {

        return $this->_checkpoints;
    }

    /**
     *
     */
    private function linearizeTree ( $node, $tail = null ) {

        if(empty($node))
            return $tail;

        $out = null;

        foreach($node as $sibling => $childs) {

            $t = $this->getTrace($sibling);

            $this->_tricky[] = '    "' . md5($tail . $sibling) . '" [label="' .
                               @$t['class'] . @$t['type'] . @$t['function'] .
                               '"]';

            $out .= $this->linearizeTree(
                $childs,
                $tail .
                '    "' . md5($tail . $sibling) . '"' .
                (!empty($childs)
                     ? "\n" . '    -> '
                     : ';' . "\n")
            );
        }

        return $out;
    }

    /**
     * Print the tree in the DOT language.
     *
     * @access  public
     * @return  string
     */
    public function __toString ( ) {

        return 'digraph {' . "\n" .
               $this->linearizeTree($this->getTree()) . "\n" .
               implode(';' . "\n", $this->_tricky) . "\n" .
               '}';
    }
}
