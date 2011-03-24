<?php

/**
 * Hoa
 *
 *
 * @license
 *
 * New BSD License
 *
 * Copyright © 2007-2011, Ivan Enderlin. All rights reserved.
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
 * \Hoa\Tree
 */
-> import('Tree.~')

/**
 * \Hoa\Log\Backtrace\Node
 */
-> import('Log.Backtrace.Node')

/**
 * \Hoa\Tree\Visitor\Dot
 */
-> import('Tree.Visitor.Dot');

}

namespace Hoa\Log\Backtrace {

/**
 * Class \Hoa\Log\Backtrace.
 *
 * Build a backtrace tree. Please, read the API documentation of the class
 * attributes to well-understand.
 * A DOT output is available.
 *
 * @author     Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright © 2007-2011 Ivan ENDERLIN.
 * @license    New BSD License
 */

class Backtrace {

    /**
     * Backtrace tree.
     *
     * @var \Hoa\Tree object
     */
    protected $_tree = null;



    /**
     * Build an empty backtrace tree, and set the root.
     *
     * @access  public
     * @return  void
     */
    public function __construct ( ) {

        $this->_tree = new \Hoa\Tree(
            new Node(array(
                'function' => 'Bootstrap',
                'line'     => 42,
                'file'     => 'BigBlackHole',
                'class'    => null,
                'object'   => null,
                'type'     => null,
                'args'     => null
            ))
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

            $node = new Node($trace);

            if(true === $currentNode->childExists($node->getId()))
                $currentNode = $currentNode->getChild($node->getId());
            else {

                $child       = new \Hoa\Tree($node);
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
        array_shift($array); // \Hoa\Log\Backtrace::debug().

        if(isset($array[0]['class']) && $array[0]['class'] == 'Hoa\Log\Log')
            array_shift($array); // Hoa\Log::log().

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

        $out = new \Hoa\Tree\Visitor\Dot();

        return $out->visit($this->getTree());
    }
}

}
