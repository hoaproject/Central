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
 * @package     Hoa_Validate
 * @subpackage  Hoa_Validate_Abstract
 *
 */

/**
 * Hoa_Validate_Exception
 */
import('Validate.Exception');

/**
 * Class Hoa_Validate_Abstract.
 *
 * This class proposes some general methods to manipulate errors, error messages
 * etc.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2010 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Validate
 * @subpackage  Hoa_Validate_Abstract
 */

abstract class Hoa_Validate_Abstract {

    /**
     * Errors messages.
     *
     * @var Hoa_Validate_Abstract array
     */
    protected $errors = array();

    /**
     * Needed arguments.
     *
     * @var Hoa_Validate_Abstract array
     */
    protected $arguments = array();

    /**
     * The validator arguments.
     *
     * @var Hoa_Validate_Abstract array
     */
    private $validatorArguments = array();

    /**
     * Occured errors.
     *
     * @var Hoa_Validate_Abstract array
     */
    protected $occuredErrors = array();



    /**
     * Set the needed arguments.
     *
     * @access  public
     * @param   array   $args    The arguments of the validator.
     * @return  void
     * @throw   Hoa_Validate_Exception
     */
    public function __construct ( Array $args = array() ) {

        $this->setValidatorArguments($args);
    }

    /**
     * Set an error message.
     *
     * @access  public
     * @param   string  $type       Type of error (should be a constant).
     * @param   string  $message    Message.
     * @return  string
     * @throw   Hoa_Validate_Exception
     */
    public function setErrorMessage ( $type, $message ) {

        if(false === $this->errorExists($type))
            throw new Hoa_Validate_Exception(
                'Error %s does not exist.', 0, $type);

        $old                 = $this->errors[$type];
        $this->errors[$type] = $message;

        return $old;
    }

    /**
     * Add an occured error.
     *
     * @access  protected
     * @param   string     $type    Type of error.
     * @param   string     $data    Data.
     * @return  void
     * @throw   Hoa_Validate_Exception
     */
    protected function addOccuredError ( $type, $data ) {

        try {

            $message = $this->getErrorMessage($type, $data);
        }
        catch ( Hoa_Core_Exception_Validate $e ) {

            throw $e;
        }

        $this->occuredErrors[$type] = $message;
    }

    /**
     * Check if an error exists.
     *
     * @access  public
     * @param   string  $type    Type of error (should be a constant).
     * @return  bool
     */
    public function errorExists ( $type ) {

        return isset($this->errors[$type]);
    }

    /**
     * Check if one or more errors have occured.
     *
     * @access  public
     * @return  bool
     */
    public function hasError ( ) {

        return $this->getOccuredErrors() != array();
    }

    /**
     * Get error message.
     *
     * @access  protected
     * @param   string     $type    Type of error.
     * @param   array      $data    Data.
     * @return  string
     * @throw   Hoa_Validate_Exception
     */
    protected function getErrorMessage ( $type, $data = null ) {

        if(false === $this->errorExists($type))
            throw new Hoa_Validate_Exception(
                    'Error %s does not exist.', 1, $type);

        if(!is_array($data))
            $data = array($data);

        return vsprintf($this->errors[$type], $data);
    }

    /**
     * Get occured errors.
     *
     * @access  public
     * @return  array
     */
    public function getOccuredErrors ( ) {

        return $this->occuredErrors;
    }

    /**
     * Check arguments of the validator.
     *
     * @access  protected
     * @return  bool
     * @throw   Hoa_Validate_Exception
     */
    protected function _checkArguments ( ) {

        $needed = array();
        $args   = $this->getValidatorArguments();

        foreach($this->getArguments() as $name => $label)
            if(!isset($args[$name]))
                $needed[] = $name . ' : ' . $label;

        if(empty($needed))
            return true;

        $message = get_class($this) . ' needs parameters :' . "\n  - " .
            implode("\n" . '  - ', $needed);

        throw new Hoa_Validate_Exception($message, 2);

        return false;
    }

    /**
     * Set arguments of the validator.
     *
     * @access  private
     * @param   array   $args    Arguments of the validator.
     * @return  array
     * @throw   Hoa_Validate_Exception
     */
    private function setValidatorArguments ( Array $args = array() ) {

        $old                      = $this->validatorArguments;
        $this->validatorArguments = $args;

        $this->_checkArguments();

        return $old;
    }

    /**
     * Get an argument of the validator.
     *
     * @access  public
     * @param   string  $arg    The argument name.
     * @return  mixed
     * @throw   Hoa_Validate_Exception
     */
    public function getValidatorArgument ( $name ) {

        if(   null !== $this->validatorArguments[$name]
                && !isset($this->validatorArguments[$name]))
            throw new Hoa_Validate_Exception(
                    'The argument %s does not exit.', 3, $name);

        return $this->validatorArguments[$name];
    }

    /**
     * Get arguments of the validator.
     *
     * @access  public
     * @return  array
     */
    public function getValidatorArguments ( ) {

        return $this->validatorArguments;
    }

    /**
     * Get needed arguments.
     *
     * @access  protected
     * @return  array
     */
    protected function getArguments ( ) {

        return $this->arguments;
    }

    /**
     * Transform object to string.
     * It prints all errors (if occured).
     *
     * @access  public
     * @return  string
     */
    public function __toString ( ) {

        if(false === $this->hasError())
            return '';

        $out = '<ul>' . "\n";

        foreach($this->getOccuredErrors() as $id => $message)
            $out .= '  <li>' . $message . '</li>' . "\n";

        $out .= '</ul>';

        return $out;
    }

    /**
     * Force to implement isValid method.
     * Check if a data is valid.
     *
     * @access  public
     * @param   string  $data    Data to valid.
     * @return  bool
     */
    abstract public function isValid ( $data = null );
}
