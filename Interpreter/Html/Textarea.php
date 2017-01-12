<?php

/**
 * Hoa
 *
 *
 * @license
 *
 * New BSD License
 *
 * Copyright © 2007-2017, Hoa community. All rights reserved.
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

namespace Hoa\Xyl\Interpreter\Html;

use Hoa\Stream;

/**
 * Class \Hoa\Xyl\Interpreter\Html\Textarea.
 *
 * The <textarea /> component.
 *
 * @copyright  Copyright © 2007-2017 Hoa community
 * @license    New BSD License
 */
class Textarea extends Generic
{
    /**
     * Attributes description.
     *
     * @var array
     */
    protected static $_attributes        = [
        'autocomplete' => parent::ATTRIBUTE_TYPE_NORMAL,
        'autofocus'    => parent::ATTRIBUTE_TYPE_NORMAL,
        'cols'         => parent::ATTRIBUTE_TYPE_NORMAL,
        'dirname'      => parent::ATTRIBUTE_TYPE_NORMAL,
        'disabled'     => parent::ATTRIBUTE_TYPE_NORMAL,
        'error'        => parent::ATTRIBUTE_TYPE_LIST,
        'form'         => parent::ATTRIBUTE_TYPE_NORMAL,
        'inputmode'    => parent::ATTRIBUTE_TYPE_NORMAL,
        'maxlength'    => parent::ATTRIBUTE_TYPE_NORMAL,
        'name'         => parent::ATTRIBUTE_TYPE_NORMAL,
        'placeholder'  => parent::ATTRIBUTE_TYPE_NORMAL,
        'readonly'     => parent::ATTRIBUTE_TYPE_NORMAL,
        'required'     => parent::ATTRIBUTE_TYPE_NORMAL,
        'rows'         => parent::ATTRIBUTE_TYPE_NORMAL,
        'validate'     => parent::ATTRIBUTE_TYPE_CUSTOM,
        'wrap'         => parent::ATTRIBUTE_TYPE_NORMAL
    ];

    /**
     * Attributes mapping between XYL and HTML.
     *
     * @var array
     */
    protected static $_attributesMapping = [
        'autocomplete',
        'autofocus',
        'cols',
        'dirname',
        'disabled',
        'form',
        'inputmode',
        'maxlength',
        'name',
        'placeholder',
        'readonly',
        'required',
        'rows',
        'wrap'
    ];

    /**
     * Whether the textarea is valid or not.
     *
     * @var bool
     */
    protected $_validity                 = null;

    /**
     * Temporize value.
     *
     * @var string
     */
    protected $_value                    = null;



    /**
     * Paint the element.
     *
     * @param   \Hoa\Stream\IStream\Out  $out    Out stream.
     * @return  void
     */
    protected function paint(Stream\IStream\Out $out)
    {
        $name = $this->getName();

        $out->writeAll('<' . $name . $this->readAttributesAsString() . '>');

        if (null === $this->_value) {
            $this->computeValue($out);
        } else {
            $out->writeAll($this->_value);
        }

        $out->writeAll('</' . $name . '>');

        return;
    }

    /**
     * Get form.
     *
     * @return  \Hoa\Xyl\Interpreter\Html\Form
     */
    public function getForm()
    {
        return Form::getMe($this);
    }

    /**
     * Whether the input is valid or not.
     *
     * @param   bool   $revalid    Re-valid or not.
     * @param   mixed  $value      Value to test.
     * @return  bool
     */
    public function isValid($revalid = false, $value)
    {
        if (false === $revalid && null !== $this->_validity) {
            return $this->_validity;
        }

        $this->_validity = true;

        if ((null === $value || '' === $value) &&
            true === $this->attributeExists('required')) {
            $this->_validity = false;

            return Form::postValidation($this->_validity, $value, $this);
        }

        if (true === $this->attributeExists('maxlength')) {
            $maxlength = intval($this->readAttribute('maxlength'));

            if (mb_strlen($value) > $maxlength) {
                $this->_validity = false;

                return Form::postValidation($this->_validity, $value, $this);
            }
        }

        if (true === $this->attributeExists('readonly')) {
            $this->_validity = $value === $this->computeValue();

            return Form::postValidation($this->_validity, $value, $this);
        }

        return Form::postValidation($this->_validity, $value, $this);
    }

    /**
     * Set value.
     *
     * @param   mixed  $value    Value.
     * @return  string
     */
    public function setValue($value)
    {
        $this->_value = $value;

        return;
    }
}
