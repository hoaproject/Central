<?php

/**
 * Hoa
 *
 *
 * @license
 *
 * New BSD License
 *
 * Copyright (c) 2007-2011, Ivan Enderlin. All rights reserved.
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
 * \Hoa\Xyl\Element\Concrete
 */
-> import('Xyl.Element.Concrete');

}

namespace Hoa\Xyl\Interpreter\Html {

/**
 * Class \Hoa\Xyl\Interpreter\Html\Grid.
 *
 * The <grid /> component.
 *
 * @author     Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright (c) 2007, 2011 Ivan ENDERLIN.
 * @license    http://gnu.org/licenses/gpl.txt GNU GPL
 */

class Grid extends \Hoa\Xyl\Element\Concrete {

    /**
     * Paint the element.
     *
     * @access  protected
     * @param   \Hoa\Stream\IStream\Out  $out    Out stream.
     * @return  void
     */
    protected function paint ( \Hoa\Stream\IStream\Out $out ) {

        $out->writeAll('<div class="grid"' .
                       $this->readAttributesAsString() . '>' . "\n");

        $grows = $this[0];
        $axis  = true;

        if(true === $this->attributeExists('axis'))
            $axis = 'normal' === $this->readAttribute('axis')
                        ? true
                        : false;


        if(false === $axis) {

            // Kick me…
            $matrix = array();

            foreach($grows as $grow) {

                $submatrix = array();

                foreach($grow as $gcell)
                    $submatrix[] = $gcell;

                $matrix[] = $submatrix;
            }

            $x = count($grows);
            $y = count($grows[0]);

            for($e = 0; $e < $y; ++$e)
                for($i = 0; $i < $x; ++$i) {

                    $h = $this[0][$e];

                    if(null === $h) {

                        $this[0]->offsetSet($e, clone $this[0][$e - 1]);
                        $h = $this[0][$e];
                    }

                    $h->offsetSet($i, $matrix[$i][$e]);
                }

            for($e = 0; $e < $y; ++$e)
                for($i = $x; $i < $y; ++$i)
                    unset($this[0][$e][$i]);

            for($e = $y; $e < $x; ++$e)
                unset($this[0][$e]);
        }

        foreach($this as $child)
            $child->render($out);

        $out->writeAll('</div>' . "\n");

        return;
    }
}

}
