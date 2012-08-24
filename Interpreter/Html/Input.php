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
 * \Hoa\Test\Praspel\Compiler
 */
-> import('Test.Praspel.Compiler');

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
        'checked'        => parent::ATTRIBUTE_TYPE_NORMAL,
        'dirname'        => parent::ATTRIBUTE_TYPE_NORMAL,
        'formaction'     => parent::ATTRIBUTE_TYPE_LINK,
        'formenctype'    => parent::ATTRIBUTE_TYPE_NORMAL,
        'formmethod'     => parent::ATTRIBUTE_TYPE_NORMAL,
        'formnovalidate' => parent::ATTRIBUTE_TYPE_NORMAL,
        'formtarget'     => parent::ATTRIBUTE_TYPE_NORMAL,
        'height'         => parent::ATTRIBUTE_TYPE_NORMAL,
        'list'           => parent::ATTRIBUTE_TYPE_NORMAL,
        'max'            => parent::ATTRIBUTE_TYPE_NORMAL,
        'maxlength'      => parent::ATTRIBUTE_TYPE_NORMAL,
        'min'            => parent::ATTRIBUTE_TYPE_NORMAL,
        'multiple'       => parent::ATTRIBUTE_TYPE_NORMAL,
        'name'           => parent::ATTRIBUTE_TYPE_NORMAL,
        'onerror'        => parent::ATTRIBUTE_TYPE_LIST,
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
        'checked',
        'dirname',
        'formaction',
        'formenctype',
        'formmethod',
        'formnovalidate',
        'formtarget',
        'height',
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
     * Praspel compiler, to interprete the validate attribute.
     *
     * @var \Hoa\Test\Praspel\Compiler object
     */
    protected static $_compiler          = null;

    /**
     * Whether the input is valid or not.
     *
     * @var \Hoa\Xyl\Interpreter\Html\Input bool
     */
    protected $_validity                 = true;



    /**
     * Set (or restore) the input value.
     *
     * @access  public
     * @param   string  $value    Value.
     * @return  string
     */
    public function setValue ( $value ) {

        $old = $this->getValue();

        switch(strtolower($this->readAttribute('type'))) {

            case 'checkbox':
                $this->writeAttribute('checked', 'checked');
              break;

            case 'radio':
                if($value == $this->readAttribute('value'))
                    $this->writeAttribute('checked', 'checked');
                else
                    $this->removeAttribute('checked');
              break;

            default:
                $this->writeAttribute('value', $value);
        }

        return $old;
    }

    /**
     * Get the input value.
     *
     * @access  public
     * @return  string
     */
    public function getValue ( ) {

        $value = $this->readAttribute('value');

        if(ctype_digit($value))
            $value = (int) $value;
        elseif(is_numeric($value))
            $value = (float) $value;

        return $value;
    }

    /**
     * Unset the input value.
     *
     * @access  public
     * @return  void
     */
    public function unsetValue ( ) {

        switch(strtolower($this->readAttribute('type'))) {

            case 'checkbox':
            case 'radio':
                $this->removeAttribute('checked');
              break;

            default:
                $this->removeAttribute('value');
        }

        return;
    }

    /**
     * Check the input validity.
     *
     * @access  public
     * @param   mixed  $value    Value (if null, will find the value).
     * @return  bool
     */
    public function checkValidity ( $value = null ) {

        $type = strtolower($this->readAttribute('type'));

        if('submit' === $type || 'reset' === $type) {

            $this->_validity = false;

            if(   null   === $value
               || $value ==  $this->getValue())
                $this->_validity = true;

            return $this->_validity;
        }

        $validates = array();

        if(true === $this->abstract->attributeExists('validate'))
            $validates['@'] = $this->abstract->readAttribute('validate');
        else
            switch($type) {

                //@TODO
                // Write Praspel for each input type.
            }

        if(true === $this->attributeExists('pattern')) {

            $pattern = 'regex(\'' . str_replace(
                           '\'',
                           '\\\'',
                           $this->readAttribute('pattern')
                       ) .
                       '\', boundinteger(1, ' .
                       ($this->attributeExists('maxlength')
                           ? $this->readAttribute('maxlength')
                           : 7) .
                       '))';

            if(!isset($validates['@']))
                $validates['@']  = $pattern;
            else
                $validates['@'] .= ' or ' . $pattern;
        }

        $validates = array_merge(
            $validates,
            $this->abstract->readCustomAttributes('validate')
        );

        if(empty($validates))
            return true;

        $onerrors = array();

        if(true === $this->abstract->attributeExists('onerror'))
            $onerrors['@'] = $this->abstract->readAttributeAsList('onerror');

        $onerrors = array_merge(
            $onerrors,
            $this->abstract->readCustomAttributesAsList('onerror')
        );

        if(null === $value)
            $value = $this->getValue();
        else
            if(ctype_digit($value))
                $value = (int) $value;
            elseif(is_numeric($value))
                $value = (float) $value;

        if(null === self::$_compiler)
            self::$_compiler = new \Hoa\Test\Praspel\Compiler();

        $this->_validity = true;

        foreach($validates as $name => $realdom) {

            self::$_compiler->compile('@requires i: ' . $realdom . ';');
            $praspel         = self::$_compiler->getRoot();
            $variable        = $praspel->getClause('requires')->getVariable('i');
            $decision        = $variable->predicate($value);
            $this->_validity = $this->_validity && $decision;

            if(true === $decision)
                continue;

            if(!isset($onerrors[$name]))
                continue;

            $errors = $this->xpath(
                '//__current_ns:error[@id="' .
                implode('" or @id="', $onerrors[$name]) .
                '"]'
            );

            foreach($errors as $error)
                $this->getConcreteElement($error)->setVisibility(true);
        }

        return $this->_validity;
    }

    /**
     * Whether the input is valid or not.
     *
     * @access  public
     * @return  bool
     */
    public function isValid ( ) {

        return $this->_validity;
    }
}

}
