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
 * Copyright (c) 2007, 2009 Ivan ENDERLIN. All rights reserved.
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
 * @subpackage  Hoa_Form_Element_SelectOptgroup
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
 * Class Hoa_Form_Element_SelectOptgroup.
 *
 * Describe the optgroup element.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2009 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Form
 * @subpackage  Hoa_Form_Element_SelectOptgroup
 */

class Hoa_Form_Element_SelectOptgroup extends Hoa_Form_Element_Abstract {

    /**
     * Collections of option.
     *
     * @var Hoa_Form_Element_SelectOptgroup array
     */
    protected $options = array();

    /**
     * List of attributes.
     *
     * @var Hoa_Form_Element_Abstract array
     */
    protected $attributes = array(
        'id'              => array(
            'default'     => null,
            'required'    => false,
            'type'        => parent::ATTRIBUTE_TYPE_ID,
            'description' => null,
            'value'       => null
        ),
        'label'           => array(
            'default'     => null,
            'required'    => true,
            'type'        => parent::ATTRIBUTE_TYPE_TEXT,
            'description' => 'for use in hierarchical menus',
            'value'       => null
        ),
        'disabled'        => array(
            'default'     => null,
            'required'    => false,
            'type'        => 'disabled',
            'description' => 'unavailable in this context',
            'value'       => null
        )
    );



    /**
     * Built a new form option.
     *
     * @access  public
     * @param   mixed   $attributes    Attributes.
     * @param   mixed   $options       Rest of options.
     * @param   bool    $addName       See the parent constructor.
     * @return  void
     */
    public function __construct ( $attributes, $options = array(), $addName = false ) {

        parent::__construct($attributes, 'id', $addName);

        if(   is_string($attributes)
           && get_class($this) == 'Hoa_Form_Element_SelectOptgroup')
            $this->setAttribute('label', $attributes);

        if(is_array($options)) {

            $this->addOption($options);
        }

        $this->setLabel(null);
        $this->setDecorator('SelectOptgroup');
    }

    /**
     * An input type hidden cannot have a label. So overload this method to
     * create a null label (will not be output).
     *
     * @access   public
     * @param    string  $label    Will be set to null.
     * @return   void
     */
    public function setLabel ( $label ) {

        return parent::setLabel(null);
    }

    /**
     * Add an element is an alias for addOption.
     *
     * @access  public
     * @param   mixed   $option    Should be a Hoa_Form_Element_InputOptgroup object
     *                             or an array that describe the option and
     *                             its arguments.
     * @return  void
     */
    public function addElement ( $option ) {

        return $this->addOption($option);
    }

    /**
     * Add an option.
     *
     * @access  public
     * @param   mixed   $option    Should be a Hoa_Form_Element_InputOptgroup object
     *                             or an array that describe the option and
     *                             its arguments.
     * @return  void
     */
    public function addOption ( $option ) {

        if(is_array($option)) {

            foreach($option as $value => $otherAttributes) {

                try {

                    $this->addOption(
                        Hoa_Factory::get('Form.Element', 'SelectOption', array($value, $otherAttributes))
                    );
                }
                catch ( Hoa_Factory_Exception $e ) {

                    throw new Hoa_Form_Exception($e->getMessage(), $e->getCode());
                }
            }

            return;
        }

        if(!($option instanceof Hoa_Form_Element_Abstract))
            throw new Hoa_Form_Exception(
                'Must give an array or an intance of Hoa_Form_Element_Abstract.', 0);

        if($option instanceof Hoa_Form_Element_Form)
            throw new Hoa_Form_Exception(
                'Cannot build nested form tag.', 1);

        if($option instanceof Hoa_Form_Element_SelectOptgroup)
            throw new Hoa_Form_Exception(
                'Cannot build nested optgroup tag.', 2);

        if(true === $this->optionExists($option->getId()))
            throw new Hoa_Form_Exception(
                'Element %s already exists.', 3, $option->getId());

        if(false === $option->attributesAreWellSet())
            throw new Hoa_Form_Exception(
                'The attribute %s requires attribute(s) %s.', 4,
                array(get_class($option),
                      implode(', ', $option->getNotSetAndRequiredAttributes())));

        $this->options[$option->getId()] = $option;
    }

    /**
     * Get all options.
     *
     * @access  protected
     * @return  array
     */
    public function getOptions ( ) {

        return $this->options;
    }

    /**
     * Check if an option alreay exists or not.
     *
     * @access  public
     * @param   string  $option    Element to check.
     * @return  bool
     */
    public function optionExists ( $option ) {

        return isset($this->options[$option]);
    }

    /**
     * Redirect the getElements method to getOptions.
     *
     * @access  public
     * @return  array
     */
    public function getElements ( ) {

        return $this->getOptions();
    }

    /**
     * Auto-complete element from an array (e.g. $_POST).
     *
     * @access  public
     * @param   string  $data    Array that contains data to auto-complete.
     * @return  void
     */
    public function autoSelect ( &$data ) {

        foreach($this->getOptions() as $id => $option)
            $option->autoSelect($data);
    }

    /**
     * Set filters to all options.
     *
     * @access  public
     * @param   array   $filters    Filter to set.
     * @return  object
     */
    public function setFilter ( $filters ) {

        foreach($this->getOptions() as $id => $option)
            $option->setFilter($filters);

        return $this;
    }

    /**
     * Set validators to all options.
     *
     * @access  public
     * @param   array   $validators    Validators to set.
     * @return  object
     */
    public function setValidator ( $validators ) {

        foreach($this->getOptions() as $id => $option)
            $option->setValidator($validators);

        return $this;
    }

    /**
     * The optgroup element cannot have a value directly.
     *
     * @access  public
     * @param   string  $value    The value.
     * @return  void
     */
    public function setValue ( $value ) {

        return;
    }

    /**
     * Get the value of an optgroup, i.e. iterate the options.
     *
     * @access  public
     * @return  string
     */
    public function getValue ( ) {

        $value = null;

        foreach($this->getOptions() as $id => $option) {

            $value = $option->getValueIfSelected();

            if(null !== $value)
                break;
        }

        return $value;
    }

    /**
     * Transform the object into a string.
     *
     * @access  public
     * @return  string
     */
    public function __toString ( ) {

        try {

            $out = '<optgroup' . $this->getAttributesChain() . '>' . "\n";
        }
        catch ( Hoa_Form_Exception $e ) {

            return $e->__toString();
        }

        foreach($this->getOptions() as $id => $option)
            $out .= $option->getDecorator()->render(
                $option,
                $option->getLabel(),
                $option->getValidator()
            );

        $out .= '</optgroup>';

        return $out;
    }
}
