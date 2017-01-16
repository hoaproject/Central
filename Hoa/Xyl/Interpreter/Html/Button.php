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

/**
 * Class \Hoa\Xyl\Interpreter\Html\Button.
 *
 * The <button /> component.
 *
 * @copyright  Copyright © 2007-2017 Hoa community
 * @license    New BSD License
 */
class Button extends GenericPhrasing
{
    /**
     * Attributes description.
     *
     * @var array
     */
    protected static $_attributes        = [
        'autofocus'      => parent::ATTRIBUTE_TYPE_NORMAL,
        'async'          => parent::ATTRIBUTE_TYPE_NORMAL,
        'asyncaction'    => parent::ATTRIBUTE_TYPE_LINK,
        'asyncenctype'   => parent::ATTRIBUTE_TYPE_NORMAL,
        'asyncmethod'    => parent::ATTRIBUTE_TYPE_NORMAL,
        'asynctarget'    => parent::ATTRIBUTE_TYPE_NORMAL,
        'asyncscope'     => parent::ATTRIBUTE_TYPE_NORMAL,
        'disabled'       => parent::ATTRIBUTE_TYPE_NORMAL,
        'form'           => parent::ATTRIBUTE_TYPE_NORMAL,
        'formaction'     => parent::ATTRIBUTE_TYPE_LINK,
        'formenctype'    => parent::ATTRIBUTE_TYPE_NORMAL,
        'formmethod'     => parent::ATTRIBUTE_TYPE_NORMAL,
        'formnovalidate' => parent::ATTRIBUTE_TYPE_NORMAL,
        'formtarget'     => parent::ATTRIBUTE_TYPE_NORMAL,
        'name'           => parent::ATTRIBUTE_TYPE_NORMAL,
        'type'           => parent::ATTRIBUTE_TYPE_NORMAL,
        'value'          => parent::ATTRIBUTE_TYPE_NORMAL
    ];

    /**
     * Attributes mapping between XYL and HTML.
     *
     * @var array
     */
    protected static $_attributesMapping = [
        'autofocus',
        'async'          => 'data-async',
        'asyncaction'    => 'data-asyncaction',
        'asyncenctype'   => 'data-asyncenctype',
        'asyncmethod'    => 'data-asyncmethod',
        'asynctarget'    => 'data-asynctarget',
        'asyncscope'     => 'data-asyncscope',
        'disabled',
        'form',
        'formaction',
        'formenctype',
        'formmethod',
        'formnovalidate',
        'formtarget',
        'name',
        'type',
        'value'
    ];

    /**
     * Whether content could exist or not.
     * 0 to false, 1 to true, 2 to maybe.
     *
     * @var int
     */
    protected $_contentFlow              = 2;

    /**
     * Whether the input is valid or not.
     *
     * @var bool
     */
    protected $_validity                 = null;



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

        $this->_validity = $value === $this->readAttribute('value');

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
        $this->writeAttribute('value', $value);

        return;
    }
}
