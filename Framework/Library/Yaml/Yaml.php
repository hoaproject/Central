<?php

/**
 * Hoa
 *
 *
 * @license
 *
 * GNU General Public License
 *
 * This file is part of HOA Open Accessibility.
 * Copyright (c) 2007, 2011 Ivan ENDERLIN. All rights reserved.
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
 * @package     Hoa_Yaml
 *
 */

/**
 * Hoa_Yaml_Exception
 */
import('Yaml.Exception');

/**
 * Hoa_Yaml_Node
 */
import('Yaml.Node');

/**
 * Hoa_Yaml_Parser
 */
import('Yaml.Parser');

/**
 * Hoa_Yaml_Dumper
 */
import('Yaml.Dumper');

/**
 * Class Hoa_Yaml.
 *
 * Yaml parser and dumper.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2011 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.2
 * @package     Hoa_Yaml
 */

class Hoa_Yaml {

    /**
     * parse
     * Alias of Hoa_Yaml_Parser.
     *
     * @access  public
     * @param   source  string    Yaml source.
     * @param   doc     int       Document number.
     * @return  array
     * @throw   Hoa_Yaml_Exception
     */
    public function parse ( $source = '', $doc = '*' ) {

        $parser = new Hoa_Yaml_Parser($source, $doc);

        if($doc === '*')
            return $parser->docs;
        else
            if(isset($parser->docs[$doc]))
                return $parser->docs[$doc];
            else
                throw new Hoa_Yaml_Exception('Document %d does not exist.', 3, $doc);
    }

    /**
     * dump
     * Alias of Hoa_Yaml_Dumper.
     *
     * @access  public
     * @param   source  array    Yaml source.
     * @return  string
     * @throw   Hoa_Yaml_Exception
     */
    public function dump ( Array $source = array() ) {

        if(!is_array($source))
            throw new Hoa_Yaml_Exception('Source could be an array.', 4);

        $nsource = array();
        foreach($source as $key => $value)
            array_push($nsource, $value);
        unset($source);

        $dumper = new Hoa_Yaml_Dumper($nsource);

        return $dumper->get();
    }

    /**
     * load
     * Load a YAML file.
     *
     * @access  public
     * @param   file    string    File to load.
     * @param   typeof  string    Type of source.
     * @return  bool
     * @throw   Hoa_Yaml_Exception
     */
    public function load ( $file = '', $typeof = 'FILE' ) {

        if($typeof == 'FILE') {
            if(file_exists($file)
               && preg_match('#ya?ml#i', substr($file, strrpos($file, '.')+1)))
                return file($file);
            else
                throw new Hoa_Yaml_Exception('%s could not be loaded.', 0, $file);
        }
        elseif($typeof == 'SOURCE')
            return explode("\n", $file);
    }
}
