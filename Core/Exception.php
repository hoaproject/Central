<?php

/**
 * Hoa
 *
 *
 * @license
 *
 * New BSD License
 *
 * Copyright © 2007-2011, Ivan Enderlin. All rights reserved.
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

namespace Hoa\Core\Exception {

/**
 * Class \Hoa\Core\Exception\Idle.
 *
 * \Hoa\Core\Exception\Idle is the mother exception class of libraries. The only
 * difference between \Hoa\Core\Exception\Idle and its directly child
 * \Hoa\Core\Exception is that the later fires event after beeing constructed.
 *
 * @author     Ivan Enderlin <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright © 2007-2011 Ivan Enderlin.
 * @license    New BSD License
 */

class Idle extends \Exception implements \Serializable {

    /**
     * Arguments to format message.
     *
     * @var \Hoa\Core\Exception array
     */
    protected $_arguments = array();

    /**
     * Backtrace.
     *
     * @var \Hoa\Core\Exception\Idle array
     */
    protected $_trace     = null;

    /**
     * Previous.
     *
     * @var \Exception object
     */
    protected $_previous  = null;



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

        // Crapy but we do not have the choice.
        $this->_trace     = $this->getTrace();
        $this->_previous  = $this->getPrevious();

        return;
    }

    /**
     * Get the backtrace.
     * Do not use \Exception::getTrace() any more.
     *
     * @access  public
     * @return  array
     */
    public function getBacktrace ( ) {

        return $this->_trace;
    }

    /**
     * Get previous.
     * Do not use \Exception::getPrevious() any more.
     *
     * @access  public
     * @return  \Exception
     */
    public function getPreviousThrow ( ) {

        return $this->_previous;
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
     * Get the source of the exception (class, method, function, main etc.).
     *
     * @access  public
     * @return  string
     */
    public function getFrom ( ) {

        $trace = $this->getBacktrace();
        $from  = '{main}';

        if(!empty($trace)) {

            $t    = $trace[0];
            $from = '';

            if(isset($t['class']))
                $from .= $t['class'] . '::';

            if(isset($t['function']))
                $from .= $t['function'] . '()';
        }

        return $from;
    }

    /**
     * Raise an exception as a string.
     *
     * @access  public
     * @param   bool    $previous    Whether raise previous exception if exists.
     * @return  string
     */
    public function raise ( $previous = false ) {

        $message = @vsprintf($this->getMessage(), $this->getArguments());
        $trace   = $this->getBacktrace();
        $file    = '/dev/null';
        $line    = -1;
        $pre     = $this->getFrom();

        if(!empty($trace)) {

            $file = @$t['file'];
            $line = @$t['line'];
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

        if(   true === $previous
           && null !== $previous = $this->getPreviousThrow())
            $out .= "\n\n" . '⬇' . "\n\n" .
                    'Nested exception (' . get_class($previous) . '):' . "\n" .
                    $previous->raise(true);

        return $out;
    }

    /**
     * Catch uncaught exception (only \Hoa\Core\Exception\Idle and children).
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

        if(null !== $previous = $exception->getPreviousThrow())
            echo "\n\n" . '⬇' . "\n\n" .
                 'Nested exception (' . get_class($previous) . '):' . "\n" .
                 $previous->raise(true);

        return;
    }

    /**
     * Catch PHP (and PHP_USER) errors and transform them into
     * \Hoa\Core\Exception\Error.
     * Obviously, if code that caused the error is preceeded by @, then we do
     * not thrown any exception.
     *
     * @access  public
     * @param   int     $errno      Level.
     * @param   string  $errstr     Message.
     * @param   string  $errfile    File.
     * @param   int     $errline    Line.
     * @return  void
     * @throw   \Hoa\Core\Exception\Error
     */
    public static function error ( $errno, $errstr, $errfile, $errline ) {

        // If @.
        if(0 == error_reporting())
            return;

        // <tricky>
        // Please @internals, what the hell is that?
        if('Redefining already defined constructor for class' ==
           substr($errstr, 0, 48))
            return;
        // </tricky>

        $trace = debug_backtrace();
        array_shift($trace);

        throw new Error($errstr, -1, $errfile, $errline, $trace);
    }

    /**
     * Serialize exception even if we cannot serialize the backtrace.
     *
     * @access  public
     * @return  string
     */
    public function serialize ( ) {

        $name   = get_class($this);
        $length = strlen($name);
        $trace  = $this->getBacktrace();

        try {

            $trace = serialize($trace);
        }
        catch ( \Exception $e ) {

            foreach($trace as &$t)
                unset($t['args']);

            $trace = serialize($trace);
        }

        try { $previous = serialize($this->getPreviousThrow()); }
        catch ( \Exception $e ) { $previous = serialize(null); }

        return 'O:' . strlen($name) . ':"' . $name . '":7:{' .
               's:10:"*message";' . serialize(str_replace(
                   "\n", '??\n??', $this->getFormattedMessage()
               )) .
               's:17:"Exceptionstring";s:0:"";' .
               's:7:"*code";' . serialize($this->getCode()) .
               's:7:"*file";' . serialize($this->getFile()) .
               's:7:"*line";' . serialize($this->getLine()) .
               's:16:"Exceptiontrace";' . $trace .
               's:19:"Exceptionprevious";' . $previous . '}';
    }

    /**
     * Unserialize exception.
     *
     * @access  public
     * @param   string  $serialized    Serialized exception.
     * @return  void
     */
    public function unserialize ( $serialized ) {

        $_ = '"(.*?)(?<!\\\")";';

        preg_match(
            '#^O:(\d+):"([^"]+)":7:{' .
            's:10:"\*message";s:\d+:' . $_ .
            's:17:"Exceptionstring";s:0:"";' .
            's:7:"\*code";i:([^;]+);' .
            's:7:"\*file";s:\d+:' . $_ .
            's:7:"\*line";i:([^;]+);' .
            's:16:"Exceptiontrace";' .
            '(.*)?}$#s',
            $serialized,
            $matches
        );

        $this->message = str_replace('??\n??', "\n", $matches[3]);
        $this->code    = $matches[4];
        $this->file    = $matches[5];
        $this->line    = $matches[6];
        $tail          = $matches[7];

        for($i = 0, $m = strlen($tail), $c = 0, $e = false, $ti = $tail[$i];
            $i < $m && ('}' != $ti || $c != 1);
            $ti = $tail[++$i]) {

            if('\\' == $ti) {

                ++$i;
                continue;
            }

            if('"' == $ti) {

                $e = !$e;
                continue;
            }

            if(true === $e)
                continue;

            if('{' == $ti)
                ++$c;
            elseif('}' == $ti)
                --$c;
        }

        $this->_trace = unserialize(substr($tail, 0, ++$i));

        preg_match(
            '#^s:19:"Exceptionprevious";(.*);$#s',
            substr($tail, $i),
            $matches
        );

        if(isset($matches[1]))
            $this->_previous = unserialize($matches[1] . ';');

        return;
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
 * @author     Ivan Enderlin <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright © 2007-2011 Ivan Enderlin.
 * @license    New BSD License
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
 * Class \Hoa\Core\Exception\Error.
 *
 * This exception is the equivalent representation of PHP errors.
 *
 * @author     Ivan Enderlin <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright © 2007-2011 Ivan Enderlin.
 * @license    New BSD License
 */

class Error extends Exception {

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
set_exception_handler(array('\Hoa\Core\Exception\Idle', 'uncaught'));

/**
 * Transform PHP error into \Hoa\Core\Exception\Error.
 */
set_error_handler(array('\Hoa\Core\Exception\Idle', 'error'));

}
