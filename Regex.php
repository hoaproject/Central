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
use Hoa\Regex as HoaRegex;

/**
 * Class \Hoa\Realdom\Regex.
 *
 * Realistic domain: regex.
 *
 * @copyright  Copyright © 2007-2017 Hoa community
 * @license    New BSD License
 */
class Regex extends Realdom
{
    /**
     * Realistic domain name.
     *
     * @const string
     */
    const NAME = 'regex';

    /**
     * Realistic domain defined arguments.
     *
     * @var array
     */
    protected $_arguments       = [
        'Conststring regex'
    ];

    /**
     * Regex compiler.
     *
     * @var \Hoa\Compiler\Llk
     */
    protected static $_compiler = null;

    /**
     * Regex visitor that use realdom.
     *
     * @var \Hoa\Regex\Visitor\Isotropic
     */
    protected static $_visitor  = null;

    /**
     * AST.
     *
     * @var \Hoa\Compiler\TreeNode
     */
    protected $_ast             = null;



    /**
     * Construct a realistic domain.
     *
     * @return  void
     */
    protected function construct()
    {
        if (null === self::$_compiler) {
            self::$_compiler = Compiler\Llk::load(
                new File\Read('hoa://Library/Regex/Grammar.pp')
            );
        }

        if (!isset($this['regex'])) {
            $this['regex'] = new Conststring('');
        }

        $this->_ast = self::$_compiler->parse(
            mb_substr(
                $regex = $this['regex']->getConstantValue(),
                1,
                mb_strrpos($regex, mb_substr($regex, 0, 1), 1) - 1
            )
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
        return 0 !== preg_match($this['regex']->getConstantValue(), $q);
    }

    /**
     * Sample one new value.
     *
     * @param   \Hoa\Math\Sampler  $sampler    Sampler.
     * @return  mixed
     */
    protected function _sample(Math\Sampler $sampler)
    {
        if (null === self::$_visitor) {
            self::$_visitor = new HoaRegex\Visitor\Isotropic($sampler);
        }

        return self::$_visitor->visit($this->_ast);
    }
}
