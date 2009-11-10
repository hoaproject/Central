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
 * @package     Hoa_Configuration
 * @subpackage  Hoa_Configuration_Ini
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
 * Class Hoa_Configuration_Ini.
 *
 * Manipulate configuration from an INI file.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2009 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Configuration
 * @subpackage  Hoa_Configuration_Ini
 */

class Hoa_Configuration_Ini extends Hoa_Configuration {

    /**
     * File to parse.
     *
     * @var Hoa_Configuration_Ini string
     */
    protected $_file     = null;

    /**
     * Category to select.
     *
     * @var Hoa_Configuration_Ini string
     */
    protected $_category = null;



    /**
     * Parse .ini file and convert it into a Hoa_StdClass 
     * object.
     *
     * @access  public
     * @param   string  $file        Filename.
     * @param   string  $category    Category.
     * @param   bool    $convert     Convert configuration to Hod_StdClass or
     *                               not.
     * @return  void
     * @throw   Hoa_Configuration_Exception
     */
    public function __construct ( $file    = '', $category = null,
                                  $convert = parent::CONVERT_TO_OBJECT ) {

        if(null !== $this->_file)
            $file = $this->_file;

        if(null !== $this->_category)
            $category = $this->_category;

        if(!file_exists($file))
            throw new Hoa_Configuration_Exception('File %s is not found.',
                0, $file);

        parent::__construct($file, $convert);

        $parse = parse_ini_file($file, isset($category));

        if(isset($category)) {

            if(!isset($parse[$category]))
                throw new Hoa_Configuration_Exception(
                    'Category %s is not found in %s file.',
                    1, array($category, $file));

            $array = $parse[$category];
        }
        else
            $array = $parse;

        $out = array();

        foreach($array as $key => $value) {

            $explode = preg_split('#((?<!\\\)\.)#',
                                  $key, -1, PREG_SPLIT_NO_EMPTY);
            $i       = count($explode) - 1;

            do {

                if(is_string($explode[$i]))
                    $explode[$i] = str_replace('\.', '.', $explode[$i]);

                $newArray = array($explode[$i] => $value);
                $value    = $newArray;
                $i--;

            } while($i >= 0);

            $out = $this->merge($out, $newArray);
        }

        $this->set($out, $file);
    }

    /**
     * Merge multi-dimensionnal arrays.
     *
     * @access  protected
     * @param   array      $a    First array.
     * @param   array      $b    Second array.
     * @return  array
     */
    protected function merge ( Array $a, Array $b ) {

        foreach($b as $key => $value) {

            if(!isset($a[$key]))
                $a[$key] = array();

            if(is_array($value)) {

                if(!is_array($a[$key]))
                    $a[$key]= array();

                $a[$key] = $this->merge($a[$key], $value);
            }
            else
                $a[$key] = $value;
        }

        return $a;
    }

    /**
     * Get current category.
     *
     * @access  public
     * @return  string
     */
    public function currentCategory ( ) {

        return $this->_category;
    }
}
