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
 * @subpackage  Hoa_Validate_GreaterThan
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
 * Class Hoa_Validate_GreaterThan.
 *
 * Validate a data, should be greater than …
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2010 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Validate
 * @subpackage  Hoa_Validate_GreaterThan
 */

class Hoa_Validate_GreaterThan extends Hoa_Validate_Abstract {

    /**
     * Is not greater than.
     *
     * @const string
     */
    const IS_NOT_GREATER_THAN = 'isNotGreaterThan';

    /**
     * Errors messages
     *
     * @var Hoa_Validate_Abstract array
     */
    protected $errors = array(
        self::IS_NOT_GREATER_THAN => 'Data could be greater than %s, given %s.'
    );

    /**
     * Needed arguments.
     *
     * @var Hoa_Validate_Abstract array
     */
    protected $arguments = array(
        'min' => 'specify the minimum value of the given number.'
    );



    /**
     * Check if a data is valid.
     *
     * @access  public
     * @param   string  $data    Data to valid.
     * @return  bool
     * @throw   Hoa_Validate_Exception
     */
    public function isValid ( $data = null ) {

        if($data <= $this->getValidatorArgument('min')) {

            $this->addOccuredError(
                self::IS_NOT_GREATER_THAN,
                array($this->getValidatorArgument('min'), $data)
            );

            return false;
        }

        return true;
    }
}
