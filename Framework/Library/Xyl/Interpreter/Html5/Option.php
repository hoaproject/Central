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
 * @subpackage  Hoa_Xyl_Interpreter_Html5_Option
 *
 */

/**
 * Hoa_Xyl_Element_Concrete
 */
import('Xyl.Element.Concrete') and load();

/**
 * Class Hoa_Xyl_Interpreter_Html5_Option.
 *
 * The <option /> component.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2010 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Xyl
 * @subpackage  Hoa_Xyl_Interpreter_Html5_Option
 */

class       Hoa_Xyl_Interpreter_Html5_Option
    extends Hoa_Xyl_Element_Concrete {

    /**
     * Paint the element.
     *
     * @access  protected
     * @param   Hoa_Stream_Interface_Out  $out    Out stream.
     * @return  void
     */
    protected function paint ( Hoa_Stream_Interface_Out $out ) {

        $out->writeAll(
            '<option' . $this->readAttributesAsString() . '>' .
            $this->computeValue() .
            '</option>' . "\n"
        );

        return;
    }

    /**
     * Set (or restore) the option value.
     *
     * @access  public
     * @param   string  $value    Value.
     * @return  string
     */
    public function setValue ( $value ) {

        $old            = $this->getValue();
        $ae             = $this->getAbstractElement();
        $ae['selected'] = 'selected';

        return $old;
    }

    /**
     * Unset the option value.
     *
     * @access  public
     * @return  void
     */
    public function unsetValue ( ) {

        $ae = $this->getAbstractElement();
        unset($ae['selected']);

        return;
    }

    /**
     * Get the option value.
     *
     * @access  public
     * @return  string
     */
    public function getValue ( ) {

        return $this->readAttribute('value');
    }

    /**
     * Whether the option is selected or not.
     *
     * @access  public
     * @return  bool
     */
    public function isSelected ( ) {

        return true === $this->attributeExists('selected');
    }
}
