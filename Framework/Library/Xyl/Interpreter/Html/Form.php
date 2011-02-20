<?php

/**
 * Hoa
 *
 *
 * @license
 *
 * GNU General Public License
 *
 * This file is part of Hoa Open Accessibility.
 * Copyright (c) 2007, 2011 Ivan ENDERLIN. All rights reserved.
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
 */

namespace {

from('Hoa')

/**
 * \Hoa\Xyl\Element\Concrete
 */
-> import('Xyl.Element.Concrete')

/**
 * \Hoa\Xyl\Element\Executable
 */
-> import('Xyl.Element.Executable');

}

namespace Hoa\Xyl\Interpreter\Html {

/**
 * Class \Hoa\Xyl\Interpreter\Html\Form.
 *
 * The <form /> component.
 *
 * @author     Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright (c) 2007, 2011 Ivan ENDERLIN.
 * @license    http://gnu.org/licenses/gpl.txt GNU GPL
 */

class          Form
    extends    \Hoa\Xyl\Element\Concrete
    implements \Hoa\Xyl\Element\Executable {

    /**
     * Form data.
     *
     * @var \Hoa\Xyl\Interpreter\Html\Form array
     */
    protected $_formData = array();

    /**
     * Whether the form is valid or not.
     *
     * @var \Hoa\Xyl\Interpreter\Html\Form bool
     */
    protected $_validity = true;



    /**
     * Paint the element.
     *
     * @access  protected
     * @param   \Hoa\Stream\IStream\Out  $out    Out stream.
     * @return  void
     */
    protected function paint ( \Hoa\Stream\IStream\Out $out ) {

        $out->writeAll(
            '<form' . $this->readAttributesAsString() . '>' . "\n"
        );

        foreach($this as $child)
            $child->render($out);

        $out->writeAll(
            '</form>' . "\n"
        );

        return;
    }

    /**
     * Pre-execute an element.
     *
     * @access  public
     * @return  void
     */
    public function preExecute ( ) {

        return;
    }

    /**
     * Post-execute an element.
     *
     * @access  public
     * @return  void
     */
    public function postExecute ( ) {

        $this->_formData = $_REQUEST;
        $inputs          = array_merge(
            $this->xpath('descendant-or-self::__current_ns:input'),
            $this->xpath('descendant-or-self::__current_ns:select'),
            $this->xpath('descendant-or-self::__current_ns:textarea')
        );

        if(empty($this->_formData))
            return;

        foreach($inputs as $input) {

            $input = $this->getConcreteElement($input);
            $name  = $input->readAttribute('name');

            if('[]' == substr($name, -2))
                $name = substr($name, 0, -2);

            if(!isset($this->_formData[$name])) {

                $input->unsetValue();
                $input->checkValidity();
                $this->_validity = $input->isValid() && $this->_validity;

                continue;
            }

            if(is_array($this->_formData[$name]))
                $value = array_shift($this->_formData[$name]);
            else
                $value = $this->_formData[$name];


            $input->setValue($value);
            $input->checkValidity($value);
            $this->_validity = $input->isValid() && $this->_validity;
        }

        if(true === $this->_validity)
            return;

        $errors = $this->xpath(
            '//__current_ns:error[@id="' .
            implode('" or @id="', $this->readAttributeAsList('onerror')) .
            '"]'
        );

        foreach($errors as $error)
            $this->getConcreteElement($error)->setVisibility(true);

        return;
    }

    /**
     * Whether the form is valid or not.
     *
     * @access  public
     * @return  bool
     */
    public function isValid ( ) {

        return $this->_validity;
    }

    /**
     * Get a data from the form.
     *
     * @access  public
     * @param   string  $index    Index (if null, return all data).
     * @return  mixed
     */
    public function getFormData ( $index = null ) {

        if(null === $index)
            return $this->_formData;

        if(!isset($this->_formData[$index]))
            return null;

        return $this->_formData[$index];
    }

    /**
     * Get form HTTP method.
     *
     * @access  public
     * @return  string
     */
    public function getMethod ( ) {

        return strtolower($this->readAttribute('method'));
    }

    /**
     * Read attributes as a string.
     *
     * @access  public
     * @return  string
     */
    public function readAttributesAsString ( ) {

        $out        = null;
        $attributes = $this->getAbstractElement()->readAttributes();
        unset($attributes['bind']);

        foreach($attributes as $name => $value) {

            if('method' == $name) {

                $value = strtolower($value);

                if('put' == $value || 'delete' == $value)
                    $value = 'post';
            }

            $out .= ' ' . $name . '="' . str_replace('"', '\"', $value) . '"';
        }

        return $out;
    }
}

}
