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
 * @subpackage  Hoa_Form_Element_InputCheckbox
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
 * Class Hoa_Form_Element_InputCheckbox.
 *
 * Describe the input type checkbox element.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2009 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Form
 * @subpackage  Hoa_Form_Element_InputCheckbox
 */

class Hoa_Form_Element_InputCheckbox extends Hoa_Form_Element_Abstract {

    /**
     * List of attributes.
     *
     * @var Hoa_Form_Element_Abstract array
     */
    protected $attributes = array(
        'type'            => array(
            'default'     => 'checkbox',
            'required'    => true,
            'type'        => parent::ATTRIBUTE_TYPE_INPUTTYPE,
            'description' => 'what kind of widget is needed',
            'value'       => 'checkbox'
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
        'checked'         => array(
            'default'     => null,
            'required'    => false,
            'type'        => 'checked',
            'description' => 'for radio buttons and checkboxes',
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
     * Build an input type checkbox.
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
            $this->setDecorator('InputCheckbox');
        }
        elseif(is_array($rest)) {

            if(isset($rest['label']))
                $this->setLabel($rest['label']);
            else
                $this->setLabel(null);

            if(isset($rest['filter']))
                $this->setFilter($rest['filter']);

            if(isset($rest['validator']))
                $this->setValidator($rest['validator']);

            if(isset($rest['decorator']))
                $this->setDecorator($rest['decorator']);
            else
                $this->setDecorator('InputCheckbox');
        }
        else {

            $this->setDecorator('InputCheckbox');
            $this->setLabel(null);
        }
    }

    /**
     * Auto-complete element from an array (e.g. $_POST).
     *
     * @access  public
     * @param   array   $data    Array that contains data to auto-complete.
     * @return  void
     */
    public function autoComplete ( Array &$data ) {

        $name  = $this->getName();
        $value = $this->getValue();

        if(substr($name, -2) == '[]')
            $name = substr($name, 0, -2);

        if(empty($data))
            return;

        if(!isset($data[$name]))
            $handle = array();
        else
            $handle = $data[$name];

        if(is_string($handle))
            $handle = array(0 => $handle);

        $this->setAttribute('checked', null);

        foreach($handle as $foo => $f)
            if($value == $f)
                $this->setAttribute('checked', 'checked');
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
