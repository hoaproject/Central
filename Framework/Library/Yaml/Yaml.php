<?php

/**
 * Hoa
 *
 *
 * @license
 *
 * New BSD License
 *
 * Copyright (c) 2007-2011, Ivan Enderlin. All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *     * Redistributions of source code must retain the above copyright
 *       notice, this list of conditions and the following disclaimer.
 *     * Redistributions in binary form must reproduce the above copyright
 *       notice, this list of conditions and the following disclaimer in the
 *       documentation and/or other materials provided with the distribution.
 *     * Neither the name of the Hoa nor the names of its contributors may be
 *       used to endorse or promote products derived from this software without
 *       specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDERS AND CONTRIBUTORS BE
 * LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
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
