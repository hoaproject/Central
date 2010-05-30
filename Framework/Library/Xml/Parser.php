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
 * @package     Hoa_Xml
 * @subpackage  Hoa_Xml_Parser
 *
 */

/**
 * Hoa_Framework
 */
require_once 'Framework.php';

/**
 * Class Hoa_Xml_Parser.
 *
 * Parse a Xml document into a nested array.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2010 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.6
 * @package     Hoa_Xml
 * @subpackage  Hoa_Xml_Parser
 */

class Hoa_Xml_Parser {

    /**
     * Xml parser container.
     *
     * @var Hoa_Xml_Parser resource
     */
    private $parser = null;

    /**
     * Parse result.
     *
     * @var Hoa_Xml_Parser array
     */
    private $out = array();

    /**
     * Contain the overlap tag temporarily.
     *
     * @var Hoa_Xml_Parser array
     */
    private $track = array();

    /**
     * Current tag level.
     *
     * @var Hoa_Xml_Parser string
     */
    private $tmpLevel = '';

    /**
     * Attribute of current tag.
     *
     * @var Hoa_Xml_Parser array
     */
    private $tmpAttrLevel = array();

    /**
     * Final key.
     *
     * @var Hoa_Xml_Parser string
     */
    private $_finalKey = null;



    /**
     * __construct
     * Set the parser Xml and theses options.
     * Xml file could be a string, a file, or CURL.
     * When the source is loaded, we run the parser.
     * After, we clean all the memory and variables,
     * and return the result in an array.
     *
     * @access  public
     * @param   src       string    Source
     * @param   typeof    string    Source type : NULL, FILE, CURL.
     * @param   encoding  string    Encoding type.
     * @return  array
     * @throw   Hoa_Xml_Exception
     */
    public function __construct ( $src, $typeof = 'FILE', $encoding = 'UTF-8' ) {

        // ini;
        // (re)set array;
        $this->out    = array();
        $this->parser = xml_parser_create();

        xml_parser_set_option($this->parser, XML_OPTION_CASE_FOLDING, 0);
        xml_parser_set_option($this->parser, XML_OPTION_TARGET_ENCODING, $encoding);

        xml_set_object($this->parser, $this);
        xml_set_element_handler($this->parser, 'startHandler', 'endHandler');
        xml_set_character_data_handler($this->parser, 'contentHandler');

        if(empty($src))
            throw new Hoa_Xml_Exception('Source could not be empty.', 0);

        // format source;
        if($typeof == NULL)
            $data = $src;
        elseif($typeof == 'FILE') {
            $fop  = fopen($src, 'r');
            $data = null;
            while(!feof($fop))
                $data .= fread($fop, 1024);
            fclose($fop);
        }
        elseif($typeof == 'CURL') {
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $src);
            curl_setopt($curl, CURLOPT_HEADER, 0);
            $data = curl_exec($curl);
            curl_close($curl);
        }
        else
            throw new Hoa_Xml_Exception('Xml parser need data.', 1);

        // parse $data;
        $parse = xml_parse($this->parser, $data);
        if(!$parse)
            throw new Hoa_Xml_Exception('XML Error : %s at line %d.', 2,
                array(xml_error_string(xml_get_error_code($this->parser)),
                      xml_get_current_line_number($this->parser)));

        // destroy parser;
        xml_parser_free($this->parser);

        // unset extra vars;
        unset($data,
              $this->track,
              $this->tmpLevel);

        $key = key($this->out[0]);
        $this->_finalKey = $key;

        // remove pointer;
        $this->clean($this->out);

        // remove global tag and return the result;
        return $this->out[0][$this->_finalKey];
    }

    /**
     * startHandler
     * Manage the open tag, and these attributs by callback.
     * The purpose is to create a pointer : {{int ptr}}.
     * If the pointer exists, we have a multi-tag situation.
     * Tags name  is stocked like : '<tag>'
     * Attributes is stocked like : '<tag>-ATTR'
     * Return true but built $this->out.
     *
     * @access  protected
     * @param   parser  resource    Parser resource.
     * @param   tag     string      Tag name.
     * @param   attr    array       Attribut.
     * @return  bool
     */
    protected function startHandler ( $parser, $tag, $attr ) {

        // built $this->track;
        $this->track[] = $tag;
        // place pointer to the end;
        end($this->track);
        // temp level;
        $this->tmpLevel = key($this->track);

        // built attrLevel into $this->tmpAttrLevel
        if(isset($this->tmpAttrLevel[$this->tmpLevel]['attrLevel']))
            $this->tmpAttrLevel[$this->tmpLevel]['attrLevel']++;

        // built $this->out;
        if(!isset($this->out[key($this->track)][$tag])) {
            $this->out[key($this->track)][$tag] = '{{' . key($this->track) . '}}';

            if(!isset($this->tmpAttrLevel[$this->tmpLevel]['attrLevel']))
                $this->tmpAttrLevel[$this->tmpLevel]['attrLevel'] = 0;				
        }

        // built attributs;
        if(!empty($attr)) {

            $this->tmpAttrLevel[$this->tmpLevel][] = $this->tmpAttrLevel[$this->tmpLevel]['attrLevel'];
            end($this->tmpAttrLevel[$this->tmpLevel]);

            // it's the first attribut;
            if(!isset($this->out[key($this->track)][$tag . '-ATTR']))
                    $this->out[key($this->track)][$tag . '-ATTR'] = $attr;

            // or it's not the first;
            else {
                // so it's the second;
                if(!prev($this->tmpAttrLevel[$this->tmpLevel])) {
                    $this->out[key($this->track)][$tag . '-ATTR'] = array(
                        current($this->tmpAttrLevel[$this->tmpLevel]) => $this->out[key($this->track)][$tag.'-ATTR'],
                        next($this->tmpAttrLevel[$this->tmpLevel])    => $attr
                    );
                }
                // or one other;
                else
                    $this->out[key($this->track)][$tag . '-ATTR'][$this->tmpAttrLevel[$this->tmpLevel]['attrLevel']] = $attr;
            }
        }

        return true;
    }

    /**
     * contentHandler
     * Detect the pointer, or the multi-tag by callback.
     * If we have a pointer, the method replaces this pointer by the content.
     * Else we have a multi-tag, the method add a element to this array.
     * Return true but built $this->out.
     *
     * @access  protected
     * @param   parser          resource    Parser resource.
     * @param   contentHandler  string      Tag content.
     * @return  bool
     */
    protected function contentHandler ( $parser, $contentHandler ) {

        // remove all spaces;
        if(!preg_match('#^\s*$#', $contentHandler)) {

            // $contentHandler is a string;
            if(is_string($this->out[key($this->track)][current($this->track)])) {

                // then $contentHandler is a pointer : {{int ptr}}     case 1;
                if(preg_match('#{{([0-9]+)}}#', $this->out[key($this->track)][current($this->track)]))
                    $this->out[key($this->track)][current($this->track)] = $contentHandler;

                // or then $contentHandler is a multi-tag content      case 2;
                else {
                    /*
                    $this->out[key($this->track)][current($this->track)] = array(
                        0 => $this->out[key($this->track)][current($this->track)],
                        1 => $contentHandler
                    );*/
                    // fix a bug with splitted encoding.
                    $this->out[key($this->track)][current($this->track)] .= $contentHandler;
                }
            }
            // or $contentHandler is an array;
            else {

                // then $contentHandler is the multi-tag array         case 1;
                if(isset($this->out[key($this->track)][current($this->track)][0]))
                    $this->out[key($this->track)][current($this->track)][] = $contentHandler;

                // or then $contentHandler is a node-tag               case 2;
                else
                    $this->out[key($this->track)][current($this->track)] = array(
                        0 => $this->out[key($this->track)][current($this->track)],
                        1 => $contentHandler
                    );
            }

        }

        return true;
    }

    /**
     * endHandler
     * Detect the last pointer by callback.
     * Move the last tags block up.
     * And reset some temp variables.
     * Return true but built $this->out.
     *
     * @access  protected
     * @param   parser  resource    Parser resource.
     * @param   tag     string      Tag name.
     * @return  bool
     */
    protected function endHandler ( $parser, $tag ) {

        // if level--;
        if(key($this->track) == $this->tmpLevel-1) {
            // search up tag;
            // use array_keys if an empty tag exists (taking the last tag);

            // if it's a normal framaset;
            $keyBack = array_keys($this->out[key($this->track)], '{{'.key($this->track).'}}');
            $count = count($keyBack);

            if($count != 0) {
                $keyBack = $keyBack{$count-1};
                // move this level up;
                $this->out[key($this->track)][$keyBack] = $this->out[key($this->track)+1];
            }

            // if we have a multi-tag framaset ($count == 0);
            else {
                // if place is set;
                if(isset($this->out[key($this->track)][current($this->track)][0])) {

                    // if it's a string, we built an array;
                    if(is_string($this->out[key($this->track)][current($this->track)]))
                        $this->out[key($this->track)][current($this->track)] = array(
                            0 => $this->out[key($this->track)][current($this->track)],
                            1 => $this->out[key($this->track)+1]
                        );

                    // else add an index into the array;
                    else
                        $this->out[key($this->track)][current($this->track)][] = $this->out[key($this->track)+1];
                }
                // else set the place;
                else
                    $this->out[key($this->track)][current($this->track)] = array(
                        0 => $this->out[key($this->track)][current($this->track)],
                        1 => $this->out[key($this->track)+1]
                    );
            }

            // kick $this->out level out;
            array_pop($this->out);
            end($this->out);
        }

        // re-temp level;
        $this->tmpLevel = key($this->track);

        while(isset($this->tmpAttrLevel[$this->tmpLevel+1]))
            array_pop($this->tmpAttrLevel);

        // kick $this->track level out;
        array_pop($this->track);
        end($this->track);

        return true;
    }

    /**
     * getResult
     * Return the final array.
     *
     * @access  public
     * @return  array
     */
    public function getResult ( ) {

        return $this->out[0][$this->_finalKey];
    }

    /**
     * clean
     * Clean all pointers.
     *
     * @access  public
     * @return  void
     */
    public function clean ( &$array ) {

        foreach($array as $key => &$value) {

            if(is_string($value)) {

                if(preg_match('#{{([0-9]+)}}#', $value))
                    $value = '';
            }
            elseif(is_array($value))
                $this->clean($value);
        }
    }
}
