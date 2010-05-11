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
 * @package     Hoa_Test
 * @subpackage  Hoa_Test_Praspel_TypeDisjunction
 *
 */

/**
 * Hoa_Framework
 */
require_once 'Framework.php';

/**
 * Hoa_Test_Praspel_Exception
 */
import('Test.Praspel.Exception');

/**
 * Hoa_Test_Praspel_Type
 */
import('Test.Praspel.Type');

/**
 * Class Hoa_Test_Praspel_TypeDisjunction.
 *
 * .
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2009 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Test
 * @subpackage  Hoa_Test_Praspel_TypeDisjunction
 */

abstract class Hoa_Test_Praspel_TypeDisjunction {

    /**
     * Collection of types.
     *
     * @var Hoa_Test_Praspel_TypeDisjunction array
     */
    protected $_types  = array();

    /**
     * Current defining type.
     *
     * @var Hoa_Test_Praspel_Type object
     */
    protected $_type   = null;

    /**
     * Make a disjunction between two variables.
     *
     * @var Hoa_Test_Praspel_TypeDisjunction object
     */
    public $_or        = null;

    /**
     * 
     *
     * 
     */
    protected $_i      = 0;



    /**
     * Constructor.
     *
     * @access  public
     * @return  void
     */
    public function __construct ( ) {

        $this->_or = $this;

        return;
    }

    /**
     * Type the variable.
     *
     * @access  public
     * @param   string  $name    Type name.
     * @return  Hoa_Test_Praspel_Type
     */
    public function isTypedAs ( $name ) {

        return $this->_type = new Hoa_Test_Praspel_Type($this, $name);
    }

    /**
     * Close the current defining type.
     *
     * @access  public
     * @return  Hoa_Test_Praspel_TypeDisjunction
     */
    public function _ok ( ) {

        if(null === $this->_type)
            return $this;

        $type                                         = $this->_type->getType();
        $this->_type                                  = null;
        $this->_types[$this->_i++ . $type->getName()] = $type;

        return $this;
    }

    /**
     * Check if the variable has a specific declared type.
     *
     * @access  public
     * @param   string  $name    Type name.
     * @return  bool
     */
    public function isTypeDeclared ( $name ) {

        return true === array_key_exists($name, $this->_types);
    }

    /**
     * Get all types.
     *
     * @access  public
     * @return  array
     */
    public function getTypes ( ) {

        return $this->_types;
    }

    /**
     * Format arguments to produce a Praspel string.
     *
     * @access  private
     * @param   array    $arguments    Arguments to format.
     * @return  array
     */
    private function formatArgumentsAsPraspel ( Array $arguments ) {

        $out = array();

        foreach($arguments as $i => $argument) {

            if(is_bool($arguments))
                $out[] = (string) $argument;

            elseif(is_int($argument) || is_float($argument))
                $out[] = (string) $argument;

            elseif(is_string($argument)) 
                $out[] = '\'' . str_replace("'", "\\'", $argument) . '\'';

            elseif(is_array($argument)) {

                $handle = null;

                foreach($argument as $e => $domran) {

                    if(null !== $handle)
                        $handle .= ',';

                    if(!empty($domran[0]))
                        $handle .= "\n" . '               from ' .
                                   implode(
                                       ' or ',
                                       $this->formatArgumentsAsPraspel($domran[0])
                                   ) . ' ';

                    if(!empty($domran[1]))
                        $handle .= "\n" . '               to ' .
                                   implode(
                                       ' or ',
                                       $this->formatArgumentsAsPraspel($domran[1])
                                   );
                }

                $out[] = '[' . $handle . "\n" . '           ]';
            }
            elseif(is_object($argument)) {

                $out[] = $argument->getName() . '(' .
                         implode(
                            ', ',
                            $this->formatArgumentsAsPraspel($argument->getArguments())
                         ) . ')';
            }
        }

        return $out;
    }

    /**
     * Format arguments to produce a string.
     *
     * @access  private
     * @param   array    $arguments    Arguments to format.
     * @return  array
     */
    private function formatArguments ( Array $arguments, $f ) {

        static $d = 1;

        $out    = array();
        $spaces = str_repeat('    ', $d);

        foreach($arguments as $i => $argument) {

            if(is_bool($arguments))
                $out[] = $spaces . '    ->with(' . $argument . ')' . "\n";

            elseif(is_int($argument) || is_float($argument))
                $out[] = $spaces . '    ->with(' . $argument . ')' . "\n";

            elseif(is_string($argument)) 
                $out[] = $spaces . '    ->with(\'' .
                         str_replace("'", "\\'", $argument) . '\')' . "\n";

            elseif(is_array($argument)) {

                $handle = null;

                foreach($argument as $e => $domran) {

                    $d += 2;

                    if(!empty($domran[0]))
                        $handle .= $spaces . '        ->from()' . "\n" .
                                   implode(
                                       $spaces . '            ->_or' . "\n",
                                       $this->formatArguments($domran[0], true)
                                   );
                    else
                        $handle .= $spaces . '        ->from()' . "\n";

                    if(!empty($domran[1]))
                        $handle .= $spaces . '        ->to()' . "\n" .
                                   implode(
                                       $spaces . '            ->_or' . "\n",
                                       $this->formatArguments($domran[1], true)
                                   );

                    $d -= 2;
                }

                $out[] = $spaces . '    ->withArray()' . "\n" .
                         $handle .
                         $spaces . '            ->end()' . "\n";
            }
            elseif(is_object($argument)) {

                $d++;

                $out[] = $spaces . '    ->' .
                         (true === $f
                             ? 'isTypedAs'
                             : 'withType'
                         ) . '(\'' . $argument->getName() . '\')' . "\n" .
                         implode(
                            $spaces . '        ->_comma' . "\n",
                            $this->formatArguments($argument->getArguments(), false)
                         ) . $spaces . '        ->_ok()' . "\n";

                $d--;
            }
        }

        return $out;
    }

    /**
     * Transform this object model into Praspel.
     *
     * @access  public
     * @return  string
     */
    public function __toPraspel ( ) {

        return $this->getName() . ': ' .
               implode(' or ', $this->formatArgumentsAsPraspel($this->getTypes()));
    }

    /**
     * Transform this object model into a string.
     *
     * @access  public
     * @return  string
     */
    public function __toString ( ) {

        return '    ->variable(\'' . $this->getName() . '\')' . "\n" .
               implode(
                   '        ->_or' . "\n",
                   $this->formatArguments($this->getTypes(), true)
               );
    }
}
