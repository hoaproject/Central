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
 * @subpackage  Hoa_Form_Element_Label
 *
 */

/**
 * Hoa_Form_Exception
 */
import('Form.Exception');

/**
 * Hoa_Form_Element_Abstract
 */
import('Form.Element.Abstract');

/**
 * Class Hoa_Form_Element_Label.
 *
 * Describe the label element.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2010 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Form
 * @subpackage  Hoa_Form_Element_Label
 */

class Hoa_Form_Element_Label extends Hoa_Form_Element_Abstract {

    /**
     * List of attributes.
     *
     * @var Hoa_Form_Element_Abstract array
     */
    protected $attributes = array(
        'for'             => array(
            'default'     => null,
            'required'    => true,
            'type'        => parent::ATTRIBUTE_TYPE_ID,
            'description' => 'matches field ID value',
            'value'       => null,
        ),
        'accesskey'       => array(
            'default'     => null,
            'required'    => false,
            'type'        => parent::ATTRIBUTE_TYPE_CHARACTER,
            'description' => 'accessibility key character',
            'value'       => null
        )
    );

    /**
     * Value of the label.
     *
     * @var Hoa_Form_Element_Label string
     */
    protected $value = null;



    /**
     * Build a label.
     *
     * @access  public
     * @param   string  $for      Label value for the attribute for.
     * @param   string  $value    Label value.
     * @return  void
     */
    public function __construct ( $for, $value ) {

        $this->setAttribute('for', $for);
        $this->setValue($value);
    }

    /**
     * Set the textarea value.
     *
     * @access  public
     * @param   string  $value    The textarea value.
     * @return  object
     */
    public function setValue ( $value ) {

        $old         = $this->value;
        $this->value = $value;

        return $this;
    }

    /**
     * Get the textarea value.
     *
     * @access  public
     * @return  string
     */
    public function getValue ( ) {

        return $this->value;
    }

    /**
     * Transform the object into a string.
     *
     * @access  public
     * @return  string
     */
    public function __toString ( ) {

        if(   null === $this->attributes['for']['value']
           && null === $this->getValue())
            return '';

        try {

            return '<label' . $this->getAttributesChain() . '>' .
                   $this->getValue() .
                   '</label>';
        }
        catch ( Hoa_Form_Exception $e ) {

            return $e->__toString();
        }
    }
}
