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
 * @subpackage  Hoa_Form_Element_InputImage
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
 * Class Hoa_Form_Element_InputImage.
 *
 * Describe the input type image element.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2010 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Form
 * @subpackage  Hoa_Form_Element_InputImage
 */

class Hoa_Form_Element_InputImage extends Hoa_Form_Element_Abstract {

    /**
     * List of attributes.
     *
     * @var Hoa_Form_Element_Abstract array
     */
    protected $attributes = array(
        'type'            => array(
            'default'     => 'image',
            'required'    => true,
            'type'        => parent::ATTRIBUTE_TYPE_INPUTTYPE,
            'description' => 'what kind of widget is needed',
            'value'       => 'image'
        ),
        'id'              => array(
            'default'     => null,
            'required'    => false,
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
        'src'             => array(
            'default'     => null,
            'required'    => true,
            'type'        => parent::ATTRIBUTE_TYPE_URI,
            'description' => 'for fields with images',
            'value'       => null
        ),
        'alt'             => array(
            'default'     => null,
            'required'    => true,
            'type'        => parent::ATTRIBUTE_TYPE_CDATA,
            'description' => 'short description',
            'value'       => null
        ),
        'usemap'          => array(
            'default'     => null,
            'required'    => false,
            'type'        => parent::ATTRIBUTE_TYPE_URI,
            'description' => 'use client-side image map',
            'value'       => null
        ),
        'ismap'           => array(
            'default'     => null,
            'required'    => false,
            'type'        => 'ismap',
            'description' => 'use server-side image map',
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
     * Build an input type image.
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
            $this->setDecorator('InputImage');
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
                $this->setDecorator('InputImage');
        }
        else {

            $this->setDecorator('InputImage');
            $this->setLabel(null);
        }
    }

    /**
     * Cannot set filter to an input image, so throw an exception.
     *
     * @access  public
     * @param   array   $filters    By the way, it is not important here.
     * @return  void
     * @throw   Hoa_Form_Exception
     */
    public function setFilter ( $filters ) {

        throw new Hoa_Form_Exception('An input image cannot have filters.', 0);
    }

    /**
     * Cannot set validator to an input image, so throw an exception.
     *
     * @access  public
     * @param   array   $validators    By the way, it is not important here.
     * @return  void
     * @throw   Hoa_Form_Exception
     */
    public function setValidator ( $validators ) {

        throw new Hoa_Form_Exception('An input image cannot have validators.', 1);
    }

    /**
     * An input type image cannot have a label. So overload this method to
     * create a null label (will not be output).
     *
     * @access  public
     * @param   string  $label    Will be set to null.
     * @return  void
     */
    public function setLabel ( $label ) {

        return parent::setLabel(null);
    }

    /**
     * An input type image cannot have a value.
     *
     * @access  public
     * @param   string  $value    The value.
     * @return  void
     */
    public function setValue ( $value ) {

        return;
    }

    /**
     * An input type image does not have a value.
     *
     * @access  public
     * @return  void
     */
    public function getValue ( ) {

        return;
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
