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
 * \Hoa\Xyl\Element\Concrete
 */
-> import('Xyl.Element.Concrete');

}

namespace Hoa\Xyl\Interpreter\Html5 {

/**
 * Class \Hoa\Xyl\Interpreter\Html5\Grid.
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
