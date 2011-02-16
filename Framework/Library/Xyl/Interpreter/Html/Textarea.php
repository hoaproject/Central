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
 * @author     Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright (c) 2007, 2011 Ivan ENDERLIN.
 * @license    http://gnu.org/licenses/gpl.txt GNU GPL
 */

class Textarea extends Generic {

    /**
     * Map.
     *
     * @var \Hoa\Xyl\Interpreter\Html\Generic string
     */
    protected $_map             = 'textarea';

    /**
     * Praspel compiler, to interprete the validate attribute.
     *
     * @var \Hoa\Test\Praspel\Compiler object
     */
    protected static $_compiler = null;

    /**
     * Whether the textarea is valid or not.
     *
     * @var \Hoa\Xyl\Interpreter\Html\Textarea bool
     */
    protected $_validity        = true;



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
            $this->readCustomAttributes('validate')
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

            self::$_compiler->compile('@requires i:' .$realdom . ';');
            $praspel  = self::$_compiler->getRoot();
            $variable = $praspel->getClause('requires')->getVariable('i');
            $decision = false;

            foreach($variable->getDomains() as $domain)
                $decision = $decision || $domain->predicate($value);

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
