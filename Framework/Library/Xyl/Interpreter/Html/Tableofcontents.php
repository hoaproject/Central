<?php

/**
 * Hoa
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
-> import('Xyl.Element.Concrete')

/**
 * \Hoa\Xyl\Element\Executable
 */
-> import('Xyl.Element.Executable');

}

namespace Hoa\Xyl\Interpreter\Html {

/**
 * Class \Hoa\Xyl\Interpreter\Html\Tableofcontents.
 *
 * The <tableofcontents /> component.
 *
 * @author     Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright (c) 2007, 2011 Ivan ENDERLIN.
 * @license    http://gnu.org/licenses/gpl.txt GNU GPL
 */

class          Tableofcontents
    extends    \Hoa\Xyl\Element\Concrete
    implements \Hoa\Xyl\Element\Executable {

    /**
     * Entries of the table of contents.
     *
     * @var \Hoa\Xyl\Interpreter\Html\Tableofcontents array
     */
    protected $_entry    = array();

    /**
     * Depth: minimum.
     *
     * @var \Hoa\Xyl\Interpreter\Html\Tableofcontents int
     */
    protected $_depthMin = 1;

    /**
     * Depth: maximum.
     *
     * @var \Hoa\Xyl\Interpreter\Html\Tableofcontents int
     */
    protected $_depthMax = 6;



    /**
     * Paint the element.
     *
     * @access  protected
     * @param   \Hoa\Stream\IStream\Out  $out    Out stream.
     * @return  void
     */
    protected function paint ( \Hoa\Stream\IStream\Out $out ) {

        $this->writeAttribute('class', 'toc');
        $out->writeAll('<ol' .
                       $this->readAttributesAsString() . '>' . "\n");

        $n     = 1;
        $first = true;

        foreach($this->_entry as $entry) {

            $ni = $entry->getDepth();

            if(true === $first)
                $n = $ni;

            if($n < $ni)
                for($i = $ni - $n - 1; $i >= 0; --$i)
                    $out->writeAll("\n" . '<ol class="toc toc-depth-' . $ni . '">' . "\n");
            elseif($n > $ni) {

                $out->writeAll('</li>' . "\n");

                for($i = $n - $ni - 1; $i >= 0; --$i)
                    $out->writeAll('</ol>' . "\n" . '</li>' . "\n");
            }
            else
                if(false === $first)
                    $out->writeAll('</li>' . "\n");
                else
                    $first = false;

            $n = $ni;

            $out->writeAll('<li>');

            if(true === $entry->attributeExists('id')) {

                $out->writeAll('<a href="#' . $entry->readAttribute('id') . '">');
                $entry->getTitle()->computeTransientValue($out);
                $out->writeAll('</a>');
            }
            else
                $entry->getTitle()->computeTransientValue($out);
        }

        for($i = $n - 1; $i >= 0; --$i)
            $out->writeAll('</li>' . "\n" . '</ol>' . "\n");

        return;
    }

    /**
     * Pre-execute an element.
     *
     * @access  public
     * @return  void
     */
    public function preExecute ( ) {

        return;
    }

    /**
     * Post-execute an element.
     *
     * @access  public
     * @return  void
     */
    public function postExecute ( ) {

        if(true === $this->attributeExists('depth-min'))
            $this->_depthMin = (int) $this->readAttribute('depth-min');

        if(true === $this->attributeExists('depth-max'))
            $this->_depthMax = (int) $this->readAttribute('depth-max');

        return;
    }

    /**
     * Add an entry in the table of contents.
     *
     * @access  public
     * @param   \Hoa\Xyl\Interpreter\Html\Section  $section    Section to add.
     * @return  void
     */
    public function addEntry ( Section $section ) {

        $n = $section->getDepth();

        if($n < $this->_depthMin || $n > $this->_depthMax)
            return;

        $this->_entry[] = $section;

        return;
    }
}

}
