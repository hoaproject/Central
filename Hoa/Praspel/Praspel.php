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
 * \Hoa\Praspel\Exception\Generic
 */
-> import('Praspel.Exception.Generic')

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
 * \Hoa\Praspel\Visitor\Interpreter
 */
-> import('Praspel.Visitor.Interpreter')

/**
 * \Hoa\Praspel\Visitor\Praspel
 */
-> import('Praspel.Visitor.Praspel')

/**
 * \Hoa\Compiler\Llk
 */
-> import('Compiler.Llk.~')

/**
 * \Hoa\File\Read
 */
-> import('File.Read');

}

namespace Hoa\Praspel {

/**
 * Class \Hoa\Praspel\Praspel.
 *
 * Take a specification + data and validate/verify a callable.
 *
 * @author     Ivan Enderlin <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright © 2007-2013 Ivan Enderlin.
 * @license    New BSD License
 */

class Praspel {

    /**
     * Specification.
     *
     * @var \Hoa\Praspel\Model\Specification object
     */
    protected $_specification  = null;

    /**
     * Data of the specification.
     *
     * @var \Hoa\Praspel array
     */
    protected $_data           = null;

    /**
     * Whether we are able to automatically generate data.
     *
     * @var \Hoa\Praspel bool
     */
    protected $_generateData   = false;

    /**
     * Callable to validate and verify.
     *
     * @var \Hoa\Core\Consistency\Xcallable object
     */
    protected $_callable       = null;

    /**
     * Visitor Praspel.
     *
     * @var \Hoa\Praspel\Visitor\Praspel object
     */
    protected $_visitorPraspel = null;



    /**
     * Construct.
     *
     * @access  public
     * @param   \Hoa\Praspel\Model\Specification  $specification    Specification.
     * @param   \Hoa\Core\Consistency\Xcallable   $callable         Callable.
     * @param   bool                              $genrateData      Generate data.
     * @return  void
     */
    public function __construct ( Model\Specification             $specification,
                                  \Hoa\Core\Consistency\Xcallable $callable,
                                  $generateData = false ) {

        $this->setSpecification($specification);
        $this->setCallable($callable);
        $this->automaticallyGenerateData($generateData);

        return;
    }

    /**
     * Runtime assertion checker.
     *
     * @access  public
     * @return  bool
     * @throw   \Hoa\Praspel\Exception\Generic
     * @throw   \Hoa\Praspel\Exception\Group
     */
    public function evaluate ( ) {

        // Start.
        $verdict       = true;
        $callable      = $this->getCallable();
        $reflection    = $callable->getReflection();
        $specification = $this->getSpecification();
        $exceptions    = new Exception\Group(
            'The Runtime Assertion Checker has detected failures for %s.',
            0, $callable);

        if($reflection instanceof \ReflectionMethod)
            $reflection->setAccessible(true);

        // Prepare data.
        if(null === $data = $this->getData())
            if(true === $this->canGenerateData())
                $data = $this->generateData();
            else
                throw new Exception\Generic(
                    'No data were given. The System Under Test %s needs data ' .
                    'to be executed.', 1, $callable);

        $arguments = array();

        foreach($reflection->getParameters() as $parameter) {

            $name = $parameter->getName();

            if(true === array_key_exists($name, $data)) {

                $arguments[$name] = &$data[$name];
                continue;
            }

            if(false === $parameter->isOptional())
                // Let the error be caught by the @requires clause.
                continue;

            $arguments[$name] = $parameter->getDefaultValue();
        }

        // Check invariant.
        if(true === $specification->clauseExists('invariant')) {

            $invariant  = $specification->getClause('invariant');
            $verdict   &= $this->checkClause(
                $invariant,
                $arguments,
                $exceptions,
                __NAMESPACE__ . '\Exception\Failure\Invariant',
                true
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
            true
        );

        if(0 < count($exceptions))
            throw $exceptions;

        try {

            // Invoke.
            if($reflection instanceof \ReflectionFunction)
                $return = $reflection->invokeArgs($arguments);
            else {

                $_callback = $callable->getValidCallback();
                $_object   = $_callback[0];
                $return    = $reflection->invokeArgs($_object, $arguments);
            }

            $arguments['\result'] = $return;
            $_exceptions          = null;

            do {

                $handle = $behavior instanceof Model\Specification
                              ? $exceptions
                              : new Exception\Group(
                                    'Behavior %s is broken.',
                                    2, $behavior->getIdentifier());

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
                        __NAMESPACE__ . '\Exception\Failure\Postcondition'
                    );
                }

            } while(null !== $behavior = $behavior->getParent());
        }
        catch ( Exception $internalException ) {

            $exceptions[] = new Exception\Failure\InternalPrecondition(
                'The System Under Test has broken an internal contract.',
                3, null, $internalException);
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
                $exceptions[] = new Exception\Failure\Exceptional(
                    'The exception %s has been unexpectedly thrown.',
                    42, array(get_class($arguments['\result']))
                );

            $verdict &= $_verdict;
        }

        if(0 < count($exceptions))
            throw $exceptions;

        // Check invariant.
        if(true === $specification->clauseExists('invariant')) {

            $invariant  = $specification->getClause('invariant');
            $verdict   &= $this->checkClause(
                $invariant,
                $arguments,
                $exceptions,
                __NAMESPACE__ . '\Exception\Failure\Invariant',
                true
            );

            if(0 < count($exceptions))
                throw $exceptions;
        }

        return (bool) $verdict;
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
     * @return  bool
     * @throw   \Hoa\Praspel\Exception
     */
    protected function checkBehavior ( Model\Behavior &$behavior,
                                       Array          &$data,
                                       Exception\Group $exceptions,
                                       $assign = false ) {

        $verdict = true;

        // Check precondition.
        if(true === $behavior->clauseExists('requires')) {

            $requires = $behavior->getClause('requires');
            $verdict  = $this->checkClause(
                $requires,
                $data,
                $exceptions,
                __NAMESPACE__ . '\Exception\Failure\Precondition',
                $assign
            );

            if(false === $verdict)
                return false;
        }

        // Check behaviors.
        if(true === $behavior->clauseExists('behavior')) {

            $_verdict    = false;
            $behaviors   = $behavior->getClause('behavior');
            $exceptions->beginTransaction();

            foreach($behaviors as $_behavior) {

                $_exceptions = new Exception\Group(
                    'Behavior %s is broken.',
                    4, $_behavior->getIdentifier());

                $_verdict = $this->checkBehavior(
                    $_behavior,
                    $data,
                    $_exceptions,
                    $assign
                );

                if(true === $_verdict)
                    break;

                $exceptions[] = $_exceptions;
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
     * @return  bool
     * @throw   \Hoa\Praspel\Exception
     */
    protected function checkClause ( Model\Declaration $clause, Array &$data,
                                     Exception\Group $exceptions, $exception,
                                     $assign = false ) {

        $verdict = true;

        foreach($clause as $name => $variable) {

            $_name = $name;

            if('\old(' === substr($name, 0, 5))
                $_name = substr($name, 5, -1);

            if(false === array_key_exists($_name, $data)) {

                $exceptions[] = new $exception(
                    'Variable %s is required and has no value.', 5, $name);

                continue;
            }

            $datum    = &$data[$_name];
            $_verdict = $variable->predicate($datum);

            if(false === $_verdict)
                $exceptions[] = new $exception(
                    'Variable %s does not verify the constraint @%s %s.',
                    6,
                    array(
                        $name,
                        $clause->getName(),
                        $this->getVisitorPraspel()->visit($variable)
                    ));
            elseif(true === $assign)
                $variable->setValue($datum);

            $verdict &= $_verdict;
        }

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
    protected function checkExceptionalClause ( Model\Throwable $clause,
                                                Array          &$data ) {

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
     * Generate data from the @requires clause.
     *
     * @access  public
     * @return  array
     */
    public function generateData ( ) {

        $data          = array();
        $specification = $this->getSpecification();

        if(false === $specification->clauseExists('requires'))
            return $data;

        foreach($specification->getClause('requires') as $name => $variable)
            $data[$name] = $variable->sample();

        $this->setData($data);

        return $data;
    }

    /**
     * Enable or disable the automatic data generation.
     *
     * @access  public
     * @param   bool  $generateData    Generate data or not.
     * @return  bool
     */
    public function automaticallyGenerateData ( $generateData ) {

        $old                 = $this->_generateData;
        $this->_generateData = $generateData;

        return $old;
    }

    /**
     * Whether we are able to automatically generate data.
     *
     * @access  public
     * @return  bool
     */
    public function canGenerateData ( ) {

        return $this->_generateData;
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

    /**
     * Get visitor Praspel.
     *
     * @access  protected
     * @return  \Hoa\Praspel\Visitor\Praspel
     */
    protected function getVisitorPraspel ( ) {

        if(null === $this->_visitorPraspel)
            $this->_visitorPraspel = new Visitor\Praspel();

        return $this->_visitorPraspel;
    }

    /**
     * Short interpreter.
     *
     * @access  public
     * @param   string  $praspel    Praspel.
     * @return  \Hoa\Praspel\Model\Clause
     */
    public static function interprete ( $praspel ) {

        static $_compiler    = null;
        static $_interpreter = null;

        if(null === $_compiler)
            $_compiler = \Hoa\Compiler\Llk::load(
                new \Hoa\File\Read('hoa://Library/Praspel/Grammar.pp')
            );

        if(null === $_interpreter)
            $_interpreter = new Visitor\Interpreter();

        $ast = $_compiler->parse($praspel);

        return $_interpreter->visit($ast);
    }

    /**
     * Extract Praspel (as a string) from a comment.
     *
     * @access  public
     * @param   string  $comment    Comment.
     * @return  string
     */
    public static function extractFromComment ( $comment ) {

        $i = preg_match('#/\*(.*?)\*/#s', $comment, $matches);

        if(0 === $i)
            return '';

        $i = preg_match_all('#^[\s\*]*\s*\*\s?([^\n]*)$#m', $matches[1], $maatches);

        if(0 === $i)
            return '';

        return trim(implode("\n", $maatches[1]));
    }
}

}

namespace {

/**
 * Alias of \Hoa\Praspel::interprete().
 *
 * @access  public
 * @param   string  $praspel    Praspel
 * @return  \Hoa\Praspel\Model\Clause
 */
if(!ƒ('praspel')) {
function praspel ( $praspel ) {

    return \Hoa\Praspel::interprete($praspel);
}}
}
