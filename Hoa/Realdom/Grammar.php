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

namespace Hoa\Realdom;

use Hoa\Compiler;
use Hoa\File;
use Hoa\Math;
use Hoa\Regex;

/**
 * Class \Hoa\Realdom\Grammar.
 *
 * Realistic domain: grammar.
 *
 * @copyright  Copyright © 2007-2017 Hoa community
 * @license    New BSD License
 */
class Grammar extends RealdomString
{
    /**
     * Realistic domain name.
     *
     * @const string
     */
    const NAME = 'grammar';

    /**
     * Realistic domain defined arguments.
     *
     * @var array
     */
    protected $_arguments       = [
        'Conststring grammar'
    ];

    /**
     * Grammar compiler.
     *
     * @var \Hoa\Compiler\Llk
     */
    protected $_compiler        = null;

    /**
     * Sampler.
     *
     * @var \Hoa\Compiler\Llk\Sampler
     */
    protected $_compilerSampler = null;



    /**
     * Construct a realistic domain.
     *
     * @return  void
     */
    protected function construct()
    {
        $this->_compiler = Compiler\Llk::load(
            new File\Read($this['grammar']->getConstantValue())
        );

        return;
    }

    /**
     * Predicate whether the sampled value belongs to the realistic domains.
     *
     * @param   mixed  $q    Sampled value.
     * @return  boolean
     */
    protected function _predicate($q)
    {
        // How to handle size (because the unit of size is token, not
        // character)?

        try {
            $this->_compiler->parse($q, null, false);
        } catch (\Exception $e) {
            return false;
        }

        return true;
    }

    /**
     * Sample one new value.
     *
     * @param   \Hoa\Math\Sampler  $sampler    Sampler.
     * @return  mixed
     */
    protected function _sample(Math\Sampler $sampler)
    {
        static $_values = [];

        if (null === $this->_compilerSampler) {
            $this->_compilerSampler = new Compiler\Llk\Sampler\Coverage(
                $this->_compiler,
                new Regex\Visitor\Isotropic($sampler)
            );
        }

        if (empty($_values)) {
            $_values = iterator_to_array($this->_compilerSampler);
            shuffle($_values);
        }

        return array_shift($_values);
    }
}
