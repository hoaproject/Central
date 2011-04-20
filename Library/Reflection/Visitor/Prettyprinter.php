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
 * \Hoa\Visitor\Registry
 */
-> import('Visitor.Registry');

}

namespace Hoa\Reflection\Visitor {

/**
 * Class \Hoa\Reflection\Visitor\Prettyprinter.
 *
 * Pretty-print the reflection.
 *
 * @author     Ivan Enderlin <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright © 2007-2011 Ivan Enderlin.
 * @license    New BSD License
 */

class Prettyprinter extends \Hoa\Visitor\Registry {

    /**
     * Initialize aggregated visitor.
     *
     * @access  public
     * @return  void
     */
    public function __construct ( ) {

        $this->addEntry(
            'Hoa\Reflection\RClass',
            array($this, 'visitClass')
        );
        $this->addEntry(
            'Hoa\Reflection\RParameter',
            array($this, 'visitParameter')
        );
        $this->addEntry(
            'Hoa\Reflection\RProperty',
            array($this, 'visitProperty')
        );
        $this->addEntry(
            'Hoa\Reflection\RFunction\RMethod',
            array($this, 'visitMethod')
        );

        $this->addEntry(
            'Hoa\Reflection\Fragment\RParameter',
            array($this, 'visitParameter')
        );
        $this->addEntry(
            'Hoa\Reflection\Fragment\RMethod',
            array($this, 'visitMethod')
        );

        return;
    }

    /**
     * Visit an element.
     *
     * @access  public
     * @param   \Hoa\Visitor\Element  $element    Element to visit.
     * @param   mixed                 &$handle    Handle (reference).
     * @param   mixed                 $eldnah     Handle (no reference).
     * @return  mixed
     */
    public function visitClass ( \Hoa\Visitor\Element $element,
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
     * @param   \Hoa\Visitor\Element  $element    Element to visit.
     * @param   mixed                &$handle    Handle (reference).
     * @param   mixed                $eldnah     Handle (no reference).
     * @return  mixed
     */
    public function visitParameter ( \Hoa\Visitor\Element $element,
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
     * @param   \Hoa\Visitor\Element  $element    Element to visit.
     * @param   mixed                &$handle    Handle (reference).
     * @param   mixed                $eldnah     Handle (no reference).
     * @return  mixed
     */
    public function visitProperty ( \Hoa\Visitor\Element $element,
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
     * @param   \Hoa\Visitor\Element  $element    Element to visit.
     * @param   mixed                &$handle    Handle (reference).
     * @param   mixed                $eldnah     Handle (no reference).
     * @return  mixed
     */
    public function visitMethod ( \Hoa\Visitor\Element $element,
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

}
