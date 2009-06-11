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
 * @package     Hoa_Configuration
 *
 */

/**
 * Hoa_Framework
 */
require_once 'Framework.php';

/**
 * Hoa_Configuration_Exception
 */
import('Configuration.Exception');

/**
 * Hoa_StdClass
 */
import('StdClass.~');

/**
 * Class Hoa_Configuration.
 *
 * This class allows you to manipulate different source/type of configuration
 * files easily.
 * This class is inherited by all other classes, it must not be instantiated
 * directly but through its daugthers' constructor.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2008 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.3
 * @package     Hoa_Configuration
 */

class Hoa_Configuration {

    /**
     * Convert configuration to Hoa_StdClass or not.
     *
     * @const bool
     */
    const CONVERT_TO_OBJECT = true;
    const CONVERT_TO_ARRAY  = false;

    /**
     * All configurations.
     *
     * @var Hoa_Configuration array
     */
    protected $_configs = array();

    /**
     * Current configuration.
     *
     * @var Hoa_Configuration string
     */
    protected $_current = null;

    /**
     * Convert configuration to Hoa_StdClass or not.
     *
     * @var Hoa_Configuration bool
     */
    protected $_convert = null;



    /**
     * Set ID for current configuration, and prepare _configs array.
     *
     * @access  protected
     * @param   string  $id         ID to set current configuration.
     * @param   bool    $convert    Convert configuration to Hoa_StdClass or
     *                              not.
     * @return  void
     */
    protected function __construct ( $id      = null,
                                     $convert = self::CONVERT_TO_OBJECT ) {

        $this->setId($id);
        $_configs[$id] = array();
        $this->setConvert($convert);
    }

    /**
     * Set a new configuration.
     * If configuration does not already exist, configuration will be set.
     * Else, configuration will be reset.
     * Every configuration is transformed to Hoa_StdClass object if
     * $this->_convert is set to true.
     *
     * @access  protected
     * @param   array   $configuration    Configuration to set.
     * @param   string  $id               ID of configuration to set.
     *                                    If null, current ID will be selected.
     * @return  void
     * @throw   Hoa_Configuration_Exception
     */
    protected function set ( Array $configuration, $id = null ) {

        if(empty($configuration))
            return null;

        if(null === $id)
            $id = $this->getId();

        if(null === $id)
            throw new Hoa_Configuration_Exception(
                'An ID for current configuration must be specify.', 1);

        if(true === $this->_convert)
            $configuration = new Hoa_StdClass($configuration);

        $this->_configs[$id] = $configuration;
    }

    /**
     * Set current ID of a configuration.
     *
     * @access  protected
     * @param   string  $id    ID that will be current.
     * @return  mixed 
     */
    protected function setId ( $id ) {

        $old            = $this->_current;
        $this->_current = $id;

        return $old;
    }

    /**
     * Set convert.
     * Convert array to Hoa_StdClass or not.
     *
     * @access  public
     * @param   bool    $convert    Enable convert or not.
     * @return  mixed
     */
    public function setConvert ( $convert = self::CONVERT_TO_OBJECT ) {

        $old            = $this->_convert;
        $this->_convert = $convert;

        return $old;
    }

    /**
     * Get a specific configuration.
     * If none parameter is specified, it returns the current configuration.
     *
     * @access  public
     * @param   string  $variable    Variable.
     * @param   string  $id          ID of configuration.
     * @return  mixed
     * @throw   Hoa_Configuration_Exception
     */
    public function get ( $variable = null, $id = null ) {

        if(null === $id)
            $id = $this->getId();

        if(!isset($this->_configs[$id]))
            throw new Hoa_Configuration_Exception(
                'Configuration %s does not exist.', 2, $id);

        if(null === $variable)
            return $this->_configs[$id];

        if(!isset($this->_configs[$id]->$variable)
           || isset($this->_configs[$id][$variable]))
            throw new Hoa_Configuration_Exception(
                    'Variable %s is not found in %s configuration.',
                    3, array($variable, $id));

        return $this->_convert
                   ? $this->_configs[$id]->$variable
                   : $this->_configs[$id][$variable];
    }

    /**
     * Test if a level of configuration got a sub-level.
     * When we iterate a configuration, isRecursive returns true if we have a
     * sub-level for the current value or not.
     *
     * @access  public
     * @param   mixed   $variable   Variable to test.
     * @return  bool
     */
    public function isRecursive ( $handle ) {

        return $this->_convert
                   ? $handle instanceof Hoa_StdClass
                   : is_array($handle);
    }

    /**
     * Overload configuration getter.
     *
     * @access  public
     * @param   string  $variable    Variable name.
     * @return  mixed
     * @throw   Hoa_Configuration_Exception
     */
    public function __get ( $variable ) {

        if(self::CONVERT_TO_OBJECT === $this->_convert
           && isset($this->_configs[$this->getId()]->$variable))
            return $this->_configs[$this->getId()]->$variable;

        elseif(self::CONVERT_TO_ARRAY === $this->_convert
               && isset($this->_configs[$this->getId()][$variable]))
            return $this->_configs[$this->getId()][$variable];

        throw new Hoa_Configuration_Exception('Variable %s is not found.',
                4, $variable);
    }

    /**
     * Get ID of current configuration.
     *
     * @access  protected
     * @return  mixed
     */
    protected function getId ( ) {

        return $this->_current;
    }

    /**
     * Transform object to string.
     *
     * @access  public
     * @return  string
     */
    public function __toString ( ) {

        if(self::CONVERT_TO_OBJECT === $this->_convert)
            return $this->_configs[$this->getId()]->__toString();

        ob_start();
        print_r($this->_configs[$this->getId()]);
        $return = ob_get_contents();
        ob_end_clean();
        return $return;
    }
}
