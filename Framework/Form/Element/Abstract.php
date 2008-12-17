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
 * @subpackage  Hoa_Form_Element_Abstract
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
 * Hoa_Form_Element_Label
 */
import('Form.Element.Label');

/**
 * Hoa_Filter
 */
import('Filter.~');

/**
 * Hoa_Validate
 */
import('Validate.~');

/**
 * Class Hoa_Form_Element_Abstract.
 *
 * Manage the differents elements. Each element must inherit (by extending) of
 * this class.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2008 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Form
 * @subpackage  Hoa_Form_Element_Abstract
 */

abstract class Hoa_Form_Element_Abstract {

    /**
     * Type of attributes.
     *
     * @const string
     */
    const ATTRIBUTE_TYPE_ID           = '^[a-zA-Z][a-zA-Z0-9\-_:\.]*$';
    const ATTRIBUTE_TYPE_URI          = null; // too complex to be treat here.
    const ATTRIBUTE_TYPE_CONTENTTYPE  = null; // ''
    const ATTRIBUTE_TYPE_CONTENTTYPES = null; // ''
    const ATTRIBUTE_TYPE_CDATA        = null; // ''
    const ATTRIBUTE_TYPE_CHARSETS     = null; // ''
    const ATTRIBUTE_TYPE_NUMBER       = '^[0-9]+$';
    const ATTRIBUTE_TYPE_CHARACTER    = '^[a-zA-Z]$';
    const ATTRIBUTE_TYPE_INPUTTYPE    = null; // ''
    const ATTRIBUTE_TYPE_TEXT         = null; // like CDATA.

    /**
     * List of attributes of an element.
     *
     * @var Hoa_Form_Element_Abstract array
     */
    protected $attributes = array();

    /**
     * Collection of validators.
     *
     * @var Hoa_Form_Element_Abstract Hoa_Validate
     */
    protected $validators = null;

    /**
     * Collection of filters.
     *
     * @var Hoa_Form_Element_Abstract Hoa_Filter
     */
    protected $filters = null;

    /**
     * Label object. Most of the element is associated with a label object.
     *
     * @var Hoa_Form_Element_Abstract Hoa_Form_Element_Label
     */
    protected $label = null;

    /**
     * Decorator.
     *
     * @var Hoa_Form_Element_Abstract Hoa_Form_Decorator_Abstract
     */
    protected $decorator = null;

    /**
     * Decorator path.
     *
     * @var Hoa_Form_Element_Abstract string
     */
    protected $decoratorPath = null;

    /**
     * Decorator class prefix.
     *
     * @var Hoa_Form_Element_Abstract string
     */
    protected $decoratorPrefix = 'Hoa_Form_Decorator_';



    /**
     * Assign attributs, manage ID, etc.
     *
     * @access  protected
     * @param   mixed      $attributes    The attributes values.
     * @param   string     $single        If the attributes values is a string,
     *                                    $single will be use to be the key of
     *                                    the array when converted.
     * @param   bool       $addName       Add name attribute if ID is defined
     *                                    and name is not defined.
     * @return  void
     */
    protected function __construct ( $attributes, $single = 'id', $addName = true ) {

        $this->id = md5(time() . rand(0, 2048));

        if(!is_array($attributes))
            $attributes = array($single => $attributes);

        if(true === $addName)
            if(isset($attributes['id']) && !isset($attributes['name']))
                $attributes['name'] = $attributes['id'];

        foreach($attributes as $attribute => $value)
            $this->setAttribute($attribute, $value);
    }

    /**
     * Set the ID. Alias of setAttribute.
     *
     * @access  public
     * @param   string  $id    The ID.
     * @return  object
     */
    public function setId ( $id ) {

        return $this->setAttribute('id', $id);
    }

    /**
     * Get the ID of an element.
     * If the ID attribute value is given, it will be return, else a random ID
     * will be return.
     *
     * @access  public
     * @return  string
     */
    public function getId ( ) {

        return $this->id;
    }

    /**
     * Set the value of an element.
     * By default, it will search the value of the attribute value. Should be
     * override for textarea element by example.
     *
     * @access  public
     * @param   string  $value     The value of the element value.
     * @return  object
     */
    public function setValue ( $value ) {

        return $this->setAttribute('value', $value);
    }

    /**
     * Get the value of an element.
     * By default, it will search the value of the attribute value. Should be
     * override for textarea element by example.
     *
     * @access  public
     * @return  string
     */
    public function getValue ( ) {

        try {

            return $this->getAttribute('value', 'value');
        }
        catch ( Hoa_Form_Exception $e ) {

            return null;
        }
    }

    /**
     * Set the name. Alias of setAttribute.
     *
     * @access  public
     * @param   string  $name    The name.
     * @return  object
     */
    public function setName ( $name ) {

        return $this->setAttribute('name', $name);
    }

    /**
     * Get the name of an element. All name attributes are required.
     *
     * @access  public
     * @return  string
     */
    public function getName ( ) {

        try {

            return $this->getAttribute('name', 'value');
        }
        catch ( Hoa_Form_Exception $e ) {

            return null;
        }
    }

    /**
     * Set an attribute.
     *
     * @access  public
     * @param   string  $attribute    The attribute name.
     * @param   string  $value        The attribute value.
     * @return  object
     */
    public function setAttribute ( $attribute, $value ) {

        if(false === $this->attributeExists($attribute)) {

            $this->createAttribute($attribute, null, false, null, null, $value);

            return $this;
        }

        $this->attributes[$attribute]['value'] = $value;

        if($attribute == 'id')
            $this->id = $value;

        /**
         * @TODO :
         *     Make a verification of value pattern (use type entry of the
         *     attribute array).
         */

        return $this;
    }

    /**
     * Get an attribute data.
     *
     * @access  public
     * @param   string  $name   The attribute name.
     * @param   string  $data   Data to get.
     * @return  array
     * @throw   Hoa_Form_Exception
     */
    public function getAttribute ( $name, $data = 'value' ) {

        if(false === $this->attributeExists($name))
            return null;

        if(null === $data)
            return $this->attributes[$name];

        if(!isset($this->attributes[$name][$data]))
            throw new Hoa_Form_Exception(
                'Cannot get %s data from %s attribute.', 0,
                array($data, $name));

        return $this->attributes[$name][$data];
    }

    /**
     * Get all attributes.
     *
     * @access  protected
     * @return  array
     */
    protected function getAttributes ( ) {

        return $this->attributes;
    }

    /**
     * Check if an attribute exists.
     *
     * @access  public
     * @param   string  $attribute    The attribute name.
     * @return  bool
     */
    public function attributeExists ( $attribute ) {

        return isset($this->attributes[$attribute]);
    }

    /**
     * Create a new attribute, if it does not already exist.
     *
     * @access  public
     * @param   string  $name           The attribute name.
     * @param   string  $default        The default value.
     * @param   bool    $required       If attribute is required or implied.
     * @param   string  $type           The attribute type.
     * @param   string  $description    The attribute description.
     * @param   string  $value          The attribute value.
     * @return  void
     * @throw   Hoa_Form_Exception
     */
    public function createAttribute ( $name, $default = null,
                                      $required       = false,
                                      $type           = null,
                                      $description    = null,
                                      $value          = null ) {

        if(true === $this->attributeExists($name))
            return;

        if(!is_bool($required))
            throw new Hoa_Form_Exception(
                'The data required from %s atttribute must be a bool.', 1,
                $name);

        if(null !== $type && !is_string($type))
            throw new Hoa_Form_Exception(
                'The data type from % attribute must be a string.', 2,
                $name);

        if(null !== $description && !is_string($description))
            throw new Hoa_Form_Exception(
                'The data description from % attribute must be a string.', 3,
                $name);

        if(null !== $value && !is_string($value))
            throw new Hoa_Form_Exception(
                'The data value from %s attribute must be a string.', 4,
                $name);

        if(null !== $default)
            $value = $default;

        $this->attributes[$name] = array(
            'default'     => $default,
            'required'    => $required,
            'type'        => $type,
            'description' => $description,
            'value'       => $value
        );
    }

    /**
     * Delete an attribute if exists.
     *
     * @access  public
     * @param   string  $name    The attribute name.
     * @return  void
     * @throw   Hoa_Form_Exception
     */
    public function deleteAttribute ( $name ) {

        if(false === $this->attributeExists($name))
            return;

        if(true === $this->getAttribute($name, 'required'))
            throw new Hoa_Form_Exception(
                'Cannot delete %s attribute, because it is required.',
                5, $name);

        unset($this->attributes[$name]);
    }

    /**
     * Built attributes chain.
     *
     * @access  public
     * @return  string
     * @throw   Hoa_Form_Exception
     */
    public function getAttributesChain ( ) {

        if(false === $this->attributesAreWellSet())
            throw new Hoa_Form_Exception(
                'The element %s requires attribute(s) %s.', 6,
                array(get_class($this),
                      implode(', ', $this->getNotSetAndRequiredAttributes())));

        $out = null;

        foreach($this->getAttributes() as $name => $data)
            if(null !== $data['value'])
                $out .= ' ' . $name . '=' . $this->smartQuote($data['value']);

        return $out;
    }

    /**
     * Smart quote a value of an attribute.
     *
     * @access  protected
     * @param   string     $string    String to quote.
     * @return  string
     */
    protected function smartQuote ( $string ) {

        return '"' . str_replace('"', '\\"', $string) . '"';
    }

    /**
     * Check if all attributes are well-set.
     * It means, it checks if needed attributes is set.
     *
     * @access  public
     * @return  bool
     */
    public function attributesAreWellSet ( ) {

        $out = true;

        foreach($this->getAttributes() as $name => $data)
            if(true === $data['required'])
                $out = $out && $data['value'] !== null;

        return $out;
    }

    /**
     * Get the list of required attributes.
     *
     * @access  public
     * @return  array
     */
    public function getRequiredAttributes ( ) {

        $out = array();

        foreach($this->getAttributes() as $name => $data)
            if($data['required'] === true)
                $out[] = $name;

        return $out;
    }

    /**
     * Get required, but not set, attributes.
     *
     * @access  public
     * @return  array
     */
    public function getNotSetAndRequiredAttributes ( ) {

        $out = array();

        foreach($this->getAttributes() as $name => $data)
            if(   $data['required'] === true
               && $data['value']    === null)
                $out[] = $name;

        return $out;
    }

    /**
     * Set label of the element.
     *
     * @access  public
     * @param   string  $label    The label message.
     * @return  object
     */
    public function setLabel ( $label ) {

        if(is_array($label))
            $label   = isset($label['label']) ? $label['label'] : null;

        $for         = null === $label ? null : $this->getId();
        $this->label = new Hoa_Form_Element_Label($for, $label);

        return $this;
    }

    /**
     * Get label of the element.
     *
     * @access  public
     * @return  object
     */
    public function getLabel ( ) {

        return $this->label;
    }

    /**
     * Set the decorator to use.
     *
     * @access  public
     * @param   string  $decorator    The decorator name.
     * @return  object
     * @throw   Hoa_Form_Exception
     */
    public function setDecorator ( $decorator ) {

        if(is_array($decorator)) {

            if(isset($decorator['path']))
                $this->setDecoratorPath($decorator['path']);

            if(isset($decorator['prefix']))
                $this->setDecoratorPrefix($decorator['prefix']);

            if(!isset($decorator['name']))
                throw new Hoa_Form_Exception(
                    'The decorator name must be specify for the %s element.', 
                    7, get_class($this));

            return $this->setDecorator($decorator['name']);
        }

        if(!is_string($decorator))
            throw new Hoa_Form_Exception(
                'Decorator name must be a string.', 8);
        
        if(null === $this->decoratorPath)
            $this->decoratorPath = dirname(dirname(__FILE__)) . DS . 'Decorator';

        $decoratorName = $this->decoratorPrefix . $decorator;

        if(!file_exists($this->decoratorPath . DS . $decorator . '.php'))
            throw new Hoa_Form_Exception(
                'The decorator %s is not found in %s.',
                9, array($decoratorName, $this->decoratorPath));

        require_once $this->decoratorPath . DS . $decorator . '.php';

        if(!class_exists($decoratorName))
            throw new Hoa_Form_Exception(
                'The decorator class %s is not found in %s.', 10,
                array($decoratorName, $this->decoratorPath . DS . $decorator . '.php'));

        $this->decorator = new $decoratorName();

        if(!($this->decorator instanceof Hoa_Form_Decorator_Abstract))
            throw new Hoa_Form_Exception(
                'The decorator %s must extend Hoa_Form_Decorator_Abstract.',
                11, $decoratorName);

        return $this;
    }

    /**
     * Get the decorator.
     *
     * @access  public
     * @return  object
     */
    public function getDecorator ( ) {

        return $this->decorator;
    }

    /**
     * Set the decorator path.
     *
     * @access  public
     * @param   string  $path    Decorator path.
     * @return  string
     */
    public function setDecoratorPath ( $path ) {

        $old                 = $this->decoratorPath;
        $this->decoratorPath = $path;

        return $old;
    }

    /**
     * Get the decorator path.
     *
     * @access  public
     * @return  string
     */
    public function getDecoratorPath ( ) {

        return $this->decoratorPath;
    }

    /**
     * Set the decorator prefix.
     *
     * @access  public
     * @param   string  $prefix    Decorator prefix.
     * @return  string
     */
    public function setDecoratorPrefix ( $prefix ) {

        $old                   = $this->decoratorPrefix;
        $this->decoratorPrefix = $prefix;

        return $old;
    }

    /**
     * Get the decorator prefix.
     *
     * @access  public
     * @return  string
     */
    public function getDecoratorPrefix ( ) {

        return $this->decoratorPrefix;
    }

    /**
     * Return the nested elements if exists (null by default). Very usefull
     * for iterate the form.
     * In the case of a select element, it contains many nested elements
     * (optgroup and option). The select element just need to overload this
     * method.
     *
     * @access  public
     * @return  array
     */
    public function getElements ( ) {

        return null;
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

        if(substr($name, -2) == '[]')
            $name = substr($name, 0, -2);

        if(empty($data) || !isset($data[$name]))
            return;

        if(is_string($data[$name])) {

            $this->setValue($data[$name]);
            return;
        }

        if(!is_array($data[$name]) || empty($data[$name]))
            return;

        $first = reset($data[$name]);
        unset($data[$name][key($data[$name])]);

        $this->setValue($first);
    }

    /**
     * Set filter.
     * Equivalent to add a filter in the collection of filters.
     * Please, see the Hoa_Filter package to have more informations.
     *
     * @access  public
     * @param   array   $filters    List of filters with their own parameters.
     * @return  object
     * @throw   Hoa_Form_Exception
     */
    public function setFilter ( $filters ) {

        try {

            $this->getFilter()->addFilter($filters);
        }
        catch ( Hoa_Filter_Exception $e ) {

            throw new Hoa_Form_Exception($e->getMessage(), $e->getCode());
        }

        return $this;
    }

    /**
     * Get the collection of filters.
     * If the collection is not initialized, a collection will be set up with a
     * null filter.
     *
     * @access  protected
     * @return  object
     */
    protected function getFilter ( ) {

        if(null === $this->filters) {

            $this->filters = new Hoa_Filter();
            $this->filters->addFilter('Null');
        }

        return $this->filters;
    }

    /**
     * Apply the filter object (that is a collection of filters) on the element
     * value.
     *
     * @access  public
     * @return  void
     */
    public function filter ( ) {

        $this->setValue($this->getFilter()->filter($this->getValue()));
    }

    /**
     * Set validator.
     * Equivalent to add a validator in the collection of validators.
     * Please, see the Hoa_Validate package to have more informations.
     *
     * @access  public
     * @param   array   $validators    The validators.
     * @return  void
     * @throw   Hoa_Form_Exception
     */
    public function setValidator ( $validators ) {

        try {

            $this->getValidator()->addValidator($validators);
        }
        catch ( Hoa_Filter_Exception $e ) {

            throw new Hoa_Form_Exception($e->getMessage(), $e->getCode());
        }

        return $this;
    }

    /**
     * Get collection of validators.
     * If the collection is not initialized, a collection will be set up with a
     * null filter.
     *
     * @access  public
     * @return  object
     */
    public function getValidator ( ) {

        if(null === $this->validators) {

            $this->validators = new Hoa_Validate();
            $this->validators->addValidator('Null');
        }

        return $this->validators;
    }

    /**
     * Apply the validator object (that is a collection of validators) on the element
     * value.
     * Please, see the Hoa_Validate package for more informations.
     *
     * @access  public
     * @return  bool
     */
    public function isValid ( ) {

        return $this->getValidator()->isValid($this->getValue());
    }

    /**
     * Check if the validation has produced errors.
     * Please, see the Hoa_Validate package for more informations.
     *
     * @access  public
     * @return  bool
     */
    public function hasError ( ) {

        return $this->getValidator()->hasError();
    }

    /**
     * Returns the occured error after the validation.
     * Please, see the Hoa_Validate package for more informations.
     *
     * @access  public
     * @return  array
     */
    public function getOccuredErrors ( ) {

        return $this->getValidator()->getOccuredErrors();
    }

    /**
     * Force to implement __toString.
     * The __toString method is complementary to decorators.
     *
     * @acces  public
     * @return string
     */
    abstract public function __toString ( );
}
