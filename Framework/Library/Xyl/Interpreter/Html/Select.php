<?php

/**
 * Hoa
 *
 *
 * @license
 *
 * New BSD License
 *
 * Copyright (c) 2007-2011, Ivan Enderlin. All rights reserved.
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
-> import('Xyl.Element.Concrete');

}

namespace Hoa\Xyl\Interpreter\Html {

/**
 * Class \Hoa\Xyl\Interpreter\Html\Select.
 *
 * The <select /> component.
 *
 * @author     Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright (c) 2007-2011 Ivan ENDERLIN.
 * @license    New BSD License
 */

class Select extends \Hoa\Xyl\Element\Concrete {

    /**
     * Whether the select is valid or not.
     *
     * @var \Hoa\Xyl\Interpreter\Html\Input bool
     */
    protected $_validity = false;



    /**
     * Paint the element.
     *
     * @access  protected
     * @param   \Hoa\Stream\IStream\Out  $out    Out stream.
     * @return  void
     */
    protected function paint ( \Hoa\Stream\IStream\Out $out ) {

        $out->writeAll('<select' . $this->readAttributesAsString() . '>' . "\n");

        foreach($this as $child)
            $child->render($out);

        $out->writeAll('</select>');

        return;
    }

    /**
     * Set (or restore) the select value.
     *
     * @access  public
     * @param   string  $value    Value.
     * @return  string
     */
    public function setValue ( $value ) {

        foreach($this->getOptions() as $option) {

            $option = $this->getConcreteElement($option);

            if($value == $option->getValue())
                $option->setValue($value);
            else
                $option->unsetValue($value);
        }

        return;
    }

    /**
     * Get the select value.
     *
     * @access  public
     * @return  string
     */
    public function getValue ( ) {

        if(null === $option = $this->getSelectedOption())
            return null;

        return $option->getValue();
    }

    /**
     * Unset the select value.
     *
     * @access  public
     * @return  void
     */
    public function unsetValue ( ) {

        if(null === $option = $this->getSelectedOption())
            return;

        $option->unsetValue();

        return;
    }

    /**
     * Get all <option /> components.
     *
     * @access  public
     * @return  array
     */
    public function getOptions ( ) {

        return array_merge(
            $this->xpath('./__current_ns:option'),
            $this->xpath('./__current_ns:optgroup/__current_ns:option')
        );
    }

    /**
     * Get the selected <option /> components.
     *
     * @access  public
     * @return  \Hoa\Xyl\Interpreter\Html\Option
     */
    public function getSelectedOption ( ) {

        $options = array_merge(
            $this->xpath('./__current_ns:option[@selected]'),
            $this->xpath('./__current_ns:optgroup/__current_ns:option[@selected]')
        );

        if(empty($options))
            return null;

        return $this->getConcreteElement($options[0]);
    }

    /**
     * Check the select validity.
     *
     * @access  public
     * @param   mixed  $value    Value (if null, will find the value).
     * @return  bool
     */
    public function checkValidity ( $value = null ) {

        $options = $this->getOptions();
        $values  = array();

        if(null === $value)
            foreach($options as $option) {

                $option = $this->getConcreteElement($option);

                if(true === $option->isSelected())
                    $values[] = $value = $option->getValue();
                else
                    $values[] = $option->getValue();
            }
        else
            foreach($options as $option ) {

                $option = $this->getConcreteElement($option);
                $values[] = $option->getValue();
            }

        return $this->_validity = in_array($value, $values);
    }

    /**
     * Whether the select is valid or not.
     *
     * @access  public
     * @return  bool
     */
    public function isValid ( ) {

        return $this->_validity;
    }
}

}
