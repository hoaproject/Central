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
 * \Hoa\Praspel\Exception\AssertionChecker
 */
-> import('Praspel.Exception.AssertionChecker')

/**
 * \Hoa\Praspel\Exception\Group
 */
-> import('Praspel.Exception.Group')

/**
 * \Hoa\Praspel\Exception\Failure\Precondition
 */
-> import('Praspel.Exception.Failure.Precondition')

/**
 * \Hoa\Praspel\Exception\Failure\Postcondition
 */
-> import('Praspel.Exception.Failure.Postcondition')

/**
 * \Hoa\Praspel\Exception\Failure\Exceptional
 */
-> import('Praspel.Exception.Failure.Exceptional')

/**
 * \Hoa\Praspel\Exception\Failure\Invariant
 */
-> import('Praspel.Exception.Failure.Invariant')

/**
 * \Hoa\Praspel\Exception\Failure\InternalPrecondition
 */
-> import('Praspel.Exception.Failure.InternalPrecondition')

/**
 * \Hoa\Praspel\AssertionChecker
 */
-> import('Praspel.AssertionChecker.~');

}

namespace Hoa\Praspel\AssertionChecker {

/**
 * Class \Hoa\Praspel\AssertionChecker\Runtime.
 *
 * Assertion checker: runtime (so-called RAC).
 *
 * @author     Ivan Enderlin <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright © 2007-2013 Ivan Enderlin.
 * @license    New BSD License
 */

class Runtime extends AssertionChecker {

    /**
     * Visitor Praspel.
     *
     * @var \Hoa\Praspel\Visitor\Praspel object
     */
    protected $_visitorPraspel   = null;



    /**
     * Runtime assertion checker.
     *
     * @access  public
     * @param   \Hoa\Praspel\Trace  $trace    Trace.
     * @return  bool
     * @throw   \Hoa\Praspel\Exception\AssertionChecker
     * @throw   \Hoa\Praspel\Exception\Group
     */
    public function evaluate ( &$trace = false ) {

        // Start.
        $verdict       = true;
        $callable      = $this->getCallable();
        $reflection    = $callable->getReflection();
        $specification = $this->getSpecification();
        $exceptions    = new \Hoa\Praspel\Exception\Group(
            'The Runtime Assertion Checker has detected failures for %s.',
            0, $callable
        );
        $isConstructor = false;

        if($reflection instanceof \ReflectionMethod) {

            $reflection->setAccessible(true);

            if('__construct' === $reflection->getName())
                $isConstructor = true;

            if(false === $reflection->isStatic()) {

                $_callback = $callable->getValidCallback();
                $_object   = $_callback[0];
                $specification->getImplicitVariable('this')->bindTo($_object);
            }
        }

        if(false !== $trace && !($trace instanceof Trace))
            $trace = new \Hoa\Praspel\Trace();

        // Prepare data.
        if(null === $data = $this->getData())
            if(true === $this->canGenerateData())
                $data = $this->generateData();
            else
                throw new \Hoa\Praspel\Exception\AssertionChecker(
                    'No data were given. The System Under Test %s needs data ' .
                    'to be executed.', 1, $callable);

        $arguments = $this->getArgumentData(
            $reflection,
            $data,
            $numberOfRequiredArguments
        );

        // Check invariant.
        if(    true === $specification->clauseExists('invariant')
           && false === $isConstructor) {

            $attributes = $this->getAttributeData($callable);
            $invariant  = $specification->getClause('invariant');
            $verdict   &= $this->checkClause(
                $invariant,
                $attributes,
                $exceptions,
                'Hoa\Praspel\Exception\Failure\Invariant',
                true,
                $trace
            );

            if(0 < count($exceptions))
                throw $exceptions;
        }

        // Check requires and behaviors.
        $behavior  = $specification;
        $verdict  &= $this->checkBehavior(
            $behavior,
            $arguments,
            $exceptions,
            true,
            $trace
        );

        if(0 < count($exceptions))
            throw $exceptions;

        $numberOfArguments = count($arguments);

        if($numberOfArguments < $numberOfRequiredArguments) {

            $exceptions[] = new \Hoa\Praspel\Exception\Failure\Precondition(
                'Callable %s needs %d arguments; %d given.',
                2, array($callable, $numberOfRequiredArguments, $numberOfArguments)
            );

            throw $exceptions;
        }

        try {

            // Invoke.
            $return = $this->invoke(
                $callable,
                $reflection,
                $arguments,
                $isConstructor
            );
            $arguments['\result'] = $return;
            $_exceptions          = null;

            do {

                $handle = $behavior instanceof \Hoa\Praspel\Model\Specification
                              ? $exceptions
                              : new \Hoa\Praspel\Exception\Group(
                                    'Behavior %s is broken.',
                                    3, $behavior->getIdentifier()
                                );

                if(null !== $_exceptions && 0 < count($_exceptions))
                    $handle[] = $_exceptions;

                $_exceptions = $handle;

                // Check normal postcondition.
                if(true === $behavior->clauseExists('ensures')) {

                    $ensures  = $behavior->getClause('ensures');
                    $verdict &= $this->checkClause(
                        $ensures,
                        $arguments,
                        $_exceptions,
                        'Hoa\Praspel\Exception\Failure\Postcondition',
                        false,
                        $trace
                    );
                }

            } while(null !== $behavior = $behavior->getParent());
        }
        catch ( \Hoa\Praspel\Exception $internalException ) {

            $exceptions[] = new \Hoa\Praspel\Exception\Failure\InternalPrecondition(
                'The System Under Test has broken an internal contract.',
                4, null, $internalException);
        }
        catch ( \Exception $exception ) {

            $_verdict             = false;
            $arguments['\result'] = $exception;

            do {

                // Check exceptional postcondition.
                if(true === $behavior->clauseExists('throwable')) {

                    $throwable = $behavior->getClause('throwable');
                    $_verdict  = $this->checkExceptionalClause(
                        $throwable,
                        $arguments
                    );
                }

            } while(   false === $_verdict
                    &&  null !== $behavior = $behavior->getParent());

            if(false === $_verdict)
                $exceptions[] = new \Hoa\Praspel\Exception\Failure\Exceptional(
                    'The exception %s has been unexpectedly thrown.',
                    5, get_class($arguments['\result']), $exception
                );

            $verdict &= $_verdict;
        }

        if(0 < count($exceptions))
            throw $exceptions;

        // Check invariant.
        if(true === $specification->clauseExists('invariant')) {

            $attributes = $this->getAttributeData($callable);
            $invariant  = $specification->getClause('invariant');
            $verdict   &= $this->checkClause(
                $invariant,
                $attributes,
                $exceptions,
                'Hoa\Praspel\Exception\Failure\Invariant',
                true,
                $trace
            );

            if(0 < count($exceptions))
                throw $exceptions;
        }

        return (bool) $verdict;
    }

    /**
     * Get argument data.
     *
     * @access  protected
     * @param   \ReflectionFunctionAbstract   $reflection                   Reflection.
     * @param   array                        &$data                         Data.
     * @param   int                           $numberOfRequiredArguments    Number of
     *                                                                      required
     *                                                                      arguments.
     * @return  array
     */
    protected function getArgumentData ( \ReflectionFunctionAbstract  $reflection,
                                         Array                       &$data,
                                         &$numberOfRequiredArguments ) {

        $arguments                 = array();
        $numberOfRequiredArguments = 0;

        foreach($reflection->getParameters() as $parameter) {

            $name = $parameter->getName();

            if(true === array_key_exists($name, $data)) {

                $arguments[$name] = &$data[$name];

                if(false === $parameter->isOptional())
                    ++$numberOfRequiredArguments;

                continue;
            }

            if(false === $parameter->isOptional()) {

                ++$numberOfRequiredArguments;

                // Let the error be caught by a @requires clause.
                continue;
            }

            $arguments[$name] = $parameter->getDefaultValue();
        }

        return $arguments;
    }

    /**
     * Get attribute data.
     *
     * @access  protected
     * @param   \Hoa\Core\Consistency\Xcallable  $callable    Callable.
     * @return  array
     */
    protected function getAttributeData ( \Hoa\Core\Consistency\Xcallable $callable ) {

        $callback = $callable->getValidCallback();
        $object   = $callback[0];

        if(!is_object($object))
            return array();

        $reflectionObject = new \ReflectionObject($object);
        $attributes       = array();

        foreach($reflectionObject->getProperties() as $property) {

            $property->setAccessible(true);
            $attributes[$property->getName()] = $property->getValue($object);
        }

        return $attributes;
    }

    /**
     * Invoke.
     *
     * @acccess  protected
     * @param    \Hoa\Core\Consistency\Xcallable    &$reflection       Callable.
     * @param    \ReflectionFunctionAbstract        &$reflection       Reflection.
     * @param    array                              &$arguments        Arguments.
     * @param    bool                                $isConstructor    Whether
     *                                                                 it is a
     *                                                                 constructor.
     * @return   mixed
     * @throw    \Exception
     */
    protected function invoke ( \Hoa\Core\Consistency\Xcallable &$callable,
                                \ReflectionFunctionAbstract     &$reflection,
                                Array                           &$arguments,
                                $isConstructor ) {

        if($reflection instanceof \ReflectionFunction)
            return $reflection->invokeArgs($arguments);

        if(false === $isConstructor) {

            $_callback = $callable->getValidCallback();
            $_object   = $_callback[0];

            return $reflection->invokeArgs($_object, $arguments);
        }

        $class      = $reflection->getDeclaringClass();
        $instance   = $class->newInstanceArgs($arguments);
        $callable   = xcallable($instance, '__construct');
        $reflection = $callable->getReflection();

        return void;
    }

    /**
     * Check behavior clauses.
     *
     * @access  protected
     * @param   \Hoa\Praspel\Model\Behavior    &$behavior      Behavior clause.
     * @param   array                          &$data          Data.
     * @param   \Hoa\Praspel\Exception\Group    $exceptions    Exceptions group.
     * @param   bool                            $assign        Assign data to
     *                                                         variable.
     * @param   \Hoa\Praspel\Trace              $trace         Trace.
     * @return  bool
     * @throw   \Hoa\Praspel\Exception
     */
    protected function checkBehavior ( \Hoa\Praspel\Model\Behavior  &$behavior,
                                       Array                        &$data,
                                       \Hoa\Praspel\Exception\Group  $exceptions,
                                       $assign = false,
                                       $trace  = false ) {

        $verdict = true;

        // Check precondition.
        if(true === $behavior->clauseExists('requires')) {

            $requires = $behavior->getClause('requires');
            $verdict  = $this->checkClause(
                $requires,
                $data,
                $exceptions,
                'Hoa\Praspel\Exception\Failure\Precondition',
                $assign,
                $trace
            );

            if(false === $verdict)
                return false;
        }

        // Check behaviors.
        if(true === $behavior->clauseExists('behavior')) {

            $_verdict  = false;
            $behaviors = $behavior->getClause('behavior');
            $exceptions->beginTransaction();

            foreach($behaviors as $_behavior) {

                $_exceptions = new \Hoa\Praspel\Exception\Group(
                    'Behavior %s is broken.',
                    6, $_behavior->getIdentifier()
                );

                $_trace = null;

                if(!empty($trace)) {

                    $_trace = new \Hoa\Praspel\Model\Behavior($trace);
                    $_trace->setIdentifier($_behavior->getIdentifier());
                }

                $_verdict = $this->checkBehavior(
                    $_behavior,
                    $data,
                    $_exceptions,
                    $assign,
                    $_trace
                );

                if(true === $_verdict) {

                    if(!empty($trace))
                        $trace->addClause($_trace);

                    break;
                }

                $exceptions[] = $_exceptions;
                unset($_trace);
            }

            if(false === $_verdict) {

                if(true === $behavior->clauseExists('default')) {

                    $exceptions->rollbackTransaction();
                    $_verdict = true;
                    $behavior = $behavior->getClause('default');
                }
                else
                    $exceptions->commitTransaction();
            }
            else {

                $exceptions->rollbackTransaction();
                $behavior = $_behavior;
            }

            $verdict &= $_verdict;
        }

        return (bool) $verdict;
    }

    /**
     * Check a clause.
     *
     * @access  protected
     * @param   \Hoa\Praspel\Model\Declaration   $clause        Clause.
     * @param   array                           &$data          Data.
     * @param   \Hoa\Praspel\Exception\Group     $exceptions    Exceptions group.
     * @param   string                           $exception     Exception to
     *                                                          throw.
     * @param   bool                             $assign        Assign data to
     *                                                          variable.
     * @param   \Hoa\Praspel\Trace               $trace         Trace.
     * @return  bool
     * @throw   \Hoa\Praspel\Exception
     */
    protected function checkClause ( \Hoa\Praspel\Model\Declaration  $clause,
                                     Array                          &$data,
                                     \Hoa\Praspel\Exception\Group    $exceptions,
                                     $exception,
                                     $assign = false,
                                     $trace  = false ) {

        $verdict     = true;
        $traceClause = null;

        if(!empty($trace))
            $traceClause = clone $clause;

        foreach($clause as $name => $variable) {

            if(false === array_key_exists($name, $data)) {

                $exceptions[] = new $exception(
                    'Variable %s is required and has no value.', 7, $name);

                continue;
            }

            $datum         = &$data[$name];
            $_verdict      = false;
            $traceVariable = null;

            if(null !== $traceClause) {

                $traceVariable        = clone $variable;
                $traceVariableDomains = $traceVariable->getDomains();
            }

            $i = 0;

            foreach($variable->getDomains() as $realdom) {

                if(false === $_verdict && true === $realdom->predicate($datum))
                    $_verdict = true;
                elseif(null !== $traceClause)
                    unset($traceVariableDomains[$i--]);

                ++$i;
            }

            if(false === $_verdict) {

                if(null !== $traceClause)
                    unset($traceClause[$name]);

                $exceptions[] = new $exception(
                    'Variable %s does not verify the constraint @%s %s.',
                    8,
                    array(
                        $name,
                        $clause->getName(),
                        $this->getVisitorPraspel()->visit($variable)
                    ));
            }
            else {

                if(true === $assign)
                    $variable->setValue($datum);

                if(null !== $traceClause) {

                    unset($traceClause[$name]);
                    $traceClause->addVariable($name, $traceVariable);
                }
            }

            $verdict &= $_verdict;
        }

        if(!empty($trace))
            $trace->addClause($traceClause);

        return (bool) $verdict;
    }

    /**
     * Check an exceptional clause.
     *
     * @access  protected
     * @param   \Hoa\Praspel\Model\Throwable   $clause    Clause.
     * @param   array                         &$data      Data.
     * @return  bool
     * @throw   \Hoa\Praspel\Exception
     */
    protected function checkExceptionalClause ( \Hoa\Praspel\Model\Throwable  $clause,
                                                Array                        &$data ) {

        $verdict = false;

        foreach($clause as $identifier) {

            $_exception   = $clause[$identifier];
            $instanceName = $_exception->getInstanceName();

            if($data['\result'] instanceof $instanceName) {

                $verdict = true;
                break;
            }

            foreach((array) $_exception->getDisjunction() as $_identifier) {

                $__exception   = $clause[$_identifier];
                $_instanceName = $__exception->getInstanceName();

                if($exception instanceof $_instanceName) {

                    $verdict = true;
                    break;
                }
            }
        }

        return $verdict;
    }

    /**
     * Isotropic random generation of data from the @requires clause.
     *
     * @access  public
     * @return  array
     */
    public function generateData ( ) {

        $data     = array();
        $behavior = $this->getSpecification();

        do {

            if(true === $behavior->clauseExists('requires'))
                foreach($behavior->getClause('requires') as $name => $variable)
                    $data[$name] = $variable->sample();

            if(false === $behavior->clauseExists('behavior'))
                break;

            $behaviors = $behavior->getClause('behavior');
            $count     = count($behaviors);
            $i         = mt_rand(0, $count);

            if($i === $count) {

                if(true === $behavior->clauseExists('default'))
                    $behavior = $behavior->getClause('default');
            }
            else
                $behavior = $behaviors->getNth($i);

        } while(true);

        $this->setData($data);

        return $data;
    }
}

}
