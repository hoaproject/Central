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
 * @subpackage  Hoa_Form_Element_Form
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
 * Hoa_Factory
 */
import('Factory.~');

/**
 * Class Hoa_Form_Element_Form.
 *
 * Describe the form element.
 * The form element is the root of all nested element. 
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2008 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Form
 * @subpackage  Hoa_Form_Element_Form
 */

class Hoa_Form_Element_Form extends Hoa_Form_Element_Abstract {

    /**
     * Collections of element.
     *
     * @var Hoa_Form_Element_Form array
     */
    protected $elements = array();

    /**
     * Elements matching.
     *
     * @var Hoa_Form_Element_Form array
     */
    private $matching = array(
        'form'     => 'Form',
        'fieldset' => 'Fieldset',
        'button'   => 'InputButton',
        'checkbox' => 'InputCheckbox',
        'file'     => 'InputFile',
        'hidden'   => 'InputHidden',
        'image'    => 'InputImage',
        'password' => 'InputPassword',
        'radio'    => 'InputRadio',
        'reset'    => 'InputReset',
        'submit'   => 'InputSubmit',
        'text'     => 'InputText',
        'label'    => 'Label',
        'textarea' => 'Textarea',
        'select'   => 'Select'
    );

    /**
     * List of attributes.
     *
     * @var Hoa_Form_Element_Abstract array
     */
    protected $attributes = array(
        'action'          => array(
            'default'     => null,
            'required'    => true, 
            'type'        => parent::ATTRIBUTE_TYPE_URI,
            'description' => 'server-side form handler',
            'value'       => null,
        ),
        'method'          => array(
            'default'     => 'post',
            'required'    => false,
            'type'        => '(get|post)',
            'description' => 'HTTP method used to submit the form',
            'value'       => 'post'
        ),
        'enctype'         => array(
            'default'     => 'application/x-www-form-urlencoded',
            'required'    => false,
            'type'        => parent::ATTRIBUTE_TYPE_CONTENTTYPE,
            'description' => null,
            'value'       => 'application/x-www-form-urlencoded'
        ),
        'accept'          => array(
            'default'     => null,
            'required'    => false,
            'type'        => parent::ATTRIBUTE_TYPE_CONTENTTYPES,
            'description' => 'list of MIME types for file upload',
            'value'       => null
        ),
        'name'            => array(
            'default'     => null,
            'required'    => false,
            'type'        => parent::ATTRIBUTE_TYPE_CDATA,
            'description' => 'name of form for scripting',
            'value'       => null
        ),
        'accept-charset'  => array(
            'default'     => null,
            'required'    => false,
            'type'        => parent::ATTRIBUTE_TYPE_CHARSETS,
            'description' => 'list of supported charsets',
            'value'       => null
        )
    );



    /**
     * Built a new form element.
     *
     * @access  public
     * @param   mixed   $attributes    Attributes.
     * @return  void
     */
    public function __construct ( $attributes ) {

        if(is_array($attributes)) {

            if(isset($attributes['element'])) {

                $this->addElements($attributes['element']);
                unset($attributes['element']);
            }

            if(isset($attributes['attribute']))
                parent::__construct($attributes['attribute'], 'action');
        }
        else
            parent::__construct($attributes, 'action');
    }

    /**
     * Add an element.
     *
     * @access  public
     * @param   mixed   $element    Should be a Hoa_Form_Element_Abstract object
     *                              or an array that describe the element and
     *                              its arguments.
     * @return  void
     */
    public function addElement ( $element ) {

        if(is_array($element)) {

            if(!isset($element['type']))
                throw new Hoa_Form_Exception(
                    'The array for %s element must contain a type (of ' .
                    'element).', 0, $el);

            if(!isset($element['attribute']))
                $element['attribute'] = array();

            $type      = $element['type'];
            $attribute = $element['attribute'];
            unset($element['type']);
            unset($element['attribute']);

            if(isset($this->matching[strtolower($type)]))
                $type = $this->matching[strtolower($type)];

            try {

                $this->addElement(
                    Hoa_Factory::get('Form.Element', $type, array($attribute, $element))
                );
            }
            catch ( Hoa_Factory_Exception $e ) {

                throw new Hoa_Form_Exception($e->getMessage(), $e->getCode());
            }

            return;
        }

        if(!($element instanceof Hoa_Form_Element_Abstract))
            throw new Hoa_Form_Exception(
                'Must give an array or an intance of Hoa_Form_Element_Abstract.', 3);

        if(     $element instanceof Hoa_Form_Element_Form
           && !($element instanceof Hoa_Form_Element_Fieldset))
            throw new Hoa_Form_Exception(
                'Cannot build nested form tag.', 4);

        if(true === $this->elementExists($element->getId()))
            throw new Hoa_Form_Exception(
                'Element %s already exists.', 5, $element->getId());

        if(false === $element->attributesAreWellSet())
            throw new Hoa_Form_Exception(
                'The element %s requires attribute(s) %s.', 6,
                array(get_class($element),
                      implode(', ', $element->getNotSetAndRequiredAttributes())));


        $this->elements[$element->getId()] = $element;
    }

    /**
     * Add more than one element.
     *
     * @access  public
     * @param   array   $elements    Elements to add.
     * @return  void
     */
    public function addElements ( Array $elements ) {

        foreach($elements as $foo => $element)
            $this->addElement($element);
    }

    /**
     * Get all elements.
     *
     * @access  protected
     * @return  array
     */
    public function getElements ( ) {

        return $this->elements;
    }

    /**
     * Check if an element alreay exists or not.
     *
     * @access  public
     * @param   string  $element    Element to check.
     * @return  bool
     */
    public function elementExists ( $element ) {

        return isset($this->elements[$element]);
    }

    /**
     * Transform the object into a string.
     *
     * @access  public
     * @return  string
     */
    public function __toString ( ) {

        try {

            $out  = '<form' . $this->getAttributesChain() . '>' . "\n";
        }
        catch ( Hoa_Form_Exception $e ) {

            return $e->__toString();
        }

        foreach($this->getElements() as $id => $element)
            $out .= $element->getDecorator()->render(
                        $element,
                        $element->getLabel(),
                        $element->getValidator()
                    );

        $out .= '</form>';

        return $out;
    }
}
