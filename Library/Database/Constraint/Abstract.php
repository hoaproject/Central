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
 *
 *
 * @category    Framework
 * @package     Hoa_Database
 * @subpackage  Hoa_Database_Constraint_Abstract
 *
 */

/**
 * Hoa_Database_Constraint_Exception
 */
import('Database.Constraint.Exception');

/**
 * Class Hoa_Database_Constraint_Abstract.
 *
 * Abstract class for constraints.
 *
 * @author      Ivan Enderlin <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright © 2007-2011 Ivan Enderlin.
 * @license     New BSD License
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Database
 * @subpackage  Hoa_Database_Constraint_Abstract
 */

abstract class Hoa_Database_Constraint_Abstract {

    /**
     * Object that needs constraints.
     *
     * @var Hoa_Database_Constraint_Abstract object
     */
    protected $object      = null;

    /**
     * Constraints list.
     *
     * @var Hoa_Database_Constraint_Abstract array
     */
    protected $constraints = array();



    /**
     * Call self::setObject().
     *
     * @access  public
     * @param   mixed   $object         Object that needs constraints.
     * @param   array   $constraints    Object constraints.
     * @return  void
     * @throw   Hoa_Database_Constraint_Exception
     */
    public function __construct ( $object, Array $constraints = array() ) {

        $this->setObject($object);
        $this->setConstraints($constraints);

        return;
    }

    /**
     * Set the object that needs constraints.
     *
     * @access  protected
     * @param   object     $object    Object that needs constraints.
     * @return  object
     * @throw   Hoa_Database_Constraint_Exception
     */
    protected function setObject ( $object ) {

        if(!is_object($object))
            throw new Hoa_Database_Constraint_Exception(
                'Need an object for applying constraints ; given %s.',
                0, gettype($object));

        $old          = $this->object;
        $this->object = $object;

        return $old;
    }

    /**
     * Get the object that needs constraints.
     *
     * @access  protected
     * @return  object
     */
    protected function getObject ( ) {

        return $this->object;
    }

    /**
     * Set constraints.
     *
     * @access  protected
     * @param   array      $constraints    Array of constraints.
     * @return  array
     * @throw   Hoa_Database_Constraint_Exception
     */
    protected function setConstraints ( Array $constraints ) {

        foreach($constraints as $name => $value)
            $this->setConstraint($name, $value);

        return $this->constraints;
    }

    /**
     * Set a specific constraint.
     *
     * @access  protected
     * @param   string     $name     The constraint name.
     * @param   mixed      $value    The constraint value.
     * @return  mixed
     * @throw   Hoa_Database_Constraint_Exception
     */
    protected function setConstraint ( $name, $value ) {

        if(false === $this->constraintExists($name))
            throw new Hoa_Database_Constraint_Exception(
                'The constraint %s does not exist.', 1, $name);

        $old                      = $this->constraints[$name];
        $this->constraints[$name] = $value;

        return $old;
    }

    /**
     * Check if a constraint exists.
     *
     * @access  public
     * @param   string  $name    Constraint name.
     * @return  bool
     */
    public function constraintExists ( $name ) {

        return array_key_exists($name, $this->constraints);
    }

    /**
     * Get all constraints.
     *
     * @access  public
     * @return  array
     */
    public function getConstraints ( ) {

        return $this->constraints;
    }

    /**
     * Get a specific constraint.
     *
     * @access  public
     * @param   string  $name    The constraint name.
     * @return  mixed
     * @throw   Hoa_Database_Constraint_Exception
     */
    public function getConstraint ( $name ) {

        if(false === $this->constraintExists($name))
            throw new Hoa_Database_Constraint_Exception(
                'The constraint %s does not exist.', 2, $name);

        return $this->constraints[$name];
    }
}
