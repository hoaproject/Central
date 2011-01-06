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
 *
 */

/**
 * Hoa_Validate_Exception
 */
import('Validate.Exception');

/**
 * Hoa_Validate_Abstract
 */
import('Validate.Abstract');

/**
 * Hoa_Factory
 */
import('Factory.~');

/**
 * Class Hoa_Validate.
 *
 * Build a strack of validator.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2010 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Validate
 */

class Hoa_Validate extends Hoa_Validate_Abstract {

    /**
     * Collection of validators.
     *
     * @var Hoa_Validate array
     */
    protected $validators = array();


    /**
     * Add a validator.
     *
     * @access  public
     * @param   mixed   $validators    The validators.
     * @return  void
     * @throw   Hoa_Validate_Exception
     */
    public function addValidator ( $validators ) {

        if(!is_array($validators))
            $validators = array($validators => array());

        foreach($validators as $validator => $arguments) {

            if(is_int($validator)) {

                $validator = $arguments;
                $arguments = array();
            }

            if(is_array($validator)) {

                $arguments = current($validator);
                $validator = key($validator);
            }

            if(!is_array($arguments))
                $arguments = array($arguments);

            $arguments = array($arguments);

            $validator = Hoa_Factory::get('Validate', $validator, $arguments);

            if(!($validator instanceof Hoa_Validate_Abstract))
                throw new Hoa_Validate_Exception(
                    'The validator %s does not extend Hoa_Validate_Abstract.',
                    0, get_class($validator));

            if($this->validatorExists(get_class($validator)))
                throw new Hoa_Validate_Exception(
                    'The validator %s already exists.',
                    1, get_class($validator));

            $this->validators[get_class($validator)] = $validator;
        }
    }

    /**
     * Check if a validator already exists or not.
     *
     * @access  public
     * @param   string  $validator    The validator.
     * @return  bool
     */
    public function validatorExists ( $validator ) {

        return isset($this->validators[$validator]);
    }

    /**
     * Get validators.
     *
     * @access  protected
     * @return  array
     */
    protected function getValidators ( ) {

        return $this->validators;
    }

    /**
     * Check if a data is valid.
     *
     * @access  public
     * @param   string  $data    Data to valid.
     * @return  bool
     */
    public function isValid ( $data = null ) {

        $out = true;

        foreach($this->getValidators() as $name => $validator) {

            $out = $out && $validator->isValid($data);

            if($validator->hasError())
                $this->occuredErrors += $validator->getOccuredErrors();
        }

        return $out;
    }
}
