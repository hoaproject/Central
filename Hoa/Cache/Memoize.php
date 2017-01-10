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

namespace Hoa\Cache;

/**
 * Class \Hoa\Cache\Memoize.
 *
 * Memoization is useful when making dynamic programming for example because it
 * avoid to compute a function if it was previously computed with the same
 * arguments. A symptomatic example is the Fibonacci number which one can
 * naively implement as follows:
 *
 *     function fib ( $x ) {
 *
 *         if($x < 2)
 *             return 1;
 *
 *         return fib($x - 1) + fib($x - 2);
 *     }
 *
 *     var_dump(fib(30)); // int(1346269)
 *
 * A first and trivial optimization could be:
 *
 *     $fib = memoize('fib');
 *     var_dump($fib(30));
 *
 * But here, we only memoize fib(30) and not fib(29), fib(28) etc. A universal
 * transformation is then proposed with a closure:
 *
 *     function fib ( $x ) {
 *
 *         $self = null;
 *         $m    = function ( $x ) use ( &$self ) {
 *
 *             if($x < 2)
 *                 return 1
 *
 *             return $self($x - 1) + $self($x - 2);
 *         }
 *         $self = memoize($m);
 *
 *         return $self($x);
 *     }
 *
 *     var_dump(fib(30));
 *
 * It's far better because we are making real dynamic programming.
 * But we could do better from a strict PHP point of view with indirect
 * recursion:
 *
 *     function fib ( $x ) {
 *
 *         $_fib = memoize('_fib');
 *
 *         return $_fib($x);
 *     }
 *
 *     function _fib ( $x ) {
 *
 *         if($x < 2)
 *             return 1;
 *
 *         $fib = memoize('fib');
 *
 *         return $fib($x - 1) + $fib($x - 2);
 *     }
 *
 *     var_dump(fib(30));
 *
 * With an old machin, the last implementation is 350,000 times faster than the
 * first implementation. Try to compute fib(1024) with the two last
 * implementations, it's fast and inconceivable with the first one.
 *
 * @copyright  Copyright © 2007-2017 Hoa community
 * @license    New BSD License
 */
class Memoize
{
    /**
     * Multiton (indexed by callable's hash).
     *
     * @var array
     */
    private static $_multiton = [];

    /**
     * Callable.
     *
     * @var \Hoa\Consistency\Xcallable
     */
    protected $_callable      = null;

    /**
     * All callable arguments (md5 serialize).
     *
     * @var array
     */
    protected $_arguments     = [];



    /**
     * Singleton.
     *
     */
    private function __construct()
    {
        return;
    }

    /**
     * Get a memoization (multiton).
     *
     * @param   mixed   $callable    Callable.
     * @return  \Hoa\Cache\Memoize
     */
    public static function getInstance($callable)
    {
        $callable = xcallable($callable);
        $hash     = $callable->getHash();

        if (!isset(self::$_multiton[$hash])) {
            self::$_multiton[$hash]            = new static();
            self::$_multiton[$hash]->_callable = $callable;
        }

        return self::$_multiton[$hash];
    }

    /**
     * Memoization algorithm.
     *
     * @param   ...  ...    Arguments.
     * @return  mixed
     */
    public function __invoke()
    {
        $arguments = func_get_args();
        $id        = md5(serialize($arguments));

        if (!isset($this->_arguments[$id])) {
            $this->_arguments[$id] = $this->_callable->distributeArguments(
                $arguments
            );
        }

        return $this->_arguments[$id];
    }
}

/**
 * Alias of \Hoa\Cache\Memoize::getInstance().
 *
 * @param   mixed   $callable    Callable.
 * @return  \Hoa\Cache\Memoize
 */
if (!function_exists('memoize')) {
    function memoize($callable)
    {
        return Memoize::getInstance($callable);
    }
}
