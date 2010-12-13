<?php

/**
 * Hoa Framework
 *
 *
 * @license
 *
 * GNU General Public License
 *
 * This file is part of Hoa Open Accessibility.
 * Copyright (c) 2007, 2010 Ivan ENDERLIN. All rights reserved.
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
 *
 *
 * @category    Framework
 * @package     Hoa_Reflection
 * @subpackage  Hoa_Reflection_Visitor_Prettyprinter
 *
 */

/**
 * Hoa_Visitor_Registry
 */
import('Visitor.Registry') and load();

/**
 * Class Hoa_Reflection_Visitor_Prettyprinter.
 *
 * Pretty-print the reflection.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2010 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Reflection
 * @subpackage  Hoa_Reflection_Visitor_Prettyprinter
 */

class Hoa_Reflection_Visitor_Prettyprinter extends Hoa_Visitor_Registry {

    /**
     * Initialize aggregated visitor.
     *
     * @access  public
     * @return  void
     */
    public function __construct ( ) {

        $this->addEntry(
            'Hoa_Reflection_RClass',
            array($this, 'visitClass')
        );
        $this->addEntry(
            'Hoa_Reflection_RParameter',
            array($this, 'visitParameter')
        );
        $this->addEntry(
            'Hoa_Reflection_RProperty',
            array($this, 'visitProperty')
        );
        $this->addEntry(
            'Hoa_Reflection_RFunction_RMethod',
            array($this, 'visitMethod')
        );

        $this->addEntry(
            'Hoa_Reflection_Fragment_RParameter',
            array($this, 'visitParameter')
        );
        $this->addEntry(
            'Hoa_Reflection_Fragment_RMethod',
            array($this, 'visitMethod')
        );

        return;
    }

    /**
     * Visit an element.
     *
     * @access  public
     * @param   Hoa_Visitor_Element  $element    Element to visit.
     * @param   mixed                &$handle    Handle (reference).
     * @param   mixed                $eldnah     Handle (no reference).
     * @return  mixed
     */
    public function visitClass ( Hoa_Visitor_Element $element,
                                 &$handle = null, $eldnah = null) {

        $out = $element->getDocComment() . "\n";

        if(true === $element->isFinal())
            $out .= 'final ';

        if(true === $element->isAbstract())
            $out .= 'abstract ';

        if(true === $element->isInterface())
            $out .= 'interface ';
        else
            $out .= 'class ';

        $out        .= $element->getName() . ' ';
        $parent      = $element->getParentClass();
        $interfaces  = $element->getInterfaceNames();

        if(false !== $parent)
            $out .= 'extends ' . $parent->getName() . ' ';

        if(!empty($interfaces))
            $out .= 'implements ' . implode($interface, ', ') . ' ';

        $out .= '{' . "\n";

        // We lost API documentation of constants :-(.
        foreach($element->getConstants() as $name => $value)
            $out .= '    const ' . $name . ' = ' .
                    var_export($value, true) . ";\n";

        foreach($element->getProperties() as $name => $property)
            $out .= $property->accept($this, $handle, $eldnah) . "\n";

        foreach($element->getMethods() as $name => $method)
            $out .= $method->accept($this, $handle, $eldnah) . "\n";

        return $out . '}';
    }

    /**
     * Visit an element.
     *
     * @access  public
     * @param   Hoa_Visitor_Element  $element    Element to visit.
     * @param   mixed                &$handle    Handle (reference).
     * @param   mixed                $eldnah     Handle (no reference).
     * @return  mixed
     */
    public function visitParameter ( Hoa_Visitor_Element $element,
                                     &$handle = null, $eldnah = null) {

        $out = null;

        if(true === $element->hasType())
            $out .= $element->getTypeAsString() . ' ';

        if(true === $element->getReference())
            $out .= '&';

        $out .= '$' . $element->getName();

        if(true === $element->isOptional())
            $out .= ' = ' . var_export($element->getDefaultValue(), true);

        return $out;
    }

    /**
     * Visit an element.
     *
     * @access  public
     * @param   Hoa_Visitor_Element  $element    Element to visit.
     * @param   mixed                &$handle    Handle (reference).
     * @param   mixed                $eldnah     Handle (no reference).
     * @return  mixed
     */
    public function visitProperty ( Hoa_Visitor_Element $element,
                                    &$handle = null, $eldnah = null) {

        $out = '    ' .
               str_replace("\n", "\n" . '    ', $element->getComment()) .
               "\n" . '    ';

        if(true === $element->isPublic())
            $out .= 'public ';
        elseif(true === $element->isProtected())
            $out .= 'protected ';
        elseif(true === $element->isPrivate())
            $out .= 'private ';

        if(true === $element->isStatic())
            $out .= 'static ';

        $out .= '$' . $element->getName();

        if(true === $element->isDefault())
            $out .= ' = ' . $element->getDefaultValue() . ';';

        return $out;
    }

    /**
     * Visit an element.
     *
     * @access  public
     * @param   Hoa_Visitor_Element  $element    Element to visit.
     * @param   mixed                &$handle    Handle (reference).
     * @param   mixed                $eldnah     Handle (no reference).
     * @return  mixed
     */
    public function visitMethod ( Hoa_Visitor_Element $element,
                                  &$handle = null, $eldnah = null) {

        $tab = '    ';
        $out = $tab .
               str_replace("\n", "\n" . $tab, $element->getComment()) .
               "\n" . $tab;

        if(true === $element->isAbstract())
            $out .= 'abstract ';

        if(true === $element->isPublic())
            $out .= 'public ';
        elseif(true === $element->isProtected())
            $out .= 'protected ';
        elseif(true === $element->isPrivate())
            $out .= 'private ';

        if(true === $element->isStatic())
            $out .= 'static ';

        if(true === $element->getReference())
            $out .= '&';

        $out   .= 'function ' . $element->getName() . ' (';
        $first  = true;
        $handle = null;

        foreach($element->getParameters() as $i => $parameter) {

            if(false === $first)
                $handle .= ',';
            else
                $first = false;

            $handle .= ' ' . $parameter->accept($this, $handle, $eldnah);
        }

        $out .= $handle . ' ) {' . "\n\n" .
                $element->getBody() . "\n" . $tab . '}';

        return $out;
    }
}
