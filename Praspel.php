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
 * \Hoa\Praspel\Exception
 */
-> import('Praspel.Exception.~');

}

namespace Hoa\Praspel {

/**
 * Class \Hoa\Praspel\Praspel.
 *
 * Take a specification + data and validate/verify a callable.
 *
 * @author     Ivan Enderlin <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright © 2007-2012 Ivan Enderlin.
 * @license    New BSD License
 */

class Praspel {

    /**
     * Specification.
     *
     * @var \Hoa\Praspel\Model\Specification object
     */
    protected $_specification = null;

    /**
     * Data of the specification.
     *
     * @var \Hoa\Praspel array
     */
    protected $_data          = array();

    /**
     * Callable to validate and verify.
     *
     * @var \Hoa\Core\Consistency\Xcallable object
     */
    protected $_callable      = null;



    /**
     * Construct.
     *
     * @access  public
     * @param   \Hoa\Praspel\Model\Specification  $specification    Specification.
     * @param   \Hoa\Core\Consistency\Xcallable   $callable         Callable.
     * @return  void
     */
    public function __construct ( Model\Specification             $specification,
                                  \Hoa\Core\Consistency\Xcallable $callable ) {

        $this->setSpecification($specification);
        $this->setCallable($callable);

        return;
    }

    /**
     * Runtime assertion checker.
     *
     * @access  public
     * @return  bool
     * @throw   \Hoa\Praspel\Exception
     */
    public function evaluate ( ) {

        $callable   = $this->getCallable();
        $reflection = $callable->getReflection();
        $variables  = $this->getData();
        $arguments  = array();

        foreach($reflection->getParameters() as $parameter) {

            $name = $parameter->getName();

            if(true === array_key_exists($name, $variables)) {

                $arguments[$name] = &$variables[$name];
                continue;
            }

            if(false === $parameter->isOptional())
                throw new Exception(
                    'The evaluated callable needs a data for the argument $%s.',
                    0, $name);

            $arguments[$name] = $parameter->getDefaultValue();
        }

        $specification = $this->getSpecification();
        $requires      = $specification->getClause('requires');
        $precondition  = true;

        foreach($arguments as $name => $value) {

            if(!isset($requires[$name]))
                $variable = $requires[$name]->in = realdom()->undefined();

            $variable = &$requires[$name];
            $variable->setValue($value);

            $precondition =    $variable->predicate()
                            && $precondition;
        }

        $return  = $callable->distributeArguments($arguments);
        $ensures = $specification->getClause('ensures');

        if(isset($ensures['result']))
            $ensures['\result'] = $return;

        $postcondition = true;

        foreach($ensures as $name => $variable) {

            $postcondition =    $variable->predicate()
                             && $postcondition;
        }

        return $precondition && $postcondition;
    }

    /**
     * Set specification.
     *
     * @access  protected
     * @param   \Hoa\Praspel\Model\Specification  $specification    Specification.
     * @return  \Hoa\Praspel\Model\Specification
     */
    protected function setSpecification ( Model\Specification $specification ) {

        $old                  = $this->_specification;
        $this->_specification = $specification;

        return $old;
    }

    /**
     * Get specification.
     *
     * @access  public
     * @return  \Hoa\Praspel\Model\Specification
     */
    public function getSpecification ( ) {

        return $this->_specification;
    }

    /**
     * Set data.
     *
     * @access  public
     * @param   array  $data    Data.
     * @return  array
     */
    public function setData ( Array $data ) {

        $old         = $this->_data;
        $this->_data = $data;

        return $old;
    }

    /**
     * Get data.
     *
     * @access  public
     * @return  array
     */
    public function getData ( ) {

        return $this->_data;
    }

    /**
     * Set callable.
     *
     * @access  protected
     * @param   \Hoa\Core\Consistency\Xcallable  $callable    Callable.
     * @return  \Hoa\Core\Consistency\Xcallable
     */
    protected function setCallable ( \Hoa\Core\Consistency\Xcallable $callable ) {

        $old             = $this->_callable;
        $this->_callable = $callable;

        return $old;
    }

    /**
     * Get callable.
     *
     * @access  public
     * @return  \Hoa\Core\Consistency\Xcallable
     */
    public function getCallable ( ) {

        return $this->_callable;
    }
}

}
