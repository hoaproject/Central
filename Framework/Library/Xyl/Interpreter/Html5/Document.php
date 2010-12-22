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
 *
 *
 * @category    Framework
 * @package     Hoa_Xyl
 * @subpackage  Hoa_Xyl_Interpreter_Html5_Document
 *
 */

/**
 * Hoa_Xyl_Element_Concrete
 */
import('Xyl.Element.Concrete') and load();

/**
 * Hoa_Xyl_Element_Executable
 */
import('Xyl.Element.Executable') and load();

/**
 * Class Hoa_Xyl_Interpreter_Html5_Document.
 *
 * The <document /> component.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2010 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Xyl
 * @subpackage  Hoa_Xyl_Interpreter_Html5_Document
 */

class          Hoa_Xyl_Interpreter_Html5_Document
    extends    Hoa_Xyl_Element_Concrete
    implements Hoa_Xyl_Element_Executable {

    /**
     * All document resources.
     *
     * @var Hoa_Xyl_Interpreter_Html5_Document array
     */
    protected $_resources = array();



    /**
     * Paint the element.
     *
     * @access  protected
     * @param   Hoa_Stream_Interface_Out  $out    Out stream.
     * @return  void
     */
    protected function paint ( Hoa_Stream_Interface_Out $out ) {

        $root = $this->getAbstractElementSuperRoot();

        $out->writeAll(
            '<!DOCTYPE html>' . "\n\n" .
            '<html>' . "\n" .
            '<head>' . "\n"
        );

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

        foreach($this as $name => $child)
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
                        throw new Hoa_Xyl_Interpreter_Html5_Exception(
                            'Failed to copy %s in %s.',
                            0, array($style, $redirect));

                $style    = $redirect;
            }

            $this->_resources['css'][] = $style;
        }

        return;
    }
}
