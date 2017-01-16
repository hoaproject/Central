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

use Hoa\Http;
use Hoa\Praspel;
use Hoa\Stream;
use Hoa\Xyl;

/**
 * Class \Hoa\Xyl\Interpreter\Html\Form.
 *
 * The <form /> component.
 *
 * @copyright  Copyright © 2007-2017 Hoa community
 * @license    New BSD License
 */
class Form extends Generic implements Xyl\Element\Executable
{
    /**
     * Attributes description.
     *
     * @var array
     */
    protected static $_attributes        = [
        'accept-charset' => parent::ATTRIBUTE_TYPE_NORMAL,
        'action'         => parent::ATTRIBUTE_TYPE_LINK,
        'autocomplete'   => parent::ATTRIBUTE_TYPE_NORMAL,
        'async'          => parent::ATTRIBUTE_TYPE_NORMAL,
        'enctype'        => parent::ATTRIBUTE_TYPE_NORMAL,
        'error'          => parent::ATTRIBUTE_TYPE_LIST,
        'method'         => parent::ATTRIBUTE_TYPE_NORMAL,
        'name'           => parent::ATTRIBUTE_TYPE_NORMAL,
        // client, security, all (=true)
        'novalidate'     => parent::ATTRIBUTE_TYPE_NORMAL,
        'target'         => parent::ATTRIBUTE_TYPE_NORMAL
    ];

    /**
     * Attributes mapping between XYL and HTML.
     *
     * @var array
     */
    protected static $_attributesMapping = [
        'accept-charset',
        'action',
        'autocomplete',
        'async'          => 'data-formasync',
        'enctype',
        'method',
        'name',
        'novalidate',
        'target'
    ];

    /**
     * Form data.
     *
     * @var array
     */
    protected $_formData                 = null;

    /**
     * Whether the form is valid or not.
     *
     * @var bool
     */
    protected $_validity                 = null;



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
        // Force Internet Explorer <= 8 to use UTF-8.
        $out->writeAll('<input type="hidden" name="__utf8" value="✓" />');
        $this->computeValue($out);
        $out->writeAll('</' . $name . '>');

        return;
    }

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
     * @return  void
     */
    public function preExecute()
    {
        if ('true' === $this->abstract->readAttribute('async')) {
            if (false === $this->attributeExists('aria-atomic')) {
                $this->writeAttribute('aria-atomic', 'true');
            }

            if (false === $this->attributeExists('aria-busy')) {
                $this->writeAttribute('aria-busy', 'false');
            }

            if (false === $this->attributeExists('aria-live')) {
                $this->writeAttribute('aria-live', 'polite');
            }

            if (false === $this->attributeExists('aria-relevant')) {
                $this->writeAttribute('aria-relevant', 'all');
            }
        }

        if (false === $this->attributeExists('action')) {
            $this->writeAttribute('action', '#');
        }

        if (false === $this->attributeExists('method')) {
            $this->writeAttribute('method', 'post');
        }

        if (true === $this->attributeExists('novalidate')) {
            $this->writeAttribute('novalidate', 'true');
        }

        return;
    }

    /**
     * Post-execute an element.
     *
     * @return  void
     */
    public function postExecute()
    {
        return;
    }

    /**
     * Get form elements (and associated elements).
     *
     * TODO : add fieldset support.
     *
     * @return  array
     */
    public function getElements()
    {

        // Form elements.
        $out = array_merge(
            $this->xpath('.//__current_ns:input[@name]'),
            $this->xpath('.//__current_ns:button[@name]'),
            $this->xpath('.//__current_ns:select[@name]'),
            $this->xpath('.//__current_ns:textarea[@name]'),
            $this->xpath('.//__current_ns:keygen[@name]'),
            $this->xpath('.//__current_ns:output[@name]')
        );

        if (null !== $id = $this->readAttribute('id')) {
            // Form-associated elements.
            $out = array_merge(
                $out,
                $this->xpath('//__current_ns:input[@name    and @form="' . $id . '"]'),
                $this->xpath('//__current_ns:button[@name   and @form="' . $id . '"]'),
                $this->xpath('//__current_ns:select[@name   and @form="' . $id . '"]'),
                $this->xpath('//__current_ns:textarea[@name and @form="' . $id . '"]'),
                $this->xpath('//__current_ns:keygen[@name   and @form="' . $id . '"]'),
                $this->xpath('//__current_ns:output[@name   and @form="' . $id . '"]')
            );
        }

        return $out;
    }

    /**
     * Get submit elements.
     *
     * @return  array
     */
    public function getSubmitElements()
    {
        $out = array_merge(
            $this->xpath('.//__current_ns:input[@type="submit"]'),
            $this->xpath('.//__current_ns:input[@type="image"]')
        );

        if (null !== $id = $this->readAttribute('id')) {
            // Form-associated elements.
            $out = array_merge(
                $this->xpath('//__current_ns:input[@type="submit" and @form="' .  $id . '"]'),
                $this->xpath('//__current_ns:input[@type="image"  and @form="' .  $id . '"]')
            );
        }

        return $out;
    }

    /**
     * Whether the form has been sent or not.
     *
     * @return  bool
     */
    public function hasBeenSent()
    {
        $novalidate = $this->abstract->readAttributeAsList('novalidate');

        if (false === $this->abstract->attributeExists('novalidate') ||
            (false === in_array('security', $novalidate) &&
            false === in_array('all', $novalidate) &&
            false === in_array('true', $novalidate))) {
            $method = strtolower($this->readAttribute('method')) ?: 'post';

            if ($method !== Http\Runtime::getMethod()) {
                return false;
            }

            $enctype = $this->readAttribute('enctype')
                           ?: 'application/x-www-form-urlencoded';

            if ($enctype !== Http\Runtime::getHeader('Content-Type')) {
                return false;
            }

            // add verifications if:
            //     <input type="submit" formaction="…" form*="…" />
        }

        return \Hoa\Http\Runtime::hasData();
    }

    /**
     * Whether the form is valid or not.
     *
     * @param   bool  $revalid    Re-valid or not.
     * @return  bool
     */
    public function isValid($revalid = false)
    {
        if (false === $revalid && null !== $this->_validity) {
            return $this->_validity;
        }

        $novalidate = $this->abstract->readAttributeAsList('novalidate');
        // what about @formnovalidate on submitable element?

        if (true === in_array('all', $novalidate) ||
            true === in_array('true', $novalidate)) {
            return $this->_validity = true;
        }

        $this->_validity = true;
        $data            = Http\Runtime::getData();
        unset($data['__utf8']);
        $this->flat($data, $flat);

        if (false === is_array($data) || empty($data)) {
            return $this->_validity = false;
        }

        $elements   = $this->getElements();
        $names      = [];
        $validation = [];

        foreach ($elements as &$_element) {
            $_element = $this->getConcreteElement($_element);
            $name     = $_element->readAttribute('name');

            if (!isset($names[$name])) {
                $names[$name] = [];
            }

            $names[$name][] = $_element;
        }

        foreach ($data as $index => $datum) {
            if (!is_array($datum)) {
                if (!isset($names[$index])) {
                    $validation[$index] = false;

                    continue;
                }

                if (1 < count($names[$index])) {
                    $validation[$index] = false;

                    foreach ($names[$index] as $element) {
                        $handle = $element->isValid($revalid, $datum);

                        if (true === $handle) {
                            $element->setValue($datum);
                        }

                        $validation[$index] = $validation[$index] || $handle;
                    }

                    unset($names[$index]);
                    unset($flat[$index]);

                    continue;
                }

                $element            = $names[$index][0];
                $validation[$index] = $element->isValid($revalid, $datum);
                $element->setValue($datum);
                unset($names[$index]);
                unset($flat[$index]);

                continue;
            }

            $validation[$index] = false;

            /*
            print_r($flat);
            print_r($validation);

            $remainder = array();

            foreach($datum as $key => &$value) {

                $key = key($flat);

                if(!isset($names[$key])) {

                    $remainder[] = $key;
                    next($flat);

                    continue;
                }

                $validation[$key] = $names[$key][0]->isValid($revalid, $value);
                unset($flat[$key]);
                unset($names[$key]);
            }

            print_r($remainder);
            */
        }

        foreach ($names as $name => $element) {
            foreach ($element as $el) {
                if (($el instanceof Input ||
                    $el instanceof Textarea ||
                    $el instanceof Select) &&
                    true === $el->attributeExists('required')) {
                    $validation[$name] = false;
                }
            }
        }

        $handle = &$this->_validity;
        array_walk($validation, function ($verdict) use (&$handle) {
            $handle = $handle && $verdict;
        });

        self::postVerification($this->_validity, $this);

        if (true === $this->_validity) {
            $this->_formData = $data;
        }

        return $this->_validity;
    }

    /**
     * Flat array into name[k1][k2]…[kn] = value
     *
     * @param   mixed   $value    Array.
     * @param   array   &$out     Result.
     * @param   string  $key      Key (prefix).
     * @return  void
     */
    protected function flat(&$value, &$out, $key = null)
    {
        if (!is_array($value)) {
            $out[$key] = &$value;

            return;
        }

        foreach ($value as $k => &$v) {
            if (null === $key) {
                $this->flat($v, $out, $k);
            } else {
                $this->flat($v, $out, $key . '[' . $k . ']');
            }
        }

        return;
    }

    /**
     * Post simple validation.
     *
     * @param   bool   $verdict             Verdict.
     * @param   bool   &$value              Value.
     * @param   \Hoa\Xyl\Interpreter\Html\Concrete  $element    Element that
     *                                                          requires it.
     * @param   bool   $postVerification    Whether we run post-verification or
     *                                      not.
     * @return  bool
     */
    public static function postValidation(
        $verdict,
        &$value,
        Concrete $element,
        $postVerification = true
    ) {
        // Order is important.
        $validates = [];

        if (true === $element->abstract->attributeExists('validate')) {
            $validates['@'] = $element->abstract->readAttribute('validate');
        }

        $validates = array_merge(
            $validates,
            $element->abstract->readCustomAttributes('validate')
        );

        if (empty($validates)) {
            if (true === $postVerification) {
                static::postVerification($verdict, $element);
            }

            return $verdict;
        }

        // Order is not important.
        $errors = $element->abstract->readCustomAttributesAsList('error');

        if (true === $element->abstract->attributeExists('error')) {
            $errors['@'] = $element->abstract->readAttributeAsList('error');
        }

        if (ctype_digit($value)) {
            $value = (int) $value;
        } elseif (is_numeric($value)) {
            $value = (float) $value;
        }

        $decision = true;

        foreach ($validates as $name => $realdom) {
            $praspel = Praspel::interpret(
                '@requires i: ' . $realdom . ';'
            );
            $clause   = $praspel->getClause('requires');
            $variable = $clause['i'];
            $decision = $variable->predicate($value);

            if ('@' === $name) {
                $decision = $verdict && $decision;
            }

            if (true === $decision) {
                unset($errors[$name]);

                continue;
            }

            if (!isset($errors[$name])) {
                continue;
            }

            $handle = $element->xpath(
                '//__current_ns:error[@id="' .
                implode('" or @id="', $errors[$name]) .
                '"]'
            );

            foreach ($handle as $error) {
                $element->getConcreteElement($error)->setVisibility(true);
            }

            unset($errors[$name]);

            break;
        }

        $verdict = $decision;

        if (true === $postVerification) {
            static::postVerification($verdict, $element, isset($errors['@']));
        }

        return $verdict;
    }

    /**
     * Post-verification.
     *
     * @param   bool                                $verdict    Verdict.
     * @param   \Hoa\Xyl\Interpreter\Html\Concrete  $element    Element that
     *                                                          requires it.
     * @return  void
     */
    public static function postVerification(
        $verdict,
        Concrete $element,
        $raise = true
    ) {
        if (true === $verdict) {
            return;
        }

        if (true !== $raise) {
            return;
        }

        $onerror = $element->abstract->readAttributeAsList('error');
        $errors  = $element->xpath(
            '//__current_ns:error[@id="' . implode('" or @id="', $onerror) . '"]'
        );

        foreach ($errors as $error) {
            $element->getConcreteElement($error)->setVisibility(true);
        }

        return;
    }

    /**
     * Get a data from the form.
     *
     * @param   string  $index    Index (if null, return all data).
     * @return  mixed
     */
    public function getData($index = null)
    {
        if (null === $index) {
            return $this->_formData;
        }

        if (!isset($this->_formData[$index])) {
            return null;
        }

        return $this->_formData[$index];
    }

    /**
     * Get form associated to an element.
     *
     * @param   \Hoa\Xyl\Interpreter\Html\Concrete  $element    Element.
     * @return  \Hoa\Xyl\Interpreter\Html\Form
     */
    public static function getMe(Concrete $element)
    {
        if (true === $element->attributeExists('form')) {
            $form = $element->xpath(
                '//__current_ns:form[@id="' . $element->readAttribute('form') . '"]'
            );
        } else {
            $form = $element->xpath('.//ancestor::__current_ns:form');
        }

        if (empty($form)) {
            return null;
        }

        return $form[0];
    }
}
