<?php

/**
 * Hoa
 *
 *
 * @license
 *
 * New BSD License
 *
 * Copyright © 2007-2015, Ivan Enderlin. All rights reserved.
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
 * \Hoa\Praspel\Exception\Preambler
 */
-> import('Praspel.Exception.Preambler')

/**
 * \Hoa\Praspel\AssertionChecker
 */
-> import('Praspel.AssertionChecker.~')

/**
 * \Hoa\Praspel
 */
-> import('Praspel.~');

}

namespace Hoa\Praspel\Preambler {

/**
 * Class \Hoa\Praspel\Preambler\EncapsulationShunter.
 *
 * Shunt encapsulation: instanciate a class and set its state by using
 * invariants and not methods calls.
 *
 * @author     Ivan Enderlin <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright © 2007-2015 Ivan Enderlin.
 * @license    New BSD License
 */

class EncapsulationShunter {

    /**
     * Assertion checker.
     * Needed to generate data for the constructor (when we need to instanciate
     * an object).
     *
     * @var \Hoa\Praspel\AssertionChecker object
     */
    protected $_assertionChecker = null;



    /**
     * Constructor.
     *
     * @access  public
     * @param   \Hoa\Praspel\AssertionChecker  $assertionChecker    Assertion
     *                                                              checker.
     * @return  void
     */
    public function __construct ( \Hoa\Praspel\AssertionChecker $assertionChecker = null ) {

        if(null !== $assertionChecker)
            $this->setAssertionChecker($assertionChecker);

        return;
    }

    /**
     * Invoke the encapsulation shunter.
     *
     * @access  public
     * @param   \Hoa\Praspel\Preambler\Handler  $preambler    Preambler.
     * @return  void
     * @throw   \Hoa\Praspel\Exception\Preambler
     */
    public function __invoke ( Handler $preambler ) {

        $callable   = $preambler->__getCallable();
        $reflection = $callable->getReflection();
        $registry   = \Hoa\Praspel::getRegistry();

        if($reflection instanceof \ReflectionClass) {

            $_object = $reflection->newInstance();
            $preambler->__setCallable(xcallable($_object, '__construct'));
        }
        elseif(!($reflection instanceof \ReflectionMethod))
            throw new \Hoa\Praspel\Exception\Preambler(
                'The callable must be a class and a (dynamic) method name.', 0);
        else {

            $callback = $callable->getValidCallback();

            if(!is_object($callback[0])) {

                $reflectionClass  = $reflection->getDeclaringClass();
                $_reflectionClass = $reflectionClass;

                while(
                    ( null === $constructor      = $_reflectionClass->getConstructor())
                 && (false !== $_reflectionClass = $_reflectionClass->getParentClass())
                );

                if(null === $constructor)
                    $_object = $reflectionClass->newInstance();
                else {

                    $className = $_reflectionClass->getName();
                    $id        = $className . '::__construct';

                    if(!isset($registry[$id]))
                        $registry[$id] = \Hoa\Praspel::interprete(
                            \Hoa\Praspel::extractFromComment(
                                $constructor->getDocComment()
                            ),
                            $className
                        );

                    $assertionChecker = $this->getAssertionChecker();

                    if(null === $assertionChecker)
                        $assertionChecker = '\Hoa\Praspel\AssertionChecker';

                    $arguments = $assertionChecker::generateData($registry[$id]);
                    $_object   = $reflectionClass->newInstanceArgs($arguments);
                }

                $preambler->__setCallable(xcallable($_object, $callback[1]));
            }
        }

        $reflectionObject = $preambler->__getReflectionObject($object);
        $className        = $reflectionObject->getName();
        $properties       = $reflectionObject->getProperties();

        foreach($properties as $property) {

            $propertyName = $property->getName();
            $id           = $className . '::$' . $propertyName;

            if(false === isset($registry[$id]))
                $registry[$id] = \Hoa\Praspel::interprete(
                    \Hoa\Praspel::extractFromComment(
                        $property->getDocComment()
                    ),
                    $className
                );

            $specification = $registry[$id];

            if(false === $specification->clauseExists('invariant'))
                throw new \Hoa\Praspel\Exception\Preambler(
                    'Cannot generate a value from %s because it has no ' .
                    '@invariant clause.',
                    1, $id);

            $preambler->$propertyName = $specification->getClause('invariant')
                                                      ->getVariable($propertyName)
                                                      ->sample();
        }

        return;
    }

    /**
     * Set an assertion checker.
     *
     * @access  public
     * @param   \Hoa\Praspel\AssertionChecker  $assertionChecker    Assertion
     *                                                              checker.
     * @return  \Hoa\Praspel\AssertionChecker
     */
    public function setAssertionChecker ( \Hoa\Praspel\AssertionChecker $assertionChecker ) {

        $old                     = $this->_assertionChecker;
        $this->_assertionChecker = $assertionChecker;

        return $old;
    }

    /**
     * Get the assertion checker.
     *
     * @access  public
     * @return  \Hoa\Praspel\AssertionChecker
     */
    public function getAssertionChecker ( ) {

        return $this->_assertionChecker;
    }
}

}
