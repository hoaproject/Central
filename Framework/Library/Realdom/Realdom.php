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
 * @author     Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright (c) 2007, 2011 Ivan ENDERLIN.
 * @license    http://gnu.org/licenses/gpl.txt GNU GPL
 */

abstract class Realdom implements \Hoa\Core\Parameterizable {

    /**
     * The \Hoa\Realdom parameters.
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
     * Realistic domain given arguments.
     *
     * @var \Hoa\Realdom array
     */
    protected $_arguments      = null;

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
        $arguments         = func_get_args();

        $this->setArguments($arguments);
        $this->setName($this->_name);

        call_user_func_array(
            array($this, 'construct'),
            $arguments
        );

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
     * Set many parameters to a class.
     *
     * @access  public
     * @param   array   $in    Parameters to set.
     * @return  void
     * @throw   \Hoa\Core\Exception
     */
    public function setParameters ( Array $in ) {

        return $this->_parameters->setParameters($this, $in);
    }

    /**
     * Get many parameters from a class.
     *
     * @access  public
     * @return  array
     * @throw   \Hoa\Core\Exception
     */
    public function getParameters ( ) {

        return $this->_parameters->getParameters($this);
    }

    /**
     * Set a parameter to a class.
     *
     * @access  public
     * @param   string  $key      Key.
     * @param   mixed   $value    Value.
     * @return  mixed
     * @throw   \Hoa\Core\Exception
     */
    public function setParameter ( $key, $value ) {

        return $this->_parameters->setParameter($this, $key, $value);
    }

    /**
     * Get a parameter from a class.
     *
     * @access  public
     * @param   string  $key    Key.
     * @return  mixed
     * @throw   \Hoa\Core\Exception
     */
    public function getParameter ( $key ) {

        return $this->_parameters->getParameter($this, $key);
    }

    /**
     * Get a formatted parameter from a class (i.e. zFormat with keywords and
     * other parameters).
     *
     * @access  public
     * @param   string  $key    Key.
     * @return  mixed
     * @throw   \Hoa\Core\Exception
     */
    public function getFormattedParameter ( $key ) {

        return $this->_parameters->getFormattedParameter($this, $key);
    }

    /**
     * Set the realistic domain name.
     *
     * @access  protected
     * @param   string     $name    Name.
     * @return  string
     */
    protected function setName ( $name ) {

        $old         = $this->_name;
        $this->_name = $name;

        return $old;
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
     * Set the realistic domain arguments.
     *
     * @access  protected
     * @param   array      $arguments    Arguments.
     * @return  array
     */
    protected function setArguments ( Array $arguments ) {

        $old              = $this->_arguments;
        $this->_arguments = $arguments;

        return $old;
    }

    /**
     * Get the realistic domain arguments.
     *
     * @access  public
     * @return  array
     */
    public function getArguments ( ) {

        return $this->_arguments;
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

        if(null === self::getSampler())
            throw new Exception(
                'No sampler set. Please, use the %s::setSampler() method.',
                0, __CLASS__);

        $maxtry  = $this->getMaxTry();
        $sampler = $this->getSampler();

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
