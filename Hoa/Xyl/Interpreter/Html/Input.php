<?php

/**
 * Hoa
 *
 *
 * @license
 *
 * New BSD License
 *
 * Copyright © 2007-2012, Ivan Enderlin. All rights reserved.
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
 * \Hoa\Xyl\Interpreter\Html\Generic
 */
-> import('Xyl.Interpreter.Html.Generic')

/**
 * \Hoa\Xyl\Interpreter\Html\Form
 */
-> import('Xyl.Interpreter.Html.Form')

/**
 * \Hoa\Realdom\Color
 */
-> import('Realdom.Color');

}

namespace Hoa\Xyl\Interpreter\Html {

/**
 * Class \Hoa\Xyl\Interpreter\Html\Input.
 *
 * The <input /> component.
 *
 * @author     Ivan Enderlin <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright © 2007-2012 Ivan Enderlin.
 * @license    New BSD License
 */

class Input extends Generic {

    /**
     * Attributes description.
     *
     * @var \Hoa\Xyl\Interpreter\Html\Input array
     */
    protected static $_attributes        = array(
        'accept'         => parent::ATTRIBUTE_TYPE_NORMAL,
        'alt'            => parent::ATTRIBUTE_TYPE_NORMAL,
        'autocomplete'   => parent::ATTRIBUTE_TYPE_NORMAL,
        'autofocus'      => parent::ATTRIBUTE_TYPE_NORMAL,
        'checked'        => parent::ATTRIBUTE_TYPE_NORMAL,
        'dirname'        => parent::ATTRIBUTE_TYPE_NORMAL,
        'disabled'       => parent::ATTRIBUTE_TYPE_NORMAL,
        'error'          => parent::ATTRIBUTE_TYPE_LIST,
        'form'           => parent::ATTRIBUTE_TYPE_NORMAL,
        'formaction'     => parent::ATTRIBUTE_TYPE_LINK,
        'formenctype'    => parent::ATTRIBUTE_TYPE_NORMAL,
        'formmethod'     => parent::ATTRIBUTE_TYPE_NORMAL,
        'formnovalidate' => parent::ATTRIBUTE_TYPE_NORMAL,
        'formtarget'     => parent::ATTRIBUTE_TYPE_NORMAL,
        'height'         => parent::ATTRIBUTE_TYPE_NORMAL,
        'inputmode'      => parent::ATTRIBUTE_TYPE_NORMAL,
        'list'           => parent::ATTRIBUTE_TYPE_NORMAL,
        'max'            => parent::ATTRIBUTE_TYPE_NORMAL,
        'maxlength'      => parent::ATTRIBUTE_TYPE_NORMAL,
        'min'            => parent::ATTRIBUTE_TYPE_NORMAL,
        'multiple'       => parent::ATTRIBUTE_TYPE_NORMAL,
        'name'           => parent::ATTRIBUTE_TYPE_NORMAL,
        'pattern'        => parent::ATTRIBUTE_TYPE_NORMAL,
        'placeholder'    => parent::ATTRIBUTE_TYPE_NORMAL,
        'readonly'       => parent::ATTRIBUTE_TYPE_NORMAL,
        'required'       => parent::ATTRIBUTE_TYPE_NORMAL,
        'size'           => parent::ATTRIBUTE_TYPE_NORMAL,
        'src'            => parent::ATTRIBUTE_TYPE_LINK,
        'step'           => parent::ATTRIBUTE_TYPE_NORMAL,
        'type'           => parent::ATTRIBUTE_TYPE_NORMAL,
        'validate'       => parent::ATTRIBUTE_TYPE_CUSTOM,
        'value'          => parent::ATTRIBUTE_TYPE_NORMAL,
        'width'          => parent::ATTRIBUTE_TYPE_NORMAL
    );

    /**
     * Attributes mapping between XYL and HTML.
     *
     * @var \Hoa\Xyl\Interpreter\Html\Input array
     */
    protected static $_attributesMapping = array(
        'accept',
        'alt',
        'autocomplete',
        'autofocus',
        'checked',
        'dirname',
        'disabled',
        'form',
        'formaction',
        'formenctype',
        'formmethod',
        'formnovalidate',
        'formtarget',
        'height',
        'inputmode',
        'list',
        'max',
        'maxlength',
        'min',
        'multiple',
        'name',
        'pattern',
        'placeholder',
        'readonly',
        'required',
        'size',
        'src',
        'step',
        'type',
        'value',
        'width'
    );

    /**
     * Whether content could exist or not.
     * 0 to false, 1 to true, 2 to maybe.
     *
     * @var \Hoa\Xyl\Interpreter\Html\Input int
     */
    protected $_contentFlow              = 0;

    /**
     * Whether the input is valid or not.
     *
     * @var \Hoa\Xyl\Interpreter\Html\Input bool
     */
    protected $_validity                 = null;



    /**
     * Get form.
     *
     * @access  public
     * @return  \Hoa\Xyl\Interpreter\Html\Form
     */
    public function getForm ( ) {

        return Form::getMe($this);
    }

    /**
     * Whether the input is valid or not.
     *
     * @access  public
     * @param   bool   $revalid    Re-valid or not.
     * @param   mixed  $value      Value to test.
     * @return  bool
     */
    public function isValid ( $revalid = false, &$value ) {

        if(false === $revalid && null !== $this->_validity)
            return $this->_validity;

        $this->_validity = true;
        $type            = strtolower($this->readAttribute('type'));

        if(   (null === $value || '' === $value)
           &&  true === $this->attributeExists('required')) {

            $this->_validity = false;

            return Form::postValidation($this->_validity, $value, $this);
        }

        if(   false !== strpos($value, "\n")
           || false !== strpos($value, "\r")) {

            $this->_validity = false;

            return Form::postValidation($this->_validity, $value, $this);
        }

        if(true === $this->attributeExists('pattern')) {

            $pattern = str_replace('#', '\#', $this->readAttribute('pattern'));

            if(0 == @preg_match('#^' . $pattern . '$#u', $value, $_)) {

                $this->_validity = false;

                return Form::postValidation($this->_validity, $value, $this);
            }
        }

        if(true === $this->attributeExists('maxlength')) {

            $maxlength = intval($this->readAttribute('maxlength'));

            if(mb_strlen($value) > $maxlength) {

                $this->_validity = false;

                return Form::postValidation($this->_validity, $value, $this);
            }
        }

        if(true === $this->attributeExists('readonly')) {

            $this->_validity = $value === $this->readAttribute('value');

            return Form::postValidation($this->_validity, $value, $this);
        }

        switch($type) {

            case 'hidden':
                $this->_validity = $value === $this->readAttribute('value');
              break;

            case 'color':
                $this->_validity = 0 !== preg_match(
                    \Hoa\Realdom\Color::REGEX,
                    $value,
                    $ 
                );
              break;

            // @TODO
            case 'tel':
            case 'url':
            case 'datetime':
            case 'date':
            case 'month':
            case 'week':
            case 'time':
            case 'datetime-local':
            case 'file':
                $this->_validity = true;
              break;

            case 'email':
                $this->_validity = false !== filter_var($value, FILTER_VALIDATE_EMAIL);
              break;

            case 'number':
                $value = floatval($value);

                if(false === $this->attributeExists('min')) {

                    if(true === $this->attributeExists('max')) {

                        $max = floatval($this->readAttribute('max'));

                        if($value > $max) {

                            $this->_validity = false;

                            break;
                        }
                    }

                    $this->_validity = true;

                    break;
                }

                $min = floatval($this->readAttribute('min'));

                if($value < $min) {

                    $this->_validity = false;

                    break;
                }

                if(false === $this->attributeExists('max')) {

                    $this->_validity = true;

                    break;
                }

                $max = floatval($this->readAttribute('max'));

                if($value > $max) {

                    $this->_validity = false;

                    break;
                }

                // @TODO step
                $this->_validity = true;
              break;

            case 'range':
                $value = floatval($value);
                $min   = floatval($this->readAttribute('min') ?:   0);
                $max   = floatval($this->readAttribute('max') ?: 100);

                if($value < $min || $value > $max)
                    $this->_validity = false;

                $step = $this->getStep();

                if(false === $step) {

                    $this->_validity = false;

                    break;
                }
                elseif(true !== $step) {

                    // @TODO
                    //$this->_validity = $min % $step === $value % $step;
                    $this->_validity = true;
                }
              break;

            case 'checkbox':
                if(null === $value)
                    $this->_validity = true;
                elseif(!is_string($value))
                    $this->_validity = false;
                else
                    $this->_validity = $value === $this->readAttribute('value');
              break;

            case 'radio':
                if(!is_string($value))
                    $this->_validity = false;
                else
                    $this->_validity = $value === $this->readAttribute('value');
              break;

            case 'submit':
            case 'image':
            case 'reset':
            case 'button':
                $this->_validity = true;
              break;

            case 'text':
            case 'search':
            case 'password':
            default:
                $this->_validity = true;
        }

        return Form::postValidation($this->_validity, $value, $this);
    }

    /**
     * Set value.
     *
     * @access  public
     * @param   mixed  $value    Value.
     * @return  string
     */
    public function setValue ( $value ) {

        $type = strtolower($this->readAttribute('type'));

        switch($type) {

            case 'checkbox':
            case 'radio':
                $this->writeAttribute('checked', 'checked');
              break;

            default:
                $this->writeAttribute('value', $value);
        }

        return;
    }

    /**
     * Get step.
     *
     * @access  protected
     * @return  mixed
     */
    protected function getStep ( ) {

        if(false === $this->attributeExists('step'))
            return true;

        $step = $this->readAttribute('step');

        if('any' === strtolower($step))
            return true;

        $step = floatval($step);

        if(0 >= $step)
            return false;

        return $step;
    }
}

}
