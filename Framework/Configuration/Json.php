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
 * @subpackage  Hoa_Configuration_Json
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
 * Hoa_Json
 */
import('Json.~');

/**
 * Class Hoa_Configuration_Json.
 *
 * Manipulate configuration from a Json document.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2008 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Configuration
 * @subpackage  Hoa_Configuration_Json
 */

class Hoa_Configuration_Json extends Hoa_Configuration {

    /**
     * Filename
     *
     * @var Hoa_Configuration_Json string
     */
    protected $_file = null;



    /**
     * Parse the JSON document into a nested array and set a new configuration.
     *
     * @access  public
     * @param   string  $file       Filename (ou filepath) of YAML document.
     * @param   string  $convert    Convert into a Hoa_StdClass object or not.
     */
    public function __construct ( $file = null, $convert = parent::CONVERT_TO_OBJECT ) {

        if(null == $file)
            $file = $this->_file;

        if(!file_exists($file))
            throw new Hoa_Configuration_Exception('File %d does not exist.',
                                                  0, $file);

        parent::__construct($file, $convert);

        $yaml = new Hoa_Json(file_get_contents($file));

        if(parent::CONVERT_TO_ARRAY === $convert)
            $configuration = $yaml->toArray();
        else
            $configuration = $yaml;

        parent::set($configuration);
    }
}
