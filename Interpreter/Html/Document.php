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
use Hoa\Xyl;

/**
 * Class \Hoa\Xyl\Interpreter\Html\Document.
 *
 * The <document /> component.
 *
 * @copyright  Copyright © 2007-2017 Hoa community
 * @license    New BSD License
 */
class Document extends Concrete implements Xyl\Element\Executable
{
    /**
     * Title.
     *
     * @var \Hoa\Xyl\Interpreter\Html\Title
     */
    protected $_title = null;



    /**
     * Paint the element.
     *
     * @param   \Hoa\Stream\IStream\Out  $out    Out stream.
     * @return  void
     */
    protected function paint(Stream\IStream\Out $out)
    {
        $root = $this->getAbstractElementSuperRoot();

        $locale   = $root->getLocale();
        $language = null;

        if (null !== $locale) {
            $language .= ' lang="' . $locale->getLanguage() . '"';
        }

        $out->writeAll(
            '<!DOCTYPE html>' . "\n\n" .
            '<!--[if lt IE 7]><html class="ie6"><![endif]-->' . "\n" .
            '<!--[if    IE 7]><html class="ie7"><![endif]-->' . "\n" .
            '<!--[if    IE 8]><html class="ie8"><![endif]-->' . "\n" .
            '<!--[if (gte IE 9)|!(IE)]>' . "\n" .
            '<html' . $language . '>' . "\n" .
            '<![endif]-->' . "\n" .
            '<head>' . "\n"
        );

        if (null !== $this->_title) {
            $out->writeAll('  ');
            $this->_title->render($out);
        }

        $out->writeAll(
            "\n" .
            '  <meta http-equiv="content-type" content="text/html; charset=utf-8" />' . "\n" .
            '  <meta http-equiv="content-type" content="text/javascript; charset=utf-8" />' . "\n" .
            '  <meta http-equiv="content-type" content="text/css; charset=utf-8" />' . "\n"
        );

        foreach ($root->getMetas() as $meta) {
            $out->writeAll('  <meta ' . $meta . ' />' . "\n");
        }

        foreach ($root->getDocumentLinks() as $link) {
            $out->writeAll('  <link ' . $link . ' />' . "\n");
        }

        $stylesheets = $root->getStylesheets();

        if (!empty($stylesheets)) {
            $out->writeAll("\n");

            foreach ($stylesheets as $href) {
                $out->writeAll(
                    '  <link type="text/css" href="' . $href .
                    '" rel="stylesheet" />' . "\n"
                );
            }
        }

        $out->writeAll(
            '</head>' . "\n" .
            '<body>' . "\n\n"
        );

        foreach ($this as $child) {
            if ('title' != $child->getName()) {
                $child->render($out);
            }
        }

        $out->writeAll(
            "\n\n" . '</body>' . "\n" . '</html>'
        );

        return;
    }

    /**
     * Pre-execute an element.
     *
     * @return  void
     */
    public function preExecute()
    {
        return;
    }

    /**
     * Post-execute an element.
     *
     * @return  void
     */
    public function postExecute()
    {
        $this->computeTitle();

        return;
    }

    /**
     * Compute title.
     *
     * @return  void
     */
    protected function computeTitle()
    {
        $xpath = $this->xpath('./__current_ns:title');

        if (empty($xpath)) {
            return;
        }

        $this->_title = $this->getConcreteElement($xpath[0]);

        return;
    }

    /**
     * Get the <title /> component.
     *
     * @return  \Hoa\Xyl\Interpreter\Html\Title
     */
    public function getTitle()
    {
        return $this->_title;
    }
}
