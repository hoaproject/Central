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
 * @package     Hoa_Configuration
 * @subpackage  Hoa_Configuration_Xml
 *
 */

/**
 * Hoa_Framework
 */
require_once 'Framework.php';

/**
 * Hoa_Configuration
 */
import('Configuration.~');

/**
 * Hoa_Xml
 */
import('Xml.~');

/**
 * Class Hoa_Configuration_Xml.
 *
 * Manipulation configuration from an XML document.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2010 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Configuration
 * @subpackage  Hoa_Configuration_Xml
 */

class Hoa_Configuration_Xml extends Hoa_Configuration {

    /**
     * Filename
     *
     * @var Hoa_Configuration_Xml string
     */
    protected $_file = null;



    /**
     * Parse the XML document into a nested array and set a new configuration.
     *
     * @access  public
     * @param   string  $file        Filename (or filepath) to XML document.
     * @param   string  $encoding    Encoding of XML document.
     * @param   bool    $convert     Convert into a Hoa_StdClass object or not.
     * @return  void
     */
    public function __construct ( $file    = null, $encoding = 'UTF-8',
                                  $convert = parent::CONVERT_TO_OBJECT ) {

        if(null == $file)
            $file = $this->_file;

        if(!file_exists($file))
            throw new Hoa_Configuration_Exception('File %d does not exist.',
                                                  0, $file);

        parent::__construct($file, $convert);

        $xml           = new Hoa_Xml();
        $configuration = $xml->parse($file, 'FILE', $encoding);

        if(parent::CONVERT_TO_OBJECT === $convert)
            $this->transform($configuration);

        parent::set($configuration);
    }

    /**
     * Transform an array to a Hoa_StdClass object.
     *
     * @access  public
     * @param   array   $configuration    Configuration array.
     * @return  void
     */
    public function transform ( &$configuration = array() ) {

        foreach($configuration as $key => &$value) {

            if(is_int($key)) {

                $configuration['_' . $key] = $value;
                unset($configuration[$key]);
            }

            if(is_array($value))
                $this->transform($value);
        }
    }
}
