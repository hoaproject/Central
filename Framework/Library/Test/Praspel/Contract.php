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
 * \Hoa\Test\Praspel\Exception
 */
-> import('Test.Praspel.Exception')

/**
 * \Hoa\Test\Praspel
 */
-> import('Test.Praspel.~')

/**
 * \Hoa\Test\Praspel\Clause\*
 */
-> import('Test.Praspel.Clause.*')

/**
 * \Hoa\Test\Praspel\Domain
 */
-> import('Test.Praspel.Domain')

/**
 * \Hoa\Log
 */
-> import('Log.~')

/**
 * \Hoa\Visitor\Element
 */
-> import('Visitor.Element')

/**
 * \Hoa\Iterator\Basic
 */
-> import('Iterator.Basic')

/**
 * \Hoa\Iterator\Aggregate
 */
-> import('Iterator.Aggregate');

}

namespace Hoa\Test\Praspel {

/**
 * Class \Hoa\Test\Praspel.
 *
 * Root of a Praspel contract.
 *
 * @author     Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright (c) 2007, 2011 Ivan ENDERLIN.
 * @license    New BSD License
 */

class          Contract
    implements \Hoa\Visitor\Element,
               \Hoa\Iterator\Aggregate {

    /**
     * Collection of clauses.
     *
     * @var \Hoa\Test\Praspel\Contract array
     */
    protected $_clauses      = array();

    /**
     * Log channel.
     *
     * @var \Hoa\Log object
     */
    protected $_log          = null;

    /**
     * Class name that contains the method.
     *
     * @var \Hoa\Test\Praspel\Contract string
     */
    protected $_class        = null;

    /**
     * Method name that is tested.
     *
     * @var \Hoa\Test\Praspel\Contract string
     */
    protected $_method       = null;

    /**
     * File where the method is.
     *
     * @var \Hoa\Test\Praspel\Contract string
     */
    protected $_file         = null;

    /**
     * Line where the method starts.
     *
     * @var \Hoa\Test\Praspel\Contract int
     */
    protected $_startLine    = null;

    /**
     * Line where the method ends.
     *
     * @var \Hoa\Test\Praspel\Contract int
     */
    protected $_endLine      = null;

    /**
     * Depth of the contract when calling it dynamically.
     *
     * @var \Hoa\Test\Praspel\Contract int
     */
    protected static $_depth = -1;

    /**
     * Arguments.
     *
     * @var \Hoa\Test\Praspel\Contract array
     */
    protected $_arguments    = array();

    /**
     * Result of the method call.
     *
     * @var \Hoa\Test\Praspel\Contract mixed
     */
    protected $_result       = null;

    /**
     * Exception thrown by the method call.
     *
     * @var Exception object
     */
    protected $_exception    = null;



    /**
     * Constructor. Create a default “requires” clause.
     *
     * @access  public
     * @param   string  $class        Class that contains the method.
     * @param   string  $method       Method name that is tested.
     * @param   string  $file         File where the method is.
     * @param   int     $startLine    Line where the method starts.
     * @param   int     $endLine      Line where the method ends.
     * @return  void
     */
    public function __construct ( $class = null, $method = null, $file = null,
                                  $startLine = -1, $endLine = -1 ) {

        $this->_class     = $class;
        $this->_method    = $method;
        $this->_file      = $file;
        $this->_startLine = $startLine;
        $this->_endLine   = $endLine;
        $this->_log       = \Hoa\Log::getChannel(
            Praspel::LOG_CHANNEL
        );

        return;
    }

    /**
     * Add a clause.
     *
     * @access  public
     * @param   string  $name    Clause name.
     * @return  \Hoa\Test\Praspel\Clause
     * @throws  \Hoa\Test\Praspel\Exception
     */
    public function clause ( $name ) {

        if(true === $this->clauseExists($name))
            return $this->_clauses[$name];

        $clause = null;

        switch(strtolower($name)) {

            case 'ensures':
                $clause = new Clause\Ensures($this);
              break;

            case 'invariant':
                $clause = new Clause\Invariant($this);
              break;

            case 'requires':
                $clause = new Clause\Requires($this);
              break;

            case 'throwable':
                $clause = new Clause\Throwable($this);
              break;

            default:
                throw new Exception(
                    'Unknown clause %s.', 0, $name);
        }

        return $this->_clauses[$name] = $clause;
    }

    /**
     * Create a domain.
     *
     * @access  public
     * @param   string  $name    Domain name.
     * @param   ...     ...      Domain arguments.
     * @return  \Hoa\Realdom
     */
    public function domain ( $name ) {

        $arguments = func_get_args();
        array_shift($arguments);
        $domain    = new Domain($name, $arguments);

        return $domain->getDomain();
    }

    /**
     * Verify the pre-condition.
     *
     * @access  public
     * @param   mixed   ...    Parameters passed to the method.
     * @return  bool
     */
    public function verifyPreCondition ( ) {

        $this->_arguments = func_get_args();
        $log              = array(
            'type'      => Praspel::LOG_TYPE_PRE,
            'class'     => $this->getClass(),
            'method'    => $this->getMethod(),
            'arguments' => $this->getArguments(),
            'result'    => $this->getResult(),
            'exception' => $this->getException(),
            'file'      => $this->getFile(),
            'startLine' => $this->getStartLine(),
            'endLine'   => $this->getEndLine(),
            'status'    => FAILED,
            'depth'     => ++self::$_depth
        );

        if(false === $this->clauseExists('requires')) {

            $log['status'] = SUCCEED;
            $this->getLog()->log(
                'There is no pre-condition, so it succeed.',
                \Hoa\Log::TEST,
                $log
            );

            return SUCCEED;
        }

        $i        = 0;
        $out      = true;
        $requires = $this->getClause('requires');

        foreach($requires->getVariables() as $variable) {

            $o = false;

            foreach($variable->getDomains() as $domain)
                $o = $o || $domain->predicate($this->_arguments[$i]);

            $out = $out && $o;

            if(false === $out) {

                $this->getLog()->log(
                    'The pre-condition failed.',
                    \Hoa\Log::TEST,
                    $log
                );

                return FAILED;
            }

            ++$i;
        }

        $log['status'] = SUCCEED;
        $this->getLog()->log(
            'The pre-condition succeed.',
            \Hoa\Log::TEST,
            $log
        );

        return SUCCEED;
    }

    /**
     * Verify the post-condition.
     *
     * @access  public
     * @param   mixed   $result    Result of the method-call.
     * @param   mixed   ...        Parameters passed to the method.
     * @return  bool
     */
    public function verifyPostCondition ( $result ) {

        $args          = func_get_args();
        $this->_result = array_shift($args);
        $log           = array(
            'type'      => Praspel::LOG_TYPE_POST,
            'class'     => $this->getClass(),
            'method'    => $this->getMethod(),
            'arguments' => $args,
            'result'    => $this->getResult(),
            'exception' => $this->getException(),
            'file'      => $this->getFile(),
            'startLine' => $this->getStartLine(),
            'endLine'   => $this->getEndLine(),
            'status'    => FAILED,
            'depth'     => self::$_depth--
        );

        if(false === $this->clauseExists('ensures')) {

            $log['status'] = SUCCEED;
            $this->getLog()->log(
                'There is no post-condition, so it succeed.',
                \Hoa\Log::TEST,
                $log
            );

            return SUCCEED;
        }

        $i       = 0;
        $out     = true;
        $ensures = $this->getClause('ensures');

        foreach($ensures->getVariables() as $variable) {

            if('\result' == $variable->getName())
                $arg = $this->getResult();
            else
                $arg = $args[$i++];

            $o = false;
            $p = false;

            foreach($variable->getDomains() as $domain) {

                if(true === $p = $domain->predicate($arg))
                    $variable->selectDomain($domain);

                $o = $o || $p;
            }

            $out = $out && $o;

            if(false === $out) {

                $this->getLog()->log(
                    'The post-condition failed.',
                    \Hoa\Log::TEST,
                    $log
                );

                return FAILED;
            }
        }

        $log['status'] = SUCCEED;
        $this->getLog()->log(
            'The post-condition succeed.',
            \Hoa\Log::TEST,
            $log
        );

        return SUCCEED;
    }

    /**
     * Verify the throwable.
     *
     * @access  public
     * @param   Exception  $exception    Exception.
     * @return  bool
     */
    public function verifyException ( Exception $exception ) {

        $this->_exception = $exception;
        $i                = 0;
        $out              = true;
        $name             = get_class($exception);
        $log              = array(
            'type'      => Praspel::LOG_TYPE_EXCEPTION,
            'class'     => $this->getClass(),
            'method'    => $this->getMethod(),
            'arguments' => $this->getArguments(),
            'result'    => $this->getResult(),
            'exception' => $this->getException(),
            'file'      => $this->getFile(),
            'startLine' => $this->getStartLine(),
            'endLine'   => $this->getEndLine(),
            'status'    => FAILED,
            'depth'     => self::$_depth--
        );

        if(false === $this->clauseExists('throwable')) {

            $this->getLog()->log(
                'An exception (' . $name . ') was thrown ' .
                'and no @throwable clause was declared.',
                \Hoa\Log::TEST,
                $log
            );

            return FAILED;
        }

        $throwable = $this->getClause('throwable');

        if(false === $throwable->exceptionExists($name)) {

            $this->getLog()->log(
                'Undeclared thrown exception (' . $name . ') ' .
                'in the @throwable clause.',
                \Hoa\Log::TEST,
                $log
            );

            return FAILED;
        }

        $log['status'] = SUCCEED;
        $this->getLog()->log(
            'The exceptional clause succeed (' . $name . ' was thrown).',
            \Hoa\Log::TEST,
            $log
        );

        return SUCCEED;
    }

    /**
     * Verify invariants.
     *
     * @access  public
     * @return  bool
     */
    public function verifyInvariants ( Array $invariants ) {

        $log = array(
            'type'      => Praspel::LOG_TYPE_INVARIANT,
            'class'     => $this->getClass(),
            'method'    => $this->getMethod(),
            'arguments' => $this->getArguments(),
            'result'    => $this->getResult(),
            'exception' => $this->getException(),
            'file'      => $this->getFile(),
            'startLine' => $this->getStartLine(),
            'endLine'   => $this->getEndLine(),
            'status'    => FAILED,
            'depth'     => 0
        );

        if(false === $this->clauseExists('invariant')) {

            $log['status'] = SUCCEED;
            $this->getLog()->log(
                'There is no invariant, so it succeed.',
                \Hoa\Log::TEST,
                $log
            );

            return SUCCEED;
        }

        $invariant = $this->getClause('invariant');

        foreach($invariant->getVariables() as $variable) {

            $out   = false;
            $p     = false;
            $value = $invariants[$variable->getName()];

            foreach($variable->getDomains() as $domain) {

                if(true === $p = $domain->predicate($value))
                    $variable->selectDomain($domain);

                $out = $out || $p;
            }

            if(false === $out) {

                $this->getLog()->log(
                    'The invariant ' . $variable->getName() . ' failed.',
                    \Hoa\Log::TEST,
                    $log
                );

                return FAILED;
            }
        }

        $log['status'] = SUCCEED;
        $this->getLog()->log(
            'All invariants succeed.',
            \Hoa\Log::TEST,
            $log
        );

        return SUCCEED;
    }

    /**
     * Get the current depth.
     *
     * @access  public
     * @return  int
     */
    public function getDepth ( ) {

        return self::$_depth;
    }

    /**
     * Check if a clause already exists or not.
     *
     * @access  public
     * @param   string     $name    Clause name.
     * @return  bool
     */
    public function clauseExists ( $name ) {

        return isset($this->_clauses[$name]);
    }

    /**
     * Get a specific clause.
     *
     * @access  public
     * @param   string     $name    Clause name.
     * @return  \Hoa\Test\Praspel\Clause
     * @throw   \Hoa\Test\Praspel\Exception
     */
    public function getClause ( $name ) {

        if(false === $this->clauseExists($name))
            throw new Exception(
                'Clause %s is not defined.', 1, $name);

        return $this->_clauses[$name];
    }

    /**
     * Get all clauses.
     *
     * @access  public
     * @return  array
     */
    public function getClauses ( ) {

        return $this->_clauses;
    }

    /**
     * Get class name that contains the method.
     *
     * @access  public
     * @return  string
     */
    public function getClass ( ) {

        return $this->_class;
    }

    /**
     * Get method name that is tested.
     *
     * @access  public
     * @return  string
     */
    public function getMethod ( ) {

        return $this->_method;
    }

    /**
     * Get file name where the method is.
     *
     * @access  public
     * @return  string
     */
    public function getFile ( ) {

        return $this->_file;
    }

    /**
     * Get line where the method starts.
     *
     * @access  public
     * @return  int
     */
    public function getStartLine ( ) {

        return $this->_startLine;
    }

    /**
     * Get line where the method ends.
     *
     * @access  public
     * @return  int
     */
    public function getEndLine ( ) {

        return $this->_endLine;
    }

    /**
     * Get log.
     *
     * @access  public
     * @return  \Hoa\Log
     */
    public function getLog ( ) {

        return $this->_log;
    }

    /**
     * Get arguments given to the method call.
     *
     * @access  public
     * @return  array
     */
    public function getArguments ( ) {

        return $this->_arguments;
    }

    /**
     * Get the result given by the method call.
     *
     * @access  public
     * @return  mixed
     */
    public function getResult ( ) {

        return $this->_result;
    }

    /**
     * Get the exception thrown by the method call.
     *
     * @access  public
     * @return  Exception
     */
    public function getException ( ) {

        return $this->_exception;
    }

    /**
     * Get contract ID.
     *
     * @access  public
     * @return  string
     */
    public function getId ( ) {

        return $this->getClass() . '::' . $this->getMethod();
    }

    /**
     * Get iterator (through clauses).
     *
     * @access  public
     * @return  \Hoa\Iterator\Basic
     */
    public function getIterator ( ) {

        return new \Hoa\Iterator\Basic($this->_clauses);
    }

    /**
     * Reset the contract for a new runtime.
     *
     * @access  public
     * @return  void
     */
    public function reset ( ) {

        $this->_arguments = array();
        $this->_result    = null;
        $this->_exception = null;

        foreach($this as $clause)
            if($clause instanceof Clause\Contract)
                foreach($clause->getVariables() as $variable)
                    $variable->reset();

        return;
    }

    /**
     * Accept a visitor.
     *
     * @access  public
     * @param   \Hoa\Visitor\Visit  $visitor    Visitor.
     * @param   mixed              &$handle    Handle (reference).
     * @param   mixed              $eldnah     Handle (no reference).
     * @return  mixed
     */
    public function accept ( \Hoa\Visitor\Visit $visitor,
                             &$handle = null, $eldnah = null ) {

        return $visitor->visit($this, $handle, $eldnah);
    }
}

}
