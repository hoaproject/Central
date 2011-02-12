<?php

/**
 * Hoa Framework
 *
 *
 * @license
 *
 * GNU General Public License
 *
 * This file is part of Hoa Open Accessibility.
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
 *
 *
 * @category    Framework
 * @package     Hoa_Json
 *
 */

/**
 * Hoa_Json_Exception
 */
import('Json.Exception');

/**
 * Hoa_StdClass
 */
import('StdClass.~');

/**
 * Class Hoa_Json.
 *
 * Manipule JSON.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2011 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Json
 */

class Hoa_Json extends Hoa_StdClass {

    /**
     * Error: no error has occured.
     *
     * @const int
     */
    const ERROR_NONE           = 0;

    /**
     * Error: the maximum stack depth has been exceeded.
     *
     * @const int
     */
    const ERROR_DEPTH          = 1;

    /**
     * Error: state mismatch (in parser).
     * It is not officially in the documentation, but it is declared
     * since the revision number 271582.
     *
     * @const int
     */
    const ERROR_STATE_MISMATCH = 2;

    /**
     * Error: control character error, possibly incorrectly encoded.
     *
     * @const int
     */
    const ERROR_CTRL_CHAR      = 3;

    /**
     * Error: syntax error.
     *
     * @const int
     */
    const ERROR_SYNTAX         = 4;

    /**
     * Error: UTF-8 error.
     * It is not officially in the documentation, but it is declared
     * since the revision number 284625.
     *
     * @const int
     */
    const ERROR_UTF8           = 5;



    /**
     * Convert a JSON tree into a Hoa_StdClass class.
     *
     * @access  public
     * @param   string  $json     JSON string.
     * @param   int     $depth    Nesting limit.
     * @return  void
     * @throw   Hoa_Json_Exception
     */
    public function __construct ( $json = '', $depth = 512 ) {

        if(false === function_exists('json_decode'))
            if(PHP_VERSION_ID > 50200)
                throw new Hoa_Json_Exception(
                    'JSON extension is available since PHP 5.2.0.', 0);
            else
                throw new Hoa_Json_Exception(
                    'JSON extension is disabled.', 1);

        // Comments support.
        $json = preg_replace_callback(
            '#(?<!\\\)".*?(?<!\\\)"#',
            array($this, 'backslash'),
            $json
        );
        $json = preg_replace('#(//[^\n]+)|(/\*.*?\*/)#s', '', $json);

        if(PHP_VERSION_ID < 50300)
            $json = json_decode($json, true);
        else
            $json = json_decode($json, true, $depth);

        if(true === $this->hasError())
            throw new Hoa_Json_Exception(
                $this->getLastError(true), 2);

        parent::__construct($json);
    }

    /**
     * Usefull for removing comments in a JSON document.
     *
     * @access  private
     * @param   array    $matches    Matches (from a callback).
     * @return  string
     */
    private function backslash ( Array $matches ) {

        $handle = str_replace('//', '\\/\\/', $matches[0]);
        $handle = str_replace('/*', '/\*',    $handle);
        $handle = str_replace('*/', '*\/',    $handle);

        return $handle;
    }

    /**
     * Check if an error ocurred when parsing JSON string.
     *
     * @access  public
     * @return  bool
     */
    public function hasError ( ) {

        if(PHP_VERSION_ID < 50300)
            return false; // Cannot find if an error has occured.

        return json_last_error() != self::ERROR_NONE;
    }

    /**
     * Get last error message.
     *
     * @access  public
     * @param   bool    $verbose    Return the error code or error message.
     * @return  string
     */
    public function getLastError ( $verbose = false ) {

        if(PHP_VERSION_ID < 50300) {

            if(false === $verbose)
                return self::ERROR_NONE;

            return 'No error has occured.';
        }

        $message = null;
        $code    = self::ERROR_NONE;

        switch(json_last_error()) {

            case self::ERROR_NONE:
                $message = 'No error has occured.';
              break;

            case self::ERROR_DEPTH:
                $message = 'The maximum stack depth has been exceeded.';
                $code    = self::ERROR_DEPTH;
              break;

            case self::ERROR_STATE_MISMATCH:
                $message = 'State mismatch (in parser).';
                $code    = self::ERROR_STATE_MISMATCH;
              break;

            case self::ERROR_CTRL_CHAR:
                $message = 'Control character error, possibly incorrectly encoded.';
                $code    = self::ERROR_CTRL_CHAR;
              break;

            case self::ERROR_SYNTAX:
                $message = 'Syntax error.';
                $code    = self::ERROR_SYNTAX;
              break;

            case self::ERROR_UTF8:
                $message = 'UTF-8 error.';
                $code    = self::ERROR_UTF8;
        }

        if(true === $verbose)
            return $message;

        return $code;
    }

    /**
     * Overload the parent::toJson() method to produce a JSON string.
     *
     * @access  public
     * @param   mixed   $value    Value to encode in JSON.
     * @return  string
     * @throw   Hoa_Json_Exception
     */
    public function toJson ( $value = null ) {

        if(is_resource($value))
            throw new Hoa_Json_Exception(
                'JSON cannot encode a resource.', 0);

        if(null === $value)
            return parent::toJson();

        return json_encode($value);
    }

    /**
     * Overload the parent::__toString() method to produce a JSON string.
     *
     * @access  public
     * @return  string
     */
    public function __toString ( ) {

        return $this->toJson();
    }
}
