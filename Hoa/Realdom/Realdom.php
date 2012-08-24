<?php

/**
 * Hoa
 *
 *
 * @license
 *
 * New BSD License
 *
 * Copyright © 2007-2012, Ivan Enderlin. All rights reserved.
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
-> import('Realdom.Exception')

/**
 * \Hoa\Test\Sampler
 */
-> import('Test.Sampler.~');

}

namespace Hoa\Realdom {

/**
 * Class \Hoa\Realdom.
 *
 * Abstract-top-super realistic domain :-).
 *
 * @author     Ivan Enderlin <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright © 2007-2012 Ivan Enderlin.
 * @license    New BSD License
 */

abstract class Realdom
    implements \Hoa\Core\Parameter\Parameterizable,
               \ArrayAccess,
               \Countable {

    /**
     * Parameters.
     *
     * @var \Hoa\Core\Parameter object
     */
    protected $_parameters     = null;

    /**
     * Realistic domain name.
     *
     * @var \Hoa\Realdom string
     */
    protected $_name           = null;

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
     * Choosen sampler.
     *
     * @var \Hoa\Test\Sampler object
     */
    protected static $_sampler = null;

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
     * Build a realistic domain.
     *
     * @access  public
     * @return  void
     */
    final public function __construct ( ) {

        $this->_parameters = new \Hoa\Core\Parameter(
            $this,
            array(),
            array()
        );

        switch($this->_arguments) {

            case null:
                $this->arguments = array();
              break;

            case …:
                $this->arguments = func_get_args();
              break;

            default:
                $arguments = func_get_args();
                $arity     = count($this->_arguments);

                if($arity > $c = count($arguments))
                    $arguments += array_fill($c, $arity - $c, null);

                $this->arguments = array_combine(
                    array_values($this->_arguments),
                    array_slice($arguments, 0, $arity)
                );
        }

        $this->construct();

        return;
    }

    /**
     * Construct a realistic domain.
     *
     * @access  public
     * @return  void
     */
    public function construct ( ) {

        return;
    }

    /**
     * Get parameters.
     *
     * @access  public
     * @return  \Hoa\Core\Parameter
     */
    public function getParameters ( ) {

        return $this->_parameters;
    }

    /**
     * Check if an argument exists.
     *
     * @access  public
     * @param   string  $offset    Attribute name.
     * @return  bool
     */
    public function offsetExists ( $offset ) {

        return isset($this->arguments[$offset]);
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

        return $this->_name;
    }

    /**
     * Set the sampler.
     *
     * @access  public
     * @param   \Hoa\Test\Sampler  $sampler    Sampler.
     * @return  \Hoa\Test\Sampler
     */
    public static function setSampler ( \Hoa\Test\Sampler $sampler ) {

        $old            = self::$_sampler;
        self::$_sampler = $sampler;

        return $old;
    }

    /**
     * Get the sampler.
     *
     * @access  public
     * @return  \Hoa\Test\Sampler
     */
    public static function getSampler ( ) {

        return self::$_sampler;
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
     * Predicate whether the sampled value belongs to the realistic domains.
     *
     * @access  public
     * @param   mixed  $q    Sampled value.
     * @return  boolean
     */
    abstract public function predicate ( $q );

    /**
     * Sample a new value.
     *
     * @access  public
     * @return  mixed
     * @throw   \Hoa\Realdom\Exception
     */
    public function sample ( ) {

        if(null === $sampler = self::getSampler())
            throw new Exception(
                'No sampler set. Please, use the %s::setSampler() method.',
                0, __CLASS__);

        $maxtry = $this->getMaxTry();

        do {

            $sampled = $this->_sample($sampler);

        } while(false === $this->predicate($sampled) && 0 < --$maxtry);

        if(0 >= $maxtry)
            throw new Exception(
                'Cannot sample a value, all tries failed (%d tries) from %s.',
                0, array($this->getMaxTry(), $this->getName()));

        $this->setValue($sampled);

        return $sampled;
    }

    /**
     * Sample one new value.
     *
     * @access  protected
     * @param   \Hoa\Test\Sampler  $sampler    Sampler.
     * @return  mixed
     */
    abstract protected function _sample ( \Hoa\Test\Sampler $sampler );
}

}
