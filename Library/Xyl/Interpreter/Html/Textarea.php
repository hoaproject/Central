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
 * Class \Hoa\Xyl\Interpreter\Html\Textarea.
 *
 * The <textarea /> component.
 *
 * @author     Ivan Enderlin <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright © 2007-2011 Ivan Enderlin.
 * @license    New BSD License
 */

class Textarea extends Generic {

    /**
     * Attributes description.
     *
     * @var \Hoa\Xyl\Interpreter\Html\Textarea array
     */
    protected static $_attributes        = array(
        'autofocus'   => parent::ATTRIBUTE_TYPE_NORMAL,
        'cols'        => parent::ATTRIBUTE_TYPE_NORMAL,
        'dirname'     => parent::ATTRIBUTE_TYPE_NORMAL,
        'disabled'    => parent::ATTRIBUTE_TYPE_NORMAL,
        'form'        => parent::ATTRIBUTE_TYPE_NORMAL,
        'maxlength'   => parent::ATTRIBUTE_TYPE_NORMAL,
        'name'        => parent::ATTRIBUTE_TYPE_NORMAL,
        'onerror'     => parent::ATTRIBUTE_TYPE_LIST,
        'placeholder' => parent::ATTRIBUTE_TYPE_NORMAL,
        'readonly'    => parent::ATTRIBUTE_TYPE_NORMAL,
        'required'    => parent::ATTRIBUTE_TYPE_NORMAL,
        'rows'        => parent::ATTRIBUTE_TYPE_NORMAL,
        'validate'    => parent::ATTRIBUTE_TYPE_CUSTOM,
        'wrap'        => parent::ATTRIBUTE_TYPE_NORMAL
    );

    /**
     * Attributes mapping between XYL and HTML.
     *
     * @var \Hoa\Xyl\Interpreter\Html\Textarea array
     */
    protected static $_attributesMapping = array(
        'autofocus',
        'cols',
        'dirname',
        'disabled',
        'form',
        'maxlength',
        'name',
        'placeholder',
        'readonly',
        'required',
        'rows',
        'wrap'
    );

    /**
     * Praspel compiler, to interprete the validate attribute.
     *
     * @var \Hoa\Test\Praspel\Compiler object
     */
    protected static $_compiler          = null;

    /**
     * Whether the textarea is valid or not.
     *
     * @var \Hoa\Xyl\Interpreter\Html\Textarea bool
     */
    protected $_validity                 = true;



    /**
     * Set (or restore) the textarea value.
     *
     * @access  public
     * @param   string  $value    Value.
     * @return  string
     */
    public function setValue ( $value ) {

        $this->truncate(0);
        $this->writeAll($value);

        return;
    }

    /**
     * Get the textarea value.
     *
     * @access  public
     * @return  string
     */
    public function getValue ( ) {

        return $this->readAll();
    }

    /**
     * Unset the textarea value.
     *
     * @access  public
     * @return  void
     */
    public function unsetValue ( ) {

        $this->truncate(0);

        return;
    }

    /**
     * Check the textarea validity.
     *
     * @access  public
     * @param   mixed  $value    Value (if null, will find the value).
     * @return  bool
     */
    public function checkValidity ( $value = null ) {

        $validates = array();

        if(true === $this->attributeExists('validate'))
            $validates['@'] = $this->readAttribute('validate');

        $validates = array_merge(
            $validates,
            $this->abstract->readCustomAttributes('validate')
        );

        if(empty($validates))
            return true;

        $onerrors = array();

        if(true === $this->attributeExists('onerror'))
            $onerrors['@'] = $this->readAttributeAsList('onerror');

        $onerrors = array_merge(
            $onerrors,
            $this->readCustomAttributesAsList('onerror')
        );

        if(null === $value)
            $value = $this->getValue();

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
     * Whether the textarea is valid or not.
     *
     * @access  public
     * @return  bool
     */
    public function isValid ( ) {

        return $this->_validity;
    }
}

}
