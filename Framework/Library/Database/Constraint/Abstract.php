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
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2010 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
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
