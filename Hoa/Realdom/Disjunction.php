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
-> import('Realdom.Exception');

}

namespace Hoa\Realdom {

/**
 * Class \Hoa\Realdom\Disjunction.
 *
 * Represent a disjunction of realistic domains.
 *
 * @author     Ivan Enderlin <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright © 2007-2012 Ivan Enderlin.
 * @license    New BSD License
 */

class Disjunction implements \IteratorAggregate, \Countable {

    /**
     * Disjointed realistic domains.
     *
     * @var \Hoa\Realdom\Disjunction mixed
     */
    protected $_realdoms      = null;

    /**
     * Chosen realistic domain.
     *
     * @var \Hoa\Realdom object
     */
    protected $_chosenRealdom = null;

    /**
     * Number of realistic domains in the disjunction.
     *
     * @var \Hoa\Realdom\Disjunction int
     */
    protected $_count         = 0;

    /**
     * Constraints.
     *
     * @var \Hoa\Realdom\Disjunction array
     */
    protected $_constraints   = array();



    /**
     * Allow to write: $disjunction->realdom1()->or->realdom2().
     *
     * @access  public
     * @param   string  $name    Must be “or”.
     * @return  \Hoa\Realdom\Disjunction
     * @throw   \Hoa\Realdom\Exception
     */
    public function __get ( $name ) {

        if('or' !== $name)
            throw new Exception(
                'Hmmm, what do you mean by %s?', 0, $name);

        return $this;
    }

    /**
     * Declare a realistic domain: $disjunction->realdomName(arg1, arg2…).
     * About constants: use for example: $disjunction->const(true).
     *
     * @access  public
     * @param   string  $name         Realistic domain name.
     * @param   array   $arguments    Arguments.
     * @return  \Hoa\Realdom\Disjunction
     */
    public function __call ( $name, Array $arguments ) {

        $name = ucfirst(strtolower($name));

        if('Const' === $name) {

            $handle    = $arguments;
            Realdom::autoBoxing($handle);
            $handle    = $handle[0];
            $arguments = array();
        }
        else {

            if(   'Array' === $name
               || 'Class' === $name
               || 'Empty' === $name)
                $name = '_' . $name;

            $handle = dnew(
                '(Hoathis or Hoa)\Realdom\\' . $name,
                $arguments
            );
        }

        $handle->setConstraints($this->_constraints);

        if(null === $this->_realdoms) {

            $this->_realdoms = $handle;
            $this->_count    = 1;

            return $this;
        }

        if(!is_array($this->_realdoms))
            $this->_realdoms = array($this->_realdoms);

        $this->_realdoms[] = $handle;
        ++$this->_count;

        return $this;
    }

    /**
     * Alias of $this->__call(…) if $name is not parsed by PHP.
     *
     * @access  public
     * @param   string  $name         Realistic domain name.
     * @param   array   $arguments    Arguments.
     * @return  \Hoa\Realdom\Disjunction
     */
    public function _call ( $name, Array $arguments = array() ) {

        return $this->__call($name, $arguments);
    }

    /**
     * Add a realdom.
     *
     * @access  public
     * @param   \Hoa\Realdom  $realdom    Realistic domain.
     * @return  \Hoa\Realdom\Disjunction
     */
    public function addRealdom ( Realdom $realdom ) {

        $this->_realdoms[] = $realdom;
        ++$this->_count;

        return $this;
    }

    /**
     * Get realistic domains.
     *
     * @access  public
     * @return  array
     */
    public function getRealdoms ( ) {

        return $this->_realdoms;
    }

    /**
     * Get chosen realistic domain.
     *
     * @access  public
     * @return  \Hoa\Realdom
     */
    public function getChosenRealdom ( ) {

        return $this->_chosenRealdom;
    }

    /**
     * Reset all realistic domains.
     *
     * @access  public
     * @return  void
     */
    public function reset ( ) {

        if(null === $this->_realdoms)
            return;

        if(!is_array($this->_realdoms))
            return $this->_realdoms->reset();

        foreach($this->_realdoms as $realdom)
            $realdom->reset();

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

        if(null === $this->_realdoms)
            return false;

        if(!is_array($this->_realdoms))
            return $this->_realdoms->predicate($q);

        foreach($this->_realdoms as $realdom)
            if(true === $realdom->predicate($q))
                return true;

        return false;
    }

    /**
     * Sample a new value.
     *
     * @access  public
     * @param   \Hoa\Math\Sampler  $sampler    Sampler.
     * @return  mixed
     * @throw   \Hoa\Realdom\Exception
     */
    public function sample ( \Hoa\Math\Sampler $sampler ) {

        if(null === $this->_realdoms)
            throw new Exception(
                'Cannot sample because the disjunction is empty.', 2);

        if(!is_array($this->_realdoms)) {

            $this->_chosenRealdom = $this->_realdoms;

            return $this->_chosenRealdom->sample($sampler);
        }

        $i                    = $sampler->getInteger(0, $this->_count - 1);
        $this->_chosenRealdom = $this->_realdoms[$i];

        return $this->_chosenRealdom->sample($sampler);
    }

    /**
     * Iterate over realistic domains.
     *
     * @access  public
     * @return  \ArrayObject
     */
    public function getIterator ( ) {

        if(null === $this->_realdoms)
            return new \ArrayObject(array());

        if(!is_array($this->_realdoms))
            return new \ArrayObject(array($this->_realdoms));

        return new \ArrayObject($this->_realdoms);
    }

    /**
     * Count number of realistics domains.
     *
     * @access  public
     * @return  int
     */
    public function count ( ) {

        return $this->_count;
    }

    /**
     * Get string representation of the disjunction.
     *
     * @access  public
     * @return  string
     */
    public function __toString ( ) {

        if(null === $this->_realdoms)
            return null;

        if(!is_array($this->_realdoms))
            return $this->_realdoms->__toString();

        $out = array();

        foreach($this->_realdoms as $realdom) {

            if($realdom instanceof Constant)
                $out[] = 'const(' . $realdom->__toString() . ')';
            else {

                $handle = array();

                foreach($realdom->getArguments() as $argument)
                    $handle[] = $argument->__toString();

                $out[]  = $realdom->getName() . '(' .
                          implode(', ', $handle) .
                          ')';
            }
        }

        return 'realdom()->' . implode('->or->', $out);
    }
}

}

namespace {

/**
 * Alias for creating a new disjunction.
 *
 * @access  public
 * @return  \Hoa\Realdom\Disjunction
 */
if(!ƒ('realdom')) {
function realdom ( ) {

    return new Hoa\Realdom\Disjunction();
}}

}
