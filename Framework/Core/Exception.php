<?php

/**
 * Hoa
 *
 *
 * @license
 *
 * GNU General Public License
 *
 * This file is part of HOA Open Accessibility.
 * Copyright (c) 2007, 2011 Ivan ENDERLIN. All rights reserved.
 *
 * HOA Open Accessibility is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * HOA Open Accessibility is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with HOA Open Accessibility; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

namespace Hoa\Core\Exception {

/**
 * Class \Hoa\Core\Exception\Idle.
 *
 * \Hoa\Core\Exception\Idle is the mother exception class of libraries. The only
 * difference between \Hoa\Core\Exception\Idle and its directly child
 * \Hoa\Core\Exception is that the later fires event after beeing constructed.
 *
 * @author     Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright (c) 2007, 2011 Ivan ENDERLIN.
 * @license    http://gnu.org/licenses/gpl.txt GNU GPL
 */

class Idle extends \Exception {

    /**
     * Arguments to format message.
     *
     * @var \Hoa\Core\Exception array
     */
    protected $_arguments = array();



    /**
     * Create an exception.
     * An exception is built with a formatted message, a code (an ID), and an
     * array that contains the list of formatted string for the message. If
     * chaining, we can add a previous exception.
     *
     * @access  public
     * @param   string      $message      Formatted message.
     * @param   int         $code         Code (the ID).
     * @param   array       $arguments    Arguments to format message.
     * @param   \Exception  $previous     Previous exception in chaining.
     * @return  void
     */
    public function __construct ( $message, $code = 0, $arguments = array(),
                                  \Exception $previous = null ) {

        if(!is_array($arguments))
            $arguments = array($arguments);

        foreach($arguments as $key => &$value)
            if(null === $value)
                $value = '(null)';

        $this->_arguments = $arguments;
        parent::__construct($message, $code, $previous);

        return;
    }

    /**
     * Get the backtrace.
     *
     * @access  public
     * @return  array
     */
    public function getBacktrace ( ) {

        return $this->getTrace();
    }

    /**
     * Get arguments for the message.
     *
     * @access  public
     * @return  array
     */
    public function getArguments ( ) {

        return $this->_arguments;
    }

    /**
     * Get the message already formatted.
     *
     * @access  public
     * @return  string
     */
    public function getFormattedMessage ( ) {

        return @vsprintf($this->getMessage(), $this->getArguments());
    }

    /**
     * Raise an exception as a string.
     *
     * @access  public
     * @return  string
     */
    public function raise ( ) {

        $message = @vsprintf($this->getMessage(), $this->getArguments());
        $trace   = $this->getBacktrace();
        $file    = '/dev/null';
        $line    = -1;
        $pre     = '{main}';

        if(!empty($trace)) {

            $t   = $trace[0];
            $pre = '';

            if(isset($t['class']))
                $pre .= $t['class'] . '::';

            if(isset($t['function']))
                $pre .= $t['function'];

            $file  = @$t['file'];
            $line  = @$t['line'];
        }

        $pre  .= ': ';

        try {

            $out = $pre . '(' . $this->getCode() . ') ' . $message . "\n" .
                   'in ' . $this->getFile() . ' at line ' .
                   $this->getLine() . '.';
        }
        catch ( \Exception $e ) {

            $out = $pre . '(' . $this->getCode() . ') ' . $message . "\n" .
                   'in ' . $file . ' around line ' . $line . '.';
        }

        return $out;
    }

    /**
     * Catch uncaught exception (only \Hoa\Core\Exception\Core and children).
     *
     * @access  public
     * @param   \Exception  $exception    The exception.
     * @return  void
     * @throw   \Exception
     */
    public static function uncaught ( \Exception $exception ) {

        if(!($exception instanceof Idle))
            throw $exception;

        echo 'Uncaught exception (' . get_class($exception) . '):' . "\n" .
             $exception->raise();

        if(null !== $previous = $exception->getPrevious()) {

            echo "\n\n" . '⬇' . "\n\n" . 'Nested ';

            $previous::uncaught($previous);
        }

        return;
    }

    /**
     * Catch PHP (and PHP_USER) errors and transform them into
     * \Hoa\Core\ErrorException.
     * Obviously, if code that caused the error is preceeded by @, then we do
     * not thrown any exception.
     *
     * @access  public
     * @param   int     $errno      Level.
     * @param   string  $errstr     Message.
     * @param   string  $errfile    File.
     * @param   int     $errline    Line.
     * @return  \Hoa\Core\ErrorException
     */
    public static function error ( $errno, $errstr, $errfile, $errline ) {

        // If @.
        if(0 == error_reporting())
            return;

        $trace = debug_backtrace();
        array_shift($trace);

        throw new Error($errstr, -1, $errfile, $errline, $trace);
    }

    /**
     * String representation of object.
     *
     * @access  public
     * @return  string
     */
    public function __toString ( ) {

        return $this->raise();
    }
}

/**
 * Class \Hoa\Core\Exception\Idle.
 *
 * Each exception must extend \Hoa\Core\Exception.
 *
 * @author     Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright (c) 2007, 2011 Ivan ENDERLIN.
 * @license    http://gnu.org/licenses/gpl.txt GNU GPL
 */

class Exception extends Idle implements \Hoa\Core\Event\Source {

    /**
     * Create an exception.
     * An exception is built with a formatted message, a code (an ID), and an
     * array that contains the list of formatted string for the message. If
     * chaining, we can add a previous exception.
     *
     * @access  public
     * @param   string      $message      Formatted message.
     * @param   int         $code         Code (the ID).
     * @param   array       $arguments    Arguments to format message.
     * @param   \Exception  $previous     Previous exception in chaining.
     * @return  void
     */
    public function __construct ( $message, $code = 0, $arguments = array(),
                                  \Exception $previous = null ) {

        parent::__construct($message, $code, $arguments, $previous);

        if(false === \Hoa\Core\Event::eventExists('hoa://Event/Exception'))
            \Hoa\Core\Event::register('hoa://Event/Exception', __CLASS__);

        $this->send();

        return;
    }

    /**
     * Send the exception on hoa://Event/Exception.
     *
     * @access  public
     * @return  void
     */
    public function send ( ) {

        \Hoa\Core\Event::notify(
            'hoa://Event/Exception',
            $this,
            new \Hoa\Core\Event\Bucket($this)
        );

        return;
    }
}

/**
 * Class \Hoa\Core\ErrorException.
 *
 * This exception is the equivalent representation of PHP errors.
 *
 * @author     Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright (c) 2007, 2011 Ivan ENDERLIN.
 * @license    http://gnu.org/licenses/gpl.txt GNU GPL
 */

class Error extends Exception {

    /**
     * Backtrace.
     *
     * @var \Hoa\Core\Error array
     */
    protected $_trace = null;



    /**
     * Constructor.
     *
     * @access  public
     * @param   string  $message    Message.
     * @param   int     $code       Code (the ID).
     * @param   string  $file       File.
     * @param   int     $line       Line.
     * @param   array   $trace      Trace.
     */
    public function __construct ( $message, $code, $file, $line,
                                  Array $trace = array() ) {

        $this->file   = $file;
        $this->line   = $line;
        $this->_trace = $trace;

        parent::__construct($message, $code);

        return;
    }

    /**
     * Get the backtrace.
     *
     * @access  public
     * @return  array
     */
    public function getBacktrace ( ) {

        return $this->_trace;
    }
}

}

namespace {

/**
 * Make the alias automatically (because it's not imported with the import()
 * function).
 */
class_alias('Hoa\Core\Exception\Exception', 'Hoa\Core\Exception');

/**
 * Catch uncaught exception.
 */
set_exception_handler(callback('\Hoa\Core\Exception\Idle::uncaught'));

/**
 * Transform PHP error into \Hoa\Core\Exception\Error.
 */
set_error_handler(callback('\Hoa\Core\Exception\Idle::error'));

}
