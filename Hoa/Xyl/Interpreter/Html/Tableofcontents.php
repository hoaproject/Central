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

namespace Hoa\Xyl\Interpreter\Html;

use Hoa\Stream;

/**
 * Class \Hoa\Xyl\Interpreter\Html\Tableofcontents.
 *
 * The <tableofcontents /> component.
 *
 * @copyright  Copyright © 2007-2017 Hoa community
 * @license    New BSD License
 */
class Tableofcontents extends Generic
{
    /**
     * Headings.
     *
     * @var array
     */
    protected $_headings = [];



    /**
     * Paint the element.
     *
     * @param   \Hoa\Stream\IStream\Out  $out    Out stream.
     * @return  void
     */
    protected function paint(Stream\IStream\Out $out)
    {
        $this->writeAttribute('class', 'toc');
        $out->writeAll('<ol' . $this->readAttributesAsString() . '>');

        $links = $this->selectChildElements('a');

        if (!empty($links)) {
            foreach ($links as $link) {
                $out->writeAll('<li>');
                $this->getConcreteElement($link)->render($out);
                $out->writeAll('</li>');
            }

            $out->writeAll('</ol>');

            return;
        }

        if (empty($this->_headings)) {
            $out->writeAll('</ol>');

            return;
        }

        $n     = 1;
        $first = true;

        foreach ($this->_headings as $heading) {
            $ni = $heading->getLevel();

            if (true === $first) {
                $n = $ni;
            }

            if ($n < $ni) {
                for ($i = $ni - $n - 1; $i >= 0; --$i) {
                    $out->writeAll('<ol class="toc toc-depth-' . $ni . '">');
                }
            } elseif ($n > $ni) {
                $out->writeAll('</li>');

                for ($i = $n - $ni - 1; $i >= 0; --$i) {
                    $out->writeAll('</ol></li>');
                }
            } elseif (false === $first) {
                $out->writeAll('</li>');
            } else {
                $first = false;
            }

            $n = $ni;

            $out->writeAll('<li>');

            if (true === $heading->attributeExists('id')) {
                $out->writeAll('<a href="#' . $heading->readAttribute('id') . '">');
                $heading->computeTransientValue($out);
                $out->writeAll('</a>');
            } else {
                $heading->computeTransientValue($out);
            }
        }

        for ($i = $n - 2; $i >= 0; --$i) {
            $out->writeAll('</li></ol>');
        }

        return;
    }

    /**
     * Add a heading in the table of content.
     *
     * @param   \Hoa\Xyl\Interpreter\Html\Heading  $heading    Heading.
     * @return  void
     */
    public function addHeading(Heading $heading)
    {
        $this->_headings[] = $heading;

        return;
    }

    /**
     * Get headings.
     *
     * @return  array
     */
    public function getHeadings()
    {
        return $this->_headings;
    }
}
