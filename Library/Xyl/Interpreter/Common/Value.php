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
 * \Hoa\Xyl\Element\Concrete
 */
-> import('Xyl.Element.Concrete')

/**
 * \Hoa\Xml\Element\Model\Phrasing
 */
-> import('Xml.Element.Model.Phrasing');

}

namespace Hoa\Xyl\Interpreter\Common {

/**
 * Class \Hoa\Xyl\Interpreter\Common\Value.
 *
 * The <value /> component.
 *
 * @author     Ivan Enderlin <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright © 2007-2012 Ivan Enderlin.
 * @license    New BSD License
 */

class          Value
    extends    \Hoa\Xyl\Element\Concrete
    implements \Hoa\Xml\Element\Model\Phrasing {

    /**
     * Attributes description.
     *
     * @var \Hoa\Xyl\Interpreter\Html\A array
     */
    protected static $_attributes = array(
        'link'      => self::ATTRIBUTE_TYPE_LINK,
        'formatter' => self::ATTRIBUTE_TYPE_NORMAL,
    );



    /**
     * Paint the element.
     *
     * @access  protected
     * @param   \Hoa\Stream\IStream\Out  $out    Out stream.
     * @return  void
     */
    public function paint ( \Hoa\Stream\IStream\Out $out ) {

        $value = $this->computeValue();

        if(true === $this->abstract->attributeExists('formatter')) {

            $formatter   = $this->abstract->readAttribute('formatter');
            $variables   = $this->abstract->readCustomAttributes('formatter');
            $self        = $this;
            array_walk($variables, $f = function ( &$variable ) use ( &$self )  {

                $variable = $self->computeAttributeValue(
                    $variable,
                    parent::ATTRIBUTE_TYPE_UNKNOWN
                );

                if(ctype_digit($variable))
                    $variable = (int) $variable;
                elseif(is_numeric($variable))
                    $variable = (float) $variable;
                elseif('true' == $variable)
                    $variable = true;
                elseif('false' == $variable)
                    $variable = false;
                elseif('null' == $variable)
                    $variable = null;
            });
            $reflection  = new \ReflectionFunction($formatter);
            $arguments   = array();
            $placeholder = $value;
            $f($placeholder);

            foreach($reflection->getParameters() as $parameter) {

                $name = strtolower($parameter->getName());

                if(true === array_key_exists($name, $variables)) {

                    $arguments[$name] = $variables[$name];
                    continue;
                }
                elseif(null !== $placeholder) {

                    $arguments[$name] = $placeholder;
                    $placeholder      = null;
                }
            }

            $value = $reflection->invokeArgs($arguments);
        }

        if(true === $this->abstract->attributeExists('link')) {

            $out->writeAll($this->computeAttributeValue(
                $this->abstract->readAttribute('link'),
                parent::ATTRIBUTE_TYPE_LINK
            ));

            return;
        }

        $out->writeAll($value);

        return;
    }
}

}
