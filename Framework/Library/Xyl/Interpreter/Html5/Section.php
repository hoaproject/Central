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
-> import('Xyl.Element.Concrete')

/**
 * \Hoa\Xyl\Element\Executable
 */
-> import('Xyl.Element.Executable');

}

namespace Hoa\Xyl\Interpreter\Html5 {

/**
 * Class \Hoa\Xyl\Interpreter\Html5\Section.
 *
 * Abstract component for <section* /> components.
 *
 * @author     Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright (c) 2007, 2011 Ivan ENDERLIN.
 * @license    http://gnu.org/licenses/gpl.txt GNU GPL
 */

abstract class Section
    extends    \Hoa\Xyl\Element\Concrete
    implements \Hoa\Xyl\Element\Executable {

    /**
     * Depth.
     *
     * @var \Hoa\Xyl\Interpreter\Html5\Section int
     */
    protected $_n     = 0;

    /**
     * Title.
     *
     * @var \Hoa\Xyl\Interpreter\Html5\Title object
     */
    protected $_title = null;



    /**
     * Paint the element.
     *
     * @access  protected
     * @param   \Hoa\Stream\IStream\Out  $out    Out stream.
     * @return  void
     */
    protected function paint ( \Hoa\Stream\IStream\Out $out ) {

        $out->writeAll('<h' . $this->_n .
                       $this->readAttributesAsString() . '>');
        $this->getTitle()->render($out);
        $out->writeAll('</h' . $this->_n . '>' . "\n");

        foreach($this as $child)
            if('title' != $child->getName())
                $child->render($out);

        return;
    }

    /**
     * Execute an element.
     *
     * @access  public
     * @return  void
     */
    public function execute ( ) {

        $this->computeFor();
        $this->computeTitle();

        return;
    }

    /**
     * Compute @for.
     *
     * @access  protected
     * @return  void
     */
    protected function computeFor ( ) {

        if(false === $this->attributeExists('for'))
            return;

        $for = $this->readAttribute('for');
        $toc = $this->xpath(
            '//__current_ns:tableofcontents[@id="' . $for . '"]'
        );

        if(!isset($toc[0]))
            return;

        $this->getConcreteElement($toc[0])->addEntry($this);

        return;
    }

    /**
     * Compute title.
     *
     * @access  protected
     * @return  void
     */
    protected function computeTitle ( ) {

        $xpath = $this->xpath('./__current_ns:*[1]');

        if(empty($xpath))
            return;

        $title = $this->getConcreteElement($xpath[0]);

        if(!($title instanceof Title))
            return;

        $this->_title = $title;

        return;
    }

    /**
     * Get the <title /> component.
     *
     * @access  pubic
     * @return  \Hoa\Xyl\Interpreter\Html5\Title
     */
    public function getTitle ( ) {

        return $this->_title;
    }
}

}
