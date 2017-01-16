<?php

/**
 * Hoa
 *
 *
 * @license
 *
 * New BSD License
 *
 * Copyright © 2007-2017, Hoa community. All rights reserved.
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

namespace Hoa\Tree\Visitor;

use Hoa\Visitor;

/**
 * Class \Hoa\Tree\Visitor\Dot.
 *
 * Transform a tree in DOT language.
 *
 * @copyright  Copyright © 2007-2017 Hoa community
 * @license    New BSD License
 */
class Dot extends Generic implements Visitor\Visit
{
    /**
     * Tree deep.
     *
     * @var int
     */
    protected $_i = 0;



    /**
     * Visit an element.
     *
     * @param   \Hoa\Visitor\Element  $element    Element to visit.
     * @param   mixed                &$handle     Handle (reference).
     * @param   mixed                 $eldnah     Handle (not reference).
     * @return  string
     */
    public function visit(
        Visitor\Element $element,
        &$handle = null,
        $eldnah  = null
    ) {
        $ou  = null;
        $t   = null;

        if ($this->_i == 0) {
            $ou  = 'digraph {' . "\n";
            $t   = '}' . "\n";
        }

        $foo = $element->getValue();
        $bar = null;
        ++$this->_i;

        if (null == $eldnah) {
            $eldnah  = $foo;
            $ou     .= '    "' . md5($foo) . '" [label = "' . $foo . '"];' . "\n";
        }

        foreach ($element->getChilds() as $child) {
            $left   = md5($eldnah);
            $right  = md5($eldnah . '.' . $child->getValue());

            $ou .=
                '    "' . $left  . '" -> "' . $right . '";' . "\n" .
                '    "' . $right . '" [label = "' .
                str_replace('\\', '\\\\', $child->getValue()) .
                '"];' . "\n";
            $bar .=
                $child->accept($this, $handle, $eldnah . '.' .
                $child->getValue());
        }

        $ou .= $bar;

        --$this->_i;

        return $ou . $t;
    }
}
