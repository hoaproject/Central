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
 * @copyright  Copyright © 2007-2011 Ivan ENDERLIN.
 * @license    New BSD License
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
