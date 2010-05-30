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
 * @package     Hoa_Controller
 * @subpackage  Hoa_Controller_Response_Standard
 *
 */

/**
 * Hoa_Framework
 */
require_once 'Framework.php';

/**
 * Hoa_Controller_Exception_ResponsePointerAlreadyExists
 */
import('Controller.Exception.ResponsePointerAlreadyExists');

/**
 * Class Hoa_Controller_Response_Standard.
 *
 * Every result from primary or secondary controllers are buffered in response
 * object. We can manipulate different responses, and headers.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2010 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Controller
 * @subpackage  Hoa_Controller_Response_Standard
 */

class Hoa_Controller_Response_Standard {

    /**
     * Output.
     *
     * @var Hoa_Controller_Response_Standard array
     */
    protected $output  = array();

    /**
     * Output pointer.
     *
     * @var Hoa_Controller_Response_Standard string
     */
    protected $pointer = null;

    /**
     * Headers list.
     *
     * @var Hoa_Controller_Response_Standard array
     */
    protected $headers = array();



    /**
     * Create a new output (but not delete previous output).
     *
     * @access  public
     * @param   string  $pointer    Name of pointer.
     * @return  Hoa_Controller_Response_Standard
     * @throw   Hoa_Controller_Exception_ResponsePointerAlreadyExists
     * @throw   Hoa_Controller_Exception
     */
    public function newOutput ( $pointer = null ) {

        if(isset($this->output[$pointer]))
            throw new Hoa_Controller_Exception_ResponsePointerAlreadyExists(
                'Pointer %s already exists into output array.',
                0, $pointer);

        if(null === $pointer) {

            $this->output[] = null;
            end($this->output);
            $pointer = key($this->output);
        }
        else
            $this->output[$pointer] = null;

        $this->pointer = $pointer;

        return $this;
    }

    /**
     * Append data to output.
     *
     * @access  public
     * @param   string  $append     Data to append.
     * @param   string  $pointer    Pointer of output.
     * @return  string
     */
    public function appendOutput ( $append = null, $pointer = null ) {

        if(null === $pointer)
            $pointer = $this->pointer;

        $this->output[$pointer] .= $append;

        return $this->output[$pointer];
    }

    /**
     * Clear output.
     * If entry does not exist, entry will be create and set to null.
     *
     * @access  public
     * @param   string  $pointer    Pointer of output.
     * @return  bool
     */
    public function clearOutput ( $pointer = null ) {

        if(null === $pointer)
            $pointer = $this->pointer;

        $this->output[$pointer] = null;
    }

    /**
     * Return output.
     *
     * @access  public
     * @param   bool    $print      Print or not output.
     * @param   string  $pointer    Pointer of output.
     * @return  mixed
     */
    public function output ( $print = true, $pointer = '*') {

        switch($pointer) {

            case null:
                $out = $this->output[$this->pointer];
              break;

            case '*':
                $out = $this->output;
              break;

            default:
                $out = $this->output[$pointer];
        }

        if(true !== $print)
            return $out;

        foreach((array) $out as $key => $value)
            echo $value;
    }

    /**
     * Set a new header or reset an existing header.
     *
     * @access  public
     * @param   string  $header       Header.
     * @param   string  $value        Value of header.
     * @param   bool    $overwrite    Overwrite if header already exists.
     * @return  string
     */
    public function setHeader ( $header, $value = '', $overwrite = false ) {

        if(true !== $overwrite && isset($this->headers[$header]))
            return $this;

        $this->headers[$header] = $value;

        return $this;
    }

    /**
     * Send headers.
     *
     * @access  public
     * @param   bool    $unset   Unset each header after sending ?
     * @return  void
     */
    public function sendHeaders ( $unset = true ) {

        foreach($this->headers as $header => $value) {

            header($header . (!empty($value) ? ': ' . $value : ''));

            if(true === $unset)
                unset($this->headers[$header]);
        }
    }

    /**
     * Set output pointer name.
     *
     * @access  public
     * @param   string  $pointer    Pointer name.
     * @return  mixed
     */
    public function setPointer ( $pointer ) {

        $old           = $this->pointer;
        $this->pointer = $pointer;
    }

    /**
     * Get current pointer.
     *
     * @access  public
     * @return  mixed
     */
    public function getPointer ( ) {

        return $this->pointer;
    }
}
