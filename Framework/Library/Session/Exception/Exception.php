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
 */

namespace Hoa\Session\Exception {

/**
 * Class \Hoa\Session\Exception.
 *
 * Extending the \Hoa\Core\Exception class.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2010 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 */

class Exception extends \Hoa\Core\Exception {

    /**
     * Whether an error has occured when trying to start a session.
     *
     * @var \Hoa\Session\Exception bool
     */
    protected static $startError                = false;

    /**
     * Whether an error has occured when trying to write and close a session.
     *
     * @var \Hoa\Session\Exception bool
     */
    protected static $writeAndCloseError        = false;

    /**
     * Whether an error has occured when trying to destroy a session.
     *
     * @var \Hoa\Session\Exception bool
     */
    protected static $destroyError              = false;

    /**
     * Start error message.
     * 
     * @var \Hoa\Session\Exception string
     */
    protected static $startErrorMessage         = null;

    /**
     * Write and close error message.
     * 
     * @var \Hoa\Session\Exception string
     */
    protected static $writeAndCloseErrorMessage = null;

    /**
     * Destroy error message.
     *
     * @var \Hoa\Session\Exception string
     */
    protected static $destroyErrorMessage       = null;



    /**
     * Call the parent. If no message is set, we use the
     * self::$startErrorMessage.
     *
     * @access  public
     * @param   string  $message    The formatted message.
     * @param   int     $code       The code (the ID).
     * @param   array   $arg        RaiseError string arguments.
     * @return  void
     */
    public function __construct ( $message = null, $code = 0, $arg = array() ) {

        parent::__construct($message, $code, $arg);
    }

    /**
     * If an error occures when trying to start a session, switch
     * self::startError to true.
     *
     * @access  public
     * @param   int     $errno         Contain the level of the error raised.
     * @param   string  $errstr        Contain the error message.
     * @param   string  $errfile       Contain the filename that the error was
     *                                 raised in.
     * @param   int     $errline       Contain the line number the error was
     *                                 raised at.
     * @param   array   $errcontext    Point to the active symbol table at the
     *                                 point the error occured. In other words,
     *                                 errorcontext will contain an array of
     *                                 every variable that existed in the scope
     *                                 the error was triggered in.
     */
    public static function handleStartError ( $errno,   $errstr,    $errfile,
                                              $errline, $errcontext ) {

        $old              = self::$startError;
        self::$startError = true;

        self::setStartErrorMessage($errno, $errstr, $errfile, $errline);

        return $old;
    }

    /**
     * If an error occures when trying to write and close a session, switch
     * self::writeAndCloseError to true.
     *
     * @access  public
     * @param   int     $errno         Contain the level of the error raised.
     * @param   string  $errstr        Contain the error message.
     * @param   string  $errfile       Contain the filename that the error was
     *                                 raised in.
     * @param   int     $errline       Contain the line number the error was
     *                                 raised at.
     * @param   array   $errcontext    Point to the active symbol table at the
     *                                 point the error occured. In other words,
     *                                 errorcontext will contain an array of
     *                                 every variable that existed in the scope
     *                                 the error was triggered in.
     */
    public static function handleWriteAndCloseError ( $errno,     $errstr,
                                                      $errfile,   $errline,
                                                      $errcontext ) {

        $old                      = self::$writeAndCloseError;
        self::$writeAndCloseError = true;

        self::setWriteAndCloseErrorMessage($errno, $errstr, $errfile, $errline);

        return $old;
    }

    /**
     * If an error occures when trying to destroy a session, switch
     * self::destroyError to true.
     *
     * @access  public
     * @param   int     $errno         Contain the level of the error raised.
     * @param   string  $errstr        Contain the error message.
     * @param   string  $errfile       Contain the filename that the error was
     *                                 raised in.
     * @param   int     $errline       Contain the line number the error was
     *                                 raised at.
     * @param   array   $errcontext    Point to the active symbol table at the
     *                                 point the error occured. In other words,
     *                                 errorcontext will contain an array of
     *                                 every variable that existed in the scope
     *                                 the error was triggered in.
     */
    public static function handleDestroyError ( $errno,     $errstr,
                                                $errfile,   $errline,
                                                $errcontext ) {

        $old                = self::$destroyError;
        self::$destroyError = true;

        self::setDestroyErrorMessage($errno, $errstr, $errfile, $errline);

        return $old;
    }

    /**
     * Turn off a handled error.
     *
     * @access  public
     * @return  void
     */
    public static function handleNull ( ) {

        return;
    }

    /**
     * Check if an error has occured when trying to start a session.
     *
     * @access  public
     * @return  bool
     */
    public static function hasStartError ( ) {

        return self::$startError;
    }

    /**
     * Check if an error has occured when trying to write and close a session.
     *
     * @access  public
     * @return  bool
     */
    public static function hasWriteAndCloseError ( ) {

        return self::$writeAndCloseError;
    }

    /**
     * Check if an error has occured when trying to destroy a session.
     *
     * @access  public
     * @return  bool
     */
    public static function hasDestroyError ( ) {

        return self::$destroyError;
    }

    /**
     * Set the start error message.
     *
     * @access  public
     * @param   int     $errno         Contain the level of the error raised.
     * @param   string  $errstr        Contain the error message.
     * @param   string  $errfile       Contain the filename that the error was
     *                                 raised in.
     * @param   int     $errline       Contain the line number the error was
     *                                 raised at.
     * @return  string
     */
    public static function setStartErrorMessage ( $errno,   $errstr,
                                                  $errfile, $errline ) {

        $old                     = self::$startErrorMessage;
        self::$startErrorMessage = '(' . $errno . ') ' .
                                   $errstr .
                                   ' in ' . $errfile .
                                   ' at ' . $errline;

        return $old;
    }

    /**
     * Set the write and close error message.
     *
     * @access  public
     * @param   int     $errno         Contain the level of the error raised.
     * @param   string  $errstr        Contain the error message.
     * @param   string  $errfile       Contain the filename that the error was
     *                                 raised in.
     * @param   int     $errline       Contain the line number the error was
     *                                 raised at.
     * @return  string
     */
    public static function setWriteAndCloseErrorMessage ( $errno,   $errstr,
                                                         $errfile, $errline ) {

        $old                             = self::$writeAndCloseErrorMessage;
        self::$writeAndCloseErrorMessage = '(' . $errno . ') ' .
                                           $errstr .
                                           ' in ' . $errfile .
                                           ' at ' . $errline;

        return $old;
    }

    /**
     * Set the destroy error message.
     *
     * @access  public
     * @param   int     $errno         Contain the level of the error raised.
     * @param   string  $errstr        Contain the error message.
     * @param   string  $errfile       Contain the filename that the error was
     *                                 raised in.
     * @param   int     $errline       Contain the line number the error was
     *                                 raised at.
     * @return  string
     */
    public static function setDestroyErrorMessage ( $errno,   $errstr,
                                                    $errfile, $errline ) {

        $old                       = self::$destroyErrorMessage;
        self::$destroyErrorMessage = '(' . $errno . ') ' .
                                     $errstr .
                                     ' in ' . $errfile .
                                     ' at ' . $errline;

        return $old;
    }

    /**
     * Get the start error message.
     *
     * @access  public
     * @return  string
     */
    public static function getStartErrorMessage ( ) {

        return self::$startErrorMessage;
    }

    /**
     * Get the write and close error message.
     *
     * @access  public
     * @return  string
     */
    public static function getWriteAndCloseErrorMessage ( ) {

        return self::$writeAndCloseErrorMessage;
    }

    /**
     * Get the destroy error message.
     *
     * @access  public
     * @return  string
     */
    public static function getDestroyErrorMessage ( ) {

        return self::$destroyErrorMessage;
    }
}

}
