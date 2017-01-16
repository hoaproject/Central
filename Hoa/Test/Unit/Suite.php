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

namespace Hoa\Test\Unit;

use atoum;
use Hoa\Protocol;
use Hoa\Test;

/**
 * Class \Hoa\Test\Unit\Suite.
 *
 * Represent a unit test suite.
 *
 * @copyright  Copyright © 2007-2017 Hoa community
 * @license    New BSD License
 */
class Suite extends atoum\test
{
    const defaultNamespace = '/\\\Test\\\Unit\\\/';



    public function __construct()
    {
        $this->setMethodPrefix('/^case_/');
        parent::__construct();

        $protocol                = Protocol::getInstance();
        $protocol['Test']        = new Protocol\Node('Test', null);
        $protocol['Test']['Vfs'] = new Test\Protocol\Vfs();

        return;
    }

    public function getTestedClassName()
    {
        if ($this instanceof Test\Decorrelated) {
            return 'StdClass';
        }

        return parent::getTestedClassName();
    }

    public function getTestedClassNamespace()
    {
        if ($this instanceof Test\Decorrelated) {
            return '\\';
        }

        return parent::getTestedClassNamespace();
    }

    public function beforeTestMethod($methodName)
    {
        $out             = parent::beforeTestMethod($methodName);
        $testedClassName = self::getTestedClassNameFromTestClass(
            $this->getClass(),
            $this->getTestNamespace()
        );
        $testedNamespace = substr(
            $testedClassName,
            0,
            strrpos($testedClassName, '\\')
        );

        $this->getPhpFunctionMocker()->setDefaultNamespace($testedNamespace);
        $this->getPhpConstantMocker()->setDefaultNamespace($testedNamespace);

        return $out;
    }
}
