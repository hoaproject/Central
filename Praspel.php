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

namespace Hoa\Praspel {

use Hoa\Compiler;
use Hoa\Consistency;
use Hoa\File;

/**
 * Class \Hoa\Praspel\Praspel.
 *
 * Take a specification + data and validate/verify a callable.
 *
 * @copyright  Copyright © 2007-2017 Hoa community
 * @license    New BSD License
 */
class Praspel
{
    /**
     * Registry of all contracts.
     *
     * @var \ArrayObject
     */
    protected static $_registry = null;



    /**
     * Short interpreter.
     *
     * @param   string  $praspel        Praspel.
     * @param   string  $bindToClass    Classname to bind.
     * @return  \Hoa\Praspel\Model\Clause
     */
    public static function interpret($praspel, $bindToClass = null)
    {
        static $_compiler    = null;
        static $_interpreter = null;

        if (null === $_compiler) {
            $_compiler = Compiler\Llk::load(
                new File\Read('hoa://Library/Praspel/Grammar.pp')
            );
        }

        if (null === $_interpreter) {
            $_interpreter = new Visitor\Interpreter();
        }

        $ast = $_compiler->parse($praspel);

        if (null !== $bindToClass) {
            $_interpreter->bindToClass($bindToClass);
        }

        return $_interpreter->visit($ast);
    }

    /**
     * Extract Praspel (as a string) from a comment.
     *
     * @param   string  $comment    Comment.
     * @return  string
     */
    public static function extractFromComment($comment)
    {
        $i = preg_match('#/\*(.*?)\*/#s', $comment, $matches);

        if (0 === $i) {
            return '';
        }

        $i = preg_match_all('#^[\s\*]*\s*\*\s?([^\n]*)$#m', $matches[1], $maatches);

        if (0 === $i) {
            return '';
        }

        return trim(implode("\n", $maatches[1]));
    }

    /**
     * Get registry of all contracts.
     *
     * @return  \ArrayObject
     */
    public static function getRegistry()
    {
        if (null === static::$_registry) {
            static::$_registry = new \ArrayObject();
        }

        return static::$_registry;
    }
}

/**
 * Flex entity.
 */
Consistency::flexEntity('Hoa\Praspel\Praspel');

}

namespace {

/**
 * Alias of \Hoa\Praspel::interpret().
 *
 * @param   string  $praspel    Praspel
 * @return  \Hoa\Praspel\Model\Clause
 */
if (!function_exists('praspel')) {
    function praspel($praspel)
    {
        return Hoa\Praspel::interpret($praspel);
    }
}

}
