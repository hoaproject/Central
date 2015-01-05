<?php

/**
 * Hoa
 *
 *
 * @license
 *
 * New BSD License
 *
 * Copyright © 2007-2015, Ivan Enderlin. All rights reserved.
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
 * \Hoa\Realdom
 */
-> import('Realdom.~')

/**
 * \Hoa\Realdom\String
 */
-> import('Realdom.String')

/**
 * \Hoa\Compiler\Llk
 */
-> import('Compiler.Llk.~')

/**
 * \Hoa\Regex\Visitor\Isotropic
 */
-> import('Regex.Visitor.Isotropic')

/**
 * \Hoa\File\Read
 */
-> import('File.Read');

}

namespace Hoa\Realdom {

/**
 * Class \Hoa\Realdom\Grammar.
 *
 * Realistic domain: grammar.
 *
 * @author     Ivan Enderlin <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright © 2007-2015 Ivan Enderlin.
 * @license    New BSD License
 */

class Grammar extends String {

    /**
     * Realistic domain name.
     *
     * @const string
     */
    const NAME = 'grammar';

    /**
     * Realistic domain defined arguments.
     *
     * @var \Hoa\Realdom array
     */
    protected $_arguments       = array(
        'Conststring grammar'
    );

    /**
     * Grammar compiler.
     *
     * @var \Hoa\Compiler\Llk object
     */
    protected $_compiler        = null;

    /**
     * Sampler.
     *
     * @var \Hoa\Compiler\Llk\Sampler object
     */
    protected $_compilerSampler = null;



    /**
     * Construct a realistic domain.
     *
     * @access  protected
     * @return  void
     */
    protected function construct ( ) {

        $this->_compiler = \Hoa\Compiler\Llk::load(
            new \Hoa\File\Read($this['grammar']->getConstantValue())
        );

        return;
    }

    /**
     * Predicate whether the sampled value belongs to the realistic domains.
     *
     * @access  protected
     * @param   mixed  $q    Sampled value.
     * @return  boolean
     */
    protected function _predicate ( $q ) {

        // How to handle size (because the unit of size is token, not
        // character)?

        try {

            $this->_compiler->parse($q, null, false);
        }
        catch ( \Exception $e ) {

            return false;
        }

        return true;
    }

    /**
     * Sample one new value.
     *
     * @access  protected
     * @param   \Hoa\Math\Sampler  $sampler    Sampler.
     * @return  mixed
     */
    protected function _sample ( \Hoa\Math\Sampler $sampler ) {

        static $_values = array();

        if(null === $this->_compilerSampler)
            $this->_compilerSampler = new \Hoa\Compiler\Llk\Sampler\Coverage(
                $this->_compiler,
                new \Hoa\Regex\Visitor\Isotropic($sampler)
            );

        if(empty($_values)) {

            $_values = iterator_to_array($this->_compilerSampler);
            shuffle($_values);
        }

        return array_shift($_values);
    }
}

}
