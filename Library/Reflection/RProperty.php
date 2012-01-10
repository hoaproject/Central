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
 * \Hoa\Reflection\Wrapper
 */
-> import('Reflection.Wrapper')

/**
 * \Hoa\Visitor\Element
 */
-> import('Visitor.Element');

}

namespace Hoa\Reflection {

/**
 * Class \Hoa\Reflection\RProperty.
 *
 * Extending ReflectionProperty.
 *
 * @author     Ivan Enderlin <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright © 2007-2012 Ivan Enderlin.
 * @license    New BSD License
 */

class RProperty extends Wrapper implements \Hoa\Visitor\Element {

    /**
     * Function comment content.
     *
     * @var \Hoa\Reflection\RProperty string
     */
    protected $_comment        = null;

    /**
     * Function name.
     *
     * @var \Hoa\Reflection\RProperty string
     */
    protected $_name           = null;

    /**
     * Method visibility.
     *
     * @var \Hoa\Reflection\RProperty int
     */
    protected $_visibility     = _public;

    /**
     * Memory stack where the method is declared.
     *
     * @var \Hoa\Reflection\RProperty int
     */
    protected $_memoryStack    = _dynamic;

    /**
     * Default value.
     *
     * @var \Hoa\Reflection\RProperty string
     */
    protected $_defaultValue   = null;



    /**
     * Reflect a function or a method.
     *
     * @access  public
     * @param   ReflectionProperty  $wrapped    Property reflection.
     * @return  void
     */
    public function __construct ( $wrapped ) {

        $this->setWrapped($wrapped);

        $comment = $wrapped->getDocComment();
        $comment = preg_replace('#^(\s*/\*\*\s*)#', '', $comment);
        $comment = preg_replace('#(\s*\*/)$#',      '', $comment);
        $comment = preg_replace('#^(\s*\*\s*)#m',   '', $comment);

        $wrapped->setAccessible(true);
        $this->setCommentContent($comment);
        $this->setName($wrapped->getName());

        if(true === $wrapped->isProtected())
            $this->setVisibility(_protected);
        elseif(true === $wrapped->isPrivate())
            $this->setVisibility(_private);

        if(true === $wrapped->isStatic())
            $this->setMemoryStack(_static);

        return;
    }

    /**
     * Set comment content.
     *
     * @access  public
     * @param   string  $comment    Comment content.
     * @return  string
     */
    public function setCommentContent ( $comment ) {

        $old            = $this->_comment;
        $this->_comment = $comment;

        return $old;
    }

    /**
     * Get comment content.
     *
     * @access  public
     * @return  string
     */
    public function getCommentContent ( ) {

        return $this->_comment;
    }

    /**
     * Get comment (content + decoration).
     *
     * @access  public
     * @return  string
     */
    public function getComment ( ) {

        if(null === $comment = $this->getCommentContent())
            return null;

        return "\n" . '/**' . "\n" . ' * ' .
               str_replace("\n", "\n" . ' * ', $comment) .
               "\n" . ' */';
    }

    /**
     * Set the function name.
     *
     * @access  public
     * @param   string  $name    Name.
     * @return  string
     */
    public function setName ( $name ) {

        $old         = $this->_name;
        $this->_name = $name;

        return $old;
    }

    /**
     * Get the function name.
     *
     * @access  public
     * @return  string
     */
    public function getName ( ) {

        return $this->_name;
    }

    /**
     * Check if the method is final or not.
     *
     * @access  public
     * @return  bool
     */
    public function isFinal ( ) {

        return _final == $this->getFinality();
    }

    /**
     * Check if the method is overridable or not.
     *
     * @access  public
     * @return  bool
     */
    public function isOverridable ( ) {

        return _overridable == $this->getFinality();
    }

    /**
     * Set the method visibility.
     *
     * @access  public
     * @param   int     $visibility    Visibility (please, see the _public,
     *                                 _protected and _private constants).
     * @return  int
     */
    public function setVisibility ( $visibility ) {

        $old               = $this->_visibility;
        $this->_visibility = $visibility;

        return $old;
    }

    /**
     * Get the method visibility.
     * Please, see the _public, _protected and _private constants.
     *
     * @access  public
     * @return  int
     */
    public function getVisibility ( ) {

        return $this->_visibility;
    }

    /**
     * Check if the method is public or not.
     *
     * @access  public
     * @return  bool
     */
    public function isPublic ( ) {

        return _public == $this->getVisibility();
    }

    /**
     * Check if the method is protected or not.
     *
     * @access  public
     * @return  bool
     */
    public function isProtected ( ) {

        return _protected == $this->getVisibility();
    }

    /**
     * Check if the method is private or not.
     *
     * @access  public
     * @return  bool
     */
    public function isPrivate ( ) {

        return _private == $this->getVisibility();
    }

    /**
     * Set the method memory stack.
     *
     * @access  public
     * @param   int     $stack    Memory stack (please, see the _dynamic and
     *                            _static constants).
     * @return  int
     */
    public function setMemoryStack ( $stack ) {

        $old                = $this->_memoryStack;
        $this->_memoryStack = $stack;

        return $old;
    }

    /**
     * Get the method memory stack.
     * Please, see the _dynamic and _static constants.
     *
     * @access  public
     * @return  int
     */
    public function getMemoryStack ( ) {

        return $this->_memoryStack;
    }

    /**
     * Check if the method is dynamic or not.
     *
     * @access  public
     * @return  bool
     */
    public function isDynamic ( ) {

        return _dynamic == $this->getMemoryStack();
    }

    /**
     * Check if the method is static or not.
     *
     * @access  public
     * @return  bool
     */
    public function isStatic ( ) {

        return _static == $this->getMemoryStack();
    }

    /**
     * Set the default value.
     *
     * @access  public
     * @param   string  $filename    Filename where the property is.
     * @return  void
     */
    public function _setDefaultValue ( $filename ) {

        if(false === $this->isDefault())
            return;

        $file  = file_get_contents($filename);
        $regex = '#(';

        if(true === $this->isStatic())
            $regex .= 'static|';

        if(true === $this->isPublic())
            $regex .= 'public';
        elseif(true === $this->isProtected())
            $regex .= 'protected';
        elseif(true === $this->isPrivate())
            $regex .= 'private';

        $regex .= ')\s*\$' . $this->getName() . '\s*=\s*([^;]+);#m';

        preg_match($regex, $file, $matches);

        $this->_defaultValue = $matches[2];

        return;
    }

    /**
     * Get the default value as string only.
     *
     * @access  public
     * @return  string
     */
    public function getDefaultValue ( ) {

        return $this->_defaultValue;
    }

    /**
     * Accept a visitor.
     *
     * @access  public
     * @param   \Hoa\Visitor\Visit  $visitor    Visitor.
     * @param   mixed              &$handle    Handle (reference).
     * @param   mixed              $eldnah     Handle (no reference).
     * @return  mixed
     */
    public function accept ( \Hoa\Visitor\Visit $visitor,
                             &$handle = null, $eldnah = null ) {

        return $visitor->visit($this, $handle, $eldnah);
    }
}

}
