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
 * \Hoa\Model\Exception
 */
-> import('Model.Exception')

/**
 * \Hoa\Test\Praspel\Compiler
 */
-> import('Test.Praspel.Compiler', true);

}

namespace Hoa\Model {

/**
 * Class \Hoa\Model\Exception.
 *
 * Extending the \Hoa\Core\Exception class.
 *
 * @author     Ivan Enderlin <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright © 2007-2011 Ivan Enderlin.
 * @license    New BSD License
 */

class Model {

    protected $_validation = true;



    public function __construct ( ) {

        return;
    }

    public function __set ( $name, $value) {

        $_name = '_' . $name;

        if(!isset($this->$_name))
            return null;

        if(false === $this->isValidationEnabled()) {

            $old          = $this->$_name;
            $this->$_name = $value;

            return $old;
        }

        $class     = new \ReflectionClass($this);
        $attribute = $class->getProperty($_name);
        $comment   = $attribute->getDocComment();
        $comment   = preg_replace('#^(\s*/\*\*\s*)#', '', $comment);
        $comment   = preg_replace('#(\s*\*/)#',       '', $comment);
        $comment   = preg_replace('#^(\s*\*\s*)#m',   '', $comment);
        $verdict   = praspel($comment)
                         ->getClause('invariant')
                         ->getVariable($name)
                         ->predicate($value);

        if(false === $verdict)
            throw new Exception(
                'Try to set the %s attribute with an invalid data.', 0, $name);

        $Name = 'validate' . ucfirst(preg_replace_callback(
            '#_(.)#',
            function ( Array $matches ) {

                return ucfirst($matches[1]);
            },
            strtolower($name)
        ));

        if(   method_exists($this, $Name)
           && false === $this->$Name($value))
            throw new Exception(
                'Try to set the %s attribute with an invalid data.',
                1, $name);

        $old          = $this->$_name;
        $this->$_name = $value;

        return;
    }

    public function __get ( $name ) {

        $_name = '_' . $name;

        if(!isset($this->$_name))
            return null;

        return $this->$_name;
    }

    public function setEnableValidation ( $enable ) {

        $old               = $this->_validation;
        $this->_validation = $enable;

        return $old;
    }

    public function isValidationEnabled ( ) {

        return $this->_validation;
    }
}

}
