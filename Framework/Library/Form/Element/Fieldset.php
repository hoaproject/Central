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
 * @package     Hoa_Form
 * @subpackage  Hoa_Form_Element_Fieldset
 *
 */

/**
 * Hoa_Form_Exception
 */
import('Form.Exception');

/**
 * Hoa_Form_Element_Form
 */
import('Form.Element.Form');

/**
 * Hoa_Form_Element_Legend
 */
import('Form.Element.Legend');

/**
 * Class Hoa_Form_Element_Fieldset.
 *
 * Describe the fieldset element.
 * Fieldset is a special element because it is like the form element : it
 * contains a collection of elements.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2010 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Form
 * @subpackage  Hoa_Form_Element_Fieldset
 */

class Hoa_Form_Element_Fieldset extends Hoa_Form_Element_Form {

    /**
     * Collections of option.
     *
     * @var Hoa_Form_Element_Fieldset array
     */
    protected $options = array();

    /**
     * The legend.
     *
     * @var Hoa_Form_Element_Fieldset Hoa_Form_Element_Legend
     */
    protected $legend = null;

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
        )
    );



    /**
     * Built the element.
     *
     * @access  public
     * @param   mixed   $attributes      Attributes.
     * @param   mixed   $rest            Rest of options.
     * @return  void
     */
    public function __construct ( $attributes, $rest = null ) {

        parent::__construct(null);

        if(is_string($attributes))
            $this->setLegend($attributes);
        elseif(is_array($attributes))
            foreach($attributes as $attribute => $value)
                $this->setAttribute($attribute, $value);

        if(is_string($rest)) {

            $this->setLegend($rest);
            $this->setDecorator('Fieldset');
        }
        elseif(is_array($rest)) {

            if(isset($rest['legend'])) {

                $value = null;
                $attr  = array();

                if(is_string($rest['legend']))
                    $value = $rest['legend'];
                
                elseif(is_array($rest['legend'])) {

                    if(isset($rest['legend']['value']))
                        $value = $rest['legend']['value'];

                    if(isset($rest['legend']['attribute']))
                        $attr = $rest['legend']['attribute'];
                }

                $this->setLegend($value, $attr);
            }

            if(isset($rest['filter']))
                $this->setFilter(null);

            if(isset($rest['validator']))
                $this->setValidator(null);

            if(isset($rest['element']))
                $this->addElements($rest['element']);

            if(isset($rest['decorator']))
                $this->setDecorator($rest['decorator']);
            else
                $this->setDecorator('Fieldset');
        }
        else
            $this->setDecorator('Fieldset');

        $this->setLabel(null);
    }

    /**
     * An fieldset cannot have a label. So overload this method to
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
     * A fieldset does not have a label but a legend.
     *
     * @access  public
     * @param   string  $legend    Should be a string or an object.
     * @return  void
     */
    public function setLegend ( $legend ) {

        if(is_string($legend))
            $legend = new Hoa_Form_Element_Legend($legend);
        
        if(!is_object($legend))
            throw new Hoa_Form_Exception(
                'The legend must be a string or an object.', 0);

        if(!($legend instanceof Hoa_Form_Element_Legend))
            throw new Hoa_Form_Exception(
                'The legend must be an Hoa_Form_Element_Legend object, given %s',
                1, get_class($legend));

        $this->legend = $legend;

        return $this;
    }

    /**
     * Get the legend.
     *
     * @access  public
     * @return  object
     */
    public function getLegend ( ) {

        return $this->legend;
    }

    /**
     * Cannot set filter to a fieldset, so throw an exception.
     *
     * @access  public
     * @param   array   $filters    By the way, it is not important here.
     * @return  void
     * @throw   Hoa_Form_Exception
     */
    public function setFilter ( $filters ) {

        throw new Hoa_Form_Exception('A fieldset cannot have filters.', 2);
    }

    /**
     * Cannot set a validator to a fieldset, so throw an exception.
     *
     * @access  public
     * @param   array   $validators    By the way, it is not important here.
     * @return  void
     * @throw   Hoa_Form_Exception
     */
    public function setValidator ( $validators ) {

        throw new Hoa_Form_Exception('A fieldset cannot have validators.', 3);
    }

    /**
     * The fieldset element cannot have a value.
     *
     * @access  public
     * @param   string  $value    The value.
     * @return  void
     */
    public function setValue ( $value ) {

        return;
    }

    /**
     * The fieldset element cannot have a value.
     *
     * @access  public
     * @return  null
     */
    public function getValue ( ) {

        return null;
    }

    /**
     * Transform the object into a string.
     *
     * @access  public
     * @return  string
     */
    public function __toString ( ) {

        $out = null;

        try {

            $out = '<fieldset' . $this->getAttributesChain() . '>' . "\n" .
                   '  ' . $this->getLegend() . "\n";
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

        $out .= '</fieldset>';

        return $out;
    }
}
