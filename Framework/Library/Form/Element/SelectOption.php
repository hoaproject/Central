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
 * @subpackage  Hoa_Form_Element_SelectOption
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
 * Class Hoa_Form_Element_SelectOption.
 *
 * Describe the option element.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2008 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Form
 * @subpackage  Hoa_Form_Element_SelectOption
 */

class Hoa_Form_Element_SelectOption extends Hoa_Form_Element_Abstract {

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
        'selected'        => array(
            'default'     => null,
            'required'    => false,
            'type'        => 'selected',
            'description' => null,
            'value'       => null
        ),
        'disabled'        => array(
            'default'     => null,
            'required'    => false,
            'type'        => 'disabled',
            'description' => 'unavailable in this context',
            'value'       => null
        ),
        'label'        => array(
            'default'     => null,
            'required'    => false,
            'type'        => parent::ATTRIBUTE_TYPE_TEXT,
            'description' => 'for use in hierarchical menus',
            'value'       => null
        ),
        'value'           => array(
            'default'     => null,
            'required'    => true,
            'type'        => parent::ATTRIBUTE_TYPE_CDATA,
            'description' => 'defaults to element content',
            'value'       => null
        )
    );

    /**
     * The option text.
     *
     * @var Hoa_Form_Element_SelectOption string
     */
    protected $text = null;



    /**
     * Built a new form element.
     *
     * @access  public
     * @param   string  $value         The value.
     * @param   mixed   $attributes    Attributes.
     * @return  void
     */
    public function __construct ( $value, $attributes ) {

        if(is_string($attributes)) {

            $this->setText($attributes);
            $attributes = array();
        }
        elseif(is_array($attributes) && isset($attributes['text'])) {

            $this->setText($attributes['text']);
            unset($attributes['text']);
        }

        $attributes['value'] = $value;

        parent::__construct($attributes, 'value');

        $this->setLabel(null);
        $this->setDecorator('SelectOption');
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
     * Set the text of the option.
     *
     * @access  public
     * @param   string  $text    The option text.
     * @return  string
     */
    public function setText ( $text ) {

        $old        = $this->text;
        $this->text = $text;

        return $old;
    }

    /**
     * Get the text of the option.
     *
     * @access  public
     * @return  string
     */
    public function getText ( ) {

        return $this->text;
    }

    /**
     * The ID of an option is her value.
     *
     * @access  public
     * @return  string
     */
    public function getId ( ) {

        return $this->getAttribute('value', 'value');
    }

    /**
     * Auto-complete element from an array (e.g. $_POST).
     *
     * @access  public
     * @param   string  $data    Array that contains data to auto-complete.
     * @return  void
     */
    public function autoSelect ( &$data ) {

        $value = $this->getValue();

        if($value === $data)
            $this->setAttribute('selected', 'selected');
        else
            $this->setAttribute('selected', null);
    }

    /**
     * Get the value if the option is selected.
     *
     * @access  public
     * @return  string
     */
    public function getValueIfSelected ( ) {

        try {

            $this->getAttribute('selected');
        }
        catch ( Hoa_Form_Exception $e ) {

            return null;
        }

        return $this->getValue();
    }

    /**
     * Transform the object into a string.
     *
     * @access  public
     * @return  string
     */
    public function __toString ( ) {

        try {

            return '<option' . $this->getAttributesChain() . '>' .
                   $this->getText() .
                   '</option>';
        }
        catch ( Hoa_Form_Exception $e ) {

            return $e->__toString();
        }
    }
}
