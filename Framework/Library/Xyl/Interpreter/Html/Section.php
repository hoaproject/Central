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
 * \Hoa\Xyl\Interpreter\Html\Concrete
 */
-> import('Xyl.Interpreter.Html.Concrete')

/**
 * \Hoa\Xyl\Element\Executable
 */
-> import('Xyl.Element.Executable');

}

namespace Hoa\Xyl\Interpreter\Html {

/**
 * Class \Hoa\Xyl\Interpreter\Html\Section.
 *
 * Abstract component for <section* /> components.
 *
 * @author     Ivan Enderlin <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright © 2007-2011 Ivan Enderlin.
 * @license    New BSD License
 */

abstract class Section extends Concrete implements \Hoa\Xyl\Element\Executable {

    /**
     * Depth.
     *
     * @var \Hoa\Xyl\Interpreter\Html\Section int
     */
    protected $_n     = 0;

    /**
     * Title.
     *
     * @var \Hoa\Xyl\Interpreter\Html\Title object
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

        if(null !== $title = $this->getTitle())
            $title->render($out);

        $out->writeAll('</h' . $this->_n . '>' . "\n");

        foreach($this as $child)
            if('title' != $child->getName())
                $child->render($out);

        return;
    }

    /**
     * Pre-execute an element.
     *
     * @access  public
     * @return  void
     */
    public function preExecute ( ) {

        $this->computeFor();

        return;
    }

    /**
     * Post-execute an element.
     *
     * @access  public
     * @return  void
     */
    public function postExecute ( ) {

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

        $e = $this->getAbstractElement();

        if(false === $e->attributeExists('for'))
            return;

        $tocs = $this->xpath(
            '//__current_ns:tableofcontents[@id="' .
            implode('" or @id="', $e->readAttributeAsList('for'))
            . '"]'
        );

        if(empty($tocs))
            return;

        foreach($tocs as $toc)
            $this->getConcreteElement($toc)->addEntry($this);

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
     * @return  \Hoa\Xyl\Interpreter\Html\Title
     */
    public function getTitle ( ) {

        return $this->_title;
    }

    /**
     * Get depth.
     *
     * @access  public
     * @return  int
     */
    public function getDepth ( ) {

        return $this->_n;
    }
}

}
