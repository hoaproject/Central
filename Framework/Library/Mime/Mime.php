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
 * @package     Hoa_Mime
 *
 */

/**
 * Hoa_Framework
 */
require_once 'Framework.php';

/**
 * Hoa_Mime_Exception
 */
import('Mime.Exception');

/**
 * Hoa_Mime_Parameter
 */
import('Mime.Parameter');

/**
 * Class Hoa_Mime.
 *
 * Manipulate Mime-type.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2009 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.2
 * @package     Hoa_Mime
 */

class Hoa_Mime {

    /**
     * Mime-type media.
     *
     * @var Hoa_Mime string
     */
    protected $media = '';

    /**
     * Mime-type subtype.
     *
     * @var Hoa_Mime string
     */
    protected $subType = '';

    /**
     * Mime-type parameter.
     *
     * @var Hoa_Mime array
     */
    protected $parameter = array();

    /**
     * Path to ini file.
     *
     * @var Hoa_Mime string
     */
    protected $path = '';

    /**
     * Parse ini file.
     *
     * @var Hoa_Mime array
     */
    protected $ini = array();

    /**
     * Parse ini file but in one level (not true argument).
     *
     * @var Hoa_Mime array
     */
    protected $iniOL = array();



    /**
     * __construct
     * Set $mime, and start parse.
     *
     * @access  public
     * @param   mime    string    Mime-type.
     * @param   path    string    Path to to ini file.
     * @return  array
     */
    public function __construct ( $mime = '', $path = '' ) {

        $this->path = empty($path) ? dirname(__FILE__).DS : $path;

        if(!empty($mime)) {
            $this->mime = $mime;
            return $this->parse($mime);
        }
    }

    /**
     * parse
     * Parse Mime-type.
     * Return media, subtype, and param into an array.
     *
     * @access  public
     * @param   mime    string    Mime-type.
     * @return  array
     * @throw   Hoa_Mime_Exception
     */
    public function parse ( $mime = '' ) {

        if(empty($mime))
            $mime = $this->mime;

        if(empty($mime))
            throw new Hoa_Mime_Exception('Mime-type could not be empty.', 0);

        $this->media   = $this->getMedia($mime);
        $this->subType = $this->getSubType($mime);

        if($this->hasParameter($mime))
            $this->parameter = $this->getParameter($mime);

        return array('media'   => $this->media,
                     'subtype' => $this->subType,
                     'param'   => $this->parameter);
    }

    /**
     * getMedia
     * Get Mime-type media.
     *
     * @access  public
     * @param   mime    string    Mime-type.
     * @return  string
     * @throw   Hoa_Mime_Exception
     */
    public function getMedia ( $mime = '' ) {

        if(empty($mime))
            $mime = $this->mime;

        if(empty($mime))
            throw new Hoa_Mime_Exception('Mime-type could not be empty.', 1);

        return strtolower(trim(substr($mime, 0, strpos($mime, '/'))));
    }

    /**
     * getSubType
     * Get Mime-type subType.
     *
     * @access  public
     * @param   mime    string    Mime-type.
     * @return  string
     * @throw   Hoa_Mime_Exception
     */
    public function getSubType ( $mime = '' ) {

        if(empty($mime))
            $mime = $this->mime;

        if(empty($mime))
            throw new Hoa_Mime_Exception('Mime-type could not be empty.', 2);

        if(strpos($mime, ';'))
            $out = substr($mime, strpos($mime, '/')+1,
                          strpos($mime, ';')-strpos($mime, '/')-1);
        else
            $out = substr($mime, strpos($mime, '/')+1);

        return strtolower(trim($out));
    }

    /**
     * hasParameter
     * Check if Mime-type has a parameter or not.
     *
     * @access  public
     * @param   mime    string    Mime-type.
     * @return  bool
     * @throw   Hoa_Mime_Exception
     */
    public function hasParameter ( $mime = '' ) {

        if(empty($mime))
            $mime = $this->mime;

        if(empty($mime))
            throw new Hoa_Mime_Exception('Mime-type could not be empty.', 3);

        return !(false === strpos($mime, ';'));
    }

    /**
     * getParameter
     * Get Mime-type parameter with Hoa_Mime_Parameter class.
     *
     * @access  public
     * @param   mime    string    Mime-type.
     * @return  array
     * @throw   Hoa_Mime_Exception
     */
    public function getParameter ( $mime = '' ) {

        if(empty($mime))
            $mime = $this->mime;

        if(empty($mime))
            throw new Hoa_Mime_Exception('Mime-type could not be empty.', 4);

        $mime      = substr($mime, strpos($mime, ';')+1);
        $parameter = new Hoa_Mime_Parameter($mime);

        return $parameter->getParameter();
    }

    /**
     * addParameter
     * Add a parameter to Mime-type.
     *
     * @access  public
     * @param   attr     string    Attribute.
     * @param   value    string    Value.
     * @param   comment  string    Comment.
     * @return  array
     * @throw   Hoa_Mime_Exception
     */
    public function addParameter ( $attr = '', $value = '', $comment = false) {

        if(empty($attr) || empty($value))
            throw new Hoa_Mime_Exception('Attribute and value could not be empties.', 5);

        return $this->parameter[$attr] = array('value' => $value, 'comment' => $comment);
    }

    /**
     * removeParameter
     * Remove a Mime-type parameter.
     *
     * @access  public
     * @param   param   string    Parameter to remove.
     * @return  void
     * @throw   Hoa_Mime_Exception
     */
    public function removeParameter ( $param = '' ) {

        if(empty($param))
            throw new Hoa_Mime_Exception('Parameter could not be empty.', 6);

        if(!array_key_exists($param, $this->parameter))
            throw new Hoa_Mime_Exception('Parameter does not exist in param[]', 7);

        unset($this->parameter[$param]);
    }

    /**
     * isExperimental
     * Check if Mime-type is experimental or not.
     *
     * @access  public
     * @param   mime    string    Mime-type.
     * @return  bool
     * @throw   Hoa_Mime_Exception
     */
    public function isExperimental ( $mime = '' ) {

        if(empty($mime))
            $mime = $this->mime;
    
        if(empty($mime))
            throw new Hoa_Mime_Exception('Mime-type could not be empty.', 8);

        return    substr($this->getMedia($mime)  , 0, 2) == 'x-'
               || substr($this->getSubType($mime), 0, 2) == 'x-';
    }

    /**
     * isVendor
     * Check if Mime-type is vendor or not.
     *
     * @access  public
     * @param   mime    string    Mime-type.
     * @throw   Hoa_Mime_Exception
     */
    public function isVendor ( $mime = '' ) {

        if(empty($mime))
            $mime = $this->mime;

        if(empty($mime))
            throw new Hoa_Mime_Exception('Mime-type could not be empty.', 9);

        return substr($this->getSubType, 0, 4) == 'vnd.';
    }

    /**
     * isWildCard
     * Check if Mime-type is wildcard or not.
     *
     * @access  public
     * @param   mime    string    Mime-type.
     * @return  bool
     * @throw   Hoa_Mime_Exception
     */
    public function isWildCard ( $mime = '' ) {

        if(empty($mime))
            $mime = $this->mime;

        if(empty($mime))
            throw new Hoa_Mime_Exception('Mime-type could not be empty.', 10);

        return $mime == '*/*' || $this->getSubType($mime) == '*';
    }

    /**
     * setIniPath
     * Set ini path.
     *
     * @access  public
     * @param   path     string    Path to ini file.
     * @param   prepend  bool      Add to currently path or not ?
     * @return  string
     * @throw   Hoa_Mime_Exception
     */
    public function setIniPath ( $path = '', $prepend = true ) {

        if(empty($path))
            throw new Hoa_Mime_Exception('Path could not be empty.', 11);

        if($prepend === false)
            $this->path  = $path;
        else
            $this->path .= $path;

        return $this->path;
    }

    /**
     * get
     * Get the new Mime-type.
     *
     * @access  public
     * @return  string
     */
    public function get ( ) {

        $out = $this->media . '/' . $this->subType;

        foreach($this->parameter as $attr => $value)
            $out .= '; ' . $attr . '=' . $value['value'] .
                    ($value['comment'] !== false
                        ? ' (' . $value['comment'] . ')'
                        : ''
                    );

        return $out;
    }

    /**
     * getList
     * Get a list of Mime-type media.
     *
     * @access  public
     * @return  array
     */
    public function getList ( ) {

        if(empty($this->ini))
            $this->ini = parse_ini_file($this->path . 'mime.types.ini', true);

        $out = array();
        foreach($this->ini as $media => $mimeType)
            $out[] = $media;

        return $out;
    }

    /**
     * getListOf
     * Get a list of Mime-type for a selected media.
     *
     * @access  public
     * @param   media   string    Mime-type media.
     * @return  array
     * @throw   Hoa_Mime_Exception
     */
    public function getListOf ( $media = '' ) {

        if(empty($media))
            throw new Hoa_Mime_Exception('Media could not be empty.', 12);

        if(empty($this->ini))
            $this->ini = parse_ini_file($this->path . 'mime.types.ini', true);

        if(!array_key_exists($media, $this->ini))
            throw new Hoa_Mime_Exception('Media does not exists in ini[].', 13);

        return $this->ini[$media];
    }

    /**
     * getFromExtension
     * Get a list of Mime-type for a selected media.
     * If occurence is not found, getFromExtension return false, else return mimeType.
     *
     * @access  public
     * @param   ext     string    Extension to find.
     * @param   media   string    Media group.
     * @return  array
     * @throw   Hoa_Mime_Exception
     */
    public function getFromExtension ( $ext = '', $media = '' ) {

        if(empty($ext))
            throw new Hoa_Mime_Exception('Extension could not be empty.', 14);

        if(empty($media)) {
            if(empty($this->iniOL))
                $this->iniOL = parse_ini_file(dirname(__FILE__) . DS . 'mime.types.ini');
            $ini = &$this->iniOL;
        }
        else
            $ini = $this->getListOf($media);

        $out = array_keys(preg_grep('#\b' . $ext . '\b#i', $ini));

        return $out[0];
    }
}
