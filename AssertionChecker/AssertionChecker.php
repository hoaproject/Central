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

namespace Hoa\Praspel\AssertionChecker;

use Hoa\Consistency;
use Hoa\Praspel;

/**
 * Class \Hoa\Praspel\AssertionChecker.
 *
 * Generic assertion checker.
 *
 * @copyright  Copyright © 2007-2017 Hoa community
 * @license    New BSD License
 */
abstract class AssertionChecker
{
    /**
     * Specification.
     *
     * @var \Hoa\Praspel\Model\Specification
     */
    protected $_specification  = null;

    /**
     * Data of the specification.
     *
     * @var array
     */
    protected $_data           = null;

    /**
     * Whether we are able to automatically generate data.
     *
     * @var bool
     */
    protected $_generateData   = false;

    /**
     * Callable to validate and verify.
     *
     * @var \Hoa\Consistency\Xcallable
     */
    protected $_callable       = null;



    /**
     * Construct.
     *
     * @param   \Hoa\Praspel\Model\Specification  $specification    Specification.
     * @param   \Hoa\Consistency\Xcallable        $callable         Callable.
     * @param   bool                              $genrateData      Generate data.
     */
    public function __construct(
        Praspel\Model\Specification $specification,
        Consistency\Xcallable       $callable,
        $generateData = false
    ) {
        $this->setSpecification($specification);
        $this->setCallable($callable);
        $this->automaticallyGenerateData($generateData);

        return;
    }

    /**
     * Preamble: put the system under test into a specific state in order to be
     * able to execute the test.
     *
     * @param   mixed  $preamble    Preamble callable.
     * @return  void
     */
    public function preamble($preamble)
    {
        $preambler = new Praspel\Preambler\Handler($this->getCallable());
        $preamble($preambler);
        $this->setCallable($preambler->__getCallable());

        return;
    }

    /**
     * Assertion checker.
     *
     * @return  bool
     * @throws  \Hoa\Praspel\Exception\Generic
     * @throws  \Hoa\Praspel\Exception\Group
     */
    abstract public function evaluate();

    /**
     * Generate data.
     * Isotropic random generation of data from the @requires clause.
     *
     * @param   \Hoa\Praspel\Model\Specification  $specification    Specification.
     * @return  array
     */
    public static function generateData(Praspel\Model\Specification $specification)
    {
        $data     = [];
        $behavior = $specification;

        do {
            if (true === $behavior->clauseExists('requires')) {
                foreach ($behavior->getClause('requires') as $name => $variable) {
                    $data[$name] = $variable->sample();
                }
            }

            if (false === $behavior->clauseExists('behavior')) {
                break;
            }

            $behaviors = $behavior->getClause('behavior');
            $count     = count($behaviors);
            $i         = mt_rand(0, $count);

            if ($i === $count) {
                if (true === $behavior->clauseExists('default')) {
                    $behavior = $behavior->getClause('default');
                }
            } else {
                $behavior = $behaviors->getNth($i);
            }
        } while (true);

        return $data;
    }

    /**
     * Set specification.
     *
     * @param   \Hoa\Praspel\Model\Specification  $specification    Specification.
     * @return  \Hoa\Praspel\Model\Specification
     */
    protected function setSpecification(Praspel\Model\Specification $specification)
    {
        $old                  = $this->_specification;
        $this->_specification = $specification;

        return $old;
    }

    /**
     * Get specification.
     *
     * @return  \Hoa\Praspel\Model\Specification
     */
    public function getSpecification()
    {
        return $this->_specification;
    }

    /**
     * Enable or disable the automatic data generation.
     *
     * @param   bool  $generateData    Generate data or not.
     * @return  bool
     */
    public function automaticallyGenerateData($generateData)
    {
        $old                 = $this->_generateData;
        $this->_generateData = $generateData;

        return $old;
    }

    /**
     * Whether we are able to automatically generate data.
     *
     * @return  bool
     */
    public function canGenerateData()
    {
        return $this->_generateData;
    }

    /**
     * Set data.
     *
     * @param   array  $data    Data.
     * @return  array
     */
    public function setData(array $data)
    {
        $old         = $this->_data;
        $this->_data = $data;

        return $old;
    }

    /**
     * Get data.
     *
     * @return  array
     */
    public function getData()
    {
        return $this->_data;
    }

    /**
     * Set callable.
     *
     * @param   \Hoa\Consistency\Xcallable  $callable    Callable.
     * @return  \Hoa\Consistency\Xcallable
     */
    protected function setCallable(Consistency\Xcallable $callable)
    {
        $old             = $this->_callable;
        $this->_callable = $callable;

        return $old;
    }

    /**
     * Get callable.
     *
     * @return  \Hoa\Consistency\Xcallable
     */
    public function getCallable()
    {
        return $this->_callable;
    }

    /**
     * Get visitor Praspel.
     *
     * @return  \Hoa\Praspel\Visitor\Praspel
     */
    protected function getVisitorPraspel()
    {
        if (null === $this->_visitorPraspel) {
            $this->_visitorPraspel = new Praspel\Visitor\Praspel();
        }

        return $this->_visitorPraspel;
    }
}

/**
 * Flex entity.
 */
Consistency::flexEntity('Hoa\Praspel\AssertionChecker\AssertionChecker');
