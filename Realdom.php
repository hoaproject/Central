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

use Hoa\Consistency;
use Hoa\Math;
use Hoa\Praspel;
use Hoa\Visitor;

/**
 * Class \Hoa\Realdom.
 *
 * Abstract-top-super realistic domain :-).
 *
 * @copyright  Copyright © 2007-2017 Hoa community
 * @license    New BSD License
 */
abstract class Realdom
    implements \ArrayAccess,
               \Countable,
               Visitor\Element
{
    /**
     * Realistic domain name.
     *
     * @const string
     */
    const NAME = '(null)';

    /**
     * Realistic domain defined arguments.
     *
     * @var array
     */
    protected $_arguments                    = null;

    /**
     * Realistic domain arguments.
     *
     * @var array
     */
    protected $arguments                     = null;

    /**
     * Default sampler.
     *
     * @var \Hoa\Math\Sampler
     */
    protected static $_sampler               = null;

    /**
     * Current sampler.
     *
     * @var \Hoa\Math\Sampler
     */
    protected $__sampler                     = null;

    /**
     * Sampled value.
     *
     * @var mixed
     */
    protected $_value                        = null;

    /**
     * Number of max try when sampling a new value.
     *
     * @var int
     */
    protected static $_maxtry                = 64;

    /**
     * Constraints.
     *
     * @var array
     */
    protected $_constraints                  = null;

    /**
     * Holder.
     *
     * @var \Hoa\Realdom\IRealdom\Holder
     */
    protected $_holder                       = null;

    /**
     * Whether the realdom has been constructed or not.
     *
     * @var bool
     */
    protected $_constructed                  = false;

    /**
     * Default Praspel visitor.
     *
     * @var \Hoa\Visitor\Visit
     */
    protected static $_defaultPraspelVisitor = null;

    /**
     * Praspel visitor.
     *
     * @var \Hoa\Visitor\Visit
     */
    protected $_praspelVisitor               = null;



    /**
     * Build a realistic domain.
     *
     * @throws  \Hoa\Realdom\Exception\IllegalArgument
     * @throws  \Hoa\Realdom\Exception\MissingArgument
     */
    final public function __construct()
    {
        switch ($this->_arguments) {
            case null:
                $this->arguments = [];

                break;

            case …:
                $this->arguments = func_get_args();
                self::autoBoxing($this->arguments, $this);

                break;

            default:
                $arguments       = func_get_args();
                $hints           = [];
                $this->arguments = [];
                $i               = 0;

                reset($this->_arguments);

                foreach ($arguments as $argument) {
                    $name = key($this->_arguments);
                    $hint = null;

                    if (… === $argument) {
                        if (is_int($name)) {
                            throw new Exception\IllegalArgument(
                                'Argument %s passed to %s() does not have a ' .
                                'default value.',
                                0,
                                [$i + 1, $this->getName()]
                            );
                        }

                        $argument = current($this->_arguments);
                    }

                    if (is_int($name)) {
                        $name = current($this->_arguments);
                        ++$i;
                    }

                    if (false !== $pos = strrpos($name, ' ')) {
                        $hint = trim(substr($name, 0, $pos));
                        $name = substr($name, $pos + 1);
                    }

                    $this->arguments[$name] = $argument;
                    $hints[]                = $hint;
                    next($this->_arguments);
                }

                while (list($name, $default) = each($this->_arguments)) {
                    if (is_int($name)) {
                        $j = 0;
                        array_walk(
                            $this->_arguments,
                            function ($_, $key) use (&$j) {
                                $j += is_int($key);

                                return;
                            }
                        );

                        throw new Exception\MissingArgument(
                            1 < $j
                                ? '%s() expects at least %d parameters, %d given.'
                                : '%s() expects at least %d parameter, %d given.',
                            1,
                            [$this->getName(), $j, $i]
                        );
                    }

                    $hint = null;

                    if (false !== $pos = strrpos($name, ' ')) {
                        $hint = trim(substr($name, 0, $pos));
                        $name = substr($name, $pos + 1);
                    }

                    $this->arguments[$name] = $default;
                    $hints[]                = $hint;
                }

                self::autoBoxing($this->arguments, $this);

                foreach ($this->arguments as &$argument) {
                    $hint = current($hints);

                    if (null === $hint) {
                        next($hints);

                        continue;
                    }

                    if ($argument instanceof IRealdom\Holder) {
                        $_realdoms = $argument->getHeld();
                    } elseif ($argument instanceof IRealdom\Crate) {
                        $_realdoms = $argument->getTypes();
                    } elseif ($argument instanceof Disjunction) {
                        $_realdoms = $argument->getRealdoms();
                    } else {
                        $_realdoms = [$argument];
                    }

                    $k = 0;

                    $_hints = explode('|', $hint);

                    foreach ($_hints as &$_hint) {
                        if ('\\' !== $_hint[0]) {
                            $_hint = __NAMESPACE__ . '\\' . $_hint;
                        }
                    }

                    foreach ($_realdoms as $_realdom) {
                        $flag = false;

                        foreach ($_hints as $__hint) {
                            if ($_realdom instanceof $__hint ||
                                $_realdom === $__hint) {
                                $flag = true;

                                break;
                            }
                        }

                        if (false === $flag) {
                            unset($_realdoms[$k]);
                        } else {
                            ++$k;
                        }
                    }

                    switch (count($_realdoms)) {
                        case 0:
                            if ($argument instanceof IRealdom\Holder) {
                                throw new Exception\IllegalArgument(
                                    'Argument %d passed to %s() must be of ' .
                                    'type %s, variable %s does not satisfy ' .
                                    'this constraint.',
                                    2,
                                    [
                                        key($hints),
                                        $this->getName(),
                                        mb_strtolower($hint),
                                        $argument->getName()
                                    ]
                                );
                            }

                            throw new Exception\IllegalArgument(
                                'Argument %d passed to %s() must be of type ' .
                                '%s, %s given.',
                                3,
                                [
                                    key($hints),
                                    $this->getName(),
                                    mb_strtolower($hint),
                                    mb_strtolower(mb_substr(
                                        $_ = get_class($argument),
                                        mb_strrpos($_, '\\') + 1
                                    ))
                                ]
                            );

                            break;

                        case 1:
                            if ($argument instanceof IRealdom\Crate) {
                                break;
                            }

                            $argument = $_realdoms[0];

                            break;

                        default:
                            throw new Exception\IllegalArgument(
                                'Variable %s, passed as argument %d of %s(), ' .
                                'has to many domains.',
                                4,
                                [
                                    $argument->getName(),
                                    key($hints),
                                    $this->getName()
                                ]
                            );
                    }

                    next($hints);
                }
        }

        return;
    }

    /**
     * Constructor of the realistic domain.
     *
     * @return  void
     */
    protected function construct()
    {
        return;
    }

    /**
     * Auto-boxing.
     *
     * @param   array         &$arguments    Arguments.
     * @param   \Hoa\Realdom  $self          Self (if we auto-box arguments).
     * @return  void
     */
    public static function autoBoxing(array &$arguments, Realdom $self = null)
    {
        if (is_object($self) &&
            (get_class($self) === 'Hoa\Realdom\Constarray' ||
             get_class($self) === 'Hoa\Realdom\Constboolean' ||
             get_class($self) === 'Hoa\Realdom\Constfloat' ||
             get_class($self) === 'Hoa\Realdom\Constinteger' ||
             get_class($self) === 'Hoa\Realdom\Constnull' ||
             get_class($self) === 'Hoa\Realdom\Conststring')) {
            return;
        }

        foreach ($arguments as &$argument) {
            switch (gettype($argument)) {
                case 'array':
                    $handle = [];

                    foreach ($argument as &$pair) {
                        $handle[] = &$pair[0];

                        if (isset($pair[1])) {
                            $handle[] = &$pair[1];
                        }
                    }

                    self::autoBoxing($handle);

                    $argument = new Constarray($argument);

                    break;

                case 'boolean':
                    $argument = new Constboolean($argument);

                    break;

                case 'double':
                    $argument = new Constfloat($argument);

                    break;

                case 'integer':
                    $argument = new Constinteger($argument);

                    break;

                case 'NULL':
                    $argument = new Constnull($argument);

                    break;

                case 'string':
                    $argument = new Conststring($argument);

                    break;

                default:
                    if ($argument instanceof IRealdom\Holder) {
                        $argument = $argument->getHeld();

                        if (null !== $self) {
                            $argument = $argument[0];
                        }

                        break;
                    }
            }
        }

        return;
    }

    /**
     * Check if an argument exists.
     *
     * @param   string  $offset    Attribute name.
     * @return  bool
     */
    public function offsetExists($offset)
    {
        return
            isset($this->arguments[$offset]) &&
            !($this->arguments[$offset] instanceof Constnull);
    }

    /**
     * Get an argument value.
     *
     * @param   string  $offset    Attribute name.
     * @return  mixed
     */
    public function offsetGet($offset)
    {
        return
            true === $this->offsetExists($offset)
                ? $this->arguments[$offset]
                : null;
    }

    /**
     * Set an argument value.
     *
     * @param   string  $offset    Attribute name.
     * @param   mixed   $value     Attribute value.
     * @return  mixed
     */
    public function offsetSet($offset, $value)
    {
        $old                      = $this->offsetGet($offset);
        $this->arguments[$offset] = $value;

        return $old;
    }

    /**
     * Unset an argument.
     *
     * @param   string  $offset    Attribute name.
     * @return  void
     */
    public function offsetUnset($offset)
    {
        unset($this->arguments[$offset]);

        return;
    }

    /**
     * Get all arguments values.
     *
     * @return  array
     */
    public function getArguments()
    {
        return $this->arguments;
    }

    /**
     * Get arity.
     *
     * @return  int
     */
    public function count()
    {
        return count($this->arguments);
    }

    /**
     * Get the realistic domain name.
     *
     * @return  string
     */
    public function getName()
    {
        return static::NAME;
    }

    /**
     * Set the default sampler.
     *
     * @param   \Hoa\Math\Sampler  $sampler    Sampler.
     * @return  \Hoa\Math\Sampler
     */
    public static function setDefaultSampler(Math\Sampler $sampler)
    {
        $old              = static::$_sampler;
        static::$_sampler = $sampler;

        return $old;
    }

    /**
     * Get the default sampler.
     *
     * @return  \Hoa\Math\Sampler
     */
    public static function getDefaultSampler()
    {
        return static::$_sampler;
    }

    /**
     * Set the sampler.
     *
     * @param   \Hoa\Math\Sampler  $sampler    Sampler.
     * @return  \Hoa\Math\Sampler
     */
    public function setSampler(Math\Sampler $sampler)
    {
        $old             = $this->__sampler;
        $this->__sampler = $sampler;

        return $old;
    }

    /**
     * Get the sampler.
     *
     * @return  \Hoa\Math\Sampler
     */
    public function getSampler()
    {
        return $this->__sampler ?: static::$_sampler;
    }

    /**
     * Set the sampled value.
     *
     * @param   mixed      $sampled    Sampled value.
     * @return  mixed
     */
    protected function setValue($sampled)
    {
        $old          = $this->_value;
        $this->_value = $sampled;

        return $old;
    }

    /**
     * Get the sampled value.
     *
     * @return  mixed
     */
    public function getValue()
    {
        return $this->_value;
    }

    /**
     * Set the max try number.
     *
     * @param   int     $maxtry    Max try authorized.
     * @return  int
     */
    public static function setMaxTry($maxtry)
    {
        $old           = self::$_maxtry;
        self::$_maxtry = $maxtry;

        return $old;
    }

    /**
     * Get the max try number.
     *
     * @return  int
     */
    public static function getMaxTry()
    {
        return self::$_maxtry;
    }

    /**
     * Reset the realistic domain.
     *
     * @return  void
     */
    public function reset()
    {
        if (false === $this->_constructed) {
            $this->construct();
            $this->_constructed = true;
        }

        if ($this instanceof IRealdom\Nonconvex &&
            isset($this->_discredited)) {
            $this->_discredited = [];
        }

        return;
    }

    /**
     * Helper to reset all arguments.
     *
     * @return  void
     */
    protected function resetArguments()
    {
        foreach ($this->getArguments() as $name => $argument) {
            if ($argument instanceof self) {
                $argument->reset();
            }
        }

        return;
    }

    /**
     * Predicate whether the sampled value belongs to the realistic domains.
     *
     * @param   mixed  $q    Sampled value.
     * @return  boolean
     */
    public function predicate($q)
    {
        if (false === $this->_constructed) {
            $this->construct();
            $this->_constructed = true;
        }

        return $this->_predicate($q);
    }

    /**
     * Predicate whether the sampled value belongs to the realistic domains.
     *
     * @param   mixed  $q    Sampled value.
     * @return  boolean
     */
    abstract protected function _predicate($q);

    /**
     * Sample a new value.
     *
     * @param   \Hoa\Math\Sampler  $sampler    Sampler.
     * @return  mixed
     * @throws  \Hoa\Realdom\Exception
     */
    public function sample(Math\Sampler $sampler = null)
    {
        if (false === $this->_constructed) {
            $this->construct();
            $this->_constructed = true;
        }

        if (null === $sampler &&
            null === $sampler = $this->getSampler()) {
            throw new Exception(
                'No sampler set. Please, use the %s::setDefaultSampler() or ' .
                '%1$s::setSampler() method.',
                4,
                __CLASS__
            );
        }

        $maxtry = $this->getMaxTry();

        do {
            $sampled   = $this->_sample($sampler);
            $predicate = $this->predicate($sampled);

            if (false === $predicate) {
                $this->reset();
            }
        } while (false === $predicate && 0 < --$maxtry);

        if (0 >= $maxtry) {
            throw new Exception(
                'Cannot sample a value, all tries failed (%d tries) from %s.',
                5,
                [$this->getMaxTry(), $this->getName()]
            );
        }

        $this->setValue($sampled);

        return $sampled;
    }

    /**
     * Sample one new value.
     *
     * @param   \Hoa\Math\Sampler  $sampler    Sampler.
     * @return  mixed
     */
    abstract protected function _sample(Math\Sampler $sampler);

    /**
     * Check if the realistic domain intersects with another.
     *
     * @param   \Hoa\Realdom  $realdom    Realistic domain.
     * @return  bool
     */
    public function intersectWith(Realdom $realdom)
    {
        return false;
    }

    /**
     * Set constraints.
     * Please, see Hoa\Praspel.
     *
     * @param   array  &$constraints    Contraints.
     * @return  \Hoa\Realdom
     */
    public function setConstraints(array &$constraints)
    {
        $this->_constraints = &$constraints;

        return $this;
    }

    /**
     * Get constraints.
     *
     * @return  array
     */
    protected function &getConstraints()
    {
        return $this->_constraints;
    }

    /**
     * Propagate constraints (public).
     *
     * @param   string  $type     Type.
     * @param   int     $index    Index.
     * @return  void
     */
    public function propagateConstraints($type, $index)
    {
        $this->_propagateConstraints($type, $index, $this->getConstraints());

        return;
    }

    /**
     * Propagate constraints.
     *
     * @param   string  $type           Type.
     * @param   int     $index          Index.
     * @param   array   $constraints    Constraints.
     * @return  void
     * @throws  \Hoa\Realdom\Exception\Inconsistent
     */
    protected function _propagateConstraints(
        $type,
        $index,
        array &$constraints
    ) {
        return;
    }

    /**
     * Test the “is” constraint.
     *
     * @param   string  $qualifier    Qualifier.
     * @return  bool
     */
    public function is($qualifier)
    {
        if (!isset($this->_constraints['is'])) {
            return false;
        }

        return in_array($qualifier, $this->_constraints['is']);
    }

    /**
     * Set holder.
     *
     * @param   \Hoa\Realdom\IRealdom\Holder  $holder    Holder.
     * @return  \Hoa\Realdom\IRealdom\Holder
     */
    public function setHolder(IRealdom\Holder $holder)
    {
        $old           = $holder;
        $this->_holder = $holder;

        return $old;
    }

    /**
     * Get holder.
     *
     * @return  \Hoa\Realdom\IRealdom\Holder
     */
    public function getHolder()
    {
        return $this->_holder;
    }

    /**
     * Set default Praspel visitor.
     *
     * @param   \Hoa\Visitor\Visit  $visitor    Visitor.
     * @return  \Hoa\Visitor\Visit
     */
    public static function setDefaultPraspelVisitor(Visitor\Visit $visitor)
    {
        $old                            = static::$_defaultPraspelVisitor;
        static::$_defaultPraspelVisitor = $visitor;

        return $old;
    }

    /**
     * Get default Praspel visitor.
     * If default visitor is null, Hoa\Praspel\Visitor\Praspel will be used.
     *
     * @param   \Hoa\Visitor\Visit  $visitor    Visitor.
     * @return  \Hoa\Visitor\Visit
     */
    public static function getDefaultPraspelVisitor()
    {
        if (null === static::$_defaultPraspelVisitor) {
            static::$_defaultPraspelVisitor = new Praspel\Visitor\Praspel();
        }

        return static::$_defaultPraspelVisitor;
    }

    /**
     * Set Praspel visitor.
     *
     * @param   \Hoa\Visitor\Visit  $visitor    Visitor.
     * @return  \Hoa\Visitor\Visit
     */
    public function setPraspelVisitor(Visitor\Visit $visitor)
    {
        $old                   = $this->_praspelVisitor;
        $this->_praspelVisitor = $visitor;

        return $old;
    }

    /**
     * Get Praspel visitor.
     *
     * @return  \Hoa\Visitor\Visit
     */
    public function getPraspelVisitor()
    {
        return $this->_praspelVisitor ?: static::getDefaultPraspelVisitor();
    }

    /**
     * Accept a visitor.
     *
     * @param   \Hoa\Visitor\Visit  $visitor    Visitor.
     * @param   mixed               &$handle    Handle (reference).
     * @param   mixed               $eldnah     Handle (no reference).
     * @return  mixed
     */
    public function accept(
        Visitor\Visit $visitor,
        &$handle = null,
        $eldnah = null
    ) {
        return $visitor->visit($this, $handle, $eldnah);
    }
}

/**
 * Flex entity.
 */
Consistency::flexEntity('Hoa\Realdom\Realdom');
