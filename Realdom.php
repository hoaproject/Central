<?php

/**
 * Hoa
 *
 *
 * @license
 *
 * New BSD License
 *
 * Copyright © 2007-2013, Ivan Enderlin. All rights reserved.
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
 * \Hoa\Realdom\Exception
 */
-> import('Realdom.Exception.~')

/**
 * \Hoa\Realdom\Exception\IllegalArgument
 */
-> import('Realdom.Exception.IllegalArgument')

/**
 * \Hoa\Realdom\Exception\MissingArgument
 */
-> import('Realdom.Exception.MissingArgument')

/**
 * \Hoa\Realdom\Disjunction
 */
-> import('Realdom.Disjunction', true)

/**
 * \Hoa\Realdom\Constarray
 */
-> import('Realdom.Constarray')

/**
 * \Hoa\Realdom\Constboolean
 */
-> import('Realdom.Constboolean')

/**
 * \Hoa\Realdom\Constfloat
 */
-> import('Realdom.Constfloat')

/**
 * \Hoa\Realdom\Constinteger
 */
-> import('Realdom.Constinteger')

/**
 * \Hoa\Realdom\Constnull
 */
-> import('Realdom.Constnull')

/**
 * \Hoa\Realdom\Conststring
 */
-> import('Realdom.Conststring')

/**
 * \Hoa\Math\Sampler
 */
-> import('Math.Sampler.~');

}

namespace Hoa\Realdom {

/**
 * Class \Hoa\Realdom.
 *
 * Abstract-top-super realistic domain :-).
 *
 * @author     Ivan Enderlin <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright © 2007-2013 Ivan Enderlin.
 * @license    New BSD License
 */

abstract class Realdom implements \ArrayAccess, \Countable {

    /**
     * Realistic domain name.
     *
     * @const string
     */
    const NAME = '(null)';

    /**
     * Realistic domain defined arguments.
     *
     * @var \Hoa\Realdom array
     */
    protected $_arguments      = null;

    /**
     * Realistic domain arguments.
     *
     * @var \Hoa\Realdom array
     */
    protected $arguments       = null;

    /**
     * Default sampler.
     *
     * @var \Hoa\Math\Sampler object
     */
    protected static $_sampler = null;

    /**
     * Current sampler.
     *
     * @var \Hoa\Math\Sampler object
     */
    protected $__sampler       = null;

    /**
     * Sampled value.
     *
     * @var \Hoa\Realdom mixed
     */
    protected $_value          = null;

    /**
     * Number of max try when sampling a new value.
     *
     * @var \Hoa\Realdom int
     */
    protected static $_maxtry  = 64;

    /**
     * Constraints.
     *
     * @var \Hoa\Realdom array
     */
    protected $_constraints    = null;

    /**
     * Holder.
     *
     * @var \Hoa\Realdom\IRealdom\Holder object
     */
    protected $_holder         = null;

    /**
     * Whether the realdom has been constructed or not.
     *
     * @var \Hoa\Realdom bool
     */
    protected $_constructed    = false;



    /**
     * Build a realistic domain.
     *
     * @access  public
     * @return  void
     * @throw   \Hoa\Realdom\Exception\IllegalArgument
     * @throw   \Hoa\Realdom\Exception\MissingArgument
     */
    final public function __construct ( ) {

        switch($this->_arguments) {

            case null:
                $this->arguments = array();
              break;

            case …:
                $this->arguments = func_get_args();
                self::autoBoxing($this->arguments, $this);
              break;

            default:
                $arguments       = func_get_args();
                $hints           = array();
                $this->arguments = array();
                $i               = 0;

                reset($this->_arguments);

                foreach($arguments as $argument) {

                    $name = key($this->_arguments);
                    $hint = null;

                    if(… === $argument) {

                        if(is_int($name))
                            throw new Exception\IllegalArgument(
                                'Argument %s passed to %s() does not have a ' .
                                'default value.',
                                0, array($i + 1, $this->getName()));

                        $argument = current($this->_arguments);
                    }

                    if(is_int($name)) {

                        $name = current($this->_arguments);
                        ++$i;
                    }

                    if(false !== $pos = strrpos($name, ' ')) {

                        $hint = trim(substr($name, 0, $pos));
                        $name = substr($name, $pos + 1);
                    }

                    $this->arguments[$name] = $argument;
                    $hints[]                = $hint;
                    next($this->_arguments);
                }

                while(list($name, $default) = each($this->_arguments)) {

                    if(is_int($name)) {

                        $j = 0;
                        array_walk(
                            $this->_arguments,
                            function ( $_, $key ) use ( &$j ) {

                                $j += is_int($key);

                                return;
                            }
                        );

                        throw new Exception\MissingArgument(
                            1 < $j
                                ? '%s() expects at least %d parameters, %d given.'
                                : '%s() expects at least %d parameter, %d given.',
                            1, array($this->getName(), $j, $i));
                    }

                    $hint = null;

                    if(false !== $pos = strrpos($name, ' ')) {

                        $hint = trim(substr($name, 0, $pos));
                        $name = substr($name, $pos + 1);
                    }

                    $this->arguments[$name] = $default;
                    $hints[]                = $hint;
                }

                self::autoBoxing($this->arguments, $this);

                foreach($this->arguments as &$argument) {

                    $hint = current($hints);

                    if(null === $hint) {

                        next($hints);

                        continue;
                    }

                    if($argument instanceof IRealdom\Holder)
                        $_realdoms = $argument->getHeld();
                    elseif($argument instanceof IRealdom\Crate)
                        $_realdoms = $argument->getTypes();
                    else
                        $_realdoms = array($argument);

                    $k = 0;

                    $_hints = explode('|', $hint);

                    foreach($_hints as &$_hint)
                        if('\\' !== $_hint[0])
                            $_hint = __NAMESPACE__ . '\\' . $_hint;

                    foreach($_realdoms as $_realdom) {

                        $flag = false;

                        foreach($_hints as $__hint) {

                            if(   $_realdom instanceof $__hint
                               || $_realdom === $__hint) {

                                $flag = true;

                                break;
                            }
                        }

                        if(false === $flag)
                            unset($_realdoms[$k]);
                        else
                            ++$k;
                    }

                    switch(count($_realdoms)) {

                        case 0:
                            if($argument instanceof IRealdom\Holder)
                                throw new Exception\IllegalArgument(
                                    'Argument %d passed to %s() must be of ' .
                                    'type %s, variable %s does not satisfy ' .
                                    'this constraint.',
                                    2, array(
                                        key($hints),
                                        $this->getName(),
                                        mb_strtolower($hint),
                                        $argument->getName()
                                    )
                                );

                            throw new Exception\IllegalArgument(
                                'Argument %d passed to %s() must be of type ' .
                                '%s, %s given.',
                                3, array(
                                    key($hints),
                                    $this->getName(),
                                    mb_strtolower($hint),
                                    mb_strtolower(mb_substr(
                                        $_ = get_class($argument),
                                        mb_strrpos($_, '\\') + 1
                                    ))
                                )
                            );
                          break;

                        case 1:
                            if($argument instanceof IRealdom\Crate)
                                break;

                            $argument = $_realdoms[0];
                          break;

                        default:
                            throw new Exception\IllegalArgument(
                                'Variable %s, passed as argument %d of %s(), ' .
                                'has to many domains.',
                                4, array(
                                    $argument->getName(),
                                    key($hints),
                                    $this->getName()
                                )
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
     * @access  protected
     * @return  void
     */
    protected function construct ( ) {

        return;
    }

    /**
     * Auto-boxing.
     *
     * @access  public
     * @param   array         &$arguments    Arguments.
     * @param   \Hoa\Realdom  $self          Self (if we auto-box arguments).
     * @return  void
     */
    public static function autoBoxing ( Array   &$arguments,
                                        Realdom $self = null ) {

        if(    is_object($self)
           && (get_class($self) === 'Hoa\Realdom\Constarray'
           ||  get_class($self) === 'Hoa\Realdom\Constboolean'
           ||  get_class($self) === 'Hoa\Realdom\Constfloat'
           ||  get_class($self) === 'Hoa\Realdom\Constinteger'
           ||  get_class($self) === 'Hoa\Realdom\Constnull'
           ||  get_class($self) === 'Hoa\Realdom\Conststring'))
            return;

        foreach($arguments as &$argument)
            switch(gettype($argument)) {

                case 'array':
                    $handle = array();

                    foreach($argument as &$pair) {

                        $handle[] = &$pair[0];

                        if(isset($pair[1]))
                            $handle[] = &$pair[1];
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
                    if($argument instanceof IRealdom\Holder) {

                        $argument = $argument->getHeld();

                        if(null !== $self)
                            $argument = $argument[0];

                        break;
                    }
            }

        return;
    }

    /**
     * Check if an argument exists.
     *
     * @access  public
     * @param   string  $offset    Attribute name.
     * @return  bool
     */
    public function offsetExists ( $offset ) {

        return    isset($this->arguments[$offset])
               && !($this->arguments[$offset] instanceof Constnull);
    }

    /**
     * Get an argument value.
     *
     * @access  public
     * @param   string  $offset    Attribute name.
     * @return  mixed
     */
    public function offsetGet ( $offset ) {

        return true === $this->offsetExists($offset)
                   ? $this->arguments[$offset]
                   : null;
    }

    /**
     * Set an argument value.
     *
     * @access  public
     * @param   string  $offset    Attribute name.
     * @param   mixed   $value     Attribute value.
     * @return  mixed
     */
    public function offsetSet ( $offset, $value ) {

        $old                      = $this->offsetGet($offset);
        $this->arguments[$offset] = $value;

        return $old;
    }

    /**
     * Unset an argument.
     *
     * @access  public
     * @param   string  $offset    Attribute name.
     * @return  void
     */
    public function offsetUnset ( $offset ) {

        unset($this->arguments[$offset]);

        return;
    }

    /**
     * Get all arguments values.
     *
     * @access  public
     * @return  array
     */
    public function getArguments ( ) {

        return $this->arguments;
    }

    /**
     * Get arity.
     *
     * @access  public
     * @return  int
     */
    public function count ( ) {

        return count($this->arguments);
    }

    /**
     * Get the realistic domain name.
     *
     * @access  public
     * @return  string
     */
    public function getName ( ) {

        return static::NAME;
    }

    /**
     * Set the default sampler.
     *
     * @access  public
     * @param   \Hoa\Math\Sampler  $sampler    Sampler.
     * @return  \Hoa\Math\Sampler
     */
    public static function setDefaultSampler ( \Hoa\Math\Sampler $sampler ) {

        $old              = static::$_sampler;
        static::$_sampler = $sampler;

        return $old;
    }

    /**
     * Get the default sampler.
     *
     * @access  public
     * @return  \Hoa\Math\Sampler
     */
    public static function getDefaultSampler ( ) {

        return static::$_sampler;
    }

    /**
     * Set the sampler.
     *
     * @access  public
     * @param   \Hoa\Math\Sampler  $sampler    Sampler.
     * @return  \Hoa\Math\Sampler
     */
    public function setSampler ( \Hoa\Math\Sampler $sampler ) {

        $old             = $this->__sampler;
        $this->__sampler = $sampler;

        return $old;
    }

    /**
     * Get the sampler.
     *
     * @access  public
     * @return  \Hoa\Math\Sampler
     */
    public function getSampler ( ) {

        return $this->__sampler ?: static::$_sampler;
    }

    /**
     * Set the sampled value.
     *
     * @access  protected
     * @param   mixed      $sampled    Sampled value.
     * @return  mixed
     */
    protected function setValue ( $sampled ) {

        $old          = $this->_value;
        $this->_value = $sampled;

        return $old;
    }

    /**
     * Get the sampled value.
     *
     * @access  public
     * @return  mixed
     */
    public function getValue ( ) {

        return $this->_value;
    }

    /**
     * Set the max try number.
     *
     * @access  public
     * @param   int     $maxtry    Max try authorized.
     * @return  int
     */
    public static function setMaxTry ( $maxtry ) {

        $old           = self::$_maxtry;
        self::$_maxtry = $maxtry;

        return $old;
    }

    /**
     * Get the max try number.
     *
     * @access  public
     * @return  int
     */
    public static function getMaxTry ( ) {

        return self::$_maxtry;
    }

    /**
     * Reset the realistic domain.
     *
     * @access  public
     * @return  void
     */
    public function reset ( ) {

        if(false === $this->_constructed) {

            $this->construct();
            $this->_constructed = true;
        }

        if(   $this instanceof IRealdom\Nonconvex
           && isset($this->_discredited))
            $this->_discredited = array();

        return;
    }

    /**
     * Helper to reset all arguments.
     *
     * @access  protected
     * @return  void
     */
    protected function resetArguments ( ) {

        foreach($this->getArguments() as $name => $argument)
            if($argument instanceof self)
                $argument->reset();

        return;
    }

    /**
     * Predicate whether the sampled value belongs to the realistic domains.
     *
     * @access  public
     * @param   mixed  $q    Sampled value.
     * @return  boolean
     */
    public function predicate ( $q ) {

        if(false === $this->_constructed) {

            $this->construct();
            $this->_constructed = true;
        }

        return $this->_predicate($q);
    }

    /**
     * Predicate whether the sampled value belongs to the realistic domains.
     *
     * @access  protected
     * @param   mixed  $q    Sampled value.
     * @return  boolean
     */
    abstract protected function _predicate ( $q );

    /**
     * Sample a new value.
     *
     * @access  public
     * @param   \Hoa\Math\Sampler  $sampler    Sampler.
     * @return  mixed
     * @throw   \Hoa\Realdom\Exception
     */
    public function sample ( \Hoa\Math\Sampler $sampler = null ) {

        if(false === $this->_constructed) {

            $this->construct();
            $this->_constructed = true;
        }

        if(   null === $sampler
           && null === $sampler = $this->getSampler())
            throw new Exception(
                'No sampler set. Please, use the %s::setDefaultSampler() or ' .
                '%1$s::setSampler() method.',
                4, __CLASS__);

        $maxtry = $this->getMaxTry();

        do {

            $sampled   = $this->_sample($sampler);
            $predicate = $this->predicate($sampled);

            if(false === $predicate)
                $this->reset();

        } while(false === $predicate && 0 < --$maxtry);

        if(0 >= $maxtry)
            throw new Exception(
                'Cannot sample a value, all tries failed (%d tries) from %s.',
                5, array($this->getMaxTry(), $this->getName()));

        $this->setValue($sampled);

        return $sampled;
    }

    /**
     * Sample one new value.
     *
     * @access  protected
     * @param   \Hoa\Math\Sampler  $sampler    Sampler.
     * @return  mixed
     */
    abstract protected function _sample ( \Hoa\Math\Sampler $sampler );

    /**
     * Check if the realistic domain intersects with another.
     *
     * @access  public
     * @param   \Hoa\Realdom  $realdom    Realistic domain.
     * @return  bool
     */
    public function intersectWith ( Realdom $realdom ) {

        return false;
    }

    /**
     * Set constraints.
     * Please, see Hoa\Praspel.
     *
     * @access  public
     * @param   array  &$constraints    Contraints.
     * @return  \Hoa\Realdom
     */
    public function setConstraints ( Array &$constraints ) {

        $this->_constraints = &$constraints;

        return $this;
    }

    /**
     * Get constraints.
     *
     * @access  protected
     * @return  array
     */
    protected function &getConstraints ( ) {

        return $this->_constraints;
    }

    /**
     * Propagate constraints (public).
     *
     * @access  protected
     * @param   string  $type     Type.
     * @param   int     $index    Index.
     * @return  void
     */
    public function propagateConstraints ( $type, $index ) {

        $this->_propagateConstraints($type, $index, $this->getConstraints());

        return;
    }

    /**
     * Propagate constraints.
     *
     * @access  protected
     * @param   string  $type           Type.
     * @param   int     $index          Index.
     * @param   array   $constraints    Constraints.
     * @return  void
     * @throw   \Hoa\Realdom\Exception\Inconsistent
     */
    protected function _propagateConstraints ( $type, $index,
                                               Array &$constraints ) {

        return;
    }

    /**
     * Test the “is” constraint.
     *
     * @access  public
     * @param   string  $qualifier    Qualifier.
     * @return  bool
     */
    public function is ( $qualifier ) {

        if(!isset($this->_constraints['is']))
            return false;

        return in_array($qualifier, $this->_constraints['is']);
    }

    /**
     * Set holder.
     *
     * @access  public
     * @param   \Hoa\Realdom\IRealdom\Holder  $holder    Holder.
     * @return  \Hoa\Realdom\IRealdom\Holder
     */
    public function setHolder ( IRealdom\Holder $holder ) {

        $old           = $holder;
        $this->_holder = $holder;

        return $old;
    }

    /**
     * Get holder.
     *
     * @access  public
     * @return  \Hoa\Realdom\IRealdom\Holder
     */
    public function getHolder ( ) {

        return $this->_holder;
    }

    /**
     * Get default Praspel representation of the realistic domain.
     *
     * @access  protected
     * @return  string
     */
    protected function defaultToPraspel ( ) {

        $handle = array();

        foreach($this->arguments as $argument)
            if(   $argument instanceof IRealdom\Crate
               || null === $holder = $argument->getHolder())
                $handle[] = $argument->toPraspel();
            else
                $handle[] = $holder->getName();

        return $this->getName() . '(' . implode(', ', $handle) . ')';
    }

    /**
     * Get Praspel representation of the realistic domain.
     *
     * @access  public
     * @return  string
     */
    public function toPraspel ( ) {

        return $this->defaultToPraspel();
    }

    /**
     * Get default string representation of the realistic domain.
     *
     * @access  protected
     * @return  string
     */
    protected function defaultToString ( ) {

        $out    = 'realdom()->' . $this->getName() . '(';
        $handle = array();

        foreach($this->getArguments() as $argument)
            if(null !== $holder = $argument->getHolder()) {

                $variable = '$' . $holder->getClause()->getId();
                $handle[] = $variable . '[\'' . $holder->getName() . '\']';
            }
            else
                $handle[] = $argument->__toString();

        $out .= implode(', ', $handle);

        return $out . ')';
    }

    /**
     * Get string representation of the realistic domain.
     *
     * @access  public
     * @return  string
     */
    public function __toString ( ) {

        return $this->defaultToString();
    }
}

}

namespace {

/**
 * Flex entity.
 */
Hoa\Core\Consistency::flexEntity('Hoa\Realdom\Realdom');

}
