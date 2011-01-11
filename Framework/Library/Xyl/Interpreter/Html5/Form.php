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
 * @subpackage  Hoa_Xyl_Interpreter_Html5_Form
 *
 */

/**
 * Hoa_Xyl_Element_Concrete
 */
import('Xyl.Element.Concrete') and load();

/**
 * Hoa_Xyl_Element_Executable
 */
import('Xyl.Element.Executable');

/**
 * Class Hoa_Xyl_Interpreter_Html5_Form.
 *
 * The <form /> component.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2010 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Xyl
 * @subpackage  Hoa_Xyl_Interpreter_Html5_Form
 */

class          Hoa_Xyl_Interpreter_Html5_Form
    extends    Hoa_Xyl_Element_Concrete
    implements Hoa_Xyl_Element_Executable {

    protected $_postData = array();



    /**
     * Paint the element.
     *
     * @access  protected
     * @param   Hoa_Stream_Interface_Out  $out    Out stream.
     * @return  void
     */
    protected function paint ( Hoa_Stream_Interface_Out $out ) {

        $out->writeAll(
            '<form' . $this->readAttributesAsString() . '>' . "\n"
        );

        foreach($this as $name => $child)
            $child->render($out);

        $out->writeAll(
            '</form>' . "\n"
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

        $this->computePostData();

        return;
    }

    /**
     * Compute post data.
     *
     * @access  public
     * @return  void
     */
    protected function computePostData ( ) {

        switch(strtolower($this->readAttribute('method'))) {

            case 'post':
                $this->_postData = $_POST;
              break;

            default:
                $this->_postData = $_GET;
        }

        $inputs = array_merge(
            $this->xpath('descendant-or-self::__current_ns:input'),
            array()
            /*
            $this->xpath('descendant-or-self::__current_ns:textarea'),
            $this->xpath('descendant-or-self::__current_ns:select')
            */
        );

        if(empty($this->_postData))
            return;

        foreach($inputs as $input) {

            $input = $this->getConcreteElement($input);
            $name  = $input->readAttribute('name');

            if(!isset($this->_postData[$name])) {

                $input->unsetValue();

                continue;
            }

            $input->setValue($this->_postData[$name]);
            $input->checkValidity();
        }
    }
}
