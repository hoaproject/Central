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
 * @package     Hoa_Validate
 * @subpackage  Hoa_Validate_Array
 *
 */

/**
 * Hoa_Core
 */
require_once 'Core.php';

/**
 * Hoa_Validate_Abstract
 */
import('Validate.Abstract');

/**
 * Hoa_Validate
 */
import('Validate.~');

/**
 * Class Hoa_Validate_Array.
 *
 * Apply validators on an array.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2010 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Validate
 * @subpackage  Hoa_Validate_Array
 */

class Hoa_Validate_Array extends Hoa_Validate_Abstract {

    /**
     * Needed arguments.
     *
     * @var Hoa_Validate_Abstract array
     */
    protected $arguments = array(
        'validators' => 'specify an associative array of key => validator to apply.'
    );



    /**
     * Apply validators.
     * In this case, the data must be an array. If it is not an array, the
     * data will be convert to an array.
     *
     * @access  public
     * @param   string  $data    Data to valid.
     * @return  array
     * @throw   Hoa_Validate_Exception
     */
    public function isValid ( $data = null ) {

        if(!is_array($data))
            $data = array($data);

        $validators    = $this->getValidatorArgument('validators');
        $lastValidator = current($validators);

        foreach($validators as $key => &$validator) {

            if($validator === null)
                $validator     = $lastValidator;
            else
                $lastValidator = $validators[$key];
        }

        foreach($data as $key => &$value) {

            $add   = new Hoa_Validate();

            if(!isset($validators[$key]))
                if(isset($validators['*']))
                    $add->addValidator($validators['*']);
                else
                    continue;
            else
                $add->addValidator($validators[$key]);

            $add->isValid($value);

            if($add->hasError())
                $this->occuredErrors[] = $add->getOccuredErrors();

            $add   = null;
        }

        return $data;
    }
}
