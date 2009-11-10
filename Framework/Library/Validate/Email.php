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
 * Copyright (c) 2007, 2009 Ivan ENDERLIN. All rights reserved.
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
 * @subpackage  Hoa_Validate_Email
 *
 */

/**
 * Hoa_Framework
 */
require_once 'Framework.php';

/**
 * Hoa_Validate_Abstract
 */
import('Validate.Abstract');

/**
 * Hoa_Mail_Rfc882
 */
import('Mail.Rfc882');

/**
 * Class Hoa_Validate_Email.
 *
 * Validate a data, should be a correct email address.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2009 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Validate
 * @subpackage  Hoa_Validate_Email
 */

class Hoa_Validate_Email extends Hoa_Validate_Abstract {

    /**
     * Is not correct email address.
     *
     * @const string
     */
    const IS_NOT_EMAIL = 'isNotEmail';

    /**
     * Errors messages
     *
     * @var Hoa_Validate_Abstract array
     */
    protected $errors = array(
        self::IS_NOT_EMAIL => '%s is not a correct email address.'
    );

    /**
     * Needed arguments.
     *
     * @var Hoa_Validate_Abstract array
     */
    protected $arguments = array(
        'level'   => 'specify the level of validation ; please look the Hoa_Mail_Rfc882.',
        'timeout' => 'specify the timeout of the connection.'
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

        try {

            $rfc882 = new Hoa_Mail_Rfc882(
                $data,
                $this->getValidatorArgument('level'),
                $this->getValidatorArgument('timeout')
            );
        }
        catch ( Hoa_Mail_Exception $e ) {

            $this->addOccuredError(self::IS_NOT_EMAIL, $data);

            return false;
        }

        $result = $rfc882->getResult();

        if(false === $result)
            $this->addOccuredError(self::IS_NOT_EMAIL, $data);

        return $result;
    }
}
