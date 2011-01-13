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
 * @subpackage  Hoa_Xyl_Interpreter_Html5_Input
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
 * Hoa_Test_Praspel_Compiler
 */
import('Test.Praspel.Compiler');

/**
 * Class Hoa_Xyl_Interpreter_Html5_Input.
 *
 * The <input /> component.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2010 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Xyl
 * @subpackage  Hoa_Xyl_Interpreter_Html5_Input
 */

class          Hoa_Xyl_Interpreter_Html5_Input
    extends    Hoa_Xyl_Element_Concrete
    implements Hoa_Xyl_Element_Executable {

    /**
     * Type of input: button, checkbox, color, date, datetime, datetime-local,
     * email, file, hidden, image, month, number, password, radio, range, reset,
     * search, submit, tel, text, time, url and week.
     *
     * @var Hoa_Xyl_Interpreter_Html5_Input string
     */
    protected $_type            = null;

    /**
     * Praspel compiler, to interprete the validate attribute.
     *
     * @var Hoa_Test_Praspel_Compiler object
     */
    protected static $_compiler = null;

    /**
     * Whether the input is valid or not.
     *
     * @var Hoa_Xyl_Interpreter_Html5_Input bool
     */
    protected $_validity        = true;



    /**
     * Paint the element.
     *
     * @access  protected
     * @param   Hoa_Stream_Interface_Out  $out    Out stream.
     * @return  void
     */
    protected function paint ( Hoa_Stream_Interface_Out $out ) {

        $out->writeAll(
            '<input' . $this->readAttributesAsString() . ' />'
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

        $type = strtolower($this->readAttribute('type'));

        switch($type) {

            case 'button':
            case 'checkbox':
            case 'color':
            case 'date':
            case 'datetime':
            case 'datetime-local':
            case 'email':
            case 'file':
            case 'hidden':
            case 'image':
            case 'month':
            case 'number':
            case 'password':
            case 'radio':
            case 'range':
            case 'reset':
            case 'search':
            case 'submit':
            case 'tel':
            case 'text':
            case 'time':
            case 'url':
            case 'week':
                $this->_type = $type;
              break;

            default:
                $this->_type = 'text';
        }

        return;
    }

    /**
     * Set (or restore) the input value.
     *
     * @access  public
     * @param   string  $value    Value.
     * @return  string
     */
    public function setValue ( $value ) {

        $old = $this->getValue();
        $ae  = $this->getAbstractElement();

        switch($this->getType()) {

            case 'checkbox':
                $ae['checked'] = 'checked';
              break;

            case 'radio':
                if($value == $this->readAttribute('value'))
                    $ae['checked'] = 'checked';
                else
                    unset($ae['checked']);
              break;

            default:
                $ae['value'] = $value;
        }

        return $old;
    }

    /**
     * Get the input value.
     *
     * @access  public
     * @return  string
     */
    public function getValue ( ) {

        $value = $this->readAttribute('value');

        if(ctype_digit($value))
            $value = (int) $value;
        elseif(is_numeric($value))
            $value = (float) $value;

        return $value;
    }

    /**
     * Unset the input value.
     *
     * @access  public
     * @return  void
     */
    public function unsetValue ( ) {

        switch($this->getType()) {

            case 'checkbox':
            case 'radio':
                $ae = $this->getAbstractElement();
                unset($ae['checked']);
              break;
        }

        return;
    }

    /**
     * Check the input validity.
     *
     * @access  public
     * @param   mixed  $value    Value (if null, will find the value).
     * @return  bool
     */
    public function checkValidity ( $value = null ) {

        $type = $this->getType();

        if('submit' === $type || 'reset' === $type) {

            $this->_validity = false;

            if(   null   === $value
               || $value ==  $this->getValue())
                $this->_validity = true;

            return $this->_validity;
        }

        $validates = array();

        if(true === $this->attributeExists('validate'))
            $validates['@'] = $this->readAttribute('validate');

        $validates = array_merge(
            $validates,
            $this->readCustomAttributes('validate')
        );

        if(empty($validates))
            return true;

        $onerrors = array();

        if(true === $this->attributeExists('onerror'))
            $onerrors['@'] = $this->readAttribute('onerror');

        $onerrors = array_merge(
            $onerrors,
            $this->readCustomAttributes('onerror')
        );

        if(null === $value)
            $value = $this->getValue();
        else
            if(ctype_digit($value))
                $value = (int) $value;
            elseif(is_numeric($value))
                $value = (float) $value;

        if(null === self::$_compiler)
            self::$_compiler = new Hoa_Test_Praspel_Compiler();

        $this->_validity = true;

        foreach($validates as $name => $realdom) {

            self::$_compiler->compile('@requires i:' .$realdom . ';');
            $praspel  = self::$_compiler->getRoot();
            $variable = $praspel->getClause('requires')->getVariable('i');
            $decision = false;

            foreach($variable->getDomains() as $domain)
                $decision = $decision || $domain->predicate($value);

            $this->_validity = $this->_validity && $decision;

            if(true === $decision)
                continue;

            if(!isset($onerrors[$name]))
                continue;

            $errors = $this->xpath(
                '//__current_ns:error[@id="' .
                implode('" or @id="', explode(' ', $onerrors[$name])) .
                '"]'
            );

            foreach($errors as $error)
                $this->getConcreteElement($error)->setVisibility(true);
        }

        return $this->_validity;
    }

    /**
     * Whether the input is valid or not.
     *
     * @access  public
     * @return  bool
     */
    public function isValid ( ) {

        return $this->_validity;
    }

    /**
     * Get the input type.
     *
     * @access  public
     * @return  string
     */
    public function getType ( ) {

        return $this->_type;
    }

    /**
     * Read attributes as a string.
     *
     * @access  public
     * @return  string
     */
    public function readAttributesAsString ( ) {

        $out        = null;
        $attributes = $this->getAbstractElement()->readAttributes();
        unset($attributes['bind']);

        foreach($attributes as $name => $value) {

            if('validate' == substr($name, 0, 8))
                continue;

            if('onerror' == substr($name, 0, 7))
                continue;

            $out .= ' ' . $name . '="' . str_replace('"', '\"', $value) . '"';
        }

        return $out;
    }
}
