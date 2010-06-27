<?php

/**
 * Hoa Framework
 *
 *
 * @license
 *
 * GNU General Public License
 *
 * This file is part of HOA Open Accessibility.
 * Copyright (c) 2007, 2010 Ivan ENDERLIN. All rights reserved.
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
 *
 *
 * @category    Framework
 * @package     Hoa_Exception
 *
 */

/**
 * Class Hoa_Exception.
 *
 * Hoa_Exception is the mother exception class of the framework. Each exception
 * must extend Hoa_Exception, itself extends PHP Exception class.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2010 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Core
 * @subpackage  Hoa_Core_Exception
 */

class Hoa_Exception extends Exception {

    /**
     * Error type: return the message.
     *
     * @const int
     */
    const ERROR_RETURN  = 1;

    /**
     * Error type: print the message.
     *
     * @const int
     */
    const ERROR_PRINT   = 2;

    /**
     * Error type: trigger the message.
     *
     * @const int
     */
    const ERROR_TRIGGER = 4;

    /**
     * Error type: print the message and exit the program.
     *
     * @const int
     */
    const ERROR_DIE     = 8;

    /**
     * RaiseError string arguments.
     *
     * @var Hoa_Exception array
     */
    protected $_arg = array();



    /**
     * Create an exception.
     * An exception is built with a formatted message, a code (an ID), and an
     * array that contains the list of formatted string for the message.
     *
     * @access  public
     * @param   string  $message    Formatted message.
     * @param   int     $code       Code (the ID).
     * @param   array   $args       RaiseError string arguments.
     * @return  void
     */
    public function __construct ( $message, $code = 0, $args = array() ) {

        if(!is_array($args))
            $args = array($args);

        foreach($args as $key => &$value)
            if(null === $value)
                $value = '(null)';

        $this->_arg = $args;

        parent::__construct($message, $code);

        return;
    }

    /**
     * Get the message already formatted.
     *
     * @access  public
     * @return  string
     */
    public function getFormattedMessage ( ) {

        return @vsprintf($this->getMessage(), $this->_arg);
    }

    /**
     * Raise an error.
     * An exception is transformed to a string message, that could be returned,
     * printed, throw with trigger_error user function, or killed with die/exit
     * function.
     *
     * @access  public
     * @param   int     $output    Type of output (given by ERROR_* constants).
     * @param   int     $opt       Trigger error option.
     * @param   string  $pre       Prepend text to error.
     * @return  mixed
     */
    public function raiseError ( $output = self::ERROR_PRINT,
                                 $opt    = E_USER_WARNING,
                                 $pre    = '' ) {

        $message = @vsprintf($this->getMessage(), $this->_arg);
        $trace   = $this->getTrace();

        if(!empty($trace))
            $pre .= @$trace[0]['class'] . '::' . @$trace[0]['function'] . ': ';

        $out = $pre . '(' . $this->getCode() . ') ' . $message . "\n" .
               'in ' . $this->getFile() . ' at ' . $this->getLine() . '.' . "\n\n";

        switch($output) {

            case self::ERROR_PRINT:
                echo $out;
              break;

            case self::ERROR_TRIGGER:
                trigger_error($out, $opt);
              break;

            case self::ERROR_DIE:
                return exit($out);
              break;

            case self::ERROR_RETURN:
            default:
                return $out;
        }

        return;
    }

    /**
     * Catch uncaught exception.
     * Each uncaught exception is redirected to self::raiseError method with a
     * prepend text (e.g. "Uncaught exception:").
     * Catch only Hoa's exceptions!
     *
     * @access  public
     * @param   object  $exception    The exception.
     * @return  mixed
     * @throw   Exception
     */
    public static function handler ( $exception ) {

        if($exception instanceof Hoa_Exception)
            return $exception->raiseError(
                self::ERROR_PRINT,
                E_USER_WARNING,
                'Uncaught exception (' . get_class($exception) . '):' . "\n"
            );

        throw $exception;
    } 

    /**
     * String representation of object.
     *
     * @access  public
     * @return  string
     */
    public function __toString ( ) {

        return $this->raiseError(self::ERROR_RETURN);
    }
}


/**
 * Catch uncaught exception.
 */
set_exception_handler(array('Hoa_Exception', 'handler'));
