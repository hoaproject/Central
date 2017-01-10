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
 * Class \Hoa\Praspel\AssertionChecker\Runtime.
 *
 * Assertion checker: runtime (so-called RAC).
 *
 * @copyright  Copyright © 2007-2017 Hoa community
 * @license    New BSD License
 */
class Runtime extends AssertionChecker
{
    /**
     * Visitor Praspel.
     *
     * @var \Hoa\Praspel\Visitor\Praspel
     */
    protected $_visitorPraspel   = null;



    /**
     * Runtime assertion checker.
     *
     * @param   \Hoa\Praspel\Trace  $trace    Trace.
     * @return  bool
     * @throws  \Hoa\Praspel\Exception\AssertionChecker
     * @throws  \Hoa\Praspel\Exception\Group
     */
    public function evaluate(&$trace = false)
    {
        // Start.
        $registry      = Praspel::getRegistry();
        $verdict       = true;
        $callable      = $this->getCallable();
        $reflection    = $callable->getReflection();
        $specification = $this->getSpecification();
        $exceptions    = new Praspel\Exception\Group(
            'The Runtime Assertion Checker has detected failures for %s.',
            0,
            $callable
        );
        $classname     = null;
        $isConstructor = false;

        if ($reflection instanceof \ReflectionMethod) {
            $reflection->setAccessible(true);

            if ('__construct' === $reflection->getName()) {
                $isConstructor = true;
            }

            if (false === $reflection->isStatic()) {
                $_callback = $callable->getValidCallback();
                $_object   = $_callback[0];
                $specification->getImplicitVariable('this')->bindTo($_object);
            }

            $classname = $reflection->getDeclaringClass()->getName();
        }

        if (false !== $trace && !($trace instanceof Trace)) {
            $trace = new Praspel\Trace();
        }

        // Prepare data.
        if (null === $data = $this->getData()) {
            if (true === $this->canGenerateData()) {
                $data = static::generateData($specification);
                $this->setData($data);
            } else {
                throw new Praspel\Exception\AssertionChecker(
                    'No data were given. The System Under Test %s needs data ' .
                    'to be executed.',
                    1,
                    $callable
                );
            }
        }

        $arguments = $this->getArgumentData(
            $reflection,
            $data,
            $numberOfRequiredArguments
        );

        // Check invariant.
        $invariant  = $specification->getClause('invariant');
        $attributes = $this->getAttributeData($callable);

        foreach ($attributes as $name => $_) {
            $entryName = $classname . '::$' . $name;

            if (!isset($registry[$entryName])) {
                continue;
            }

            $entry = $registry[$entryName];

            if (true === $entry->clauseExists('invariant')) {
                foreach ($entry->getClause('invariant') as $variable) {
                    $invariant->addVariable($variable->getName(), $variable);
                }
            }
        }

        if (false === $isConstructor) {
            $verdict &= $this->checkClause(
                $invariant,
                $attributes,
                $exceptions,
                'Hoa\Praspel\Exception\Failure\Invariant',
                true,
                $trace
            );

            if (0 < count($exceptions)) {
                throw $exceptions;
            }
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

        if (0 < count($exceptions)) {
            throw $exceptions;
        }

        $rootBehavior      = $behavior instanceof Praspel\Model\Specification;
        $numberOfArguments = count($arguments);

        if ($numberOfArguments < $numberOfRequiredArguments) {
            $exceptions[] = new Praspel\Exception\Failure\Precondition(
                'Callable %s needs %d arguments; %d given.',
                2,
                [$callable, $numberOfRequiredArguments, $numberOfArguments]
            );

            throw $exceptions;
        }

        $_exceptions =
            true === $rootBehavior
                ? $exceptions
                : new Praspel\Exception\Group(
                    'Behavior %s is broken.',
                    3,
                    $behavior->getIdentifier()
                );

        try {
            // Invoke.
            $return = $this->invoke(
                $callable,
                $reflection,
                $arguments,
                $isConstructor
            );
            $arguments['\result'] = $return;

            // Check normal postcondition.
            if (true === $behavior->clauseExists('ensures')) {
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
        } catch (Praspel\Exception $internalException) {
            $_exceptions[] = new Praspel\Exception\Failure\InternalPrecondition(
                'The System Under Test has broken an internal contract.',
                4,
                null,
                $internalException
            );
        } catch (\Exception $exception) {
            $arguments['\result'] = $exception;

            // Check exceptional postcondition.
            if (true === $behavior->clauseExists('throwable')) {
                $throwable  = $behavior->getClause('throwable');
                $verdict   &= $this->checkExceptionalClause(
                    $throwable,
                    $arguments
                );

                if (false == $verdict) {
                    $_exceptions[]  = new Praspel\Exception\Failure\Exceptional(
                        'The exception %s has been unexpectedly thrown.',
                        5,
                        get_class($arguments['\result']),
                        $exception
                    );
                }
            } else {
                $verdict       &= false;
                $_exceptions[]  = new Praspel\Exception\Failure\Exceptional(
                    'The System Under Test cannot terminate exceptionally ' .
                    'because no exceptional postcondition has been specified ' .
                    '(there is no @throwable clause).',
                    6,
                    [],
                    $exception
                );
            }
        }

        if (0      <  count($_exceptions) &&
            false === $rootBehavior) {
            $_behavior = $behavior;

            while (
                (null !== $_behavior = $_behavior->getParent()) &&
                !($_behavior instanceof Praspel\Model\Specification)
            ) {
                $handle = new Praspel\Exception\Group(
                    'Behavior %s is broken.',
                    7,
                    $_behavior->getIdentifier()
                );
                $handle[]    = $_exceptions;
                $_exceptions = $handle;
            }

            $exceptions[] = $_exceptions;
        }

        if (0 < count($exceptions)) {
            throw $exceptions;
        }

        // Check invariant.
        $attributes = $this->getAttributeData($callable);
        $verdict   &= $this->checkClause(
            $invariant,
            $attributes,
            $exceptions,
            'Hoa\Praspel\Exception\Failure\Invariant',
            true,
            $trace
        );

        if (0 < count($exceptions)) {
            throw $exceptions;
        }

        return (bool) $verdict;
    }

    /**
     * Get argument data.
     *
     * @param   \ReflectionFunctionAbstract   $reflection                   Reflection.
     * @param   array                        &$data                         Data.
     * @param   int                           $numberOfRequiredArguments    Number of
     *                                                                      required
     *                                                                      arguments.
     * @return  array
     */
    protected function getArgumentData(
        \ReflectionFunctionAbstract  $reflection,
        array                       &$data,
        &$numberOfRequiredArguments
    ) {
        $arguments                 = [];
        $numberOfRequiredArguments = 0;

        foreach ($reflection->getParameters() as $parameter) {
            $name = $parameter->getName();

            if (true === array_key_exists($name, $data)) {
                $arguments[$name] = &$data[$name];

                if (false === $parameter->isOptional()) {
                    ++$numberOfRequiredArguments;
                }

                continue;
            }

            if (false === $parameter->isOptional()) {
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
     * @param   \Hoa\Consistency\Xcallable  $callable    Callable.
     * @return  array
     */
    protected function getAttributeData(Consistency\Xcallable $callable)
    {
        $callback = $callable->getValidCallback();

        if ($callback instanceof \Closure) {
            return [];
        }

        $object = $callback[0];

        if (!is_object($object)) {
            return [];
        }

        $reflectionObject = new \ReflectionObject($object);
        $attributes       = [];

        foreach ($reflectionObject->getProperties() as $property) {
            $property->setAccessible(true);
            $attributes[$property->getName()] = $property->getValue($object);
        }

        return $attributes;
    }

    /**
     * Invoke.
     *
     * @acccess  protected
     * @param    \Hoa\Consistency\Xcallable     &$reflection       Callable.
     * @param    \ReflectionFunctionAbstract    &$reflection       Reflection.
     * @param    array                          &$arguments        Arguments.
     * @param    bool                            $isConstructor    Whether
     *                                                             it is a
     *                                                             constructor.
     * @return   mixed
     * @throws   \Exception
     */
    protected function invoke(
        Consistency\Xcallable       &$callable,
        \ReflectionFunctionAbstract &$reflection,
        array &$arguments,
        $isConstructor
    ) {
        if ($reflection instanceof \ReflectionFunction) {
            return $reflection->invokeArgs($arguments);
        }

        if (false === $isConstructor) {
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
     * @param   \Hoa\Praspel\Model\Behavior    &$behavior      Behavior clause.
     * @param   array                          &$data          Data.
     * @param   \Hoa\Praspel\Exception\Group    $exceptions    Exceptions group.
     * @param   bool                            $assign        Assign data to
     *                                                         variable.
     * @param   \Hoa\Praspel\Trace              $trace         Trace.
     * @return  bool
     * @throws  \Hoa\Praspel\Exception
     */
    protected function checkBehavior(
        Praspel\Model\Behavior &$behavior,
        array &$data,
        Praspel\Exception\Group $exceptions,
        $assign = false,
        $trace  = false
    ) {
        $verdict = true;

        // Check precondition.
        if (true === $behavior->clauseExists('requires')) {
            $requires = $behavior->getClause('requires');
            $verdict  = $this->checkClause(
                $requires,
                $data,
                $exceptions,
                'Hoa\Praspel\Exception\Failure\Precondition',
                $assign,
                $trace
            );

            if (false === $verdict) {
                return false;
            }
        }

        // Check behaviors.
        if (true === $behavior->clauseExists('behavior')) {
            $_verdict  = false;
            $behaviors = $behavior->getClause('behavior');
            $exceptions->beginTransaction();

            foreach ($behaviors as $_behavior) {
                $_exceptions = new Praspel\Exception\Group(
                    'Behavior %s is broken.',
                    8,
                    $_behavior->getIdentifier()
                );

                $_trace = null;

                if (!empty($trace)) {
                    $_trace = new Praspel\Model\Behavior($trace);
                    $_trace->setIdentifier($_behavior->getIdentifier());
                }

                $_verdict = $this->checkBehavior(
                    $_behavior,
                    $data,
                    $_exceptions,
                    $assign,
                    $_trace
                );

                if (true === $_verdict) {
                    if (!empty($trace)) {
                        $trace->addClause($_trace);
                    }

                    break;
                }

                $exceptions[] = $_exceptions;
                unset($_trace);
            }

            if (false === $_verdict) {
                if (true === $behavior->clauseExists('default')) {
                    $exceptions->rollbackTransaction();
                    $_verdict = true;
                    $behavior = $behavior->getClause('default');
                } else {
                    $exceptions->commitTransaction();
                }
            } else {
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
     * @param   \Hoa\Praspel\Model\Declaration   $clause        Clause.
     * @param   array                           &$data          Data.
     * @param   \Hoa\Praspel\Exception\Group     $exceptions    Exceptions group.
     * @param   string                           $exception     Exception to
     *                                                          throw.
     * @param   bool                             $assign        Assign data to
     *                                                          variable.
     * @param   \Hoa\Praspel\Trace               $trace         Trace.
     * @return  bool
     * @throws  \Hoa\Praspel\Exception
     */
    protected function checkClause(
        Praspel\Model\Declaration $clause,
        array &$data,
        \Hoa\Praspel\Exception\Group $exceptions,
        $exception,
        $assign = false,
        $trace  = false
    ) {
        $verdict     = true;
        $traceClause = null;

        if (!empty($trace)) {
            $traceClause = clone $clause;
        }

        foreach ($clause as $name => $variable) {
            if (false === array_key_exists($name, $data)) {
                $exceptions[] = new $exception(
                    'Variable %s in @%s is required and has no value.',
                    9,
                    [$name, $clause->getName()]
                );

                continue;
            }

            $datum         = &$data[$name];
            $_verdict      = false;
            $traceVariable = null;

            if (null !== $traceClause) {
                $traceVariable        = clone $variable;
                $traceVariableDomains = $traceVariable->getDomains();
            }

            $i = 0;

            foreach ($variable->getDomains() as $realdom) {
                if (false === $_verdict && true === $realdom->predicate($datum)) {
                    $_verdict = true;
                } elseif (null !== $traceClause) {
                    unset($traceVariableDomains[$i--]);
                }

                ++$i;
            }

            if (false === $_verdict) {
                if (null !== $traceClause) {
                    unset($traceClause[$name]);
                }

                $exceptions[] = new $exception(
                    'Variable %s does not verify the constraint @%s %s.',
                    10,
                    [
                        $name,
                        $clause->getName(),
                        $this->getVisitorPraspel()->visit($variable)
                    ]
                );
            } else {
                if (true === $assign) {
                    $variable->setValue($datum);
                }

                if (null !== $traceClause) {
                    unset($traceClause[$name]);
                    $traceClause->addVariable($name, $traceVariable);
                }
            }

            $verdict &= $_verdict;
        }

        $predicateEvaluator = function ($__hoa_arguments, $__hoa_code) {
            extract($__hoa_arguments);

            return true == eval('return ' . $__hoa_code . ';');
        };

        foreach ($clause->getPredicates() as $predicate) {
            $_predicate = $predicate;

            preg_match_all(
                '#(?<!\\\)\$([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)#',
                $_predicate,
                $matches
            );

            $predicateArguments = [];

            foreach ($matches[1] as $variable) {
                if (true === array_key_exists($variable, $data)) {
                    $predicateArguments[$variable] = $data[$variable];
                }
            }

            if (false !== strpos($_predicate, '\result')) {
                if (!($clause instanceof Praspel\Model\Ensures) &&
                    !($clause instanceof Praspel\Model\Throwable)) {
                    $verdict      &= false;
                    $exceptions[]  = new $exception(
                        'Illegal \result in the following predicate: %s.',
                        11,
                        $predicate
                    );

                    continue;
                }

                $placeholder = '__hoa_praspel_' . uniqid();
                $_predicate  = str_replace(
                    '\\result',
                    '$' . $placeholder,
                    $_predicate
                );
                $predicateArguments[$placeholder] = $data['\result'];
            }

            $_verdict = $predicateEvaluator($predicateArguments, $_predicate);

            if (false === $_verdict) {
                $exceptions[] = new $exception(
                    'Violation of the following predicate: %s.',
                    11,
                    $predicate
                );
            }

            $verdict &= $_verdict;
        }

        if (!empty($trace)) {
            $trace->addClause($traceClause);
        }

        return (bool) $verdict;
    }

    /**
     * Check an exceptional clause.
     *
     * @param   \Hoa\Praspel\Model\Throwable   $clause    Clause.
     * @param   array                         &$data      Data.
     * @return  bool
     * @throws  \Hoa\Praspel\Exception
     */
    protected function checkExceptionalClause(
        Praspel\Model\Throwable $clause,
        array &$data
    ) {
        $verdict = false;

        foreach ($clause as $identifier) {
            $_exception   = $clause[$identifier];
            $instanceName = $_exception->getInstanceName();

            if ($data['\result'] instanceof $instanceName) {
                $verdict = true;

                break;
            }

            foreach ((array) $_exception->getDisjunction() as $_identifier) {
                $__exception   = $clause[$_identifier];
                $_instanceName = $__exception->getInstanceName();

                if ($exception instanceof $_instanceName) {
                    $verdict = true;

                    break;
                }
            }
        }

        return $verdict;
    }
}
