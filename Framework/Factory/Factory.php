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
 * Copyright (c) 2007, 2008 Ivan ENDERLIN. All rights reserved.
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
 * @package     Hoa_Factory
 *
 */

/**
 * Hoa_Framework
 */
require_once 'Framework.php';

/**
 * Hoa_Factory_Exception
 */
import('Factory.Exception');

/**
 * Class Hoa_Factory.
 *
 * General factory pattern.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2008 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.2
 * @package     Hoa_Factory
 */

class Hoa_Factory {

    /**
     * Get an instance of a classe.
     *
     * @access  public
     * @param   string  $package       Package name and sub-directory if necessary.
     * @param   mixed   $object        Object.
     * @param   array   $parameters    Constructor parameters.
     * @param   string  $method        Method to call when object is created.
     * @param   bool    $implements    Check if an interface is implemented.
     * @return  object
     * @throw   Hoa_Factory_Exception
     */
    public static function get ( $package          = ''     , $object = null,
                                 Array $parameters = array(), $method = '__construct',
                                 $implements       = false ) {

        if($object === null || empty($object))
            throw new Hoa_Factory_Exception('Object could not be empty or null.', 0);

        if(is_string($object)) {

            $object    = ucfirst($object);
            $class     = 'Hoa_' . str_replace('.', '_', $package) . '_' . $object;
            $interface = 'Hoa_' . str_replace('.', '_', $package) . '_Interface';

            import($package . '.' . $object);

            try {

                $reflection = new ReflectionClass($class);

                if(false !== $implements) {

                    if($reflection->implementsInterface($interface)) {

                        if($reflection->hasMethod($method)) {

                            if($method == '__construct')
                                $object = $reflection->newInstanceArgs($parameters);
                            else {

                                $object = $reflection->newInstance();
                                $object = call_user_func_array(
                                              array(&$object, $method),
                                              $parameters);
                            }
                        }
                        else
                            $object = $reflection->newInstance();
                    }
                }
                else {

                    if($reflection->hasMethod($method)) {

                        if($method == '__construct')
                            $object = $reflection->newInstanceArgs($parameters);
                        else {

                            $object = $reflection->newInstance();
                            $object = call_user_func_array(
                                          array(&$object, $method),
                                          $parameters);
                        }
                    }
                    else
                        $object = $reflection->newInstance();
                }
            }
            catch ( ReflectionException $e ) {
                throw new Hoa_Factory_Exception($e->getMessage(), $e->getCode());
            }

            return $object;
        }

        elseif(is_object($object)) {

            $reflection = new ReflectionClass(get_class($object));

            if(false !== $implements) {
                if($reflection->implementsInterface($implements))
                    return $object;

                else
                    throw new Hoa_Factory_Exception(
                        'Filter object %s must implements Hoa_Filter_Interface',
                        1, get_class($object));
            }

            return $object;
        }
    }
}
