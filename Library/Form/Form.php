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
 * Copyright (c) 2007, 2008 Ivan ENDERLIN. All rights reserved.
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
 * @package     Hoa_Form
 *
 */

/**
 * Hoa_Framework
 */
require_once 'Framework.php';

/**
 * Hoa_Form_Exception
 */
import('Form.Exception');

/**
 * Hoa_Form_Element_Abstract
 */
import('Form.Element.Abstract');

/**
 * Hoa_Form_Element_Form
 */
import('Form.Element.Form');

/**
 * Class Hoa_Form.
 *
 * Create the form, redirects unknown calls on the form element, and
 * proposes the auto-complete, filter, and validate actions throught many
 * iterator.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2008 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Form
 */

class Hoa_Form implements IteratorAggregate {

    /**
     * Constant of element name.
     *
     * @const string
     */
    const ELEMENT_FORM           = 'form';
    const ELEMENT_INPUT_BUTTON   = 'button';
    const ELEMENT_INPUT_CHECKBOX = 'checkbox';
    const ELEMENT_INPUT_FILE     = 'file';
    const ELEMENT_INPUT_HIDDEN   = 'hidden';
    const ELEMENT_INPUT_IMAGE    = 'image';
    const ELEMENT_INPUT_PASSWORD = 'password';
    const ELEMENT_INPUT_RADIO    = 'radio';
    const ELEMENT_INPUT_RESET    = 'reset';
    const ELEMENT_INPUT_SUBMIT   = 'submit';
    const ELEMENT_INPUT_TEXT     = 'text';
    const ELEMENT_INPUT_LABEL    = 'label';
    const ELEMENT_TEXTAREA       = 'textarea';
    const ELEMENT_SELECT         = 'select';
    const ELEMENT_FIELDSET       = 'fieldset';

    /**
     * The form element.
     *
     * @var Hoa_Form Hoa_Form_Element_Form
     */
    protected $form     = null;



    /**
     * Built the form element manager.
     *
     * @access  public
     * @param   mixed   $formArgs    Form element arguments. Should be a string
     *                               (form action attribute value) or an array
     *                               (all configurations).
     * @return  void
     */
    public function __construct ( $formArgs ) {

        $this->createForm($formArgs);
    }

    /**
     * Create a form.
     *
     * @access  protected
     * @param   mixed   $formArgs    Form element arguments. Should be a string
     *                               (form action attribute value) or an array
     *                               (all configurations).
     * @return  void
     */
    protected function createForm ( $formArgs ) {

        $this->form = new Hoa_Form_Element_Form($formArgs);
    }

    /**
     * Get the form.
     *
     * @access  public
     * @return  Hoa_Form_Element_Form
     */
    public function getForm ( ) {

        return $this->form;
    }

    /**
     * Redirect unknown method on the form element.
     *
     * @access  public
     * @param   string  $name         The method name.
     * @param   array   $arguments    The method arguments.
     * @return  mixed
     */
    public function __call ( $name, Array $arguments ) {

        if(false === method_exists($this->getForm(), $name))
            throw new Hoa_Form_Exception(
                'Call to undefined method %s().', 0, $name);

        return call_user_func_array(array($this->getForm(), $name), $arguments);
    }

    /**
     * Set the value, select to right option, the right radio, etc., according
     * to the data array.
     *
     * @access  public
     * @param   array   $data    Data array.
     * @param   array   $in      Needed for the recursivity. Contains the
     *                           collection to iterate (null will select the
     *                           form elements).
     * @return  void
     */
    public function autoComplete ( Array &$data, $in = null ) {

        if(null === $in)
            $in = $this->getForm()->getElements();

        foreach($in as $id => $element) {

            $element->autoComplete($data);
            $elements = $element->getElements();
            null !== $elements && $this->autoComplete($data, $elements);
        }
    }

    /**
     * Filter the data of each form elements.
     *
     * @access  public
     * @param   array   $in    Needed for the recursivity. Contains the
     *                         collection to iterate (null will select the form
     *                         elements).
     * @return  void
     */
    public function filter ( $in = null ) {

        if(null === $in)
            $in = $this->getForm()->getElements();

        foreach($in as $id => $element) {

            $element->filter();
            $elements = $element->getElements();
            null !== $elements && $this->filter($elements);
        }
    }

    /**
     * Check if the form is valid.
     * It is not very nice to valid all form in the same page.
     * We try to detect if it is the right form. It should be
     * wrong, but we cannot detect as much as we would like because of
     * Javascript and bad HTML construction.
     *
     * @access  public
     * @return  bool
     */
    public function isValid ( ) {

        $data = $this->getDataArray();

        if($data == array())
            return false;

        $isThisForm = false;

        foreach($this->getIterator() as $key => $value)
            $isThisForm |= array_key_exists($value->getName(), $data);

        if(false === (bool) $isThisForm)
            return false;

        $this->autoComplete($data);
        $this->filter();
        return $this->isValidSuit();
    }

    /**
     * The real validation method.
     * Test if each element of the form is valid (with the isValid method from
     * the validator).
     *
     * @access  private
     * @param   array    $in       Needed for the recursivity. Contains the
     *                             collection to iterate (null will select the
     *                             form elements).
     * @param   bool     $valid    The validity of the form suggestion.
     * @return  bool
     */
    private function isValidSuit ( $in = null, $valid = true ) {

        if(null === $in)
            $in = $this->getForm()->getElements();

        foreach($in as $id => $element) {

            $valid    = $element->isValid() && $valid;
            $elements = $element->getElements();

            if(null !== $elements)
                $valid = $this->isValidSuit($elements, $valid) && $valid;
        }

        return $valid;
    }

    /**
     * Get an element from the form. Do not use $_GET or $_POST directly, but
     * must use the get method.
     *
     * @access  public
     * @param   string  $name    The element name.
     * @return  mixed
     */
    public function get ( $name = null ) {

        if(null === $name)
            return null;

        $out = new ArrayObject(
                   array(),
                   ArrayObject::ARRAY_AS_PROPS,
                   'ArrayIterator'
               );

        foreach($this->getIterator() as $id => $element)
            if($element->getName() == $name)
                $out->offsetSet($id, $element);

        switch($out->count()) {

            case 0:
                return null;
              break;

            case 1:
                return current($out);
              break;

            default:
                return $out;
        }
    }

    /**
     * Get an iterator.
     *
     * @access  public
     * @return  ArrayObject
     */
    public function getIterator ( ) {

        $return = array();

        $this->getIteratorIterate(null, $return);

        return new ArrayObject(
            $return,
            ArrayObject::ARRAY_AS_PROPS,
            'ArrayIterator'
        );
    }

    /**
     * Get an iterator of an iterator.
     *
     * @access  public
     * @return  ArrayIterator
     */
    public function getIteratorIterator ( ) {

        return $this->getIterator()->getIterator();
    }

    /**
     * Built the iterator.
     *
     * @access  private
     * @param   array    $in        Collection of elements.
     * @param   array    $return    The iterator.
     * @return  void
     */
    private function getIteratorIterate ( $in = null, &$return = array() ) {

        if(null === $in)
            $in = $this->getForm()->getElements();

        foreach($in as $id => $element) {

            $return[$element->getId()] = $element;
            $elements = $element->getElements();
            null !== $elements && $this->getIteratorIterate($elements, $return);
        }
    }

    /**
     * Get the data array, i.e. the array that contains all the form data.
     *
     * @access  protected
     * @return  array
     */
    protected function getDataArray ( ) {

        switch(strtolower($this->getForm()->getAttribute('method'))) {

            case 'post':
                return $_POST;

            case 'get':
                return $_GET;

            default:
                return $_POST;
        }
    }

    /**
     * Transform the form into a string.
     *
     * @access  public
     * @return  string
     */
    public function __toString ( ) {

        $data = $this->getDataArray();

        $this->autoComplete($data);
        $this->filter();
        return $this->getForm()->__toString();
    }
}
