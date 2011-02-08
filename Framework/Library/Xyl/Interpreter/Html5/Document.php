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
 * Copyright (c) 2007, 2010 Ivan ENDERLIN. All rights reserved.
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
 * Class \Hoa\Xyl\Interpreter\Html5\Document.
 *
 * The <document /> component.
 *
 * @author     Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright (c) 2007, 2010 Ivan ENDERLIN.
 * @license    http://gnu.org/licenses/gpl.txt GNU GPL
 */

class          Document
    extends    \Hoa\Xyl\Element\Concrete
    implements \Hoa\Xyl\Element\Executable {

    /**
     * Title.
     *
     * @var \Hoa\Xyl\Interpreter\Html5\Title object
     */
    protected $_title     = null;

    /**
     * All document resources.
     *
     * @var \Hoa\Xyl\Interpreter\Html5\Document array
     */
    protected $_resources = array();



    /**
     * Paint the element.
     *
     * @access  protected
     * @param   \Hoa\Stream\IStream\Out  $out    Out stream.
     * @return  void
     */
    protected function paint ( \Hoa\Stream\IStream\Out $out ) {

        $root = $this->getAbstractElementSuperRoot();

        $out->writeAll(
            '<!DOCTYPE html>' . "\n\n" .
            '<!--[if lt IE 7]><html class="ie6"><![endif]-->' . "\n" .
            '<!--[if    IE 7]><html class="ie7"><![endif]-->' . "\n" .
            '<!--[if    IE 8]><html class="ie8"><![endif]-->' . "\n" .
            '<!--[if (gte IE 9)|!(IE)]>' . "\n" .
            '<html>' . "\n" .
            '<![endif]-->' . "\n" .
            '<head>' . "\n" .
            '  <title>'
        );

        if(null !== $this->_title)
            $this->_title->render($out);

        $out->writeAll('</title>' . "\n");

        if(isset($this->_resources['css']))
            foreach($this->_resources['css'] as $href)
                $out->writeAll(
                    '  <link type="text/css" href="' .
                    $root->resolve($href) .
                    '" rel="stylesheet" />' . "\n"
                );

        $out->writeAll(
            '</head>' . "\n" .
            '<body>' . "\n\n"
        );

        foreach($this as $child)
            if('title' != $child->getName())
                $child->render($out);

        $out->writeAll(
            "\n" . '</body>' . "\n" . '</html>'
        );

        return;
    }

    /**
     * Execute an element.
     *
     * @access  public
     * @return  void
     */
    public function execute ( ) {

        $this->computeTitle();
        $this->computeStylesheet();

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
     * Compute stylesheet.
     *
     * @access  protected
     * @return  void
     */
    protected function computeStylesheet ( ) {

        $root                    = $this->getAbstractElementSuperRoot();
        $styles                  = $root->getStylesheets();
        $this->_resources['css'] = array();

        foreach($styles as $style) {

            $resolved = $root->resolve($style);

            if(false === file_exists($resolved))
                continue;

            if('hoa://Library/Xyl/' == substr($style, 0, 18)) {

                $redirect = 'hoa://Application/Public/' . substr($style, 18);

                if(false === file_exists($redirect))
                    if(false === copy($resolved, $redirect))
                        throw new \Hoa\Xyl\Interpreter\Html5\Exception(
                            'Failed to copy %s in %s.',
                            0, array($style, $redirect));

                $style    = $redirect;
            }

            $this->_resources['css'][] = $style;
        }

        return;
    }

    /**
     * Get the <title /> component.
     *
     * @access  public
     * @return  \Hoa\Xyl\Interpreter\Html5\Title
     */
    public function getTitle ( ) {

        return $this->_title;
    }
}

}
