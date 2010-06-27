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
 * @subpackage  Hoa_Form_Element_InputButton
 *
 */

/**
 * Hoa_Core
 */
require_once 'Core.php';

/**
 * Hoa_Form_Exception
 */
import('Form.Exception');

/**
 * Hoa_Form_Element_Abstract
 */
import('Form.Element.Abstract');

/**
 * Class Hoa_Form_Element_InputButton.
 *
 * Describe the input type button element.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2010 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Form
 * @subpackage  Hoa_Form_Element_InputButton
 */

class Hoa_Form_Element_InputButton extends Hoa_Form_Element_Abstract {

    /**
     * List of attributes.
     *
     * @var Hoa_Form_Element_Abstract array
     */
    protected $attributes = array(
        'type'            => array(
            'default'     => 'button',
            'required'    => true,
            'type'        => parent::ATTRIBUTE_TYPE_INPUTTYPE,
            'description' => 'what kind of widget is needed',
            'value'       => 'button'
        ),
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
            'description' => 'submit as part of form',
            'value'       => null
        ),
        'value'           => array(
            'default'     => null,
            'required'    => true,
            'type'        => parent::ATTRIBUTE_TYPE_CDATA,
            'description' => 'specify for radio buttons and checkboxes',
            'value'       => null
        ),
        'disabled'        => array(
            'default'     => null,
            'required'    => false,
            'type'        => 'disabled',
            'description' => 'unavailable in this context',
            'value'       => null
        ),
        'size'            => array(
            'default'     => null,
            'required'    => false,
            'type'        => parent::ATTRIBUTE_TYPE_CDATA,
            'description' => 'specific to each type of field',
            'value'       => null
        ),
        'tabindex'        => array(
            'default'     => null,
            'required'    => false,
            'type'        => parent::ATTRIBUTE_TYPE_NUMBER,
            'description' => 'position in tabbing order',
            'value'       => null
        ),
        'accesskey'       => array(
            'default'     => null,
            'required'    => false,
            'type'        => parent::ATTRIBUTE_TYPE_CHARACTER,
            'description' => 'accessibility key character',
            'value'       => null
        ),
        'accept'          => array(
            'default'     => null,
            'required'    => false,
            'type'        => parent::ATTRIBUTE_TYPE_CONTENTTYPE,
            'description' => 'list of MIME types for file upload',
            'value'       => null
        )
    );



    /**
     * Build an input type button.
     *
     * @access  public
     * @param   mixed   $attributes    Attributes.
     * @param   mixed   $rest          Rest of options.
     * @return  void
     */
    public function __construct ( $attributes, $rest = null ) {

        parent::__construct($attributes, 'id');

        if(is_string($rest)) {

            $this->setLabel($rest);
            $this->setDecorator('InputButton');
        }
        elseif(is_array($rest)) {

            if(isset($rest['label']))
                $this->setLabel($rest['label']);
            else
                $this->setLabel(null);

            if(isset($rest['filter']))
                $this->setFilter(null);

            if(isset($rest['validator']))
                $this->setValidator(null);

            if(isset($rest['decorator']))
                $this->setDecorator($rest['decorator']);
            else
                $this->setDecorator('InputButton');
        }
        else {

            $this->setDecorator('InputButton');
            $this->setLabel(null);
        }
    }

    /**
     * Cannot set filter to a button, so throw an exception.
     *
     * @access  public
     * @param   array   $filters    By the way, it is not important here.
     * @return  void
     * @throw   Hoa_Form_Exception
     */
    public function setFilter ( $filters ) {

        throw new Hoa_Form_Exception('A button cannot have filters.', 0);
    }

    /**
     * Cannot set validator to a button, so throw an exception.
     *
     * @access  public
     * @param   array   $validators    By the way, it is not important here.
     * @return  void
     * @throw   Hoa_Form_Exception
     */
    public function setValidator ( $validators ) {

        throw new Hoa_Form_Exception('A button cannot have validators.', 1);
    }

    /**
     * Transform the object into a string.
     *
     * @access  public
     * @return  string
     */
    public function __toString ( ) {

        try {

            return '<input'. $this->getAttributesChain() . ' />';
        }
        catch ( Hoa_Form_Exception $e ) {

            return $e->__toString();
        }
    }
}
