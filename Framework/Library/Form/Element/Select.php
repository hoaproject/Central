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
 * @subpackage  Hoa_Form_Element_Select
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
 * Hoa_Form_Element_SelectOption
 */
import('Form.Element.SelectOption');

/**
 * Hoa_Form_Element_SelectOptgroup
 */
import('Form.Element.SelectOptgroup');

/**
 * Hoa_Factory
 */
import('Factory.~');

/**
 * Class Hoa_Form_Element_Select.
 *
 * Describe the select element.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2009 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Form
 * @subpackage  Hoa_Form_Element_Select
 */

class Hoa_Form_Element_Select extends Hoa_Form_Element_SelectOptgroup {

    /**
     * Collections of optgroup.
     *
     * @var Hoa_Form_Element_SelectOptgroup array
     */
    protected $optgroups = array();

    /**
     * List of attributes.
     *
     * @var Hoa_Form_Element_Abstract array
     */
    protected $attributes = array(
        'id'              => array(
            'default'     => null,
            'required'    => true,
            'type'        => parent::ATTRIBUTE_TYPE_ID,
            'description' => null,
            'value'       => null
        ),
        'name'            => array(
            'default'     => null,
            'required'    => true,
            'type'        => parent::ATTRIBUTE_TYPE_CDATA,
            'description' => 'field name',
            'value'       => null
        ),
        'size'            => array(
            'default'     => null,
            'required'    => false,
            'type'        => parent::ATTRIBUTE_TYPE_NUMBER,
            'description' => 'rows visible',
            'value'       => null
        ),
        'multiple'        => array(
            'default'     => null,
            'required'    => false,
            'type'        => 'multiple',
            'description' => 'default is single selection',
            'value'       => null
        ),
        'disabled'        => array(
            'default'     => null,
            'required'    => false,
            'type'        => 'disabled',
            'description' => 'unavailable in this context',
            'value'       => null
        ),
        'tabindex'        => array(
            'default'     => null,
            'required'    => false,
            'type'        => parent::ATTRIBUTE_TYPE_NUMBER,
            'description' => 'position in tabbing order',
            'value'       => null
        )
    );



    /**
     * Built a new form option.
     *
     * @access  public
     * @param   mixed   $attributes       Attributes.
     * @param   mixed   $nestedElement    Collection of nested element.
     * @return  void
     */
    public function __construct ( $attributes, $nestedElement = array() ) {

        parent::__construct($attributes, 'id', true);

        if(is_array($nestedElement)) {

            if(isset($nestedElement['label']))
                $this->setLabel($nestedElement['label']);
            else
                $this->setLabel(null);

            if(isset($nestedElement['optgroup']))
                $this->addOptGroup($nestedElement['optgroup']);

            if(isset($nestedElement['option']))
                $this->addOption($nestedElement['option']);

            if(isset($nestedElement['filter']))
                $this->setFilter($nestedElement['filter']);

            if(isset($nestedElement['validator']))
                $this->setValidator($nestedElement['validator']);

            if(isset($rest['decorator']))
                $this->setDecorator($nestedElement['decorator']);
            else
                $this->setDecorator('Select');
        }
        else
            $this->setDecorator('Select');
    }

    /**
     * The parent class cannot have an label and set it to a null label. So
     * rewrite the original setLabel method.
     *
     * @access   public
     * @param    string  $label    Will be set to null.
     * @return   object
     */
    public function setLabel ( $label ) {

        if(is_array($label))
            $label   = isset($label['label']) ? $label['label'] : null;

        $for         = null === $label ? null : $this->getId();
        $old         = $this->label;
        $this->label = new Hoa_Form_Element_Label($for, $label);

        return $this;
    }

    /**
     * Add an optgroup.
     *
     * @access  public
     * @param   mixed   $optgroup    Should be a Hoa_Form_Element_InputOptgroup object
     *                               or an array that describe the option and
     *                               its arguments.
     * @return  void
     */
    public function addOptGroup ( $optgroup ) {

        if(is_array($optgroup)) {

            foreach($optgroup as $foo => $attrAndOptions) {

                $option    = array();
                $attribute = array();

                if(isset($attrAndOptions['option'])) {

                    $option = $attrAndOptions['option'];
                    unset($attrAndOptions['option']);
                }

                if(isset($attrAndOptions['attribute'])) {

                    $attribute = $attrAndOptions['attribute'];
                    unset($attrAndOptions['attribute']);
                }

                try {

                    $this->addOptGroup(
                        Hoa_Factory::get('Form.Element', 'SelectOptgroup',
                                         array($attribute, $option))
                    );
                }
                catch ( Hoa_Factory_Exception $e ) {

                    throw new Hoa_Form_Exception($e->getMessage(), $e->getCode());
                }
            }

            return;
        }

        if(!($optgroup instanceof Hoa_Form_Element_Abstract))
            throw new Hoa_Form_Exception(
                'Must give an array or an intance of Hoa_Form_Element_Abstract.', 0);

        if($optgroup instanceof Hoa_Form_Element_Form)
            throw new Hoa_Form_Exception(
                'Cannot build nested form tag.', 1);

        if($optgroup instanceof Hoa_Form_Element_Select)
            throw new Hoa_Form_Exception(
                'Cannot build nested select tag.', 2);

        if(true === $this->optGroupExists($optgroup->getId()))
            throw new Hoa_Form_Exception(
                'Element %s already exists.', 3, $optgroup->getId());

        $this->optgroups[$optgroup->getId()] = $optgroup;
    }

    /**
     * Get all optgroups.
     *
     * @access  protected
     * @return  array
     */
    protected function getOptGroups ( ) {

        return $this->optgroups;
    }

    /**
     * Overload the getElements method because a select contains options and
     * optgroups elements.
     *
     * @access  public
     * @return  array
     */
    public function getElements ( ) {

        return $this->getOptGroups() + $this->getOptions();
    }

    /**
     * Auto-complete element from an array (e.g. $_POST).
     *
     * @access  public
     * @param   array   $data    Array that contains data to auto-complete.
     * @return  void
     */
    public function autoComplete ( Array &$data ) {

        $name = $this->getName();

        if(!isset($data[$name]))
            return;

        foreach($this->getOptGroups() as $id => $optgroup)
            $optgroup->autoSelect($data[$name]);

        foreach($this->getOptions() as $id => $option)
            $option->autoSelect($data[$name]);
    }

    /**
     * Check if an optgroup alreay exists or not.
     *
     * @access  public
     * @param   string  $optgroup    Element to check.
     * @return  bool
     */
    public function optGroupExists ( $optgroup ) {

        return isset($this->optgroups[$optgroup]);
    }

    /**
     * Set filter to all options.
     *
     * @access  public
     * @param   array   $filters    Filters to apply.
     * @return  object
     */
    public function setFilter ( $filters ) {

        foreach($this->getOptGroups() as $id => $optgroup)
            $optgroup->setFilter($filters);

        foreach($this->getOptions() as $id => $option)
            $option->setFilter($filters);

        return $this;
    }

    /**
     * Set validator to all options.
     *
     * @access  public
     * @param   array   $validators    Vadlidators to apply.
     * @return  object
     */
    public function setValidator ( $validators ) {

        foreach($this->getOptGroups() as $id => $optgroup)
            $optgroup->setValidator($validators);

        foreach($this->getOptions() as $id => $option)
            $option->setValidator($validators);

        return $this;
    }

    /**
     * The select element cannot have a value directly.
     *
     * @access  public
     * @param   string  $value    The value.
     * @return  void
     */
    public function setValue ( $value ) {

        return;
    }

    /**
     * Get the value of a select, i.e. iterate the optgroups and options.
     *
     * @access  public
     * @return  array
     */
    public function getValue ( ) {

        $value  = array();
        $handle = null;

        foreach($this->getOptGroups() as $id => $optgroup) {

            $handle = $optgroup->getValue();

            if(null !== $handle)
                $value[] = $handle;
        }

        $handle = null;

        foreach($this->getOptions() as $id => $option) {

            $handle = $option->getValueIfSelected();

            if(null !== $handle)
                $value[] = $handle;
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

            $out = '<select' . $this->getAttributesChain() . '>' . "\n";
        }
        catch ( Hoa_Form_Exception $e ) {

            return $e->__toString();
        }

        foreach($this->getOptGroups() as $id => $optgroup)
            $out .= $optgroup->getDecorator()->render(
                $optgroup,
                $optgroup->getLabel(),
                $optgroup->getValidator()
            );

        foreach($this->getOptions() as $id => $option)
            $out .= $option->getDecorator()->render(
                $option,
                $option->getLabel(),
                $option->getValidator()
            );

        $out .= '</select>';

        return $out;
    }
}
