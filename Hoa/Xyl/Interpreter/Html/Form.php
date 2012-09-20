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
 * @author     Ivan Enderlin <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright © 2007-2012 Ivan Enderlin.
 * @license    New BSD License
 */

class Form extends Generic implements \Hoa\Xyl\Element\Executable {

    /**
     * Attributes description.
     *
     * @var \Hoa\Xyl\Interpreter\Html\Form array
     */
    protected static $_attributes        = array(
        'accept-charset' => parent::ATTRIBUTE_TYPE_NORMAL,
        'action'         => parent::ATTRIBUTE_TYPE_LINK,
        'autocomplete'   => parent::ATTRIBUTE_TYPE_NORMAL,
        'async'          => parent::ATTRIBUTE_TYPE_NORMAL,
        'enctype'        => parent::ATTRIBUTE_TYPE_NORMAL,
        'method'         => parent::ATTRIBUTE_TYPE_NORMAL,
        'name'           => parent::ATTRIBUTE_TYPE_NORMAL,
        'novalidate'     => parent::ATTRIBUTE_TYPE_NORMAL,
        'onerror'        => parent::ATTRIBUTE_TYPE_LIST,
        'target'         => parent::ATTRIBUTE_TYPE_NORMAL
    );

    /**
     * Attributes mapping between XYL and HTML.
     *
     * @var \Hoa\Xyl\Interpreter\Html\Form array
     */
    protected static $_attributesMapping = array(
        'accept-charset',
        'action',
        'autocomplete',
        'async'          => 'data-formasync',
        'enctype',
        'method',
        'name',
        'novalidate',
        'target'
    );

    /**
     * Form data.
     *
     * @var \Hoa\Xyl\Interpreter\Html\Form array
     */
    protected $_formData                 = null;

    /**
     * Whether the form is valid or not.
     *
     * @var \Hoa\Xyl\Interpreter\Html\Form bool
     */
    protected $_validity                 = true;

    /**
     * Whether the form has been validated or not.
     *
     * @var bool
     */
    protected $_hasBeenValidated         = false;



    /**
     * Pre-execute an element.
     *
     * ARIA and @async attribute
     *   In http://w3.org/TR/wai-aria/, the section “6.5.2 Live Region
     *   Attributes” specifies 4 attributes corresponding to our needs:
     *     • aria-atomic
     *     • aria-busy
     *     • aria-live
     *     • aria-relevant
     *
     * @access  public
     * @return  void
     */
    public function preExecute ( ) {

        if('true' === $this->abstract->readAttribute('async')) {

            if(false === $this->abstract->attributeExists('aria-atomic'))
                $this->writeAttribute('aria-atomic', 'true');

            if(false === $this->abstract->attributeExists('aria-busy'))
                $this->writeAttribute('aria-busy', 'false');

            if(false === $this->abstract->attributeExists('aria-live'))
                $this->writeAttribute('aria-live', 'polite');

            if(false === $this->abstract->attributeExists('aria-relevant'))
                $this->writeAttribute('aria-relevant', 'all');
        }

        return;
    }

    /**
     * Post-execute an element.
     *
     * @access  public
     * @return  void
     */
    public function postExecute ( ) {

        $this->isValid();

        return;
    }

    /**
     * Whether the form is valid or not.
     *
     * @access  public
     * @return  bool
     */
    public function isValid ( $revalid = false ) {

        if(false === $revalid && true === $this->_hasBeenValidated)
            return $this->_validity;

        $this->_hasBeenValidated = true;
        $this->_formData         = array_merge($_POST, $_GET);
        $validate                = false === $this->attributeExists('novalidate');
        $inputs                  = array_merge(
            $this->xpath('.//__current_ns:input'),
            $this->xpath('.//__current_ns:select'),
            $this->xpath('.//__current_ns:textarea')
        );

        if(empty($this->_formData))
            return $this->_validity = false;

        foreach($inputs as $input) {

            $input = $this->getConcreteElement($input);
            $name  = $input->readAttribute('name');

            if('[]' == substr($name, -2))
                $name = substr($name, 0, -2);

            if(!isset($this->_formData[$name])) {

                if(true === $validate) {

                    $input->checkValidity();
                    $this->_validity = $input->isValid() && $this->_validity;
                }

                continue;
            }

            if(is_array($this->_formData[$name]))
                $value = array_shift($this->_formData[$name]);
            else
                $value = $this->_formData[$name];

            $input->setValue($value);

            if(true === $validate) {

                $input->checkValidity($value);
                $this->_validity = $input->isValid() && $this->_validity;
            }
        }

        if(true !== $validate)
            return true;

        if(true === $this->_validity)
            return $this->_validity;

        $errors = $this->xpath(
            '//__current_ns:error[@id="' .
            implode('" or @id="', $this->abstract->readAttributeAsList('onerror')) .
            '"]'
        );

        foreach($errors as $error)
            $this->getConcreteElement($error)->setVisibility(true);

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
     * Whether the form has been sent or not.
     *
     * @access  public
     * @return  bool
     */
    public function hasBeenSent ( ) {

        return !empty($_POST) || !empty($_GET);
    }
}

}
