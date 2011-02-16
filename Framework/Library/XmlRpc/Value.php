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
 * @package     Hoa_XmlRpc
 * @subpackage  Hoa_XmlRpc_Value
 *
 */

/**
 * Class Hoa_XmlRpc_Value.
 *
 * Built parameters in Xml format for payload.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2011 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.2
 * @package     Hoa_XmlRpc_Value
 * @subpackage  Hoa_XmlRpc_Value
 */

class Hoa_XmlRpc_Value extends Hoa_XmlRpc {

    /**
     * Memory.
     *
     * @var Hoa_XmlRpc_Value array
     */
    protected $mem = array();



    /**
     * __construct
     * This method runs other methods according to $type.
     *
     * @access  public
     * @param   source  mixed    Message source.
     * @param   type    int      Type of source.
     * @return  object
     * @throw   Hoa_XmlRpc_Exception
     */
    public function __construct ( $source, $type = '' ) {

        if(empty($type))
            $type = 'string';

        $type = strtolower($type);

        if(array_key_exists($type, $this->metatype)) {
            switch($this->metatype[$type]) {
                case 1:
                    $out = $this->addScalar($source, $type);
                  break;
                case 2:
                    $out = $this->addArray($source);
                  break;
                case 3:
                    $out = $this->addStruct($source);
                  break;
                default:
                    throw new Hoa_XmlRpc_Exception('Type %s is not found', 0, $type);
            }
        }
        else
            throw new Hoa_XmlRpc_Exception('Data type %s does not exist', 1, $type);

        $this->mem[] = $out;
    }

    /**
     * get
     * Return first index (0) of mem.
     *
     * @access  public
     * @return  string
     */
    public function get ( ) {

        if(!isset($this->mem[0]))
            return false;

        return $this->mem[0];
    }

    /**
     * addScalar
     * This method cast source and return a string in Xml format,
     * for i4, int, boolean, double, string, datetime, and base64 type.
     *
     * @access  protected
     * @param   source     int, string etc.    Message source.
     * @param   type       int                 Type of source.
     * @return  string
     */
    protected function addScalar ( $source, $type ) {

        if($type     == 'base64')
            $source  = base64_encode($source);

        elseif($type == 'i4' || $type == 'int')
            $source  = (int) $source;

        elseif($type == 'boolean')
            $source  = (int) (boolean) $source;

        elseif($type == 'dateTime')
            $source  = $source;

        else
            settype($source, $type);


        $tag = $this->datatype[$type];

        return '<value><' . $tag . '>' . $source . '</' . $tag . '></value>';
    }

    /**
     * addArray
     * This method writes an array in Xml format.
     *
     * @access  protected
     * @param   source     array    Array to write.
     * @return  string
     */
    protected function addArray ( $source ) {

        $out      = '<array>' . "\n" . '<data>' . "\n";
        foreach((array)$source as $key => $value)
            $out .= $value->get() . "\n";
        $out     .= '</data>' . "\n" . '</array>';

        return $out;
    }

    /**
     * addStruct
     * This method writes a structure in XML format.
     *
     * @access  protected
     * @param   source     array    Structure to write.
     * @return  string
     */
    protected function addStruct ( $source ) {
 
        $out = '<struct>' . "\n";
        foreach((array)$source as $key => $value)
            $out .= '<member>' . "\n" . '<name>' . $key . '</name>' . "\n".
                    $value->get() . "\n" . '</member>' . "\n";
        $out .= '</struct>';

        return $out;
    }

    /**
     * decorate
     * This method complete values.
     *
     * @access  protected
     * @param   source     string    Values to complete.
     * @return  string
     */
    protected function decorate ( $source ) {

        return '<param><value>' . $source . '</value></param>';
    }
}
