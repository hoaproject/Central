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
 * \Hoa\Tree\Visitor\Generic
 */
-> import('Tree.Visitor.Generic')

/**
 * \Hoa\Visitor\Visit
 */
-> import('Visitor.Visit');

}

namespace Hoa\Tree\Visitor {

/**
 * Class \Hoa\Tree\Visitor\Dump.
 *
 * Dump a tree.
 *
 * @author     Ivan Enderlin <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright © 2007-2012 Ivan Enderlin.
 * @license    New BSD License
 */

class Dump extends Generic implements \Hoa\Visitor\Visit {

    /**
     * Tree deep.
     *
     * @var \Hoa\Tree\Visitor\Dump int
     */
    protected $_i = 0;



    /**
     * Just change the default transversal order value.
     *
     * @access  public
     * @param   int     $order    Traversal order (please, see the * self::*_ORDER
     *                            constants).
     * @return  void
     */
    public function __construct ( $order = parent::IN_ORDER ) {

        parent::__construct($order);

        return;
    }

    /**
     * Visit an element.
     *
     * @access  public
     * @param   \Hoa\Visitor\Element  $element    Element to visit.
     * @param   mixed                 &$handle    Handle (reference).
     * @param   mixed                 $eldnah     Handle (not reference).
     * @return  string
     */
    public function visit ( \Hoa\Visitor\Element $element,
                            &$handle = null,
                             $eldnah = null ) {

        $pre    = null;
        $in     = '> ' . str_repeat('  ', $this->_i) .
                  $element->getValue() . "\n";
        $post   = null;
        $childs = $element->getChilds();
        $i      = 0;
        $max    = floor(count($childs) / 2);

        ++$this->_i;

        foreach($childs as $id => $child)
            if($i++ < $max)
                $pre  .= $child->accept($this, $handle, $eldnah);
            else
                $post .= $child->accept($this, $handle, $eldnah);

        --$this->_i;

        switch($this->getOrder()) {

            case parent::IN_ORDER:
                return $in  . $pre . $post;
              break;

            case parent::POST_ORDER:
                return $post . $in . $pre;
              break;

            default:
                return $pre  . $in . $post;
              break;
        }
    }
}

}
