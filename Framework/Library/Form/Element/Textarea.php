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
 * @subpackage  Hoa_Form_Element_Textarea
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
 * Class Hoa_Form_Element_Textarea.
 *
 * Describe the textarea element.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2008 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Form
 * @subpackage  Hoa_Form_Element_Textarea
 */

class Hoa_Form_Element_Textarea extends Hoa_Form_Element_Abstract {

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
            'description' => 'element ID',
            'value'       => null,
        ),
        'name'            => array(
            'default'     => null,
            'required'    => true,
            'type'        => parent::ATTRIBUTE_TYPE_CDATA,
            'description' => 'name of form for scripting',
            'value'       => null
        ),
        'rows'            => array(
            'default'     => null,
            'required'    => true,
            'type'        => parent::ATTRIBUTE_TYPE_NUMBER,
            'description' => null,
            'value'       => null
        ),
        'cols'            => array(
            'default'     => null,
            'required'    => true,
            'type'        => parent::ATTRIBUTE_TYPE_NUMBER,
            'description' => null,
            'value'       => null
        ),
        'disabled'        => array(
            'default'     => 'disabled',
            'required'    => false,
            'type'        => null,
            'description' => 'unavailable in this context',
            'value'       => null
        ),
        'readonly'        => array(
            'default'     => 'readonly',
            'required'    => false,
            'type'        => null,
            'description' => null,
            'value'       => null
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
     * Value of the textarea.
     *
     * @var Hoa_Form_Element_Textarea string
     */
    protected $value = null;



    /**
     * Built a textarea.
     *
     * @access  public
     * @param   mixed   $attributes    Attributes.
     * @param   mixed   $rest          Rest of options.
     * @return  void
     */
    public function __construct ( $attributes, $rest = array() ) {

        parent::__construct($attributes, 'id');

        if(is_string($rest)) {

            $this->setValue($rest);
            $this->setDecorator('InputButton');
        }
        elseif(is_array($rest)) {

            if(isset($rest['label']))
                $this->setLabel($rest['label']);
            else
                $this->setLabel(null);

            if(isset($rest['value']))
                $this->setValue($rest['value']);

            if(isset($rest['filter']))
                $this->setFilter($rest['filter']);

            if(isset($rest['validator']))
                $this->setValidator($rest['validator']);

            if(isset($rest['decorator']))
                $this->setDecorator($rest['decorator']);
            else
                $this->setDecorator('InputButton');
        }
        else
            $this->setDecorator('InputButton');
    }

    /**
     * Set the textarea value.
     *
     * @access public
     * @param  string  $value    The textarea value.
     * @return string
     */
    public function setValue ( $value ) {

        $old         = $this->value;
        $this->value = $value;

        return $old;
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

        try {

            return '<textarea'. $this->getAttributesChain() . '>' .
                   $this->getValue() .
                   '</textarea>';
        }
        catch ( Hoa_Form_Exception $e ) {

            return $e->__toString();
        }
    }
}
