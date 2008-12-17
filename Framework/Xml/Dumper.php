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
 * @package     Hoa_Xml
 * @subpackage  Hoa_Xml_Dumper
 *
 */

/**
 * Hoa_Framework
 */
require_once 'Framework.php';

/**
 * Class Hoa_Xml_Dumper.
 *
 * Dump a Xml document from an array.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2008 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.2
 * @package     Hoa_Xml_Dumper
 * @subpackage  Hoa_Xml_Dumper
 */

class Hoa_Xml_Dumper {

    /**
     * The result.
     *
     * @var Hoa_Xml_Dumper string
     */
    protected $out = '';



    /**
     * __construct
     * Built Xml document from an array with recursivity.
     * 
     * @access  public
     * @param   parsed         array     Source.
     * @param   handlerAttr    array     Multi-tag attributs.
     * @return  string
     */
    public function __construct ( $parsed, $handlerAttr = null ) {

        static $i       = 0;
        static $lastTag = '';
        $intTag         = 0;
        $endArr         = 1;

        foreach($parsed as $tag => $data) {

            if(!eregi('-ATTR', $tag)) {

                $donot = !is_array($data) || !isset($data[0]);

                // multi-tag
                if(is_int($tag)) {
                    $intTag = $tag;
                    $tag = $lastTag;
                }

                // attributs and values
                $attr = '';
                if(isset($parsed[$tag.'-ATTR'])) {
                    foreach($parsed[$tag . '-ATTR'] as $a => $v)
                        $attr .= ' ' . $a . '="' . $v . '"';
                }
                elseif(is_array($handlerAttr) && isset($handlerAttr[$intTag])) {
                    foreach($handlerAttr[$intTag] as $a => $v)
                        $attr .= ' ' . $a . '="' . $v . '"';
                    unset($handlerAttr[$intTag]);
                }

                // start handler
                if($donot) {
                    if($i <= 0) $i = 0;
                    $this->out .= str_repeat('  ', $i) . '<' . $tag . $attr . '>';
                }
                else {
                    $handlerAttr = $parsed[$tag . '-ATTR'];
                    $i--;
                }


                if(is_array($data)) {
                    $lastTag = $tag;
                    $i++;
                    if($donot)
                        $this->out .= "\n";
                    $this->__construct($data, $handlerAttr);
                    $i--;
                }
                else {
                    $this->out .= $data;
                    $endArr = 0;
                }

                $i = $i <= 0 ? 0 : $i;

                // end handler
                if($donot)
                    $this->out .= ($endArr == 1 ? str_repeat('  ', $i) : '') . '</'.$tag.'>' . "\n";
            }
        }

        return $this->out . "\n";
    }

    /**
     * get
     * Get result.
     *
     * @access  public
     * @param   handlerTag  string    Global/First tag.
     * @param   encoding    string    Encoding type.
     * @param   hdrftr      bool      Add header and footer ?
     * @return  string
     */
    public function get ( $handlerTag = 'global', $encoding = 'utf-8', $hdrftr = true ) {

        if($hdrftr)
            $this->out  = '<?xml version="1.0" encoding="' . $encoding . '"?>' . "\n\n" .
                          '<' . $handlerTag . '>' . "\n\n" .
                          $this->out . "\n" .
                          '</' . $handlerTag . '>';

        return $this->out;
    }
}
