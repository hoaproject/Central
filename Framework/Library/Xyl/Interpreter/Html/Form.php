<?php

/**
 * Hoa
 *
 *
 * @license
 *
 * New BSD License
 *
 * Copyright © 2007-2011, Ivan Enderlin. All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *     * Redistributions of source code must retain the above copyright
 *       notice, this list of conditions and the following disclaimer.
 *     * Redistributions in binary form must reproduce the above copyright
 *       notice, this list of conditions and the following disclaimer in the
 *       documentation and/or other materials provided with the distribution.
 *     * Neither the name of the Hoa nor the names of its contributors may be
 *       used to endorse or promote products derived from this software without
 *       specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDERS AND CONTRIBUTORS BE
 * LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
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
 * @copyright  Copyright © 2007-2011 Ivan ENDERLIN.
 * @license    New BSD License
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
