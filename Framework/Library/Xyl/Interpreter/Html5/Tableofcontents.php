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
 * @subpackage  Hoa_Xyl_Interpreter_Html5_Tableofcontents
 *
 */

/**
 * Hoa_Xyl_Element_Concrete
 */
import('Xyl.Element.Concrete') and load();

/**
 * Class Hoa_Xyl_Interpreter_Html5_Tableofcontents.
 *
 * The <tableofcontents /> component.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2010 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Xyl
 * @subpackage  Hoa_Xyl_Interpreter_Html5_Tableofcontents
 */

class       Hoa_Xyl_Interpreter_Html5_Tableofcontents
    extends Hoa_Xyl_Element_Concrete {

    /**
     * Entries of the table of contents.
     *
     * @var Hoa_Xyl_Interpreter_Html5_Tableofcontents array
     */
    protected $_entry = array();



    /**
     * Paint the element.
     *
     * @access  protected
     * @param   Hoa_Stream_Interface_Out  $out    Out stream.
     * @return  void
     */
    protected function paint ( Hoa_Stream_Interface_Out $out ) {

        $out->writeAll('<ul class="toc"' .
                       $this->readAttributesAsString() . '>' . "\n");

        foreach($this->_entry as $entry)
            $out->writeAll(
                '  <li>' . $entry . '</li>' .
                "\n"
            );

        $out->writeAll('</ul>' . "\n");

        return;
    }

    /**
     * Add an entry in the table of contents.
     *
     * @access  public
     * @param   Hoa_Xyl_Interpreter_Html5_Section  $section    Section to add.
     * @return  void
     */
    public function addEntry ( Hoa_Xyl_Interpreter_Html5_Section $section ) {

        $this->_entry[] = $section->computeValue();

        return;
    }
}
