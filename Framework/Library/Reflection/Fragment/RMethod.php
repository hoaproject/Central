<?php

/**
 * Hoa
 *
 *
 * @license
 *
 * New BSD License
 *
 * Copyright (c) 2007-2011, Ivan Enderlin. All rights reserved.
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
 * \Hoa\Reflection\RFunction\RMethod
 */
-> import('Reflection.RFunction.RMethod');

}

namespace Hoa\Reflection\Fragment {

/**
 * Class \Hoa\Reflection\Fragment\RMethod.
 *
 * Fragment of a \Hoa\Reflection\RFunction\RMethod class.
 *
 * @author     Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright (c) 2007, 2011 Ivan ENDERLIN.
 * @license    New BSD License
 */

class RMethod extends \Hoa\Reflection\RFunction\RMethod {

    /**
     * Reflect a fragment of method.
     *
     * @access  public
     * @param   string  $name    Method name.
     * @return  void
     */
    public function __construct ( $name ) {

        $this->setName($name);
        $this->_firstP = false;

        return;
    }

    /**
     * Get the method body.
     *
     * @access  public
     * @return  string
     */
    public function getBody ( ) {

        return $this->_body;
    }

    /**
     * Override the ReflectionMethod method.
     *
     * @access  public
     * @return  int
     */
    public function getStartLine ( ) {

        return -1;
    }

    /**
     * Override the ReflectionMethod method.
     *
     * @access  public
     * @return  int
     */
    public function getEndLine ( ) {

        return -1;
    }

    /**
     * Override the ReflectionMethod method.
     *
     * @access  public
     * @return  int
     */
    public function getFileName ( ) {

        return null;
    }
}

}
